<?php

namespace ShyimProfiler\Components\Collectors;

use Enlight_Exception;

class SmartyCollector implements CollectorInterface
{
    public function getName()
    {
        return 'Smarty';
    }

    public function collect(\Enlight_Controller_Action $controller)
    {
        $smarty = $controller->View()->Engine();
        $assigns = $controller->View()->getAssign();

        /*
         * Clear Assigns, to fix cannot serialize pdo instances errors
         */
        unset($assigns['Shopware']);
        unset($assigns['Controller']);
        unset($assigns['Shop']);
        unset($assigns['Locale']);
        unset($assigns['sProfiler']);
        unset($assigns['sProfilerCollectors']);
        unset($assigns['sProfilerID']);

        $result = [
            'template' => [
                'cache_dir'    => $smarty->getCacheDir(),
                'compile_dir'  => $smarty->getCompileDir(),
                'template_dir' => $smarty->getTemplateDir(),
                'plugin_dir'   => $smarty->getPluginsDir(),
                'vars'         => $assigns,
                'start_time'   => $smarty->start_time,
            ],
        ];

        // Catch "Template was not loaded failure" Exception
        try {
            $result['template']['template'] = explode('|', $controller->View()->Template()->template_resource);
        } catch (Enlight_Exception $e) {}

        return $result;
    }

    public function getToolbarTemplate()
    {
        return '@Profiler/toolbar/smarty.tpl';
    }
}
