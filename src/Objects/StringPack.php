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
        } elseif (is_array($unpackData)) {
            $type = self::PACK_DATA_TYPE_ARRAY;
            $className = 'array';
            $packedArrDataStr = '';
            foreach ($unpackData as $unpackDataItem) {
                if ($packedArrDataStr) {
                    $packedArrDataStr .= self::PACK_DATA_DELIMITER;
                }
                $packedArrDataItemStr = static::pack($unpackDataItem);
                $packedArrDataStr .= strlen($packedArrDataItemStr) . self::PACK_DATA_DELIMITER . $packedArrDataItemStr;
            }
            $content = count($unpackData) . self::PACK_DATA_DELIMITER . $packedArrDataStr;
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
     * @throws PackedDataErrorException
     */
    public static function unpack($packedData)
    {
        $unpackData = explode(self::PACK_DATA_DELIMITER, $packedData, 3);
        if (count($unpackData) < 3) {
            throw (new PackedDataErrorException($packedData));
        }
        list($type, $className, $content) = $unpackData;

        switch ($type) {
            case self::PACK_DATA_TYPE_STRING_PACK:
                if (!is_callable([$className, 'unpackString'])) {
                    throw (new PackedDataErrorException($packedData));
                }
                $content = forward_static_call_array([$className, 'unpackString'], [$content]);
                break;
            case self::PACK_DATA_TYPE_ARRAY:
                $content = [];
                $unpackArrData = explode(self::PACK_DATA_DELIMITER, $content, 2);
                if (count($unpackArrData) < 2) {
                    throw (new PackedDataErrorException($packedData));
                }
                list($arrCount, $packedArrDataStr) = $unpackArrData;
                $delimiterLength = strlen(self::PACK_DATA_DELIMITER);
                $dealArrDataItemLength = 0;
                $packedArrDataLength = strlen($packedArrDataStr);
                $restPackedArrDataStr = $packedArrDataStr;
                for ($i = 0; $i < $arrCount; $i++) {
                    $tempDataItem = explode(self::PACK_DATA_DELIMITER, $restPackedArrDataStr, 2);
                    if (count($tempDataItem) < 2) {
                        throw (new PackedDataErrorException($packedData));
                    }
                    list($packedArrDataItemLength, $restPackedArrDataStr) = $tempDataItem;
                    $dealArrDataItemLength += $packedArrDataItemLength + $delimiterLength;
                    if ($dealArrDataItemLength > $packedArrDataLength) {
                        throw (new PackedDataErrorException($packedData));
                    }
                    $packedArrDataItemStr = substr($restPackedArrDataStr, 0, $packedArrDataItemLength);
                    $content[] = static::unpack($packedArrDataItemStr);
                    $restPackedArrDataStr = substr($restPackedArrDataStr, $packedArrDataItemLength);
                }
                break;
            case self::PACK_DATA_TYPE_OTHER:
                $content = unserialize($content);
                break;
            default:
                throw (new PackedDataErrorException($packedData));
        }

        return $content;
    }
}
