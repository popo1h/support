<?php

namespace Popo1h\Support\Traits;

trait InstancePool
{
    protected static $_instancePool;
    protected static $_instancePoolDefaultKey = 'default';

    public static function getInstanceFromPool($key = null, $autoCreate = true)
    {
        if (!isset($key)) {
            $key = static::$_instancePoolDefaultKey;
        }

        if (!isset(static::$_instancePool[$key])) {
            if (!$autoCreate) {
                return null;
            }

            static::$_instancePool[$key] = new static();
            if (is_callable([static::$_instancePool[$key], '_initialize'])) {
                call_user_func([static::$_instancePool[$key], '_initialize']);
            }
        }

        return static::$_instancePool[$key];
    }

    public static function setInstancePoolDefaultKey($key)
    {
        static::$_instancePoolDefaultKey = $key;
    }

    public function pushInstanceIntoPool($key = null, $setAsDefault = true)
    {
        if (!isset($key)) {
            $key = static::$_instancePoolDefaultKey;
        } elseif ($setAsDefault) {
            static::setInstancePoolDefaultKey($key);
        }

        static::$_instancePool[$key] = $this;
    }
}
