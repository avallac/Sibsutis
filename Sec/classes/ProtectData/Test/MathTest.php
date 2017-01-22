<?php

namespace ProtectData\Test;

use ProtectData\Math;

class MathTest extends \PHPUnit_Framework_TestCase
{
    public function testPow()
    {
        $this->assertSame(4, Math::powm(3, 19, 11));
        $this->assertSame(gmp_intval(gmp_powm(3, 16, 17)), Math::powm(3, 16, 17));
        $this->assertSame(gmp_intval(gmp_powm(10, 18, 19)), Math::powm(10, 18, 19));
        $this->assertSame(gmp_intval(gmp_powm(3, 19, 11)), Math::powm(3, 19, 11));
        $this->assertSame(gmp_intval(gmp_powm(99999999, 99999998, 99999992)), Math::powm(99999999, 99999998, 99999992));
    }

    public function gcdProvider()
    {
        return [
            [72, 48],
            [48, 72],
            [10, 0],
            [0, 10],
            [99999999, 99999997],
        ];
    }

    /**
     * @dataProvider gcdProvider
     */
    public function testGcd($a, $b)
    {
        $V = gmp_gcdext($a, $b);
        $this->assertSame([gmp_intval($V['g']), gmp_intval($V['s']), gmp_intval($V['t'])], Math::gcdext($a, $b));
    }
}