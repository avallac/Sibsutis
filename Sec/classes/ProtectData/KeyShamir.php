<?php

namespace ProtectData;

class KeyShamir extends KeyBase
{
    protected $private2;

    public function __construct($p, $bits)
    {
        $this->p = $p;
        $cmp = gmp_sub($p, 1);
        do {
            $this->private = gmp_mod(gmp_random(), gmp_pow(2, $bits));
        } while (gmp_intval(gmp_gcd($this->private, $cmp)) !== 1);
        $this->private2 = gmp_mod(gmp_gcdext($cmp, $this->private)['t'], $cmp);
        if (gmp_intval(gmp_mod($this->private2 * $this->private, $cmp)) !== 1) {
            throw new \Exception('Беда беда беда');
        }
    }

    public function step1($m)
    {
        return gmp_powm($m, $this->private, $this->p);
    }

    public function step2($m)
    {
        return gmp_intval(gmp_powm($m, $this->private2, $this->p));
    }

    public function dump()
    {
        print "КлючC:".$this->private."\n";
        print "КлючD:".$this->private2."\n";
    }

}