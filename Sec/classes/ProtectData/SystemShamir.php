<?php

namespace ProtectData;

class SystemShamir extends SystemBase
{
    protected $P;
    protected $bits;

    public function __construct($b = 8)
    {
        $this->bits = $b;
        $this->P = gmp_nextprime(gmp_pow(2, $b));
    }
}