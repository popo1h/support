<?php

namespace Popo1h\Support\Traits\StringPack;

trait JsonTrait
{
    use StringPackTrait;

    protected static function stringPackPack($needPackArr)
    {
        return json_encode($needPackArr);
    }

    protected static function stringPackUnpack($packedString)
    {
        return json_decode($packedString, true);
    }
}
