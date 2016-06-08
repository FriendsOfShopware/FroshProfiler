<?php

namespace ShyimProfiler\Subscriber;

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

        $profileId = uniqid();

        $view = $controller->View();
        $view->addTemplateDir(Shopware()->Container()->getParameter('shyim_profiler.plugin_dir') . '/Resources/views');
        $view->assign('sProfilerID', $profileId);

        Shopware()->Container()->get('shyim_profiler.smarty_extensions')->addPlugins($view->Engine());
        Shopware()->Container()->set('profileId', $profileId);
        Shopware()->Container()->set('profileController', $controller);
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

    public function onDispatchLoopShutdown(\Enlight_Event_EventArgs $args)
    {
        if (Shopware()->Container()->has('profileId')) {
            /** @var \Enlight_Controller_Response_ResponseHttp $response */
            $response = $args->get('response');

            $profileTemplate = [];
            $profileTemplate['renderedTemplates'] = $this->renderedTemplates;
            $profileTemplate['blockCalls'] = $this->blockCalls;
            $profileTemplate['templateCalls'] = $this->templateCalls;
            $profileTemplate['renderTime'] = $this->renderTime;

            if (Shopware()->Container()->has('front') && Shopware()->Container()->has('profileId')) {
                $profileData = Shopware()->Container()->get('shyim_profiler.collector')->collectInformation(Shopware()->Container()->get('profileController'));
                $profileData['template'] = array_merge($profileData['template'], $profileTemplate);

                Shopware()->Container()->get('shyim_profiler.collector')->saveCollectInformation(
                    Shopware()->Container()->get('profileId'),
                    $profileData
                );

                $view = Shopware()->Container()->get('template');

                $view->assign('sProfiler', $profileData);
                $view->assign('sProfilerCollectors', Shopware()->Container()->get('shyim_profiler.collector')->getCollectors());
                $view->assign('sProfilerID', Shopware()->Container()->get('profileId'));
                $view->assign('sProfilerTime', round(microtime(true) - STARTTIME, 3));

                $view->addTemplateDir(Shopware()->Container()->getParameter('shyim_profiler.plugin_dir') . '/Resources/views/');
                $profileTemplate = $view->fetch('@Profiler/index.tpl');

                $content = $response->getBody();

                $content = str_replace('</body>', $profileTemplate . '</body>', $content);
                $response->setBody($content);
            }
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
