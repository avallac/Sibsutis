<?php

namespace ProtectData\Test;

use ProtectData\KeyDeffieHellman;
use ProtectData\SystemDeffieHellman;

class DiffieHellmanTest extends \PHPUnit_Framework_TestCase
{

    public function lengthProvider()
    {
        return [[8], [16], [32], [64], [128]];
    }

    /**
     * @dataProvider lengthProvider
     */
    public function testKey($len)
    {
        $system = new SystemDeffieHellman($len);
        $key1 = new KeyDeffieHellman($system->getP(), $system->getG(), $system->getBits());
        $key2 = new KeyDeffieHellman($system->getP(), $system->getG(), $system->getBits());
        $sKey1 = gmp_strval($key1->genShare($key2->getPublic())); 
        $sKey2 = gmp_strval($key2->genShare($key1->getPublic())); 
        $this->assertSame($sKey1, $sKey2);
    }
}