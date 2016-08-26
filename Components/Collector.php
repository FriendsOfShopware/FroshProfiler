<?php

namespace ShyimProfiler\Components;

use Doctrine\Common\Cache\CacheProvider;
use ShyimProfiler\Components\Collectors\CollectorInterface;
use ShyimProfiler\Components\Collectors\ConfigCollector;
use ShyimProfiler\Components\Collectors\DBCollector;
use ShyimProfiler\Components\Collectors\EventCollector;
use ShyimProfiler\Components\Collectors\ExceptionCollector;
use ShyimProfiler\Components\Collectors\GeneralCollector;
use ShyimProfiler\Components\Collectors\PHPCollector;
use ShyimProfiler\Components\Collectors\SmartyCollector;
use ShyimProfiler\Components\Collectors\UserCollector;

class Collector
{
    /**
     * @var CollectorInterface
     */
    private $collectors = [];

    /**
     * @var \Enlight_Event_EventManager
     */
    private $events;

    /**
     * @var CacheProvider
     */
    private $cache;

    public function __construct(\Enlight_Event_EventManager $events, CacheProvider $cache)
    {
        $this->events = $events;
        $this->cache = $cache;
    }

    public function getCollectors()
    {
        if (empty($this->collectors)) {
            $this->collectors = [
                new GeneralCollector(),
                new PHPCollector(),
                new UserCollector(),
                new SmartyCollector(),
                new EventCollector(),
                new DBCollector(),
                new ConfigCollector(),
                new ExceptionCollector()
            ];

            $this->collectors = $this->events->filter('Profiler_onCollectCollectors', $this->collectors);
        }

        return $this->collectors;
    }

    public function collectInformation(\Enlight_Controller_Action $controller)
    {
        $result = [];

        $collectors = $this->getCollectors();

        foreach ($collectors as $collector) {
            if ($collector instanceof CollectorInterface) {
                $result = array_merge($result, $collector->collect($controller));
            }
        }

        return $result;
    }

    public function saveCollectInformation($id, $information, $subrequets = false)
    {
        if ($subrequets) {
            $data = $this->cache->fetch($id);
            $data['subrequest'][] = $information;
            $this->cache->save($id, $data);
        } else {
            $this->cache->save($id, $information);

            $indexArray = $this->cache->fetch('index');
            if (empty($indexArray)) {
                $indexArray = [];
            }

            $indexArray[$id] = array_merge($information['request'], $information['response']);

            $this->cache->save('index', $indexArray);
        }

        return $id;
    }
}
