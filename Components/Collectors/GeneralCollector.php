<?php

namespace Shopware\Profiler\Components\Collectors;

class GeneralCollector implements CollectorInterface
{
    public function getName()
    {
        return 'General';
    }

    public function collect(\Enlight_Controller_Action $controller)
    {
        return [
            'response'  => $controller->Response(),
            'request'   => $controller->Request(),
            'startTime' => STARTTIME,
        ];
    }

    public function getToolbarTemplate()
    {
        return '@Profiler/toolbar/general.tpl';
    }
}
