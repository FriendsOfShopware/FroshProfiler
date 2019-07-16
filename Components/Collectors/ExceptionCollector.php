<?php

namespace FroshProfiler\Components\Collectors;

use Enlight_Controller_Action;
use FroshProfiler\Components\Struct\Profile;

/**
 * Class ExceptionCollector
 */
class ExceptionCollector implements CollectorInterface
{
    public function getName(): string
    {
        return 'exception';
    }

    public function collect(Enlight_Controller_Action $controller, Profile $profile): void
    {
        $error = $controller->Request()->getParam('error_handler');

        if ($error && isset($error->exception)) {
            $profile->setException($error->exception);
        }
    }

    public function getToolbarTemplate(): ?string
    {
        return null;
    }
}
