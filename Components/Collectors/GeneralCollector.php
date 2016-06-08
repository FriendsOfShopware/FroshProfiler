<?php

namespace ShyimProfiler\Components\Collectors;

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
            'logs' => $this->getLogs(),
            'server' => $_SERVER,
            'startTime' => STARTTIME,
            'bundles' => $this->getBundles()
        ];
    }

    public function getToolbarTemplate()
    {
        return '@Profiler/toolbar/general.tpl';
    }

    public function getLogs()
    {
        $logs = [];

        if (Shopware()->Container()->has('corelogger')) {
            $logs = array_merge(Shopware()->Container()->get('corelogger')->getLoggedMessages(), $logs);
        }

        if (Shopware()->Container()->has('pluginlogger')) {
            $logs = array_merge(Shopware()->Container()->get('pluginlogger')->getLoggedMessages(), $logs);
        }

        if (Shopware()->Container()->has('debuglogger')) {
            $logs = array_merge(Shopware()->Container()->get('debuglogger')->getLoggedMessages(), $logs);
        }

        return $logs;
    }

    public function getBundles()
    {
        $bundles = Shopware()->Container()->get('cache')->load('LoadedBundles');

        if (empty($bundles)) {
            $bundles = [];
            $bundleDir = Shopware()->Container()->getParameter('kernel.root_dir') . '/engine/Shopware/Bundle/';
            $folderContent = scandir($bundleDir);

            foreach ($folderContent as $item) {
                if ($item != '.' && $item != '..') {
                    $bundles[] = [$item, $bundleDir . $item];
                }
            }

            Shopware()->Container()->get('cache')->save($bundles, 'LoadedBundles');
        }

        return $bundles;
    }
}
