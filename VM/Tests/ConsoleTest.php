<?php

namespace Tests;

use System\Console;

require_once("System/Console.php");

class ConsoleTest extends \PHPUnit_Framework_TestCase
{
    public function testGetSet()
    {
        $console = new Console();
        $console->cmd('1', 0);
        $console->cmd('2', 0);
        $console->cmd('3', 0);
        $console->cmd('4', 0);
        $console->cmd('5', 0);
        $console->cmd('6', 0);
        $this->assertEquals(array('1', '2', '3', '4', '5', '6'), $console->get());
    }
}
