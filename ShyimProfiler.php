<?php

namespace ShyimProfiler;

use Doctrine\DBAL\Logging\DebugStack;
use Doctrine\ORM\Tools\SchemaTool;
use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\ActivateContext;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use Shopware\Components\Plugin\Context\UpdateContext;
use ShyimProfiler\Components\CompilerPass\AddTemplatePluginDirCompilerPass;
use ShyimProfiler\Components\CompilerPass\CustomEventService;
use ShyimProfiler\Components\CompilerPass\ProfilerCollectorCompilerPass;
use ShyimProfiler\Models\Profile;
use Symfony\Component\DependencyInjection\ContainerBuilder;

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

/**
 * Class ShyimProfiler
 * @package ShyimProfiler
 */
class ShyimProfiler extends Plugin
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
        $container->setParameter('shyim_profiler.plugin_dir', $this->getPath());

        parent::build($container);

        $container->addCompilerPass(new CustomEventService());
        $container->addCompilerPass(new ProfilerCollectorCompilerPass());
        $container->addCompilerPass(new AddTemplatePluginDirCompilerPass());
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
