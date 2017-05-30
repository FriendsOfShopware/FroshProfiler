<?php

namespace ShyimProfiler\Components\CompilerPass;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use ShyimProfiler\Components\Event\EventManager;

/**
 * Class CustomEventService
 * @package ShyimProfiler\Components\CompilerPass
 */
class CustomEventService implements CompilerPassInterface
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
