<?php

namespace System;

class Console
{
    private $buff;
    public function __construct()
    {
        $this->buff = array();
        $this->reInit();
    }

    public function reInit()
    {
        $this->buff[0] =" ____  _                 _         ____                            _";
        $this->buff[1] ="/ ___|(_)_ __ ___  _ __ | | ___   / ___|___  _ __ ___  _ __  _   _| |_ ___ _ __";
        $this->buff[2] ="\\___ \\| | '_ ` _ \\| '_ \\| |/ _ \\ | |   / _ \\| '_ ` _ \\| '_ \\| | | | __/ _ \\ '__|";
        $this->buff[3] =" ___) | | | | | | | |_) | |  __/ | |__| (_) | | | | | | |_) | |_| | ||  __/ |";
        $this->buff[4] ="|____/|_|_| |_| |_| .__/|_|\\___|  \\____\\___/|_| |_| |_| .__/ \\__,_|\\__\\___|_|";
        $this->buff[5] ="                  |_|                                 |_|";
    }

    public function get()
    {
        return $this->buff;
    }

    public function cmd($cmd, $user)
    {
        for ($i = 0; $i < 5; $i++) {
            $this->buff[$i] = $this->buff[$i + 1];
        }
        if ($user) {
            $this->buff[5] = 'guest@sc> '.$cmd;
        } else {
            $this->buff[5] = $cmd;
        }


    }
}