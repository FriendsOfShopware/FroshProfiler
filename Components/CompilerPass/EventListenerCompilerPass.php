<?php
namespace ShyimProfiler\Components\CompilerPass;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * @category  Shopware
 * @package   Shopware\Components\DependencyInjection\Compiler
 * @copyright Copyright (c) shopware AG (http://www.shopware.de)
 */
class EventListenerCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('shyim_profiler.event_manager')) {
            return;
        }

        $definition = $container->getDefinition('shyim_profiler.event_manager');

        foreach ($container->findTaggedServiceIds('shopware.event_listener') as $id => $events) {
            $def = $container->getDefinition($id);
            if (!$def->isPublic()) {
                throw new \InvalidArgumentException(sprintf('The service "%s" must be public as event listeners are lazy-loaded.', $id));
            }
            if ($def->isAbstract()) {
                throw new \InvalidArgumentException(sprintf('The service "%s" must not be abstract as event listeners are lazy-loaded.', $id));
            }

            foreach ($events as $event) {
                $priority = isset($event['priority']) ? $event['priority'] : 0;
                if (!isset($event['event'])) {
                    throw new \InvalidArgumentException(sprintf('Service "%s" must define the "event" attribute on "%s" tags.', $id, 'shopware.event_listener'));
                }

                if (!isset($event['method'])) {
                    throw new \InvalidArgumentException(sprintf('Service "%s" must define the "method" attribute on "%s" tags.', $id, 'shopware.event_listener'));
                }

                $definition->addMethodCall('addListenerService', [$event['event'], [$id, $event['method']], $priority]);
            }
        }
    }
}
