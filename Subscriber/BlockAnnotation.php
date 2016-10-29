<?php

namespace ShyimProfiler\Subscriber;

use Enlight\Event\SubscriberInterface;
use Shopware\Components\DependencyInjection\Container;

class BlockAnnotation implements SubscriberInterface
{
    /**
     * @var Container
     */
    private $container;
    private $config = [];
    private $templateDirConfigured = false;

    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PreDispatch_Frontend' => 'onPreDispatch',
            'Enlight_Controller_Action_PreDispatch_Widgets' => 'onPreDispatch',
        ];
    }

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->config = $this->container->get('shopware.plugin.cached_config_reader')->getByPluginName('ShyimProfiler');

        // Disable frontend blocks, if ip is not whitelisted
        if (!empty($this->config['whitelistIP']) && !in_array(Shopware()->Front()->Request()->getClientIp(), explode("\n", $this->config['whitelistIP']))) {
            $this->config['frontendblocks'] = false;
        }
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
