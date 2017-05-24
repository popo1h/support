<?php

namespace Popo1h\Support\Traits\StringPack;

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
        $packArr = [];
        foreach (static::getPackPropertyNames() as $propertyName) {
            if (isset($this->$propertyName)) {
                $packArr[$propertyName] = StringPack::pack($this->$propertyName);
            }
        }

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

        $propertyNames = static::getPackPropertyNames();
        foreach ($propertyNames as $propertyName) {
            if (isset($packArr[$propertyName]) && $reflectionClass->hasProperty($propertyName)) {
                $property = $reflectionClass->getProperty($propertyName);
                $property->setAccessible(true);
                $property->setValue($instance, StringPack::unpack($packArr[$propertyName]));
            }
        }

        $initMethodName = static::getUnpackObjectInitMethodName();
        if ($reflectionClass->hasMethod($initMethodName)) {
            $initMethod = $reflectionClass->getMethod($initMethodName);
            $initMethod->invoke($instance);
        }

        return $instance;
    }
}
