<?php

namespace Tests;

use System\CPU;
use System\Memory;
use System\VM;

require_once("System/CPU.php");
require_once("System/Memory.php");
require_once("System/VM.php");

class CpuTest extends \PHPUnit_Framework_TestCase
{
    public function testStopFlag()
    {
        $cpu = new CPU(null);
        $this->assertEquals(' T', $cpu->getFlags());
        $cpu->run();
        $this->assertEquals('', $cpu->getFlags());
        $cpu->reInit();
        $this->assertEquals(' T', $cpu->getFlags());
    }

    public function testCommands()
    {
        $this->assertEquals(20, CPU::getCommandID('LOAD'));
        $this->assertEquals(43, CPU::getCommandID('HALT'));
    }

    public function testJUMP()
    {
        $vm = new \stdClass;
        $vm->memory = new Memory();
        $cpu = new CPU($vm);
        $this->assertEquals(0, $cpu->getInstructionCounter());
        $vm->memory->set(0, VM::encodeCommand('JUMP', '10'));
        $cpu->run();
        $cpu->tick();
        $this->assertEquals(10, $cpu->getInstructionCounter());
        $this->assertEquals('JUMP:a', $cpu->getCurrentCommand());
    }
    public function testLOAD()
    {
        $vm = new \stdClass;
        $vm->memory = new Memory();
        $cpu = new CPU($vm);
        $this->assertEquals(0, $cpu->getInstructionCounter());
        $vm->memory->set(10, 10);
        $vm->memory->set(0, VM::encodeCommand('LOAD', '10'));
        $cpu->run();
        $cpu->tick();
        $this->assertEquals(10, $cpu->getAcc());
        $this->assertEquals('LOAD:a', $cpu->getCurrentCommand());
    }
}

