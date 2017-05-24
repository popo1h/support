<?php

namespace Popo1h\Support\Objects;

use Popo1h\Support\Interfaces\StringPackInterface;

class StringPack
{
    const PACK_DATA_DELIMITER = ':';

    const PACK_DATA_TYPE_STRING_PACK = '1';
    const PACK_DATA_TYPE_OTHER = '0';

    /**
     * @param mixed $unpackData
     * @return string
     */
    public static function pack($unpackData)
    {
        if ($unpackData instanceof StringPackInterface) {
            $type = self::PACK_DATA_TYPE_STRING_PACK;
            $className = get_class($unpackData);
            $content = $unpackData->packToString();
        } else {
            $type = self::PACK_DATA_TYPE_OTHER;
            $className = '';
            $content = serialize($unpackData);
        }

        return $type . self::PACK_DATA_DELIMITER . $className . self::PACK_DATA_DELIMITER . $content;
    }

    /**
     * @param string $packedData
     * @return mixed
     */
    public static function unpack($packedData)
    {
        list($type, $className, $content) = explode(self::PACK_DATA_DELIMITER, $packedData, 3);

        switch ($type) {
            case self::PACK_DATA_TYPE_STRING_PACK:
                $content = forward_static_call_array([$className, 'unpackString'], [$content]);
                break;
        }

        return $content;
    }
}
