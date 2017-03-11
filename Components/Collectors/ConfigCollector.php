<?php

namespace ShyimProfiler\Components\Collectors;

use Enlight_Controller_Action;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ConfigCollector implements CollectorInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getName()
    {
        return 'Config';
    }

    public function collect(Enlight_Controller_Action $controller)
    {
        $result = [
            'config' => [
                $this->container->getParameterBag()->all(),
            ],
        ];

        return $result;
    }

    public function getToolbarTemplate()
    {
        return '@Toolbar/toolbar/config.tpl';
    }
}
