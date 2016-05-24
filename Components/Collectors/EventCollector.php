<?php
/**
 * Created by PhpStorm.
 * User: shyim
 * Date: 17.12.15
 * Time: 13:58.
 */
namespace ShopwarePlugins\Profiler\Components\Collectors;

class EventCollector implements CollectorInterface
{
    public function getName()
    {
        return 'Events';
    }

    public function collect(\Enlight_Controller_Action $controller)
    {
        /** @var \ShopwarePlugins\Profiler\Components\Event\EventManager $pluginEventManager */
        $pluginEventManager = Shopware()->Container()->get('profiler.event_manager');

        return [
           'events' => [
               'eventAmount'  => $pluginEventManager->getEventAmount(),
               'calledEvents' => $pluginEventManager->getCalledEvents(),
           ],
        ];
    }

    public function getToolbarTemplate()
    {
        return '@Profiler/toolbar/events.tpl';
    }
}
