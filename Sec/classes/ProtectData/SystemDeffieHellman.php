<?php

namespace ProtectData;

class SystemDeffieHellman extends SystemBase
{
    protected $G;
    protected $Q;

    public function __construct($b = 8)
    {
        $this->bits = $b;
        $next = gmp_nextprime(gmp_pow(2, $b));
        while(!gmp_prob_prime(gmp_add(gmp_mul($next, 2), 1))) {
            $next = gmp_nextprime($next);
        }
        $this->Q = $next;
        $this->P = gmp_add(gmp_mul($next, 2), 1);
        do {
            $this->G = gmp_mod(gmp_random(1), $this->P);
        } while (gmp_cmp(gmp_powm($this->G, $this->Q, $this->P), 1) === 0);
    }

    public function dump()
    {
        print "Q = ".gmp_strval($this->Q)."\n";
        print "P = ".gmp_strval($this->P)."\n";
        print "G = ".gmp_strval($this->G)."\n";
    }



    public function getG()
    {
        return $this->G;
    }
}