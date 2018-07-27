<?php

namespace Popo1h\Support\Objects;

class GeoHash
{
    private $base32HashMap = [
        '0', '1', '2', '3', '4', '5', '6', '7',
        '8', '9', 'b', 'c', 'd', 'e', 'f', 'g',
        'h', 'j', 'k', 'm', 'n', 'p', 'q', 'r',
        's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
    ];

    private function bitArrayToBase32Hash($bitArray)
    {
        $base32Str = '';
        $bitOffset = 0;
        $val = 0;
        foreach ($bitArray as $bit) {
            $val <<= 1;
            $val += $bit;
            if ($bitOffset++ == 4) {
                $base32Str .= $this->base32HashMap[$val];
                $bitOffset = 0;
                $val = 0;
            }
        }

        return $base32Str;
    }

    private function base32HashToBitArray($base32Hash)
    {
        $length = strlen($base32Hash);

        $base32ValMap = array_flip($this->base32HashMap);
        $bitArrayReverse = [];
        for ($i = $length - 1; $i >= 0; $i--) {
            $base32Val = $base32ValMap[$base32Hash[$i]];

            for ($j = 0; $j < 5; $j++) {
                $bitArrayReverse[] = $base32Val % 2;
                $base32Val >>= 1;
            }
        }

        return array_reverse($bitArrayReverse);
    }

    private function getGeoHashByBitArray($latBitArray, $lngBitArray)
    {
        $geoBitArray = [];

        $offset = 0;
        while (isset($latBitArray[$offset])) {
            $geoBitArray[] = $latBitArray[$offset];
            $geoBitArray[] = $lngBitArray[$offset];
            $offset++;
        }

        return $this->bitArrayToBase32Hash($geoBitArray);
    }

    private function getBitArrayByGeoHash($geoHash)
    {
        $geoBitArray = $this->base32HashToBitArray($geoHash);

        $latBitArray = [];
        $lngBitArray = [];
        $latBitFlag = true;
        foreach ($geoBitArray as $geoBit) {
            if ($latBitFlag) {
                $latBitArray[] = $geoBit;
            } else {
                $lngBitArray[] = $geoBit;
            }
            $latBitFlag = !$latBitFlag;
        }

        return [
            'lat_bit_array' => $latBitArray,
            'lng_bit_array' => $lngBitArray,
        ];
    }

    /**
     * @param double $lat 纬度
     * @param double $lng 经度
     * @param int $geoHashLength geo hash长度(仅可为偶数)
     * @return string
     */
    public function getGeoHash($lat, $lng, $geoHashLength = 12)
    {
        $latL = -90.0;
        $latR = 90.0;
        $lngL = -180.0;
        $lngR = 180.0;

        //geo使用base32编码, 每个字符5位, 经纬度各一半
        $bitLength = 5 * $geoHashLength / 2;

        $latBitArray = [];
        $lngBitArray = [];
        for ($i = 0; $i < $bitLength; $i++) {
            $latM = ($latL + $latR) / 2.0;
            if ($lat >= $latM) {
                $latBitArray[] = 1;
                $latL = $latM;
            } else {
                $latBitArray[] = 0;
                $latR = $latM;
            }

            $lngM = ($lngL + $lngR) / 2.0;
            if ($lng >= $lngM) {
                $lngBitArray[] = 1;
                $lngL = $lngM;
            } else {
                $lngBitArray[] = 0;
                $lngR = $lngM;
            }
        }

        return $this->getGeoHashByBitArray($latBitArray, $lngBitArray);
    }

    /**
     * @param string $geoHash
     * @return array [ 'lat' => 0.0, 'lng' => 0.0 ]
     */
    public function getCenterLocation($geoHash)
    {
        $bitArrays = $this->getBitArrayByGeoHash($geoHash);
        $latBitArray = $bitArrays['lat_bit_array'];
        $lngBitArray = $bitArrays['lng_bit_array'];

        $latL = -90.0;
        $latR = 90.0;
        $lngL = -180.0;
        $lngR = 180.0;

        $lat = 0;
        $lng = 0;
        $length = count($latBitArray);
        for ($i = 0; $i < $length; $i++) {
            if ($latBitArray[$i] == 1) {
                $latL = $lat;
            } else {
                $latR = $lat;
            }
            $lat = ($latL + $latR) / 2.0;

            if ($lngBitArray[$i] == 1) {
                $lngL = $lng;
            } else {
                $lngR = $lng;
            }
            $lng = ($lngL + $lngR) / 2.0;
        }

        return [
            'lat' => $lat,
            'lng' => $lng,
        ];
    }

    /**
     * @param string $geoHash
     * @return array 依次从左上->右上->左下->右下
     */
    public function getNearGeoHash($geoHash)
    {
        $bitArrays = $this->getBitArrayByGeoHash($geoHash);
        $latBitArray = $bitArrays['lat_bit_array'];
        $lngBitArray = $bitArrays['lng_bit_array'];

        /**
         * @param array $bitArray
         * @param int $type 1:加一, -1:减一
         * @return array
         */
        $funcBitArrayCal = function ($bitArray, $type) {
            $count = count($bitArray);

            for ($i = $count - 1; $i >= 0; $i--) {
                if ($bitArray[$i] == 0) {
                    $bitArray[$i] = 1;
                    if ($type == 1) {
                        break;
                    }
                } else {
                    $bitArray[$i] = 0;
                    if ($type == -1) {
                        break;
                    }
                }
            }

            return $bitArray;
        };

        $latBitArrayInc1 = $funcBitArrayCal($latBitArray, 1);
        $latBitArrayDec1 = $funcBitArrayCal($latBitArray, -1);
        $lngBitArrayInc1 = $funcBitArrayCal($lngBitArray, 1);
        $lngBitArrayDec1 = $funcBitArrayCal($lngBitArray, -1);

        return [
            $this->getGeoHashByBitArray($latBitArrayInc1, $lngBitArrayDec1),
            $this->getGeoHashByBitArray($latBitArrayInc1, $lngBitArray),
            $this->getGeoHashByBitArray($latBitArrayInc1, $lngBitArrayInc1),
            $this->getGeoHashByBitArray($latBitArray, $lngBitArrayDec1),
            $this->getGeoHashByBitArray($latBitArray, $lngBitArrayInc1),
            $this->getGeoHashByBitArray($latBitArrayDec1, $lngBitArrayDec1),
            $this->getGeoHashByBitArray($latBitArrayDec1, $lngBitArray),
            $this->getGeoHashByBitArray($latBitArrayDec1, $lngBitArrayInc1),
        ];
    }
}
