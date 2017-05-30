<?php

namespace ShyimProfiler\Components\Collectors;

use Enlight_Controller_Action;
use ShyimProfiler\Components\Struct\Profile;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class UserCollector
 * @package ShyimProfiler\Components\Collectors
 */
class UserCollector implements CollectorInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * UserCollector constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'User';
    }

    /**
     * @param Enlight_Controller_Action $controller
     * @param Profile $profile
     * @return array
     */
    public function collect(Enlight_Controller_Action $controller, Profile $profile)
    {
        $result = [
            'loggedin' => $this->container->get('session')->offsetGet('sUserId'),
        ];

        if (!empty($result['user']['loggedin'])) {
            $result['user'] = array_merge($result['user'], $this->container->get('Modules')->Admin()->sGetUserData());
        }

        return $result;
    }

    public function getToolbarTemplate()
    {
        return '@Toolbar/toolbar/user.tpl';
    }
}
