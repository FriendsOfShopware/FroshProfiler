<?php

namespace Shopware\Profiler\Subscriber;

use Enlight\Event\SubscriberInterface;

class Collector implements SubscriberInterface
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
            'Enlight_Controller_Action_PostDispatch_Frontend' => 'onPostDispatchFrontend',
        ];
    }

    public function onPostDispatchFrontend(\Enlight_Event_EventArgs $args)
    {
        /** @var \Enlight_Controller_Action $controller */
        $controller = $args->getSubject();

        $view = $controller->View();

        Shopware()->Container()->get('profiler.smarty_extensions')->addPlugins($view->Engine());
        $view->sProfiler = Shopware()->Container()->get('profiler.collector')->collectInformation($controller);
        $view->sProfilerCollectors = Shopware()->Container()->get('profiler.collector')->getCollectors();

        $view->addTemplateDir($this->bootstrap->Path().'/Views');
        $view->extendsTemplate('@Profiler/index.tpl');
    }
}
