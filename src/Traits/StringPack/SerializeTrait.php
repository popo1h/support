<?php

namespace Popo1h\Support\Traits\StringPack;

trait SerializeTrait
{
    use StringPackTrait;

    protected static function stringPackPack($needPackArr)
    {
        return serialize($needPackArr);
    }

    protected static function stringPackUnpack($packedString)
    {
        return unserialize($packedString);
    }
}
