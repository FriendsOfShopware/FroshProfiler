<?php

namespace FroshProfiler\Components\Collectors;

use Enlight_Controller_Action;
use FroshProfiler\Components\Struct\Profile;
use Shopware\Kernel;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PHPCollector implements CollectorInterface
{
    /**
     * @var Kernel
     */
    private $kernel;

    /**
     * @var string
     */
    private $version;

    public function __construct(ContainerInterface $container)
    {
        $this->version = $container->get('config')->get('version');

        /** @var Kernel $kernel */
        $kernel = $container->get('kernel');
        $this->kernel = $kernel;
    }

    public function getName(): string
    {
        return 'PHP';
    }

    public function collect(Enlight_Controller_Action $controller, Profile $profile): void
    {
        $profile->setPhp([
            'memory_limit' => ini_get('memory_limit'),
            'used_memory' => memory_get_usage(),
            'version' => phpversion(),
            'xdebug' => extension_loaded('xdebug'),
            'accel' => extension_loaded('accel'),
            'apc' => function_exists('apcu_clear_cache'),
            'ioncube' => extension_loaded('ioncube'),
            'opcache' => extension_loaded('Zend OPcache') && ini_get('opcache.enable'),
            'httpcache' => $this->kernel->isHttpCacheEnabled(),
            'env' => $this->kernel->getEnvironment(),
            'sapi' => PHP_SAPI,
            'shopware_version' => $this->version,
            'architecture' => PHP_INT_MAX === 2147483647 ? 32 : 64,
            'timezone' => $this->kernel->getContainer()->getParameter('shopware.phpsettings.date.timezone'),
        ]);
    }

    public function getToolbarTemplate(): ?string
    {
        return '@Toolbar/toolbar/php.tpl';
    }
}
