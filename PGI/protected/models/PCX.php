<?php

class PCX extends ImageBase
{
    private $handle;
    private $params;
    private $buffer;

    private $palette;
    private $current = 1;

    public function load($fileName, $readData = 1)
    {
        $this->handle = fopen($fileName, "r");
        $this->readHeader();
        $dataSize = filesize($fileName) - 128;
        if (($this->buffer = fread($this->handle, $dataSize)) === false) {
            return false;
        }
        if ($readData) {
            $this->buffer = unpack('C'.$dataSize, $this->buffer);
            $this->current = 1;
            $this->decodePalette();
            $this->readData();
        }
    }

    private function decodePalette()
    {
        if ($this->header['BitPerPixel'] == 4) {
            for ($code = 0; $code < 16; $code++) {
                $r = $this->header['Palette' . ($code * 3 + 1)];
                $g = $this->header['Palette' . ($code * 3 + 2)];
                $b = $this->header['Palette' . ($code * 3 + 3)];
                $this->palette[$code] = $b + $g * 256 + $r * 256 * 256;
            }
        }
        if ($this->header['BitPerPixel'] == 8) {
            $start = sizeof($this->buffer) - 768;
            $ver = $this->buffer[$start++];
            if ($ver == 12) {
                for ($i = 0; $i < 256; $i++) {
                    $b = $this->buffer[$start++];
                    $g = $this->buffer[$start++];
                    $r = $this->buffer[$start++];
                    $this->palette[$i] = $r + $g * 256 + $b * 256 * 256;
                }
            }
        }
    }

    private function readHeader()
    {
        if (($fileHeader = fread($this->handle, 128)) === false) {
            return false;
        }
        $pattern = 'CID/CVersion/CCoding/CBitPerPixel/';
        $pattern.= 'SXMin/SYMin/SXMax/SYMax/SHRes/SVRes/';
        $pattern.= 'C48Palette/CReserved/CPlanes/SBytePerLine/SPaletteInfo/';
        $pattern.= 'SHScreenSize/SVScreenSize/X54';
        $this->header = unpack($pattern, $fileHeader);
        if ($this->header['ID'] != 10) {
            return false;
        }
        $this->params['xSize'] = $this->header['XMax'] - $this->header['XMin'] + 1;
        $this->params['ySize'] = $this->header['YMax'] - $this->header['YMin'] + 1;
    }

    private function readData()
    {
        for ($j = 0; $j < $this->params['ySize']; $j++) {
            for ($i = 0; $i < $this->header['BytePerLine']; $i ++) {
                $arr[$i] = 0;
            }
            for ($pl = $this->header['Planes'] - 1; $pl >= 0; $pl--) {
                $base = pow(256, $pl);
                $len = 0;
                while ($len < $this->header['BytePerLine']) {
                    $b = $this->buffer[$this->current++];
                    if (($b & 192) == 192) {
                        $count = $b & 63;
                        $t = $this->buffer[$this->current++];
                        for ($i = 0; $i < $count; $i++) {
                            $arr[$len++] += $t * $base;
                        }
                    } else {
                        $arr[$len++] += $b * $base;
                    }
                }
            }
            if ($this->header['BitPerPixel'] == 4) {
                $this->decode4($j, $arr);
            } elseif ($this->header['BitPerPixel'] == 8) {
                if ($this->header['Planes'] == 1) {
                    $this->decode8($j, $arr);
                } else {
                    $this->decode24($j, $arr);
                }
            }
        }
    }

    public function decode4($j, $arr)
    {
        $link = &$this->image[$j];
        for ($i = 0; $i < $this->header['BytePerLine']; $i ++) {
            $link[2 * $i + 1] = $this->palette[$arr[$i] & 15];
            $link[2 * $i] = $this->palette[$arr[$i] >> 4];
        }
    }

    public function decode8($j, $arr)
    {
        $link = &$this->image[$j];
        for ($i = 0; $i < $this->header['BytePerLine']; $i ++) {
            $link[$i] = $this->palette[$arr[$i]];
        }
    }

    public function decode24($j, $arr)
    {
        $link = &$this->image[$j];
        for ($i = 0; $i < $this->header['BytePerLine']; $i ++) {
            $link[$i] = $arr[$i];
        }
    }

    public function show()
    {
        $image = imagecreatetruecolor($this->params['xSize'], $this->params['ySize']);
        for ($i = 0; $i < $this->params['ySize']; $i ++) {
            for ($j = 0; $j < $this->params['xSize']; $j ++) {
                if (isset($this->image[$i][$j])) {
                    imagesetpixel($image, $j, $i, $this->image[$i][$j]);
                }
            }
        }
        imagepng($image);
        imagedestroy($image);
    }
}
