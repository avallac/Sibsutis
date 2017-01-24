<?php

namespace ProtectData;

class KeyElGamal extends KeyDeffieHellman
{
    public function crypt($m, $pub)
    {
        $k = gmp_mod(gmp_random(), $this->p);
        $r = gmp_powm($this->g, $k, $this->p);
        $e = gmp_mod(gmp_mul($m, gmp_powm($pub, $k, $this->p)), $this->p);
        return [$r, $e];
    }

    public function decrypt($r, $e)
    {
        return gmp_mod(gmp_mul($e, gmp_powm($r, $this->p - 1 - $this->private, $this->p)), $this->p);
    }
}