<?php

namespace Popo1h\Support\Traits\StringPack;

use Popo1h\Support\Exceptions\StringPack\PackPropertyNamesErrorException;
use Popo1h\Support\Objects\StringPack;

trait StringPackTrait
{
    protected static function getUnpackObjectInitMethodName()
    {
        return '_initialize';
    }

    /**
     * @return array [ 'property name',... ]
     */
    protected static function getPackPropertyNames()
    {
        return [];
    }

    /**
     * @param array $needPackArr
     * @return string
     */
    protected static function stringPackPack(
        /** @noinspection PhpUnusedParameterInspection */
        $needPackArr
    ) {
        return '';
    }

    /**
     * @param string $packedString
     * @return array
     */
    protected static function stringPackUnpack(
        /** @noinspection PhpUnusedParameterInspection */
        $packedString
    ) {
        return [];
    }

    /**
     * @return string
     */
    public function packToString()
    {
        $funcGetPackArr = function (\ReflectionClass $reflectionClass, $propertyNames) use (&$funcGetPackArr) {
            $packArr = [];

            foreach ($propertyNames as $key => $propertyName) {
                if ($key === '--parent') {
                    $parentReflectionClass = $reflectionClass->getParentClass();
                    if ($propertyName && !$parentReflectionClass) {
                        throw (new PackPropertyNamesErrorException(['class' => $reflectionClass->getName(), 'property_names' => $propertyNames, 'error_property_name' => $key]));
                    }
                    $packArr[$key] = $funcGetPackArr($reflectionClass->getParentClass(), $propertyName);
                } else {
                    if (!$reflectionClass->hasProperty($propertyName)) {
                        throw (new PackPropertyNamesErrorException(['class' => $reflectionClass->getName(), 'property_names' => $propertyNames, 'error_property_name' => $propertyName]));
                    }
                    $property = $reflectionClass->getProperty($propertyName);
                    $property->setAccessible(true);
                    $packArr[$propertyName] = StringPack::pack($property->getValue($this));
                }
            }

            return $packArr;
        };

        $reflectionClass = new \ReflectionClass(static::class);
        $packArr = $funcGetPackArr($reflectionClass, static::getPackPropertyNames());

        return static::stringPackPack($packArr);
    }

    /**
     * @param string $string
     * @return static
     */
    public static function unpackString($string)
    {
        $packArr = static::stringPackUnpack($string);

        $reflectionClass = new \ReflectionClass(static::class);
        /**
         * @var static $instance
         */
        $instance = $reflectionClass->newInstanceWithoutConstructor();

        $funcSetProperty = function (\ReflectionClass $reflectionClass, $propertyNames, $packArr) use (&$funcSetProperty, $instance) {
            foreach ($propertyNames as $key => $propertyName) {
                if ($key === '--parent') {
                    $parentReflectionClass = $reflectionClass->getParentClass();
                    if ($propertyName && !$parentReflectionClass) {
                        throw (new PackPropertyNamesErrorException(['class' => $reflectionClass->getName(), 'property_names' => $propertyNames, 'error_property_name' => $key]));
                    }
                    if (isset($packArr[$key])) {
                        $funcSetProperty($parentReflectionClass, $propertyName, $packArr[$key]);
                    }
                } else {
                    if (!$reflectionClass->hasProperty($propertyName)) {
                        throw (new PackPropertyNamesErrorException(['class' => $reflectionClass->getName(), 'property_names' => $propertyNames, 'error_property_name' => $propertyName]));
                    }
                    if (isset($packArr[$propertyName])) {
                        $property = $reflectionClass->getProperty($propertyName);
                        $property->setAccessible(true);
                        $property->setValue($instance, StringPack::unpack($packArr[$propertyName]));
                    }
                }
            }
        };

        $funcSetProperty($reflectionClass, static::getPackPropertyNames(), $packArr);

        $initMethodName = static::getUnpackObjectInitMethodName();
        if ($reflectionClass->hasMethod($initMethodName)) {
            $initMethod = $reflectionClass->getMethod($initMethodName);
            $initMethod->invoke($instance);
        }

        return $instance;
    }
}
