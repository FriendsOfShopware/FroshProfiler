<?php

namespace FroshProfiler\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Controller_Front;
use Enlight_Event_EventArgs;
use Enlight_Template_Manager;
use FroshProfiler\Components\BlockAnnotation\BlockAnnotator;
use Shopware_Components_Config;

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

    public static function getSubscribedEvents(): array
    {
        return [
            'Enlight_Controller_Action_PreDispatch_Frontend' => 'onPreDispatch',
            'Enlight_Controller_Action_PreDispatch_Widgets' => 'onPreDispatch',
        ];
    }

    public function onPreDispatch(Enlight_Event_EventArgs $args): void
    {
        if (!$this->pluginConfig['frontendblocks']) {
            return;
        }

        /** @var \Enlight_Controller_Action $controller */
        $controller = $args->get('subject');
        $view = $controller->View();

        // set own caching dirs
        $this->reconfigureTemplateDirs($view->Engine());

        // configure shopware to not strip HTML comments
        $this->config->offsetSet('sSEOREMOVECOMMENTS', false);
        $view->Engine()->registerFilter('pre', [$this, 'preFilter']);
    }

    public function preFilter(string $source, \Smarty_Internal_Template $template): string
    {
        return $this->blockAnnotator->annotate($source, $template, $this->pluginConfig);
    }

    private function reconfigureTemplateDirs(Enlight_Template_Manager $templateManager): void
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
