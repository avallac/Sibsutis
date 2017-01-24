<?php

namespace ProtectData;

class KeyDeffieHellman extends KeyBase
{

    protected $g;

    public function __construct($p, $g, $bits)
    {
        $this->p = $p;
        $this->g = $g;
        $this->private = gmp_mod(gmp_random(), gmp_pow(2, $bits));
        $this->public = gmp_powm($g, $this->private, $p);
    }

    public function genShare($public)
    {
        return gmp_powm($public, $this->private, $this->p);
    }
}