<?php

namespace System;

use VM\Memory;

class VM
{
    public $memory;

    public function __construct()
    {
        $this->init();
    }

    public function init()
    {
        $this->memory = new Memory();
    }
}
