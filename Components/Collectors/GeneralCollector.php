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
            'response' => [
                'httpResponse' => $controller->Response()->getHttpResponseCode()
            ],
            'request' => [
                'moduleName' => $controller->Request()->getModuleName(),
                'controllerName' => $controller->Request()->getControllerName(),
                'actionName' => $controller->Request()->getActionName(),
                'httpMethod' => $controller->Request()->getMethod(),
                'params' => $controller->Request()->getParams(),
                'uri' => $controller->Request()->getRequestUri(),
                'ip' => $controller->Request()->getClientIp(),
                'time' => time()
            ],
            'startTime' => STARTTIME,
        ];
    }

    public function getToolbarTemplate()
    {
        return '@Profiler/toolbar/general.tpl';
    }
}
