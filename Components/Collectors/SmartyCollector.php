<?php

namespace ShyimProfiler\Components\Collectors;

use Enlight_Controller_Action;
use Enlight_Exception;
use ShyimProfiler\Components\Struct\Profile;

/**
 * Class SmartyCollector
 * @package ShyimProfiler\Components\Collectors
 */
class SmartyCollector implements CollectorInterface
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'Smarty';
    }

    /**
     * @param Enlight_Controller_Action $controller
     * @param Profile $profile
     * @return void
     */
    public function collect(Enlight_Controller_Action $controller, Profile $profile)
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
            'cache_dir'    => $smarty->getCacheDir(),
            'compile_dir'  => $smarty->getCompileDir(),
            'template_dir' => $smarty->getTemplateDir(),
            'plugin_dir'   => $smarty->getPluginsDir(),
            'vars'         => $assigns,
            'start_time'   => $smarty->start_time,
        ];

        // Catch "Template was not loaded failure" Exception
        try {
            $result['template'] = explode('|', $controller->View()->Template()->template_resource);
        } catch (Enlight_Exception $e) {
        }

        $profile->setTemplate($result);
    }

    /**
     * @return string
     */
    public function getToolbarTemplate()
    {
        return '@Toolbar/toolbar/smarty.tpl';
    }
}
