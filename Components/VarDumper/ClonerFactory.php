<?php

namespace FroshProfiler\Components\VarDumper;

use Shopware\Bundle\StoreFrontBundle\Struct\Attribute;
use Symfony\Component\VarDumper\Cloner\VarCloner;

class ClonerFactory
{
    public static function factory()
    {
        $cloner = new VarCloner();
        $cloner->addCasters([
            Attribute::class => 'FroshProfiler\Components\VarDumperCaster::castAttributeObject',
        ]);

        return $cloner;
    }
}