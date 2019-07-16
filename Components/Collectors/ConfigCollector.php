<?php

namespace FroshProfiler\Components\Collectors;

use Enlight_Controller_Action;
use FroshProfiler\Components\Struct\Profile;
use Symfony\Component\DependencyInjection\Container;

class ConfigCollector implements CollectorInterface
{
    /**
     * @var Container
     */
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function getName(): string
    {
        return 'Config';
    }

    public function collect(Enlight_Controller_Action $controller, Profile $profile): void
    {
        $config = $this->container->getParameterBag()->all();

        array_walk_recursive($config, function (&$value, $key) {
            if (stripos($key, 'password') !== false) {
                $value = '******';
            }
        });

        $profile->setConfig($config);
    }

    public function getToolbarTemplate(): ?string
    {
        return '@Toolbar/toolbar/config.tpl';
    }
}
