<?php

namespace Popo1h\Support\Traits\StringPack;

trait JsonAutoTrait
{
    use JsonTrait, AutoGetPropertyNamesTrait {
        AutoGetPropertyNamesTrait::getPackPropertyNames insteadof JsonTrait;
    }
}
