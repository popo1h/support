<?php

namespace Popo1h\Support\Traits\StringPack;

trait SerializeAutoTrait
{
    use SerializeTrait, AutoGetPropertyNamesTrait {
        AutoGetPropertyNamesTrait::getPackPropertyNames insteadof SerializeTrait;
    }
}
