<?php

namespace Popo1h\Support\Traits\StringPack;

use Popo1h\Support\Exceptions\StringPack\PackedDataErrorException;

trait JsonTrait
{
    use StringPackTrait;

    protected static function stringPackPack($needPackArr)
    {
        return json_encode($needPackArr);
    }

    protected static function stringPackUnpack($packedString)
    {
        $arr = json_decode($packedString, true);
        if (json_last_error() != JSON_ERROR_NONE) {
            throw (new PackedDataErrorException());
        }

        return $arr;
    }
}
