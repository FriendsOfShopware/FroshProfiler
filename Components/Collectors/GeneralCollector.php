<?php

namespace FroshProfiler\Components\Collectors;

use Enlight_Controller_Action;
use FroshProfiler\Components\Struct\Profile;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class GeneralCollector
 */
class GeneralCollector implements CollectorInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * GeneralCollector constructor.
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
        return 'General';
    }

    /**
     * @param Enlight_Controller_Action $controller
     * @param Profile                   $profile
     */
    public function collect(Enlight_Controller_Action $controller, Profile $profile)
    {
        $attributes = [
            'response' => [
                'httpResponse' => $controller->Response()->getHttpResponseCode(),
                'headers' => $controller->Response()->getHeaders(),
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
                'url' => ($controller->Request()->isSecure() ? 'https' : 'http') . '://' . $controller->Request()->getHttpHost() . $controller->Request()->getRequestUri(),
                'ip' => $controller->Request()->getClientIp(),
                'time' => time(),
            ],
            'logs' => $this->getLogs(),
            'server' => $_SERVER,
            'startTime' => $this->container->get('frosh_profiler.current.profile')->getStartTime(),
            'bundles' => $this->getBundles(),
        ];

        if ($this->container->initialized('session')) {
            $attributes['session'] = [
                'meta' => $this->container->get('dbal_connection')->fetchAssoc('SELECT expiry,modified FROM s_core_sessions WHERE id = ?', [$this->container->get('session')->get('sessionId')]),
                'data' => $_SESSION['Shopware'],
            ];
        }

        $profile->setAttributes($attributes);
    }

    /**
     * @return string
     */
    public function getToolbarTemplate()
    {
        return '@Toolbar/toolbar/general.tpl';
    }

    /**
     * @return array
     */
    public function getLogs()
    {
        $logs = [];

        if ($this->container->has('corelogger')) {
            $logs = array_merge($this->container->get('corelogger')->getLoggedMessages(), $logs);
        }

        if ($this->container->has('pluginlogger')) {
            $logs = array_merge($this->container->get('pluginlogger')->getLoggedMessages(), $logs);
        }

        if ($this->container->has('debuglogger')) {
            $logs = array_merge($this->container->get('debuglogger')->getLoggedMessages(), $logs);
        }

        return $logs;
    }

    /**
     * @return array|false|mixed
     */
    public function getBundles()
    {
        $bundles = $this->container->get('cache')->load('LoadedBundles');

        if (empty($bundles)) {
            $bundles = [];
            $bundleDir = $this->container->getParameter('kernel.root_dir') . '/engine/Shopware/Bundle/';
            $folderContent = scandir($bundleDir);

            foreach ($folderContent as $item) {
                if ($item != '.' && $item != '..') {
                    $bundles[] = [$item, $bundleDir . $item];
                }
            }

            $this->container->get('cache')->save($bundles, 'LoadedBundles');
        }

        return $bundles;
    }
}
