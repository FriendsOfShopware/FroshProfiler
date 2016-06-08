<?php

namespace ShyimProfiler\Components\Collectors;

class UserCollector implements CollectorInterface
{
    public function getName()
    {
        return 'User';
    }

    public function collect(\Enlight_Controller_Action $controller)
    {
        $result = [
            'user' => [
                'loggedin' => Shopware()->Session()->offsetGet('sUserId'),
            ],
        ];

        if (!empty($result['user']['loggedin'])) {
            $result['user'] = array_merge($result['user'], Shopware()->Modules()->Admin()->sGetUserData());
        }

        return $result;
    }

    public function getToolbarTemplate()
    {
        return '@Profiler/toolbar/user.tpl';
    }
}
