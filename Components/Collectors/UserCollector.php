<?php

namespace ShyimProfiler\Components\Collectors;

use Enlight_Controller_Action;
use ShyimProfiler\Components\Struct\Profile;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class UserCollector
 */
class UserCollector implements CollectorInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * UserCollector constructor.
     *
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
     * @param Profile                   $profile
     */
    public function collect(Enlight_Controller_Action $controller, Profile $profile)
    {
        $result = [
            'loggedin' => $this->container->get('session')->offsetGet('sUserId'),
        ];

        if (!empty($result['loggedin'])) {
            $userData = $this->container->get('Modules')->Admin()->sGetUserData();
            $result = array_merge($result, $userData);
            $result['data'] = $userData;
        }

        $encoders = [];

        foreach ($this->container->get('PasswordEncoder')->getCompatibleEncoders() as $compatibleEncoder) {
            $encoders[] = get_class($compatibleEncoder);
        }

        $result['encoders'] = $encoders;

        $profile->setUser($result);
    }

    /**
     * @return string
     */
    public function getToolbarTemplate()
    {
        return '@Toolbar/toolbar/user.tpl';
    }
}
