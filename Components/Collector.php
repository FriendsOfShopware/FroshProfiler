<?php

namespace ShyimProfiler\Components;

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
    private $collectors = [];

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

            $this->collectors = Shopware()->Events()->filter('Profiler_onCollectCollectors', $this->collectors);
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

    public function saveCollectInformation($id, $information)
    {
        Shopware()->Container()->get('shyim_profiler.cache')->save($id, $information);

        $indexArray = Shopware()->Container()->get('shyim_profiler.cache')->fetch('index');
        if(empty($indexArray)) {
            $indexArray = [];
        }

        $indexArray[$id] = array_merge($information['request'], $information['response']);

        Shopware()->Container()->get('shyim_profiler.cache')->save('index', $indexArray);

        return $id;
    }
}
