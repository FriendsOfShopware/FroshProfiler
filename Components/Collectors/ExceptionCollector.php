<?php

namespace ShyimProfiler\Components\Collectors;

use Enlight_Controller_Action;
use ShyimProfiler\Components\Struct\Profile;

/**
 * Class ExceptionCollector
 * @package ShyimProfiler\Components\Collectors
 */
class ExceptionCollector implements CollectorInterface
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'exception';
    }

    /**
     * @param Enlight_Controller_Action $controller
     * @param Profile $profile
     * @return void
     */
    public function collect(Enlight_Controller_Action $controller, Profile $profile)
    {
        $error = $controller->Request()->getParam('error_handler');

        if ($error && isset($error->exception)) {
            $profile->setException($error->exception);
        }
    }

    /**
     * @return void
     */
    public function getToolbarTemplate()
    {
        return;
    }
}
