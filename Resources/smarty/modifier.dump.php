<?php

use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;

function smarty_modifier_dump($var)
{
    $dumper = new HtmlDumper();
    $cloner = new VarCloner();
    $data = $cloner->cloneVar($var);
    $data->withMaxDepth(Shopware()->Config()->get('dumpDepth', 20));

    return $dumper->dump($data);
}
