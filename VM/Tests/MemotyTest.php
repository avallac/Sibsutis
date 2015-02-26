<?php

namespace Tests;

use System\Memory;

require_once("System/Memory.php");

class MemoryTest extends \PHPUnit_Framework_TestCase
{
    public function testGetSet()
    {
        $val = 0;
        $memory = new Memory();
        $memory->set(1, 1);
        $this->assertEquals(1, $memory->get(2, $val));
        $this->assertEquals(0, $val);
        $this->assertEquals(1, $memory->get(1, $val));
        $this->assertEquals(1, $val);
    }
}
