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
     * @return static
     */
    public static function unpackString($string);
}
