<?php

namespace FroshProfiler\Components\Collectors;

use Enlight_Controller_Action;
use Enlight_Exception;
use FroshProfiler\Components\Struct\Profile;

/**
 * Class SmartyCollector
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
     * @param Profile                   $profile
     */
    public function collect(Enlight_Controller_Action $controller, Profile $profile)
    {
        $smarty = $controller->View()->Engine();
        $assigns = $controller->View()->getAssign();

        $result = [
            'cache_dir' => $smarty->getCacheDir(),
            'compile_dir' => $smarty->getCompileDir(),
            'template_dir' => $smarty->getTemplateDir(),
            'plugin_dir' => $smarty->getPluginsDir(),
            'vars' => $assigns,
            'start_time' => $smarty->start_time,
        ];

        try {
            $result['template'] = explode('|', $controller->View()->Template()->template_resource);
        } catch (Enlight_Exception $e) {
            // Catch "Template was not loaded failure" Exception
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
