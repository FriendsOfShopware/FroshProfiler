<?php

namespace ShopwarePlugins\Profiler\Subscriber;

use Enlight\Event\SubscriberInterface;

class Collector implements SubscriberInterface
{
    private $renderedTemplates = [];
    private $templateCalls = 0;
    private $blockCalls = 0;
    private $renderTime = 0;

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

        if (strtolower($controller->Request()->getControllerName()) == 'profiler') {
            return;
        }

        $view = $controller->View();

        Shopware()->Container()->get('profiler.smarty_extensions')->addPlugins($view->Engine());
        Shopware()->Container()->set('profileId', uniqid());
        Shopware()->Container()->set('profileController', $controller);
    }

    public function onRender(\Enlight_Event_EventArgs $eventArgs)
    {
        $this->templateCalls++;
        $name = $this->normalizePath($eventArgs->get('name'));

        // Ignore Profiler Templates in Profiling Result
        if (!strstr($name, '@Profiler') && !strstr($name, 'frontend/profiler/')) {
            if(!isset($this->renderedTemplates[$name])) {
                $this->renderedTemplates[$name] = 1;
            } else {
                $this->renderedTemplates[$name]++;
            }
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
            $profileData = [];
            $profileData['renderedTemplates'] = $this->renderedTemplates;
            $profileData['blockCalls'] = $this->blockCalls;
            $profileData['templateCalls'] = $this->templateCalls;
            $profileData['renderTime'] = $this->renderTime;

            Shopware()->Container()->set('profileData.template', $profileData);
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
