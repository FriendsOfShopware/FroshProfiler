<?php

namespace FroshProfiler\Components\Collectors;

use Enlight_Controller_Action;
use Enlight_Exception;
use FroshProfiler\Components\Struct\Profile;

class SmartyCollector implements CollectorInterface
{
    public function getName(): string
    {
        return 'Smarty';
    }

    public function collect(Enlight_Controller_Action $controller, Profile $profile): void
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

    public function getToolbarTemplate(): ?string
    {
        return '@Toolbar/toolbar/smarty.tpl';
    }
}
