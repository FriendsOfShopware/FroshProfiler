<?php

use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;

function smarty_modifier_dump($var)
{
    $dumper = new HtmlDumper();
    $cloner = new VarCloner();

    return $dumper->dump($cloner->cloneVar($var));
}
