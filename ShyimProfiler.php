<?php

namespace ShyimProfiler;

use Doctrine\DBAL\Logging\DebugStack;
use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\ActivateContext;
use Shopware\Components\Plugin\Context\InstallContext;
use ShyimProfiler\Components\CompilerPass\EventListenerCompilerPass;
use ShyimProfiler\Components\CompilerPass\EventSubscriberCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ShyimProfiler extends Plugin
{
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Front_StartDispatch' => 'onStartDispatch'
        ];
    }

    public function activate(ActivateContext $context)
    {
        $context->scheduleClearCache(InstallContext::CACHE_LIST_ALL);
    }


    public function build(ContainerBuilder $container)
    {
        $container->setParameter('shyim_profiler.plugin_dir', $this->getPath());

        parent::build($container);

        $container->addCompilerPass(new EventListenerCompilerPass());
        $container->addCompilerPass(new EventSubscriberCompilerPass());
    }

    public function onStartDispatch()
    {
        require_once $this->getPath() . '/vendor/autoload.php';
        define('STARTTIME', microtime(true));

        $uri = $this->container->get('front')->Request()->getRequestUri();
        if (!strstr($uri, '/backend') && !strstr($uri, '/api') && !strstr($uri, 'Profiler')) {
            /**
             * Set a custom SYSPLUGINS Path, to disable default smarty autoloading
             */
            define('SMARTY_SYSPLUGINS_DIR', $this->getPath() . '/Components/Smarty/sysplugins/');
        }

        $this->initDatabaseProfiler();
    }

    private function initDatabaseProfiler()
    {
        // Zend DB Profiler
        $this->container->get('db')->setProfiler(new \Zend_Db_Profiler(true));

        // Doctrine Profiler
        $logger = new DebugStack();
        $logger->enabled = true;
        $this->container->get('models')->getConfiguration()->setSQLLogger($logger);
    }
}
