<?php

namespace FroshProfiler\Components\Tracleable;

use Shopware\Bundle\MediaBundle\Strategy\StrategyInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class TraceableCompilerPass
 * @package FroshProfiler\Components\Tracleable
 */
class TraceableCompilerPass implements CompilerPassInterface
{
    const TRACEABLE_SERVICES = [
        'shopware.emotion',
        'shopware.routing',
        'shopware_search',
        'shopware_media',
        'shopware_account',
        'shopware_emotion',
        'shopware_storefront',
    ];

    const IGNORE_SERVICES = [
        'shopware_media.cache_optimizer_service',
        'shopware_search.variant_search',
        'shopware_search_es.variant_search'
    ];

    const IGNORE_CLASS = [
        StrategyInterface::class
    ];

    /**
     * @var TraceableGenerator
     */
    private $generator;

    /**
     * TraceableCompilerPass constructor.
     */
    public function __construct()
    {
        $this->generator = new TraceableGenerator();
    }

    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasParameter('shopware.traceable')) {
            return;
        }

        $directory = $container->getParameter('kernel.cache_dir');
        $directory .= '/tracer';

        if (!file_exists($directory)) {
            if (!mkdir($directory, 0777, true) && !is_dir($directory)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $directory));
            }
        }

        foreach ($container->getServiceIds() as $serviceId) {
            $allowed = false;
            foreach (self::TRACEABLE_SERVICES as $service) {
                if (stripos($serviceId, $service) !== false) {
                    $allowed = true;
                }
            }

            if (in_array($serviceId, self::IGNORE_SERVICES)) {
                continue;
            }

            if ($allowed) {
                $definition = $container->getDefinition($serviceId);

                if (in_array($definition->getClass(), self::IGNORE_CLASS)) {
                    continue;
                }

                list($name, $code) = $this->generator->generateProxyClass($definition->getClass());
                $namespacePath = explode("\\", $name);
                $fileName = array_pop($namespacePath);
                $folderPath = implode($namespacePath, DIRECTORY_SEPARATOR);
                $namespaceFolderPath = $directory . '/' . $folderPath;

                if (!file_exists($namespaceFolderPath)) {
                    mkdir($directory . '/' . $folderPath, 0777, true);
                }

                file_put_contents($directory . '/' . $folderPath . '/' . $fileName . '.php', $code);

                $new = new Definition(
                    $name, [
                    new Reference($serviceId . '.inner'),
                ]);
                $container->setDefinition($serviceId . '.inner', $definition);
                $container->setDefinition($serviceId, $new);
            }
        }
    }
}