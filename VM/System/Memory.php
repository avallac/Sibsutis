<?php

namespace System;

class Memory
{
    const MAX = 127;
    const CAPACITY = 15;
    private $memory;

    public function __construct()
    {
        $this->memory = array();
        $this->reInit();
    }

    public function reInit()
    {
        foreach (range(0, self::MAX) as $number) {
            $this->memory[$number] = 0;
        }
    }

    public function get($num, &$val)
    {
        $num = (int)$num;
        if ($this->isCorrect($num)) {
            $val = $this->memory[$num];
            return 1;
        }
        return 0;
    }

    public function set($num, $val)
    {
        $num = (int)$num;
        if ($this->isCorrect($num)) {
            $val = (int)$val;
            $val = $val % pow(2, self::CAPACITY);
            $this->memory[$num] = (int)$val;
            return 1;
        }
        return 0;
    }

    public function export()
    {
        return $this->memory;
    }

    private function isCorrect($num)
    {
        if (($num <= self::MAX) || ($num >= 0)) {
            return true;
        } else {
            return false;
        }
    }
}
