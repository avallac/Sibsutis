<?php

declare(ticks = 1);

function __autoload($name)
{
    $name = str_replace("\\", DIRECTORY_SEPARATOR, $name);
    $file = dirname(__FILE__) . DIRECTORY_SEPARATOR . $name . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
}

$VM = new \System\VM();

function sig_handler($sign)
{
    global $VM;
    $VM->tick();
    \pcntl_alarm(1);
}

pcntl_signal(SIGALRM, "sig_handler", true);
\pcntl_alarm(1);
$server = new \Http\Server();
$server->run('0.0.0.0', 8080, $VM);
