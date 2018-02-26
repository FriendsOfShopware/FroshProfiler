<?php

namespace ShyimProfiler\Components\Collectors;

use Enlight_Controller_Action;
use ShyimProfiler\Components\Struct\Profile;

class FormCollector implements CollectorInterface
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'Forms';
    }

    /**
     * @param Enlight_Controller_Action $controller
     * @param Profile                   $profile
     */
    public function collect(Enlight_Controller_Action $controller, Profile $profile)
    {
        $profile->setAttributes(['forms' => $controller->get('shyim_profiler.forms.data_collector')->getData()]);
    }

    /**
     * @return string|void
     */
    public function getToolbarTemplate()
    {
        return false;
    }
}
