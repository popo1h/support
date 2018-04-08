<?php

namespace Popo1h\Support\Objects;

use Popo1h\Support\Interfaces\StringPackInterface;
use Popo1h\Support\Exceptions\StringPack\PackedDataErrorException;

class StringPack
{
    const PACK_DATA_DELIMITER = ':';

    const PACK_DATA_TYPE_STRING_PACK = '1';
    const PACK_DATA_TYPE_ARRAY = '2';
    const PACK_DATA_TYPE_OTHER = '0';
    const PACK_DATA_TYPE_COMPRESS = '99';

    /**
     * @param mixed $unpackData
     * @param bool $compress
     * @return string
     */
    public static function pack($unpackData, $compress = false)
    {
        if ($unpackData instanceof StringPackInterface) {
            $type = self::PACK_DATA_TYPE_STRING_PACK;
            $className = get_class($unpackData);
            $content = $unpackData->packToString();
        } elseif (is_array($unpackData)) {
            $type = self::PACK_DATA_TYPE_ARRAY;
            $className = 'array';
            $packedDataItems = [];
            foreach ($unpackData as $key => $unpackDataItem) {
                $packedDataItems[$key] = self::pack($unpackDataItem);
            }
            $content = json_encode($packedDataItems);
        } else {
            $type = self::PACK_DATA_TYPE_OTHER;
            $className = '';
            $content = serialize($unpackData);
        }

        $packedData = $type . self::PACK_DATA_DELIMITER . $className . self::PACK_DATA_DELIMITER . $content;

        if ($compress) {
            $compressStr = base64_encode(gzcompress($packedData));
            $packedData = self::PACK_DATA_TYPE_COMPRESS . self::PACK_DATA_DELIMITER . '' . self::PACK_DATA_DELIMITER . $compressStr;
        }

        return $packedData;
    }

    /**
     * @param string $packedData
     * @return mixed
     * @throws PackedDataErrorException
     */
    public static function unpack($packedData)
    {
        $tempArr = explode(self::PACK_DATA_DELIMITER, $packedData, 3);
        if (count($tempArr) < 3) {
            throw (new PackedDataErrorException($packedData));
        }
        list($type, $className, $content) = $tempArr;

        switch ($type) {
            case self::PACK_DATA_TYPE_STRING_PACK:
                if (!is_callable([$className, 'unpackString'])) {
                    throw (new PackedDataErrorException($packedData));
                }
                $unpackData = forward_static_call_array([$className, 'unpackString'], [$content]);
                break;
            case self::PACK_DATA_TYPE_ARRAY:
                $unpackData = [];
                foreach (json_decode($content) as $key => $packedDataItem) {
                    $unpackData[$key] = self::unpack($packedDataItem);
                }
                break;
            case self::PACK_DATA_TYPE_OTHER:
                $unpackData = unserialize($content);
                break;
            case self::PACK_DATA_TYPE_COMPRESS:
                $unCompressPackedData = gzuncompress(base64_decode($content));
                $unpackData = self::unpack($unCompressPackedData);
                break;
            default:
                throw (new PackedDataErrorException($packedData));
        }

        return $unpackData;
    }
}
