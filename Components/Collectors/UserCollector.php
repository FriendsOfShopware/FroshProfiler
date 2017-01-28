<?php

namespace ShyimProfiler\Components\Collectors;

use Enlight_Controller_Action;
use Symfony\Component\DependencyInjection\ContainerInterface;

class UserCollector implements CollectorInterface
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
        return 'User';
    }

    public function collect(Enlight_Controller_Action $controller)
    {
        $result = [
            'user' => [
                'loggedin' => $this->container->get('session')->offsetGet('sUserId'),
            ],
        ];

        if (!empty($result['user']['loggedin'])) {
            $result['user'] = array_merge($result['user'], $this->container->get('Modules')->Admin()->sGetUserData());
        }

        return $result;
    }

    public function getToolbarTemplate()
    {
        return '@Profiler/toolbar/user.tpl';
    }
}
