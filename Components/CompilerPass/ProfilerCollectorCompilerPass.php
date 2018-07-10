<?php

namespace FroshProfiler\Components\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

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

        $collectorDefinition = $container->getDefinition('frosh_profiler.collector');

        foreach ($services as $id => $tags) {
            $collectorDefinition->addMethodCall('addCollector', [new Reference($id)]);
        }
    }
}
