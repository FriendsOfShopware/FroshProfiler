<?php

namespace FroshProfiler\Components\Collectors;

use Enlight_Controller_Action;
use Shopware;
use Shopware\Kernel;
use FroshProfiler\Components\Struct\Profile;
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
     * PHPCollector constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
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
            'apc' => extension_loaded('apc'),
            'ioncube' => extension_loaded('ioncube'),
            'opcache' => extension_loaded('opcache'),
            'httpcache' => $this->kernel->isHttpCacheEnabled(),
            'env' => $this->kernel->getEnvironment(),
            'sapi' => php_sapi_name(),
            'shopware_version' => Shopware::VERSION,
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
