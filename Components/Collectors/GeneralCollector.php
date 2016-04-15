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
                'httpResponse' => $controller->Response()->getHttpResponseCode(),
                'headers' => $controller->Response()->getHeaders()
            ],
            'request' => [
                'moduleName' => $controller->Request()->getModuleName(),
                'controllerName' => $controller->Request()->getControllerName(),
                'actionName' => $controller->Request()->getActionName(),
                'httpMethod' => $controller->Request()->getMethod(),
                'params' => $controller->Request()->getParams(),
                'get' => $controller->Request()->getQuery(),
                'post' => $controller->Request()->getPost(),
                'cookies' => $controller->Request()->getCookie(),
                'uri' => $controller->Request()->getRequestUri(),
                'url' => 'http://' . Shopware()->Shop()->getHost() . Shopware()->Shop()->getBaseUrl() . $controller->Request()->getRequestUri(),
                'ip' => $controller->Request()->getClientIp(),
                'time' => time()
            ],
            'session' => [
                'meta' => Shopware()->Db()->fetchRow('SELECT expiry,modified FROM s_core_sessions WHERE id = ?', [Shopware()->Session()->get('sessionId')]),
                'data' => $_SESSION['Shopware']
            ],
            'server' => $_SERVER,
            'startTime' => STARTTIME,
        ];
    }

    public function getToolbarTemplate()
    {
        return '@Profiler/toolbar/general.tpl';
    }
}
