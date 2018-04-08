<?php

namespace Popo1h\Support\Exceptions\StringPack;

class PackPropertyNamesErrorException extends StringPackException
{
    public function __construct($packedData = '')
    {
        parent::__construct('pack property names error', 0, null);
    }
}
