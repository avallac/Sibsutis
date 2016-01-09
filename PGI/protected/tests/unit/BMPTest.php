<?php
/**
 * Created by PhpStorm.
 * User: avallac
 * Date: 08.01.16
 * Time: 20:53
 */

class BMPTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider additionProvider
     */
    public function testRead($fileName, $header, $mapInfo)
    {
        $bmp = BMPReader::loadFromFile($fileName);
        $this->assertEquals($header, $bmp->getHeader());
        $this->assertEquals($mapInfo, $bmp->getMapInfo());
    }

    public function additionProvider()
    {
        return [
            [
                'img/CAT_4.BMP',
                [
                    'bfType' => 19778,
                    'bfSize' => 109558,
                    'bfReserved1' => 0,
                    'bfReserved2' => 0,
                    'bfOffBits' => 118
                ],
                [
                    'Width' => 456,
                    'Height' => 480,
                    'Planes' => 1,
                    'BitCount' => 4,
                    'Compression' => 0,
                    'SizeImage' => 109440,
                    'XPelsPerMeter' => 0,
                    'YPelsPerMeter' => 0,
                    'ClrUsed' => 16,
                    'ClrImportant' => 16,
                    'Size' => 40
                ]
            ],
            [
                'img/CAT_8.BMP',
                [
                    'bfType' => 19778,
                    'bfSize' => 320686,
                    'bfReserved1' => 0,
                    'bfReserved2' => 0,
                    'bfOffBits' => 1078
                ],
                [
                    'Width' => 551,
                    'Height' => 579,
                    'Planes' => 1,
                    'BitCount' => 8,
                    'Compression' => 0,
                    'SizeImage' => 319608,
                    'XPelsPerMeter' => 0,
                    'YPelsPerMeter' => 0,
                    'ClrUsed' => 236,
                    'ClrImportant' => 236,
                    'Size' => 40
                ]
            ],
            [
                'img/CAT_24.BMP',
                [
                    'bfType' => 19778,
                    'bfSize' => 958878,
                    'bfReserved1' => 0,
                    'bfReserved2' => 0,
                    'bfOffBits' => 54
                ],
                [
                    'Width' => 551,
                    'Height' => 579,
                    'Planes' => 1,
                    'BitCount' => 24,
                    'Compression' => 0,
                    'SizeImage' => 958824,
                    'XPelsPerMeter' => 0,
                    'YPelsPerMeter' => 0,
                    'ClrUsed' => 0,
                    'ClrImportant' => 0,
                    'Size' => 40
                ]
            ],
            [
                'img/FISH_24.BMP',
                [
                    'bfType' => 19778,
                    'bfSize' => 2359350,
                    'bfReserved1' => 0,
                    'bfReserved2' => 0,
                    'bfOffBits' => 54
                ],
                [
                    'Width' => 1024,
                    'Height' => 768,
                    'Planes' => 1,
                    'BitCount' => 24,
                    'Compression' => 0,
                    'SizeImage' => 0,
                    'XPelsPerMeter' => 11811,
                    'YPelsPerMeter' => 11811,
                    'ClrUsed' => 0,
                    'ClrImportant' => 0,
                    'Size' => 40
                ]
            ],
        ];
    }
}