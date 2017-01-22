<?php

namespace ProtectData;

class Key
{
    protected $private;
    protected $public;
    protected $p;

    public function __construct($p, $g, $bits)
    {
        $this->p = $p;
        $this->private = gmp_mod(gmp_random(), gmp_pow(2, $bits));
        $this->public = gmp_powm($g, $this->private, $p);
    }

    public function genShare($public)
    {
        return gmp_powm($public, $this->private, $this->p);
    }

    public function getPublic()
    {
        return $this->public;
    }

    public function getPrivate()
    {
        return $this->private;
    }
}