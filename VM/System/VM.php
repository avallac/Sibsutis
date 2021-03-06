<?php

namespace System;

class VM
{
    const CPU = 2;
    public $memory;
    public $console;
    public $cpu = array();
    public $tick = 0;
    private $consoleLock = 0;

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
        $this->consoleLock = 0;
        $this->memory->reInit();
        $this->console->reInit();
        foreach (range(0, self::CPU-1) as $number) {
            $this->cpu[$number]->reInit();
        }
    }

    public function tick()
    {
        $this->tick++;
        foreach (range(0, self::CPU-1) as $number) {
            $this->cpu[$number]->tick();
        }
    }

    public function getTick()
    {
        return $this->tick;
    }

    public function getCpuState($param)
    {
        $ret = array();
        foreach (range(0, self::CPU-1) as $number) {
            $ret[]= $this->cpu[$number]->$param();
        }
        return $ret;
    }

    public function isConsoleLock()
    {
        return $this->consoleLock;
    }

    public function setConsoleLock()
    {
        $this->consoleLock = 1;
    }

    public function unsetConsoleLock()
    {
        $this->consoleLock = 0;
    }

    public static function encodeCommand($command, $param)
    {
        $command = CPU::getCommandID($command);
        $ret = $command << 7;
        $ret += $param;
        $ret += CPU::COMMAND_FLAG;
        return $ret;
    }

    public function program($str)
    {
        var_dump($str);
        $pg = explode("\n", $str);
        $pattern = '/^(\d+)\s+(\S+)\s+(\d+)/';
        foreach ($pg as $command) {
            if (preg_match($pattern, $command, $matches)) {
                if ($matches[2] == '=') {
                    $this->memory->set($matches[1], $matches[3]);
                } elseif (CPU::getCommandID($matches[2])) {
                    $command = self::encodeCommand($matches[2], $matches[3]);
                    $this->memory->set($matches[1], $command);
                    $this->console->cmd("Set: " . $matches[1] . " " . $command, 0);
                } else {
                    $this->console->cmd("Bad command: " . $command, 0);
                    return;
                }
            } else {
                $this->console->cmd("Bad command: " . $command, 0);
                return;
            }
        }
        foreach (range(0, self::CPU-1) as $number) {
            $ret[]= $this->cpu[$number]->run();
        }
    }

}
