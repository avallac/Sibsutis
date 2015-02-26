<?php

namespace System;

class CPU
{
    const COMMAND_FLAG = 16384;
    const COMMAND_MASK = 16256;
    const PARAM_MASK = 127;
    private $instructionCounter = 0;
    private $acc = 0;
    private $VM;
    private $stop = 1;
    private $curCommand;
    private $cpuId;

    private static $commands = array(
        01 => 'CPUID',
        20 => 'LOAD',
        21 => 'STORE',
        30 => 'ADD',
        31 => 'SUB',
        32 => 'DIVIDE',
        33 => 'MUL',
        40 => 'JUMP',
        41 => 'JNEG',
        42 => 'JZ',
        43 => 'HALT',
        52 => '_AND',
        60 => 'CHL',
        61 => 'SHR',
    );

    public function __construct($VM, $id)
    {
        $this->VM = $VM;
        $this->cpuId = $id;
        $this->reInit();
    }

    public function reInit()
    {
        $this->instructionCounter = 0;
        $this->acc = 0;
        $this->stop = 1;
    }

    public function getCurrentCommand()
    {
        return $this->curCommand;
    }

    public function getAcc()
    {
        return $this->acc;
    }

    public function getInstructionCounter()
    {
        return $this->instructionCounter;
    }

    public function run()
    {
        $this->stop = 0;
    }

    public function tick()
    {
        if (!$this->stop) {
            $this->decode($this->readMemory($this->instructionCounter));
            $this->instructionCounter++;
        }
    }

    private function decode($command)
    {
        if (!($command & self::COMMAND_FLAG)) {
            $command_id = $command & self::COMMAND_MASK;
            $command_id = $command_id >> 7;
            $param = $command & self::PARAM_MASK;
            if (isset(self::$commands[$command_id])) {
                $eCommand = self::$commands[$command_id];
                if ($eCommand) {
                    $this->$eCommand($param);
                    $this->curCommand = $eCommand . ":" . base_convert($param, 10, 16);
                }
            } else {
                $this->console->cmd("Bad command: " . $command, 0);
                $this->HALT(0);
            }
        } else {
            $this->console->cmd("Bad command: " . $command, 0);
            $this->HALT(0);
        }
    }

    private function readMemory($i)
    {
        $tmp = 0;
        if ($this->VM->memory->get($i, $tmp)) {
            return $tmp;
        } else {
            $this->HALT();
        }
    }

    private function writeMemory($i, $val)
    {
        if (!$this->VM->memory->set($i, $val)) {
            $this->HALT();
        }
    }

    private function ADD($param)
    {
        $this->acc += $this->readMemory($param);
    }

    private function LOAD($param)
    {
        $this->acc = $this->readMemory($param);
    }

    private function STORE($param)
    {
        $this->writeMemory($param, $this->acc);
    }

    private function SUB($param)
    {
        $this->acc -= $this->readMemory($param);
    }

    private function JZ($param)
    {
        if (!$this->acc) {
            $this->JUMP($param);
        }
    }

    private function CHL($param)
    {
        $this->acc = $this->acc << $param;
    }

    private function SHR($param)
    {
        $this->acc = $this->acc >> $param;
    }

    private function _AND($param)
    {
        $this->acc = $this->acc & $param;
    }

    private function CPUID($param = '')
    {
        $this->acc = $this->cpuId | (VM::CPU << 3);
    }

    private function MUL($param)
    {
        $this->acc *= $this->readMemory($param);
    }

    private function JUMP($param)
    {
        $this->instructionCounter = $param - 1;
    }

    private function HALT($param = '')
    {
        $this->stop = 1;
    }

    public function getFlags()
    {
        $ret = "";
        if ($this->stop) {
            $ret .= " T";
        }
        return $ret;
    }

    public static function getCommandID($command)
    {
        return array_search($command, self::$commands);
    }
}
