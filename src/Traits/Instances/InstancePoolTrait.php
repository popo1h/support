<?php

namespace Popo1h\Support\Traits\Instances;

trait InstancePoolTrait
{
    protected static $_instancePool = [];
    protected static $_instancePoolDefaultKey = 'default';

    protected static function createInstance()
    {
        return null;
    }

    public static function getInstanceFromPool($key = null, $autoCreate = true)
    {
        if (!isset($key)) {
            $key = static::$_instancePoolDefaultKey;
        }

        if (!isset(static::$_instancePool[$key])) {
            if (!$autoCreate) {
                return null;
            }

            static::$_instancePool[$key] = static::createInstance();
        }

        return static::$_instancePool[$key];
    }

    public static function setInstancePoolDefaultKey($key)
    {
        static::$_instancePoolDefaultKey = $key;
    }

    public static function pushInstanceIntoPool($instance, $key = null, $setAsDefault = true)
    {
        if (!isset($key)) {
            $key = static::$_instancePoolDefaultKey;
        } elseif ($setAsDefault) {
            static::setInstancePoolDefaultKey($key);
        }

        static::$_instancePool[$key] = $instance;
    }
}
