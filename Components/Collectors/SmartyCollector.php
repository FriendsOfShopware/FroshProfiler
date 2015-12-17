<?php

namespace Shopware\Profiler\Components\Collectors;

class SmartyCollector implements CollectorInterface
{
    public function getName()
    {
        return 'Smarty';
    }

    public function collect(\Enlight_Controller_Action $controller)
    {
        $smarty = $controller->View()->Engine();

        $result = [
            'template' => [
                'cache_dir' => $smarty->getCacheDir(),
                'compile_dir' => $smarty->getCompileDir(),
                'template_dir' => $smarty->getTemplateDir(),
                'plugin_dir' => $smarty->getPluginsDir(),
                'template' => explode('|', $controller->View()->Template()->template_resource),
                'vars' => $controller->View()->getAssign(),
                'start_time' => $smarty->start_time
            ]
        ];

        return $result;
    }

    public function getToolbarTemplate() {
        return '@Profiler/toolbar/smarty.tpl';
    }
}