<?php

namespace System;

class Console
{
    private $outBuff;
    private $userInput = '';
    public function __construct()
    {
        $this->buff = array();
        $this->reInit();
    }

    public function reInit()
    {
        $this->outBuff[0] =" ____  _                 _         ____                            _";
        $this->outBuff[1] ="/ ___|(_)_ __ ___  _ __ | | ___   / ___|___  _ __ ___  _ __  _   _| |_ ___ _ __";
        $this->outBuff[2] ="\\___ \\| | '_ ` _ \\| '_ \\| |/ _ \\ | |   / _ \\| '_ ` _ \\| '_ \\| | | | __/ _ \\ '__|";
        $this->outBuff[3] =" ___) | | | | | | | |_) | |  __/ | |__| (_) | | | | | | |_) | |_| | ||  __/ |";
        $this->outBuff[4] ="|____/|_|_| |_| |_| .__/|_|\\___|  \\____\\___/|_| |_| |_| .__/ \\__,_|\\__\\___|_|";
        $this->outBuff[5] ="                  |_|                                 |_|";
    }

    public function get()
    {
        return $this->outBuff;
    }

    public function cmd($cmd, $user = 0)
    {
        for ($i = 0; $i < 5; $i++) {
            $this->outBuff[$i] = $this->outBuff[$i + 1];
        }
        if ($user) {
            $this->outBuff[5] = 'guest@sc> '.$cmd;
            $this->userInput = $cmd;
        } else {
            $this->outBuff[5] = $cmd;
        }
    }

    public function getInput(&$val)
    {
        if ($this->userInput != '') {
            $val = $this->userInput;
            $this->userInput = '';
            return 1;
        }
        return 0;
    }
}
