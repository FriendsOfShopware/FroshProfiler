<?php

namespace ShyimProfiler\Components\Collectors;

use Enlight_Controller_Action;
use ShyimProfiler\Components\Struct\Profile;

/**
 * Interface CollectorInterface
 */
interface CollectorInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param Enlight_Controller_Action $controller
     * @param Profile                   $profile
     */
    public function collect(Enlight_Controller_Action $controller, Profile $profile);

    /**
     * @return string|void
     */
    public function getToolbarTemplate();
}
