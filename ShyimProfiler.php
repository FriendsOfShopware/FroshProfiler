<?php

namespace ShyimProfiler;

use Doctrine\DBAL\Logging\DebugStack;
use Doctrine\ORM\Tools\SchemaTool;
use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\ActivateContext;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use ShyimProfiler\Components\CompilerPass\AddTemplatePluginDirCompilerPass;
use ShyimProfiler\Components\CompilerPass\CustomEventService;
use ShyimProfiler\Components\CompilerPass\ProfilerCollectorCompilerPass;
use ShyimProfiler\Models\Profile;
use Symfony\Component\DependencyInjection\ContainerBuilder;

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

class ShyimProfiler extends Plugin
{
    public function activate(ActivateContext $context)
    {
        $context->scheduleClearCache(InstallContext::CACHE_LIST_DEFAULT);
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

        $container->addCompilerPass(new CustomEventService());
        $container->addCompilerPass(new ProfilerCollectorCompilerPass());
        $container->addCompilerPass(new AddTemplatePluginDirCompilerPass());
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
