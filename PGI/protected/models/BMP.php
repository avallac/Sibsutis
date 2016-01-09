<?php

/**
 * Created by PhpStorm.
 * User: avallac
 * Date: 08.01.16
 * Time: 20:27
 */

class BMP extends ImageBase
{
    private $mapInfo;
    private $palette = [];

    private $usedPalette = 0;

    public function __construct($header, $mapInfo, $palette, $image)
    {
        $this->header = $header;
        $this->mapInfo = $mapInfo;
        $this->palette = $palette;
        $this->image = $image;
        if ($this->mapInfo['BitCount'] <= 8) {
            $this->usedPalette = 1;
        }
    }

    public function getMapInfo()
    {
        return $this->mapInfo;
    }

    /**
     * @param $img BMP
     * @param $x int
     * @param $y int
     */
    public function add($img, $x, $y)
    {
        $this->convertToTC();
        $img->convertToTC();
        $data = $img->getImage();
        $mi = $img->getMapInfo();
        for ($i = 0; ($i < $mi['Height']) && ($i + $x < $this->mapInfo['Height']); $i++) {
            for ($j = 0; ($j < $mi['Width']) && ($j + $y < $this->mapInfo['Width']); $j++) {
                $b1 = $this->image[$i+$x][$j+$y] % 256;
                $g1 = $this->image[$i+$x][$j+$y] % (256 * 256) >> 8;
                $r1 = $this->image[$i+$x][$j+$y] >> 16;
                $b2 = $data[$i][$j] % 256;
                $g2 = $data[$i][$j] % (256 * 256) >> 8;
                $r2 = $data[$i][$j] >> 16;
                $this->image[$i+$x][$j+$y] =
                    (int)($b1 * 0.55 + $b2 * 0.45) +
                    (int)($g1 * 0.55 + $g2 * 0.45) * 256 +
                    (int)($r1 * 0.55 + $r2 * 0.45) * 256 * 256;
            }
        }
    }

    public function drawBorder()
    {
        if ($this->usedPalette) {
            $color = rand(0, pow(2, $this->mapInfo['BitCount']));
        } else {
            $color = rand(0, 255) + rand(0, 255) * 256 + rand(0, 255) * 256 * 256;
        }
        for ($i = 0; $i < 15; $i ++) {
            for ($j = 0; $j < $this->mapInfo['Width']; $j++) {
                    $this->image[$this->mapInfo['Height'] - $i][$j] = $color;
                    $this->image[$i][$j] = $color;
            }
        }
        for ($i = 0; $i < $this->mapInfo['Height']; $i ++) {
            for ($j = 0; $j < 15; $j++) {
                $this->image[$i][$j] = $color;
                $this->image[$i][$this->mapInfo['Width'] - $j] = $color;
            }
        }
    }

    public function convertToGrey()
    {
        if ($this->usedPalette) {
            $count = pow(2, $this->mapInfo['BitCount']);
            for ($i = 0; $i < $count; $i++) {
                $e = $this->palette[$i];
                $middle = (int)(($e['Blue'] + $e['Green'] + $e['Red']) / 3);
                $this->palette[$i]['Blue'] = $middle;
                $this->palette[$i]['Green'] = $middle;
                $this->palette[$i]['Red'] = $middle;
            }
        } else {
            for ($i = 0; $i < $this->mapInfo['Height']; $i++) {
                for ($j = 0; $j < $this->mapInfo['Width']; $j++) {
                    $b = $this->image[$i][$j] % 256;
                    $g = $this->image[$i][$j] % (256 * 256) >> 8;
                    $r = $this->image[$i][$j] >> 16;
                    $middle = (int)(($b + $g + $r) / 3);
                    $this->image[$i][$j] = $middle * (1 + 256 + 256 * 256);
                }
            }
        }
    }

    public function save()
    {
        if ($this->mapInfo['BitCount'] == 24) {
            $this->fixHeader();
        }
        $out = '';
        $out .= pack(
            "vLvvL",
            $this->header['bfType'],
            $this->header['bfSize'],
            $this->header['bfReserved1'],
            $this->header['bfReserved2'],
            $this->header['bfOffBits']
        );
        $out .= pack(
            "LLLvvLLLLLL",
            $this->mapInfo['Size'],
            $this->mapInfo['Width'],
            $this->mapInfo['Height'],
            $this->mapInfo['Planes'],
            $this->mapInfo['BitCount'],
            $this->mapInfo['Compression'],
            $this->mapInfo['SizeImage'],
            $this->mapInfo['XPelsPerMeter'],
            $this->mapInfo['YPelsPerMeter'],
            $this->mapInfo['ClrUsed'],
            $this->mapInfo['ClrImportant']
        );
        if ($this->usedPalette) {
            $count = pow(2, $this->mapInfo['BitCount']);
            for ($i = 0; $i < $count; $i++) {
                $e = $this->palette[$i];
                $out .= pack('C4', $e['Blue'], $e['Green'], $e['Red'], 0);
            }
        }
        $out .= $this->packData();
        return $out;
    }

    private function packData()
    {
        $out = '';
        if ($this->mapInfo['BitCount'] == 4) {
            $wb = 0;
            for ($i = $this->mapInfo['Height'] - 1; $i >= 0; $i--) {
                for ($j = 0; $j < $this->mapInfo['Width']; $j+=2) {
                    $out .= pack('C', ($this->image[$i][$j]<<4) + $this->image[$i][$j+1]);
                    $wb++;
                }
                while ($wb % 4) {
                    $out .= ' ';
                    $wb++;
                }
            }

        }
        if ($this->mapInfo['BitCount'] == 8) {
            for ($i = $this->mapInfo['Height'] - 1; $i >= 0; $i--) {
                for ($j = 0; $j < $this->mapInfo['Width']; $j++) {
                    $out .= pack('C', $this->image[$i][$j]);
                }
                while ($j % 4) {
                    $out .= pack('C', 0);
                    $j++;
                }
            }
        }
        if ($this->mapInfo['BitCount'] == 24) {
            for ($i = $this->mapInfo['Height'] - 1; $i >= 0; $i--) {
                for ($j = 0; $j < $this->mapInfo['Width']; $j++) {
                    $b = $this->image[$i][$j] % 256;
                    $g = $this->image[$i][$j] % (256*256) >> 8;
                    $r = $this->image[$i][$j] >> 16;
                    $out .= pack('CCC', $b, $g, $r);
                }
                $wb = $j * 3;
                while ($wb % 4) {
                    $out .= ' ';
                    $wb++;
                }
            }
        }
        return $out;
    }

    private function fixHeader()
    {
        $this->mapInfo['ClrUsed'] = 0;
        $this->mapInfo['ClrImportant'] = 0;
        $this->mapInfo['SizeImage'] = 0;
        $lineSize = 3 * $this->mapInfo['Width'];
        while ($lineSize % 4) {
            $lineSize ++;
        }
        $dataSize = $lineSize * $this->mapInfo['Height'];
        $this->header['bfSize'] = $dataSize + 54;
        $this->header['bfOffBits'] = 54;
        $this->mapInfo['size'] = 40;
    }

    public function convertToTC()
    {
        if ($this->usedPalette) {
            for ($i = 0; $i < $this->mapInfo['Height']; $i++) {
                for ($j = 0; $j < $this->mapInfo['Width']; $j++) {
                    $e = $this->palette[$this->image[$i][$j]];
                    $this->image[$i][$j] = $e['Blue'] + $e['Green'] * 256 + $e['Red'] * 256 * 256;
                }
            }
            $this->mapInfo['BitCount'] = 24;
            $this->usedPalette = 0;
        }
    }

    public function show()
    {
        $this->convertToTC();
        $image = imagecreatetruecolor($this->mapInfo['Width'], $this->mapInfo['Height']);
        for ($i = 0; $i < $this->mapInfo['Height']; $i ++) {
            for ($j = 0; $j < $this->mapInfo['Width']; $j ++) {
                imagesetpixel($image, $j, $i, $this->image[$i][$j]);
            }
        }
        imagepng($image);
        imagedestroy($image);
    }
}
