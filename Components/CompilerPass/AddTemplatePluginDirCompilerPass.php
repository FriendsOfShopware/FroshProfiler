<?php

namespace FroshProfiler\Components\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class AddTemplatePluginDirCompilerPass
 */
class AddTemplatePluginDirCompilerPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $template = $container->getDefinition('template');
        $template->addMethodCall('addPluginsDir', [$container->getParameter('frosh_profiler.smarty_dir')]);
    }
}
