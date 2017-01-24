<?php

require_once __DIR__.'/vendor/autoload.php';

use ProtectData\KeyShamir;
use ProtectData\SystemShamir;

$n = 32;
$system = new SystemShamir($n);
print "Система: ".$n." бит (P = ".$system->getP().")\n";
print "Ключ 1:\n";
$key1 = new KeyShamir($system->getP(), $system->getBits());
$key1->dump();
print "Ключ 2:\n";
$key2 = new KeyShamir($system->getP(), $system->getBits());
$key2->dump();
$mIn = rand(0, pow(2, $n));
print "Отправленное случайное число:".$mIn."\n";
$mOut = $key2->step2($key1->step2($key2->step1($key1->step1($mIn))));
print "Полученное число:".$mOut."\n";

