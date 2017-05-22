<?php

namespace Popo1h\Support\Traits\Instances;

trait GlobalInstanceTrait
{
    protected static $_globalInstance;

    public static function getGlobalInstance()
    {
        return static::$_globalInstance;
    }

    public function setAsGlobalInstance()
    {
        static::$_globalInstance = $this;
    }
}
