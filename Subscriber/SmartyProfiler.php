<?php

namespace FroshProfiler\Subscriber;

use Composer\Autoload\ClassLoader;
use Enlight\Event\SubscriberInterface;

/**
 * Class SmartyProfiler
 */
class SmartyProfiler implements SubscriberInterface
{
    /**
     * @var string
     */
    private $pluginDir;

    public function __construct(string $pluginDir)
    {
        $this->pluginDir = $pluginDir;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'Enlight_Bootstrap_InitResource_template' => 'onInitTemplate',
        ];
    }

    public function onInitTemplate(): void
    {
        /** @var ClassLoader $loader */
        global $loader;

        if (!empty($_SERVER['REQUEST_URI'])) {
            if (strpos($_SERVER['REQUEST_URI'], '/backend') === false && strpos($_SERVER['REQUEST_URI'], '/api') === false && strpos($_SERVER['REQUEST_URI'], 'Profiler') === false) {
                /*
                 * Set a custom SYSPLUGINS Path, to disable default smarty autoloading
                 */
                define('SMARTY_SYSPLUGINS_DIR', $this->pluginDir);

                $loader->addClassMap([
                    'Smarty_Internal_Compile_Block' => $this->pluginDir . '/smarty_internal_compile_block.php',
                    'Smarty_Internal_Resource_Extends' => $this->pluginDir . '/smarty_internal_resource_extends.php',
                    'Smarty_Internal_Resource_File' => $this->pluginDir . '/smarty_internal_resource_file.php',
                    'Smarty_Internal_TemplateBase' => $this->pluginDir . '/smarty_internal_templatebase.php',
                ]);
            }
        }
    }
}
