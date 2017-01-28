<?php

namespace ShyimProfiler\Components;

use Doctrine\Common\Cache\CacheProvider;
use Enlight_Event_EventManager;
use Monolog\Formatter\NormalizerFormatter;
use Shopware\Components\Plugin\CachedConfigReader;
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

    /**
     * @var array
     */
    private $pluginConfig;

    public function __construct(Enlight_Event_EventManager $events, CacheProvider $cache, CachedConfigReader $configReader)
    {
        $this->events = $events;
        $this->cache = $cache;
        $this->normalizer = new NormalizerFormatter();
        $this->pluginConfig = $configReader->getByPluginName('ShyimProfiler');
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

            if (count($indexArray) > $this->pluginConfig['maxProfiles'] && !empty($this->pluginConfig['maxProfiles'])) {
                $deleteProfiles = count($indexArray) - $this->pluginConfig['maxProfiles'];

                foreach ($indexArray as $key => $item) {
                    if ($deleteProfiles == 0) {
                        break;
                    }

                    $this->cache->delete($key);
                    unset($indexArray[$key]);
                    $deleteProfiles--;
                }
            }

            $this->cache->save('index', $indexArray);
        }

        return $id;
    }
}
