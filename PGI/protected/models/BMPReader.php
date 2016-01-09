<?php
/**
 * Created by PhpStorm.
 * User: avallac
 * Date: 09.01.16
 * Time: 1:42
 */

class BMPReader
{
    private $handle;
    private $header;
    private $mapInfo;
    private $palette = [];
    private $image;

    public function __construct($filename, $readData)
    {
        $this->load($filename, $readData);
    }

    public static function loadFromFile($fileName, $readData = 1)
    {
        $self = new BMPReader($fileName, $readData);
        return new BMP(
            $self->header,
            $self->mapInfo,
            $self->palette,
            $self->image
        );
    }

    private function load($fileName, $readData)
    {
        $this->handle = fopen($fileName, "r");
        $this->readHeader();
        $this->readBitMap();
        if ($readData) {
            $this->readPalette();
            $this->readData();
        }
        fclose($this->handle);
    }

    private function readHeader()
    {
        if (($bitMapFileHeader = fread($this->handle, 14)) === false) {
            return false;
        }
        $this->header = unpack('vbfType/LbfSize/vbfReserved1/vbfReserved2/LbfOffBits', $bitMapFileHeader);
        if ($this->header['bfType'] != hexdec('4D42')) {
            return false;
        }
    }

    private function readBitMap()
    {
        if (($bitMapFileHeader = fread($this->handle, 4)) === false) {
            return false;
        }
        $bitMapInfoSize = unpack('LSize', $bitMapFileHeader)['Size'];
        if ($bitMapInfoSize == 12) {
            $format = '';
        } else {
            $format = 'LWidth/LHeight/vPlanes/vBitCount/LCompression/LSizeImage/LXPelsPerMeter/';
            $format .= 'LYPelsPerMeter/LClrUsed/LClrImportant';
        }
        if (($bitMapFileHeader = fread($this->handle, $bitMapInfoSize - 4)) === false) {
            return false;
        }
        $this->mapInfo = unpack($format, $bitMapFileHeader);
        $this->mapInfo['Size'] = $bitMapInfoSize;
    }

    private function readPalette()
    {
        if ($this->mapInfo['BitCount'] <= 8) {
            $count = pow(2, $this->mapInfo['BitCount']);
            for ($i = 0; $i < $count; $i++) {
                if (($bitMapFileHeader = fread($this->handle, 4)) === false) {
                    return false;
                }
                $this->palette[$i] = unpack('CBlue/CGreen/CRed/X', $bitMapFileHeader);
            }
        }
    }

    private function readData()
    {
        $lineSize = $this->mapInfo['Width'] * $this->mapInfo['BitCount'];
        while ($lineSize % 32) {
            $lineSize ++;
        }
        $lineSize /= 8;
        for ($i = $this->mapInfo['Height']-1; $i >= 0; $i --) {
            if (($buffer = fread($this->handle, $lineSize)) === false) {
                return false;
            }
            //var_dump($i);
            $buffer = unpack('C'.$lineSize, $buffer);
            $queue = [];
            if ($this->mapInfo['BitCount'] == 4) {
                for ($b = 1; $b <= $lineSize; $b ++) {
                    $queue[] = ($buffer[$b] & 240) >> 4;
                    $queue[] = $buffer[$b] & 15;
                }
            } elseif ($this->mapInfo['BitCount'] == 8) {
                for ($b = 1; $b <= $lineSize; $b ++) {
                    $queue[] = $buffer[$b];
                }
            } elseif ($this->mapInfo['BitCount'] == 24) {
                for ($b = 1; $b+2 <= $lineSize; $b +=3) {
                    $queue[] = $buffer[$b] + $buffer[$b+1] * 256 + $buffer[$b+2] * 256 * 256;
                }
            }
            for ($j = 0; $j < $this->mapInfo['Width']; $j ++) {
                $this->image[$i][$j] = $queue[$j];
            }
        }
    }
}
