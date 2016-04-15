<?php
namespace Shopware\Profiler\Subscriber;

use Enlight\Event\SubscriberInterface;
use Shopware\Profiler\Components\Logger;

class Decorator implements SubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Bootstrap_AfterInitResource_pluginlogger' => 'onInitLogger',
            'Enlight_Bootstrap_AfterInitResource_corelogger' => 'onInitLogger',
            'Enlight_Bootstrap_AfterInitResource_debuglogger' => 'onInitLogger',
        ];
    }

    public function onInitLogger(\Enlight_Event_EventArgs $eventArgs)
    {
        $name = str_replace('Enlight_Bootstrap_AfterInitResource_', '', $eventArgs->getName());
        Shopware()->Container()->set($name, new Logger(
            Shopware()->Container()->get($name),
            $name
        ));
    }
}