<?php

namespace ShyimProfiler\Subscriber;

use Enlight\Event\SubscriberInterface;
use Shopware\Components\DependencyInjection\Container;

class Collector implements SubscriberInterface
{
    /**
     * @var Container
     */
    private $container;
    private $config = [];

    private $templateDirConfigured = false;

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
            'Enlight_Controller_Front_DispatchLoopShutdown' => 'onDispatchLoopShutdown',
            'Enlight_Controller_Action_PreDispatch_Frontend' => 'onPreDispatch',
            'Enlight_Controller_Action_PreDispatch_Widgets' => 'onPreDispatch'
        ];
    }

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->config = $this->container->get('shopware.plugin.config_reader')->getByPluginName('ShyimProfiler');
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
        $view->addTemplateDir($this->container->getParameter('shyim_profiler.plugin_dir') . '/Resources/views');
        $view->assign('sProfilerID', $profileId);

        $this->container->get('shyim_profiler.smarty_extensions')->addPlugins($view->Engine());
        $this->container->set('profileId', $profileId);
        $this->container->set('profileController', $controller);
    }

    public function onRender(\Enlight_Event_EventArgs $eventArgs)
    {
        $this->templateCalls++;
        $name = $this->normalizePath($eventArgs->get('name'));

        if (!isset($this->renderedTemplates[$name])) {
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
        if (!$this->container->has('profileId')) {
            return;
        }

        if (!$this->container->has('front')) {
            return;
        }

        /** @var \Enlight_Controller_Response_ResponseHttp $response */
        $response = $args->get('response');

        $profileTemplate = [];
        $profileTemplate['renderedTemplates'] = $this->renderedTemplates;
        $profileTemplate['blockCalls'] = $this->blockCalls;
        $profileTemplate['templateCalls'] = $this->templateCalls;
        $profileTemplate['renderTime'] = $this->renderTime;

        $profileData = $this->container->get('shyim_profiler.collector')->collectInformation($this->container->get('profileController'));
        $profileData['template'] = array_merge($profileData['template'], $profileTemplate);

        $this->container->get('shyim_profiler.collector')->saveCollectInformation(
            $this->container->get('profileId'),
            $profileData
        );

        $view = $this->container->get('template');

        $view->assign('sProfiler', $profileData);
        $view->assign('sProfilerCollectors', $this->container->get('shyim_profiler.collector')->getCollectors());
        $view->assign('sProfilerID', $this->container->get('profileId'));
        $view->assign('sProfilerTime', round(microtime(true) - STARTTIME, 3));

        $view->addTemplateDir($this->container->getParameter('shyim_profiler.plugin_dir') . '/Resources/views/');
        $profileTemplate = $view->fetch('@Profiler/index.tpl');

        $content = $response->getBody();

        $content = str_replace('</body>', $profileTemplate . '</body>', $content);
        $response->setBody($content);
    }

    private function normalizePath($path)
    {
        if (strstr($path, 'frontend')) {
            $pos = strpos($path, 'frontend');
            $path = substr($path, $pos);
        }

        if (strstr($path, 'widgets')) {
            $pos = strpos($path, 'widgets');
            $path = substr($path, $pos);
        }

        return $path;
    }

    /**
     * PreDispatch callback for widget and frontend requests
     *
     * @param \Enlight_Event_EventArgs $args
     * @return bool
     */
    public function onPreDispatch(\Enlight_Event_EventArgs $args)
    {
        if (!$this->config['frontendblocks']) {
            return;
        }

        /** @var $controller \Enlight_Controller_Action */
        $controller = $args->getSubject();
        $view = $controller->View();

        // set own caching dirs
        $this->reconfigureTemplateDirs($view->Engine());
        // configure shopware to not strip HTML comments
        Shopware()->Config()->offsetSet('sSEOREMOVECOMMENTS', false);
        $view->Engine()->registerFilter('pre', array($this, 'preFilter'));
    }

    /**
     * Smarty preFilter callback. Modify template and return
     *
     * @param $source
     * @param $template
     * @return mixed
     */
    public function preFilter($source, $template)
    {
        return $this->container->get('shyim_profiler.block_annotator')->annotate($source);
    }

    /**
     * Set own template directory
     *
     * @param $templateManager
     */
    private function reconfigureTemplateDirs(\Enlight_Template_Manager $templateManager)
    {
        if (!$this->templateDirConfigured) {
            $compileDir = $templateManager->getCompileDir() . 'blocks/';
            $cacheDir = $templateManager->getCacheDir() . 'blocks/';
            $templateManager->setCompileDir($compileDir);
            $templateManager->setCacheDir($cacheDir);
            $this->templateDirConfigured = true;
        }
    }
}
