<?php

namespace ShyimProfiler\Components\Collectors;

use Shopware\Kernel;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PHPCollector implements CollectorInterface
{
    /**
     * @var Kernel
     */
    private $kernel;

    public function __construct(ContainerInterface $container)
    {
        $this->kernel = $container->get('kernel');
    }

    public function getName()
    {
        return 'PHP';
    }

    public function collect(\Enlight_Controller_Action $controller)
    {
        return [
            'php' => [
                'memory_limit'     => ini_get('memory_limit'),
                'used_memory'      => memory_get_usage(),
                'version'          => phpversion(),
                'xdebug'           => extension_loaded('xdebug'),
                'accel'            => extension_loaded('accel'),
                'apc'              => extension_loaded('apc'),
                'ioncube'          => extension_loaded('ioncube'),
                'opcache'          => extension_loaded('opcache'),
                'httpcache'        => $this->kernel->isHttpCacheEnabled(),
                'env'              => $this->kernel->getEnvironment(),
                'sapi'             => php_sapi_name(),
                'shopware_version' => \Shopware::VERSION,
            ],
        ];
    }

    public function getToolbarTemplate()
    {
        return '@Profiler/toolbar/php.tpl';
    }
}
