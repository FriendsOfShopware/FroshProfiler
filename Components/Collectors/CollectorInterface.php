<?php

namespace ShyimProfiler\Components\Collectors;

interface CollectorInterface
{
    public function getName();

    public function collect(\Enlight_Controller_Action $controller);

    public function getToolbarTemplate();
}
