<?php

namespace ShyimProfiler\Components\Collectors;

class ConfigCollector implements CollectorInterface
{
    public function getName()
    {
        return 'Config';
    }

    public function collect(\Enlight_Controller_Action $controller)
    {
        $result = [
            'config' => [
                Shopware()->Container()->getParameterBag()->all(),
            ],
        ];

        return $result;
    }

    public function getToolbarTemplate()
    {
        return '@Profiler/toolbar/config.tpl';
    }
}
