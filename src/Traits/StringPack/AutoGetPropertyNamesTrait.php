<?php

namespace Popo1h\Support\Traits\StringPack;

trait AutoGetPropertyNamesTrait
{
    protected static function getPackPropertyNames()
    {
        $funcGetPropertyNames = function (\ReflectionClass $reflectionClass) use (&$funcGetPropertyNames) {
            $propertyNames = [];

            foreach ($reflectionClass->getProperties() as $property) {
                $propertyName = $property->getName();
                if (isset($hasSetPropertyNames[$propertyName])) {
                    continue;
                }
                $propertyNames[] = $propertyName;
            }

            $parentReflectionClass = $reflectionClass->getParentClass();
            if ($parentReflectionClass) {
                $propertyNames['--parent'] = $funcGetPropertyNames($parentReflectionClass);
            }

            return $propertyNames;
        };

        $reflectionClass = new \ReflectionClass(static::class);
        return $funcGetPropertyNames($reflectionClass);
    }
}
