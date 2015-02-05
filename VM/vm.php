<?php

require_once('Http/Server.php');
require_once('Http/Template.php');
require_once('Http/MainController.php');
require_once('System/Memory.php');
require_once('System/VM.php');

$VM = new \System\VM();

$server = new \Http\Server();
$server->run('0.0.0.0', 8080, $VM);
