<?php

namespace VM;

class Memory
{
    const MAX = 99;
    const CAPACITY = 7;
    private $memory;

    public function __construct()
    {
        $this->memory = array();
        foreach (range(0, self::MAX) as $number) {
            $this->memory[$number] = 0;
        }
    }

    public function get($num, &$val)
    {
        if ($this->isCorrect($num)) {
            $val = $this->memory[$num];
            return 1;
        }
        return 0;
    }

    public function set($num, $val)
    {
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
