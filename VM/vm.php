<?php

require_once('Http/Server.php');
require_once('Http/Template.php');
require_once('Http/MainController.php');
require_once('System/Memory.php');
require_once('System/VM.php');

$VM = new \System\VM();

$server = new \Http\Server();
$server->run('10.0.0.106', 8080, $VM);
