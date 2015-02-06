<?php

require_once("System/Memory.php");

class Unit_AppTest extends PHPUnit_Framework_TestCase
{
    public function testGetSet()
    {
        $memory = new System\Memory();
        $memory->set(1, 1);
        $this->assertEquals(1, $memory->get(2, $val));
        $this->assertEquals(0, $val);
        $this->assertEquals(1, $memory->get(1, $val));
        $this->assertEquals(1, $val);
    }
}
