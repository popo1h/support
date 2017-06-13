<?php

namespace Popo1h\Support\Traits\StringPack;

use Popo1h\Support\Exceptions\StringPack\PackedDataErrorException;

trait SerializeTrait
{
    use StringPackTrait;

    protected static function stringPackPack($needPackArr)
    {
        return serialize($needPackArr);
    }

    protected static function stringPackUnpack($packedString)
    {
        try {
            return unserialize($packedString);
        }
        catch (\Exception $e){
            throw (new PackedDataErrorException());
        }
    }
}
