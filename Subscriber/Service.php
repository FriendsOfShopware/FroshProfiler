<?php

namespace Shopware\Profiler\Subscriber;

use Doctrine\Common\Cache\FilesystemCache;
use Enlight\Event\SubscriberInterface;
use Shopware\Plugins\Local\Core\Profiler\Components\Helper\CacheHelper;
use Shopware\Profiler\Components\Collector as ProfilerCollector;
use Shopware\Profiler\Components\SmartyExtensions;

class Service implements SubscriberInterface
{
    /** @var  \Shopware_Plugins_Core_Profiler_Bootstrap */
    private $bootstrap;

    public function __construct($bootstrap)
    {
        $this->bootstrap = $bootstrap;
    }

    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Bootstrap_InitResource_profiler.smarty_extensions' => 'onInitSmartyExtensions',
            'Enlight_Bootstrap_InitResource_profiler.collector'         => 'onInitCollectorService',
            'Enlight_Bootstrap_InitResource_profiler.cache'             => 'onInitCacheService',
        ];
    }

    public function onInitSmartyExtensions()
    {
        return new SmartyExtensions();
    }

    public function onInitCollectorService()
    {
        return new ProfilerCollector();
    }

    public function onInitCacheService()
    {
        return new FilesystemCache($this->bootstrap->Path() . 'ProfilerCache');
    }
}
