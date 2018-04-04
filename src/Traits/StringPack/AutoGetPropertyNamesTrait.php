<?php

namespace Popo1h\Support\Traits\StringPack;

trait AutoGetPropertyNamesTrait
{
    protected static function getPackPropertyNames()
    {
        $reflectionClass = new \ReflectionClass(static::class);

        $propertyNames = [];
        foreach ($reflectionClass->getProperties() as $property) {
            $propertyNames[] = $property->getName();
        }
        return $propertyNames;
    }
}
