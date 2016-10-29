<?php

namespace ShyimProfiler\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Controller_Front;
use Shopware\Components\DependencyInjection\Container;
use Shopware\Components\Plugin\CachedConfigReader;
use Shopware_Components_Config;
use ShyimProfiler\Components\BlockAnnotation\BlockAnnotator;

class BlockAnnotation implements SubscriberInterface
{
    /**
     * @var Shopware_Components_Config
     */
    private $config;

    /**
     * @var BlockAnnotator
     */
    private $blockAnnotator;

    /**
     * @var array
     */
    private $pluginConfig = [];

    /**
     * @var bool
     */
    private $templateDirConfigured = false;

    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PreDispatch_Frontend' => 'onPreDispatch',
            'Enlight_Controller_Action_PreDispatch_Widgets' => 'onPreDispatch',
        ];
    }

    /**
     * @param Shopware_Components_Config $config
     * @param CachedConfigReader $cachedConfigReader
     * @param BlockAnnotator $blockAnnotator
     * @param Enlight_Controller_Front $front
     */
    public function __construct(
        Shopware_Components_Config $config,
        CachedConfigReader $cachedConfigReader,
        BlockAnnotator $blockAnnotator,
        Enlight_Controller_Front $front
    ){
        $this->config = $config;
        $this->blockAnnotator = $blockAnnotator;
        $this->pluginConfig = $cachedConfigReader->getByPluginName('ShyimProfiler');

        // Disable frontend blocks, if ip is not whitelisted
        if (!empty($this->pluginConfig['whitelistIP']) && !in_array($front->Request()->getClientIp(), explode("\n", $this->pluginConfig['whitelistIP']))) {
            $this->pluginConfig['frontendblocks'] = false;
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
        if (!$this->pluginConfig['frontendblocks']) {
            return;
        }

        /** @var $controller \Enlight_Controller_Action */
        $controller = $args->getSubject();
        $view = $controller->View();

        // set own caching dirs
        $this->reconfigureTemplateDirs($view->Engine());

        // configure shopware to not strip HTML comments
        $this->config->offsetSet('sSEOREMOVECOMMENTS', false);
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
        return $this->blockAnnotator->annotate($source);
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
