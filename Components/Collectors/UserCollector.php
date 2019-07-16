<?php

namespace FroshProfiler\Components\Collectors;

use Enlight_Controller_Action;
use FroshProfiler\Components\Struct\Profile;
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

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getName(): string
    {
        return 'User';
    }

    public function collect(Enlight_Controller_Action $controller, Profile $profile): void
    {
        $result = [];

        if ($this->container->initialized('session')) {
            $result['loggedin'] = $this->container->get('session')->offsetGet('sUserId');

            if (!empty($result['loggedin'])) {
                $userData = $this->container->get('Modules')->Admin()->sGetUserData();
                $result = array_merge($result, $userData);
                $result['data'] = $userData;
            }
        }

        $encoders = [];

        foreach ($this->container->get('PasswordEncoder')->getCompatibleEncoders() as $compatibleEncoder) {
            $encoders[] = get_class($compatibleEncoder);
        }

        $result['encoders'] = $encoders;

        $profile->setUser($result);
    }

    public function getToolbarTemplate(): ?string
    {
        return '@Toolbar/toolbar/user.tpl';
    }
}
