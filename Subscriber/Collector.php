<?php

namespace Shopware\Profiler\Subscriber;

use Enlight\Event\SubscriberInterface;

class Collector implements SubscriberInterface
{
    /** @var  \Shopware_Plugins_Core_Profiler_Bootstrap */
    private $bootstrap;
    private $renderedTemplates = [];
    private $templateCalls = 0;
    private $blockCalls = 0;
    private $renderTime = 0;

    public function __construct($bootstrap)
    {
        $this->bootstrap = $bootstrap;
    }

    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PostDispatch_Frontend' => 'onPostDispatchFrontend',
            'Profiler_Smarty_Render' => 'onRender',
            'Profiler_Smarty_Render_Block' => 'onRenderBlock',
            'Profiler_Smarty_RenderTime' => 'onRenderTime',
            'Enlight_Controller_Front_DispatchLoopShutdown' => 'onDispatchLoopShutdown'
        ];
    }

    public function onPostDispatchFrontend(\Enlight_Event_EventArgs $args)
    {
        /** @var \Enlight_Controller_Action $controller */
        $controller = $args->getSubject();

        if (strtolower($controller->Request()->getControllerName()) == 'profiler' || strtolower($controller->Request()->getControllerName()) == 'error') {
            return;
        }

        $view = $controller->View();

        Shopware()->Container()->get('profiler.smarty_extensions')->addPlugins($view->Engine());
        $view->sProfiler = Shopware()->Container()->get('profiler.collector')->collectInformation($controller);
        $view->sProfilerCollectors = Shopware()->Container()->get('profiler.collector')->getCollectors();
        $view->sProfilerID = uniqid();
        Shopware()->Container()->set('profileId', $view->sProfilerID);
        Shopware()->Container()->set('profileData', $view->sProfiler);

        $view->addTemplateDir($this->bootstrap->Path() . '/Views');
        $view->extendsTemplate('@Profiler/index.tpl');
    }

    public function onRender(\Enlight_Event_EventArgs $eventArgs)
    {
        $this->templateCalls++;
        $name = $this->normalizePath($eventArgs->get('name'));
        if(!isset($this->renderedTemplates[$name])) {
            $this->renderedTemplates[$name] = 1;
        } else {
            $this->renderedTemplates[$name]++;
        }
    }

    public function onRenderBlock()
    {
        $this->blockCalls++;
    }

    public function onRenderTime(\Enlight_Event_EventArgs $eventArgs)
    {
        $this->renderTime = $eventArgs->get('time');
    }

    public function onDispatchLoopShutdown()
    {
        if (Shopware()->Container()->has('profileId')) {
            $profileData = Shopware()->Container()->get('profileData');
            $profileData['template']['renderedTemplates'] = $this->renderedTemplates;
            $profileData['template']['blockCalls'] = $this->blockCalls;
            $profileData['template']['templateCalls'] = $this->templateCalls;
            $profileData['template']['renderTime'] = $this->renderTime;

            Shopware()->Container()->get('profiler.collector')->saveCollectInformation(
                Shopware()->Container()->get('profileId'),
                $profileData
            );
        }
    }

    private function normalizePath($path)
    {
        if(strstr($path, 'frontend')) {
            $pos = strpos($path, 'frontend');
            $path = substr($path, $pos);
        }

        if(strstr($path, 'widgets')) {
            $pos = strpos($path, 'widgets');
            $path = substr($path, $pos);
        }

        return $path;
    }
}
