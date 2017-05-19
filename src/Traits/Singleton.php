<?php

namespace Popo1h\Support\Traits;

trait Singleton
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