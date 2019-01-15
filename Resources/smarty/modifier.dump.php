<?php

use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;

function smarty_modifier_dump($var)
{
    $dumper = new HtmlDumper();
    $cloner = Shopware()->Container()->get('var_dumper.cloner');

    $maxItems = Shopware()->Config()->get('dumpSize', 2500);
    $cloner->setMaxItems(($maxItems === -1 ? PHP_INT_MAX : $maxItems));

    $data = $cloner->cloneVar($var);
    $data->withMaxDepth(Shopware()->Config()->get('dumpDepth', 20));

    return $dumper->dump($data);
}
