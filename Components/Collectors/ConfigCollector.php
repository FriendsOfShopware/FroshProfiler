<?php

namespace ShyimProfiler\Components\Collectors;

use Enlight_Controller_Action;
use ShyimProfiler\Components\Struct\Profile;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class ConfigCollector
 */
class ConfigCollector implements CollectorInterface
{
    /**
     * @var Container
     */
    private $container;

    /**
     * ConfigCollector constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'Config';
    }

    /**
     * @param Enlight_Controller_Action $controller
     * @param Profile                   $profile
     */
    public function collect(Enlight_Controller_Action $controller, Profile $profile)
    {
        $config = $this->container->getParameterBag()->all();

        array_walk_recursive($config, function (&$value, $key) {
            if (stripos($key, 'password') !== false) {
                $value = '******';
            }
        });

        $profile->setConfig($config);
    }

    /**
     * @return string
     */
    public function getToolbarTemplate()
    {
        return '@Toolbar/toolbar/config.tpl';
    }
}
