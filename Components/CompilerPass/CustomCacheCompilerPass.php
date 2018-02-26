<?php

namespace ShyimProfiler\Components\CompilerPass;

use ShyimProfiler\Components\Cache\Cache;
use ShyimProfiler\Components\Cache\CacheFactory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class CustomCacheCompilerPass
 */
class CustomCacheCompilerPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('cache');
        $definition->setClass(Cache::class);
        $definition = $container->getDefinition('cache_factory');
        $definition->setClass(CacheFactory::class);
    }
}
