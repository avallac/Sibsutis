<?php

namespace ProtectData;

class Math
{

    // https://oeis.org/A005384
    CONST Q = 1511;
    CONST P = 3023;

    // 3^1 = 3 (%11) = 3
    // 3^2 = 9 (%11) = 9
    // 3^4 = 3^2 * 3^2 (%11) = 9 * 9 (% 11) = 4
    // 3^8 = 3^4 * 3^4 (%11) = 4 * 4 (% 11) = 5
    // 3^16 = 3^8 * 3^8 (%11) = 5 * 5 (% 11) = 3
    // 3*9*3 (%11) = 4
    public static function powm($a, $x, $p) {
        $result = 1;
        $c = $a;
        while($x) {
            if ($x & 1) {
                $result = $result * $c % $p;
            }
            $x >>= 1;
            $c = $c * $c % $p;
        }
        return $result;
    }

    public static function gcdext($a, $b)
    {
        $U = [$a, 1, 0];
        if ($b === 0) return $U;
        $V = [$b, 0, 1];
        while (1) {
            $q = (int)($U[0]/$V[0]);
            $T = [$U[0] - $V[0] * $q, $U[1] - $V[1] * $q, $U[2] - $V[2] * $q];
            if ($T[0] === 0) {
                return $V;
            } else {
                $U = $V;
                $V = $T;
            }
        }
    }

    public function genG()
    {
        $x = rand(100);
        var_dump(self::powm($x, self::Q,  self::P));exit;
    }
}