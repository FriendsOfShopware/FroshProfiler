<?php

namespace ShyimProfiler\Components\CompilerPass;

use ShyimProfiler\Components\Event\EventManager;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class CustomEventManagerCompilerPass
 */
class CustomEventManagerCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('events');
        $definition->setClass(EventManager::class);
    }
}
