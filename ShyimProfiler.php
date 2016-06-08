<?php

namespace ShyimProfiler;

use Doctrine\DBAL\Logging\DebugStack;
use Shopware\Components\Plugin;
use ShyimProfiler\Components\CompilerPass\EventListenerCompilerPass;
use ShyimProfiler\Components\CompilerPass\EventSubscriberCompilerPass;
use ShyimProfiler\Subscriber\Decorator;
use ShyimProfiler\Subscriber\Service;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ShyimProfiler extends Plugin
{
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Front_StartDispatch' => 'onStartDispatch'
        ];
    }

    public function build(ContainerBuilder $container)
    {
        $container->setParameter('shyim_profiler.plugin_dir', $this->getPath() . '/');
        $container->setParameter('shyim_profiler.cache_dir', $this->getPath() . '/ProfilerCache');
        parent::build($container);
        
        $container->addCompilerPass(new EventListenerCompilerPass());
        $container->addCompilerPass(new EventSubscriberCompilerPass());
    }

    public function onStartDispatch()
    {
        require_once $this->getPath() . '/vendor/autoload.php';
        define('STARTTIME', microtime(true));

        $uri = Shopware()->Container()->get('front')->Request()->getRequestUri();
        if(!strstr($uri, '/backend') && !strstr($uri, '/api') && !strstr($uri, 'Profiler')) {
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
        Shopware()->Db()->setProfiler(new \Zend_Db_Profiler(true));

        // Doctrine Profiler
        $logger = new DebugStack();
        $logger->enabled = true;
        Shopware()->Models()->getConfiguration()->setSQLLogger($logger);
    }
}
