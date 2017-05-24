<?php

namespace Popo1h\Support\Interfaces;

interface StringPackInterface
{
    /**
     * @return string
     */
    public function packToString();

    /**
     * @param string $string
     * @return object
     */
    public static function unpackString($string);
}
