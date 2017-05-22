<?php

namespace Popo1h\Support\Traits\Instances;

trait SingletonTrait
{
    public static function getInstance()
    {
        static $instance = null;

        if (! isset($instance)) {
            $instance = new static();
        }

        return $instance;
    }
}
