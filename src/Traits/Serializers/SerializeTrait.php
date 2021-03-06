<?php

namespace Popo1h\Support\Traits\Serizlizers;

trait SerializeTrait
{
    /**
     * @return array [ 'property name',... ]
     */
    abstract protected function getSerializePropertyNames();

    public function serialize()
    {
        $arr = [];
        foreach ($this->getSerializePropertyNames() as $item) {
            $arr[$item] = $this->$item;
        }

        return serialize($arr);
    }

    public function unserialize($serialized)
    {
        $arr = unserialize($serialized);

        foreach ($this->getSerializePropertyNames() as $item) {
            $this->$item = $arr[$item];
        }
    }
}
