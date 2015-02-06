<?php

declare(ticks = 1);
require_once('Http/Server.php');
require_once('Http/Template.php');
require_once('Http/MainController.php');
require_once('System/CPU.php');
require_once('System/Memory.php');
require_once('System/Console.php');
require_once('System/VM.php');

$VM = new \System\VM();

function sig_handler($sign)
{
    GLOBAL $VM;
    $VM->tick();
    \pcntl_alarm(1);
}

pcntl_signal(SIGALRM,  "sig_handler", true);
\pcntl_alarm(1);
$server = new \Http\Server();
$server->run('0.0.0.0', 8080, $VM);
