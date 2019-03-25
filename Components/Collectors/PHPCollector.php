<?php

namespace FroshProfiler\Components\Collectors;

use Enlight_Controller_Action;
use FroshProfiler\Components\Struct\Profile;
use Shopware;
use Shopware\Kernel;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class PHPCollector
 */
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

    /**
     * PHPCollector constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->version = $container->get('config')->get('version');
        $this->kernel = $container->get('kernel');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'PHP';
    }

    /**
     * @param Enlight_Controller_Action $controller
     * @param Profile                   $profile
     *
     * @return array
     */
    public function collect(Enlight_Controller_Action $controller, Profile $profile)
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

    /**
     * @return string
     */
    public function getToolbarTemplate()
    {
        return '@Toolbar/toolbar/php.tpl';
    }
}
