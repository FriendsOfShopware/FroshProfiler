<?php

namespace ShopwarePlugins\Profiler\Components;

use ShopwarePlugins\Profiler\Components\Collectors\CollectorInterface;
use ShopwarePlugins\Profiler\Components\Collectors\ConfigCollector;
use ShopwarePlugins\Profiler\Components\Collectors\DBCollector;
use ShopwarePlugins\Profiler\Components\Collectors\EventCollector;
use ShopwarePlugins\Profiler\Components\Collectors\ExceptionCollector;
use ShopwarePlugins\Profiler\Components\Collectors\GeneralCollector;
use ShopwarePlugins\Profiler\Components\Collectors\PHPCollector;
use ShopwarePlugins\Profiler\Components\Collectors\SmartyCollector;
use ShopwarePlugins\Profiler\Components\Collectors\UserCollector;

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

            $this->collectors = Enlight()->Events()->filter('Profiler_onCollectCollectors', $this->collectors);
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
        Shopware()->Container()->get('profiler.cache')->save($id, $information);

        $indexArray = Shopware()->Container()->get('profiler.cache')->fetch('index');
        if(empty($indexArray)) {
            $indexArray = [];
        }

        $indexArray[$id] = array_merge($information['request'], $information['response']);

        Shopware()->Container()->get('profiler.cache')->save('index', $indexArray);

        return $id;
    }
}
