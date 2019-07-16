<?php

namespace FroshProfiler\Components\Collectors;

use Enlight_Controller_Action;
use FroshProfiler\Components\Struct\Profile;

interface CollectorInterface
{
    public function getName(): string;

    public function collect(Enlight_Controller_Action $controller, Profile $profile): void;

    public function getToolbarTemplate(): ?string;
}
