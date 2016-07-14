<?php

namespace ShyimProfiler\Components;

use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;

class SmartyExtensions
{
    private $hasPlugins = false;

    public function addPlugins(\Enlight_Template_Manager $template_Manager)
    {
        if (!$this->hasPlugins) {
            $template_Manager->registerPlugin('modifier', 'convertMemory', [$this, 'convertMemory']);
            $template_Manager->registerPlugin('modifier', 'dump', [$this, 'dump']);
            $template_Manager->registerPlugin('modifier', 'sqlFormat', [$this, 'sqlFormat']);
            $this->hasPlugins = true;
        }
    }

    public function convertMemory($size)
    {
        $unit = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];

        return @round($size / pow(1024, $i = floor(log($size, 1024))), 2).' '.$unit[$i];
    }

    public function dump($var)
    {
        $dumper = new HtmlDumper();
        $cloner = new VarCloner();
        return $dumper->dump($cloner->cloneVar($var));
    }

    public function sqlFormat($var)
    {
        return \SqlFormatter::format($var);
    }
}
