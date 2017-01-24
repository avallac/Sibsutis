<?php

namespace ProtectData;


class RSA
{
    protected $N;
    protected $D;
    protected $C;

    public function __construct($bits)
    {
        $P = gmp_nextprime(gmp_mod(gmp_random(), gmp_pow(2, $bits)));
        $Q = gmp_nextprime(gmp_mod(gmp_random(), gmp_pow(2, $bits)));
        $this->N = gmp_mul($P, $Q);
        $fi = gmp_mul(gmp_sub($P, 1), gmp_sub($Q, 1));
        do {
            $this->D = gmp_mod(gmp_random(), $this->N);
        } while (gmp_intval(gmp_gcd($this->D, $fi)) !== 1);
        $this->C = gmp_mod(gmp_gcdext($fi, $this->D)['t'], $fi);
        if (gmp_intval(gmp_mod($this->C * $this->D, $fi)) !== 1) {
            throw new \Exception('Беда беда беда');
        }
    }

    public function getN()
    {
        return $this->N;
    }

    public function getC()
    {
        return $this->C;
    }

    public function getD()
    {
        return $this->D;
    }

    public function code($m)
    {
        return gmp_strval(gmp_powm($m, $this->D, $this->N));
    }

    public function decode($m)
    {
        return gmp_strval(gmp_powm($m, $this->C, $this->N));
    }
}