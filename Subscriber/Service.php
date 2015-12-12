<?php
namespace Shopware\Profiler\Subscriber;

use Enlight\Event\SubscriberInterface;
use Shopware\Profiler\Components\SmartyExtensions;

class Service implements SubscriberInterface
{
    /** @var  \Shopware_Plugins_Core_Profiler_Bootstrap */
    private $bootstrap;


    public function __construct($bootstrap)
    {
        $this->bootstrap = $bootstrap;
    }

    public static function getSubscribedEvents() {
        return [
            'Enlight_Bootstrap_InitResource_profiler.smarty_extensions' => 'onInitSmartyExtensions',
            'Enlight_Bootstrap_InitResource_profiler.collector' => 'onInitCollectorService',
        ];
    }

    public function onInitSmartyExtensions() {
        return new SmartyExtensions();
    }

    public function onInitCollectorService() {
        return new \Shopware\Profiler\Components\Collector();
    }
}