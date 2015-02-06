<?php

namespace System;

class VM
{
    CONST CPU = 1;
    public $memory;
    public $console;
    public $cpu = array();
    public $tick = 0;

    public function __construct()
    {
        $this->memory = new Memory();
        $this->console = new Console();
        foreach (range(0, self::CPU-1) as $number) {
            $this->cpu[$number] = new CPU($this, $number);
        }
    }

    public function init()
    {
        $this->memory->reInit();
        $this->console->reInit();
        foreach (range(0, self::CPU-1) as $number) {
            $this->cpu[$number]->reInit();
        }
        $pg = array(
            '00 LOAD  10',
            '01 STORE 12',
            '02 SUB   11',
            '03 JZ    9',
            '04 STORE 10',
            '05 MUL   12',
            '06 STORE 12',
            '07 LOAD  10',
            '08 JUMP  02',
            '09 HALT  00',
            '10 = 5',
            '11 = 1',
            '12 = 0',
        );
        $pattern = '/(\d+)\s+(\S+)\s+(\d+)/';
        foreach($pg as $command) {
            if (preg_match($pattern, $command, $matches)) {
                if($matches[2] == '=') {
                    $this->memory->set($matches[1], $matches[3]);
                }elseif($command = CPU::getCommandID($matches[2])){
                    $command = $command << 7;
                    $command += $matches[3];
                    $this->memory->set($matches[1], $command);
                }else{
                    print("Error - $command\n");
                    exit;
                }
            }else{
                print("Error - $command\n");
                exit;
            }
        }
    }

    public function tick()
    {
        $this->tick++;
        foreach (range(0, self::CPU-1) as $number) {
            $this->cpu[$number]->run();
        }
    }

    public function getTick()
    {
        return $this->tick;
    }

    public function getInstructionCounter()
    {
        return $this->cpu[0]->getInstructionCounter();
        $ret = '';
        foreach (range(0, self::CPU-1) as $number) {
            $ret.= $this->cpu[$number]->getInstructionCounter()." ";
        }
        return $ret;
    }

    public function getAcc()
    {
        $ret = '';
        foreach (range(0, self::CPU-1) as $number) {
            $ret.= $this->cpu[$number]->getAcc()." ";
        }
        return $ret;
    }


}
