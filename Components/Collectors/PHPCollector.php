<?php

namespace ShyimProfiler\Components\Collectors;

class PHPCollector implements CollectorInterface
{
    public function getName()
    {
        return 'PHP';
    }

    public function collect(\Enlight_Controller_Action $controller)
    {
        return [
            'php' => [
                'memory_limit'     => ini_get('memory_limit'),
                'used_memory'      => memory_get_usage(),
                'version'          => phpversion(),
                'xdebug'           => extension_loaded('xdebug'),
                'accel'            => extension_loaded('accel'),
                'apc'              => extension_loaded('apc'),
                'ioncube'          => extension_loaded('ioncube'),
                'opcache'          => extension_loaded('opcache'),
                'httpcache'        => Shopware()->Container()->get('kernel')->isHttpCacheEnabled(),
                'env'              => Shopware()->Container()->get('kernel')->getEnvironment(),
                'sapi'             => php_sapi_name(),
                'shopware_version' => \Shopware::VERSION,
            ],
        ];
    }

    public function getToolbarTemplate()
    {
        return '@Profiler/toolbar/php.tpl';
    }
}
