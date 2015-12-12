<?php

namespace Shopware\Profiler\Components;

class SmartyExtensions
{
    private $hasPlugins = false;

    public function addPlugins(\Enlight_Template_Manager $template_Manager)
    {
        if(!$this->hasPlugins) {
            $template_Manager->registerPlugin('modifier', 'convertMemory', [$this, 'convertMemory']);
            $this->hasPlugins = true;
        }
    }

    public function convertMemory($size) {
        $unit = array('B','KB','MB','GB','TB','PB');
        return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
    }
}