<?php

namespace ShyimProfiler\Components\Collectors;

use Enlight_Controller_Action;
use ShyimProfiler\Components\Struct\Profile;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class ConfigCollector
 * @package ShyimProfiler\Components\Collectors
 */
class ConfigCollector implements CollectorInterface
{
    /**
     * @var Container
     */
    private $container;

    /**
     * ConfigCollector constructor.
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
     * @param Profile $profile
     */
    public function collect(Enlight_Controller_Action $controller, Profile $profile)
    {
        $profile->setConfig($this->container->getParameterBag()->all());
    }

    /**
     * @return string
     */
    public function getToolbarTemplate()
    {
        return '@Toolbar/toolbar/config.tpl';
    }
}
