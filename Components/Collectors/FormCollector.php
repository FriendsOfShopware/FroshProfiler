<?php

namespace FroshProfiler\Components\Collectors;

use Enlight_Controller_Action;
use FroshProfiler\Components\Struct\Profile;

class FormCollector implements CollectorInterface
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return 'Forms';
    }

    public function collect(Enlight_Controller_Action $controller, Profile $profile): void
    {
        $profile->setAttributes(['forms' => $controller->get('frosh_profiler.forms.data_collector')->getData()]);
    }

    public function getToolbarTemplate(): ?string
    {
        return null;
    }
}
