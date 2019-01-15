<?php

namespace FroshProfiler\Components;


use Shopware\Bundle\StoreFrontBundle\Struct\Attribute;
use Symfony\Component\VarDumper\Cloner\Stub;

class VarDumperCaster
{
    public static function castAttributeObject(Attribute $object, $array, Stub $stub)
    {
        return array_merge($array, $object->jsonSerialize());
    }
}