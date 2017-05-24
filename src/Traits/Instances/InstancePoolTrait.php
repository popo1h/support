<?php

namespace Popo1h\Support\Traits\Instances;

trait InstancePoolTrait
{
    protected $_instancePool = [];
    protected $_instancePoolDefaultKey = 'default';

    public function getInstanceFromPool($key = null)
    {
        if (!isset($key)) {
            $key = $this->_instancePoolDefaultKey;
        }

        if (!isset($this->_instancePool[$key])) {
            return null;
        }

        return $this->_instancePool[$key];
    }

    public function setInstancePoolDefaultKey($key)
    {
        $this->_instancePoolDefaultKey = $key;
    }

    public function pushInstanceIntoPool($instance, $key = null, $setAsDefault = true)
    {
        if (!isset($key)) {
            $key = $this->_instancePoolDefaultKey;
        } elseif ($setAsDefault) {
            $this->setInstancePoolDefaultKey($key);
        }

        $this->_instancePool[$key] = $instance;
    }
}
