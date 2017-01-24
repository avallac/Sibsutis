<?php

namespace ProtectData;

abstract class SystemBase
{
    protected $bits;
    protected $P;

    public function getBits()
    {
        return $this->bits;
    }

    public function getP()
    {
        return $this->P;
    }
}