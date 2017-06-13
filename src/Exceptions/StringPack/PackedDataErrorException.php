<?php

namespace Popo1h\Support\Exceptions\StringPack;

class PackedDataErrorException extends StringPackException
{
    public function __construct($packedData = '')
    {
        parent::__construct('packed data error', 0, null);
    }

}
