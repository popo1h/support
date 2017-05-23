<?php

namespace Popo1h\Support\Traits\Serizlizers;

trait SerializeTrait
{
    /**
     * @return array [ 'attribute name',... ]
     */
    abstract protected function getSerializeValMap();

    public function serialize()
    {
        $arr = [];
        foreach ($this->getSerializeValMap() as $item) {
            $arr[$item] = $this->$item;
        }

        return serialize($arr);
    }

    public function unserialize($serialized)
    {
        $arr = unserialize($serialized);

        foreach ($this->getSerializeValMap() as $item) {
            $this->$item = $arr[$item];
        }
    }
}
