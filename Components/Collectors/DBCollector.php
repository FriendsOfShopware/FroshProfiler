<?php

namespace Shopware\Profiler\Components\Collectors;

class DBCollector implements CollectorInterface
{
    public function getName()
    {
        return 'Smarty';
    }

    public function collect(\Enlight_Controller_Action $controller)
    {
        $result = [];

        return $result;
    }

    public function getToolbarTemplate() {
        return '@Profiler/toolbar/db.tpl';
    }
}