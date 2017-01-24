<?php

namespace ProtectData;

abstract class KeyBase
{
    protected $private;
    protected $public;
    protected $p;

    public function getPublic()
    {
        return $this->public;
    }

    public function getPrivate()
    {
        return $this->private;
    }

}