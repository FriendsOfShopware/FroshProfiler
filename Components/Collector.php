<?php

namespace Shopware\Profiler\Components;

use Shopware\Profiler\Components\Collectors\CollectorInterface;
use Shopware\Profiler\Components\Collectors\ConfigCollector;
use Shopware\Profiler\Components\Collectors\DBCollector;
use Shopware\Profiler\Components\Collectors\EventCollector;
use Shopware\Profiler\Components\Collectors\GeneralCollector;
use Shopware\Profiler\Components\Collectors\PHPCollector;
use Shopware\Profiler\Components\Collectors\SmartyCollector;
use Shopware\Profiler\Components\Collectors\UserCollector;

class Collector {
    private $collectors = [];

    public function getCollectors()
    {
        if(empty($this->collectors)) {
            $this->collectors = [
                new GeneralCollector(),
                new PHPCollector(),
                new UserCollector(),
                new SmartyCollector(),
                new EventCollector(),
                new DBCollector(),
                new ConfigCollector(),
            ];

            $this->collectors = Enlight()->Events()->filter('Profiler_onCollectCollectors', $this->collectors);
        }

        return $this->collectors;
    }

    public function collectInformation(\Enlight_Controller_Action $controller)
    {
        $result = [];

        $collectors = $this->getCollectors();

        foreach($collectors as $collector) {
            if($collector instanceof CollectorInterface) {
                $result = array_merge($result, $collector->collect($controller));
            }
        }

        return $result;
    }
}