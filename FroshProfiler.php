<?php

namespace FroshProfiler;

use Doctrine\ORM\Tools\SchemaTool;
use FroshProfiler\Components\CompilerPass\AddTemplatePluginDirCompilerPass;
use FroshProfiler\Components\CompilerPass\CustomCacheCompilerPass;
use FroshProfiler\Components\CompilerPass\CustomEventManagerCompilerPass;
use FroshProfiler\Components\Tracleable\TraceableCompilerPass;
use FroshProfiler\Models\Profile;
use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\ActivateContext;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use Shopware\Components\Plugin\Context\UpdateContext;
use Symfony\Component\DependencyInjection\ContainerBuilder;

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

/** @var \Composer\Autoload\ClassLoader $loader */
global $kernel, $loader;

if (method_exists($kernel, 'getCacheDir')) {
    $loader->addPsr4('FroshProfilerProxy\\', dirname($kernel->getCacheDir()) . '/tracer/FroshProfilerProxy/');
}

/**
 * Class FroshProfiler
 */
class FroshProfiler extends Plugin
{
    /**
     * @param ActivateContext $context
     */
    public function activate(ActivateContext $context)
    {
        $context->scheduleClearCache(InstallContext::CACHE_LIST_DEFAULT);
    }

    /**
     * @param InstallContext $context
     */
    public function install(InstallContext $context)
    {
        $this->installSchema();
    }

    /**
     * @param UpdateContext $context
     */
    public function update(UpdateContext $context)
    {
        parent::update($context);
        $this->installSchema();
    }

    /**
     * @param UninstallContext $context
     */
    public function uninstall(UninstallContext $context)
    {
        parent::uninstall($context);
        $this->uninstallSchema();
    }

    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        $container->setParameter('frosh_profiler.plugin_dir', $this->getPath());

        parent::build($container);

        $container->addCompilerPass(new CustomEventManagerCompilerPass());
        $container->addCompilerPass(new AddTemplatePluginDirCompilerPass());
        $container->addCompilerPass(new CustomCacheCompilerPass());
        $container->addCompilerPass(new TraceableCompilerPass());
    }

    /**
     * Install or update profile table
     */
    private function installSchema()
    {
        $tool = new SchemaTool($this->container->get('models'));

        $tool->updateSchema([$this->container->get('models')->getClassMetadata(Profile::class)], true);
    }

    /**
     * Remove profile table
     */
    private function uninstallSchema()
    {
        $tool = new SchemaTool($this->container->get('models'));

        $tool->dropSchema([$this->container->get('models')->getClassMetadata(Profile::class)]);
    }
}
