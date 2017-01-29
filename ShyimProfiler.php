<?php

namespace ShyimProfiler;

use Doctrine\DBAL\Logging\DebugStack;
use Doctrine\ORM\Tools\SchemaTool;
use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\ActivateContext;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use ShyimProfiler\Components\CompilerPass\EventListenerCompilerPass;
use ShyimProfiler\Components\CompilerPass\EventSubscriberCompilerPass;
use ShyimProfiler\Components\CompilerPass\ProfilerCollectorCompilerPass;
use ShyimProfiler\Models\Profile;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ShyimProfiler extends Plugin
{
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Front_StartDispatch' => 'onStartDispatch',
        ];
    }

    public function activate(ActivateContext $context)
    {
        $context->scheduleClearCache(InstallContext::CACHE_LIST_ALL);
    }

    public function install(InstallContext $context)
    {
        parent::install($context);
        $this->installSchema();
    }

    public function uninstall(UninstallContext $context)
    {
        parent::uninstall($context);
        $this->uninstallSchema();
    }

    public function build(ContainerBuilder $container)
    {
        $container->setParameter('shyim_profiler.plugin_dir', $this->getPath());

        parent::build($container);

        $container->addCompilerPass(new EventListenerCompilerPass());
        $container->addCompilerPass(new EventSubscriberCompilerPass());
        $container->addCompilerPass(new ProfilerCollectorCompilerPass());
    }

    public function onStartDispatch()
    {
        if (file_exists($this->getPath() . '/vendor/autoload.php')) {
            require_once $this->getPath() . '/vendor/autoload.php';
        }

        define('STARTTIME', microtime(true));

        $uri = $this->container->get('front')->Request()->getRequestUri();
        if (strpos($uri, '/backend') === false && strpos($uri, '/api') === false && strpos($uri, 'Profiler') === false) {
            /*
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

    /**
     * Install or update profile table
     * @author Soner Sayakci <s.sayakci@gmail.com>
     */
    private function installSchema()
    {
        $tool = new SchemaTool($this->container->get('models'));

        $tool->updateSchema([$this->container->get('models')->getClassMetadata(Profile::class)], true);
    }

    /**
     * Remove profile table
     * @author Soner Sayakci <s.sayakci@gmail.com>
     */
    private function uninstallSchema()
    {
        $tool = new SchemaTool($this->container->get('models'));

        $tool->dropSchema([$this->container->get('models')->getClassMetadata(Profile::class)]);
    }
}
