<?php

namespace ShyimProfiler\Components;

use Doctrine\Common\Cache\CacheProvider;
use Enlight_Event_EventManager;
use Monolog\Formatter\NormalizerFormatter;
use ShyimProfiler\Components\Collectors\CollectorInterface;

class Collector
{
    /**
     * @var CollectorInterface[]
     */
    private $collectors = [];

    /**
     * @var Enlight_Event_EventManager
     */
    private $events;

    /**
     * @var CacheProvider
     */
    private $cache;

    /**
     * @var NormalizerFormatter
     */
    private $normalizer;

    public function __construct(Enlight_Event_EventManager $events, CacheProvider $cache)
    {
        $this->events = $events;
        $this->cache = $cache;
        $this->normalizer = new NormalizerFormatter();
    }

    /**
     * @param CollectorInterface $collector
     *
     * @author Soner Sayakci <s.sayakci@gmail.com>
     */
    public function addCollector(CollectorInterface $collector)
    {
        $this->collectors[] = $collector;
    }

    /**
     * @return CollectorInterface[]
     *
     * @author Soner Sayakci <s.sayakci@gmail.com>
     */
    public function getCollectors()
    {
        return $this->collectors;
    }

    public function collectInformation(\Enlight_Controller_Action $controller)
    {
        $result = [];

        foreach ($this->collectors as $collector) {
            if ($collector instanceof CollectorInterface) {
                $result = array_merge($result, $collector->collect($controller));
            }
        }

        return $result;
    }

    public function saveCollectInformation($id, $information, $subrequets = false)
    {
        $information = $this->normalizer->format($information);

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
