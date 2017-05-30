<?php

namespace ShyimProfiler\Components\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class ProfilerCollectorCompilerPass.
 */
class ProfilerCollectorCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $services = $container->findTaggedServiceIds('profiler.collector');

        $collectorDefinition = $container->getDefinition('shyim_profiler.collector');

        foreach ($services as $id => $tags) {
            $collectorDefinition->addMethodCall('addCollector', [$container->getDefinition($id)]);
        }
    }
}
