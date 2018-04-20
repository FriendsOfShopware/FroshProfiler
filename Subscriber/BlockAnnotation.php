<?php

namespace FroshProfiler\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Controller_Front;
use Enlight_Event_EventArgs;
use Enlight_Template_Manager;
use Shopware_Components_Config;
use FroshProfiler\Components\BlockAnnotation\BlockAnnotator;

/**
 * Class BlockAnnotation
 */
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

    /**
     * @param Shopware_Components_Config $config
     * @param array                      $pluginConfig
     * @param BlockAnnotator             $blockAnnotator
     * @param Enlight_Controller_Front   $front
     */
    public function __construct(
        Shopware_Components_Config $config,
        array $pluginConfig,
        BlockAnnotator $blockAnnotator,
        Enlight_Controller_Front $front
    ) {
        $this->config = $config;
        $this->blockAnnotator = $blockAnnotator;
        $this->pluginConfig = $pluginConfig;

        // Disable frontend blocks, if ip is not whitelisted
        if (!empty(trim($this->pluginConfig['whitelistIP'])) && !in_array($front->Request()->getClientIp(), explode("\n", $this->pluginConfig['whitelistIP']))) {
            $this->pluginConfig['frontendblocks'] = false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PreDispatch_Frontend' => 'onPreDispatch',
            'Enlight_Controller_Action_PreDispatch_Widgets' => 'onPreDispatch',
        ];
    }

    /**
     * PreDispatch callback for widget and frontend requests.
     *
     * @param Enlight_Event_EventArgs $args
     *
     * @return bool
     */
    public function onPreDispatch(Enlight_Event_EventArgs $args)
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
        $view->Engine()->registerFilter('pre', [$this, 'preFilter']);
    }

    /**
     * Smarty preFilter callback. Modify template and return.
     *
     * @param $source
     * @param $template
     *
     * @return mixed
     */
    public function preFilter($source, $template)
    {
        return $this->blockAnnotator->annotate($source, $template, $this->pluginConfig);
    }

    /**
     * Set own template directory.
     *
     * @param $templateManager
     */
    private function reconfigureTemplateDirs(Enlight_Template_Manager $templateManager)
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
