<?php

namespace FroshProfiler\Components\Tracleable;

use Shopware\Bundle\MediaBundle\Strategy\StrategyInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class TraceableCompilerPass implements CompilerPassInterface
{
    private $traceableServices = [
        'shopware.emotion',
        'shopware.routing',
        'shopware_search',
        'shopware_media',
        'shopware_account',
        'shopware_emotion',
        'shopware_storefront',
    ];

    private $ignoredServices = [
        'shopware_media.cache_optimizer_service',
        'shopware_search.variant_search',
        'shopware_search_es.variant_search',
    ];

    private $ignoredClasses = [
        StrategyInterface::class,
    ];

    /**
     * @var TraceableGenerator
     */
    private $generator;

    public function __construct()
    {
        $this->generator = new TraceableGenerator();
    }

    public function process(ContainerBuilder $container)
    {
        if (!$container->hasParameter('shopware.traceable')) {
            return;
        }

        $this->traceableServices = $container->hasParameter('shopware.traceable.tracleable_services') ? $container->getParameter('shopware.traceable.tracleable_services') : $this->traceableServices;
        $this->ignoredClasses = $container->hasParameter('shopware.traceable.ignore_class') ? $container->getParameter('shopware.traceable.ignore_class') : $this->ignoredClasses;
        $this->ignoredServices = $container->hasParameter('shopware.traceable.ignore_services') ? $container->getParameter('shopware.traceable.ignore_services') : $this->ignoredServices;

        $directory = $container->getParameter('kernel.cache_dir');
        $directory .= '/tracer';

        if (!file_exists($directory)) {
            if (!mkdir($directory, 0777, true) && !is_dir($directory)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $directory));
            }
        }

        foreach ($container->getServiceIds() as $serviceId) {
            $allowed = false;
            foreach ($this->traceableServices as $service) {
                if (stripos($serviceId, $service) !== false) {
                    $allowed = true;
                }
            }

            if (in_array($serviceId, $this->ignoredServices)) {
                continue;
            }

            if ($allowed) {
                $definition = $container->getDefinition($serviceId);

                if (in_array($definition->getClass(), $this->ignoredClasses)) {
                    continue;
                }

                list($name, $code) = $this->generator->generateProxyClass($definition->getClass());
                $namespacePath = explode('\\', $name);
                $fileName = array_pop($namespacePath);
                $folderPath = implode($namespacePath, DIRECTORY_SEPARATOR);
                $namespaceFolderPath = $directory . '/' . $folderPath;

                if (!file_exists($namespaceFolderPath)) {
                    if (!mkdir($directory . '/' . $folderPath, 0777, true) && !is_dir($directory . '/' . $folderPath)) {
                        throw new \RuntimeException(sprintf('Directory "%s" was not created', $directory . '/' . $folderPath));
                    }
                }

                file_put_contents($directory . '/' . $folderPath . '/' . $fileName . '.php', $code, LOCK_EX);

                $new = new Definition(
                    $name, [
                    new Reference($serviceId . '.inner'),
                    new Reference('frosh_profiler.stop_watch'),
                ]);
                $container->setDefinition($serviceId . '.inner', $definition);
                $container->setDefinition($serviceId, $new);
            }
        }
    }
}
