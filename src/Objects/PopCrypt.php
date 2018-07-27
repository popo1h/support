<?php

namespace Popo1h\Support\Objects;

class PopCrypt
{
    protected $encodeBitLength = 6;

    protected $oriBitLength = 8;

    protected $hashMap = [
        '1', '2', '3', '4', '5', '6', '7', '8', '9', '0', 'a', 'b', 'c', 'd', 'e', 'f',
        'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v',
        'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L',
        'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '-', '+',
    ];

    public function encodeStr($str, $key)
    {
        $md5Key = md5($key);
        $md5KeyLength = 32;

        $md5CharValArray = [];
        for ($i = 0; $i < $md5KeyLength; $i++) {
            $md5CharValArray[$i] = ord($md5Key[$i]);
        }

        $strLength = strlen($str);

        $encodeBitCount = 0;
        $encodeBitMaxOffset = $this->encodeBitLength - 1;
        $encodeHashValue = 0;
        $oriBitMaxOffset = $this->oriBitLength - 1;
        $encodeStr = '';
        for ($i = 0; $i < $strLength; $i++) {
            $strCharVal = ord($str[$i]);
            $md5CharVal = $md5CharValArray[$i % $md5KeyLength];

            for ($j = $oriBitMaxOffset; $j >= 0; $j--) {
                $bit1 = ($strCharVal >> $j) % 2;
                $bit2 = ($md5CharVal >> $j) % 2;
                $encodeHashValue += ($bit1 ^ $bit2) << ($encodeBitMaxOffset - $encodeBitCount);
                if ($encodeBitCount == $encodeBitMaxOffset) {
                    $encodeStr .= $this->hashMap[$encodeHashValue];

                    $encodeBitCount = 0;
                    $encodeHashValue = 0;
                } else {
                    $encodeBitCount++;
                }
            }
        }
        if ($encodeBitCount != 0) {
            $encodeStr .= $this->hashMap[$encodeHashValue];
        }

        return $encodeStr;
    }

    public function decodeStr($encodeStr, $key)
    {
        $md5Key = md5($key);
        $md5KeyLength = 32;

        $md5CharValArray = [];
        for ($i = 0; $i < $md5KeyLength; $i++) {
            $md5CharValArray[$i] = ord($md5Key[$i]);
        }

        $encodeStrLength = strlen($encodeStr);

        $hashValMap = array_flip($this->hashMap);

        $str = '';
        $strOffset = 0;
        $oriBitMaxOffset = $this->oriBitLength - 1;
        $strBitNowOffset = $oriBitMaxOffset;
        $strCharVal = 0;
        $md5CharVal = $md5CharValArray[$strOffset];
        $encodeBitMaxOffset = $this->encodeBitLength - 1;
        for ($i = 0; $i < $encodeStrLength; $i++) {
            $encodeHashValue = $hashValMap[$encodeStr[$i]];
            for ($j = $encodeBitMaxOffset; $j >= 0; $j--) {
                $bit1 = ($encodeHashValue >> $j) % 2;
                $bit2 = ($md5CharVal >> $strBitNowOffset) % 2;
                $strCharVal += ($bit1 ^ $bit2) << $strBitNowOffset;
                if ($strBitNowOffset == 0) {
                    $str .= chr($strCharVal);
                    $strOffset++;

                    $strCharVal = 0;
                    $md5CharVal = $md5CharValArray[$strOffset % $md5KeyLength];
                    $strBitNowOffset = $oriBitMaxOffset;
                } else {
                    $strBitNowOffset--;
                }
            }
        }

        return $str;
    }
}
