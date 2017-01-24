<?php

require_once __DIR__.'/vendor/autoload.php';

use ProtectData\SystemDeffieHellman;
use ProtectData\KeyElGamal;

$b = 128;
print "Length: ".$b."\n";
$system = new SystemDeffieHellman($b);
$system->dump();
$key1 = new KeyElGamal($system->getP(), $system->getG(), $system->getBits());
$key2 = new KeyElGamal($system->getP(), $system->getG(), $system->getBits());
$mIn = gmp_mod(gmp_random(), gmp_pow(2, $b));
print "Отправленное случайное число:".gmp_strval($mIn)."\n";
$arr = $key1->crypt($mIn, $key2->getPublic());
print "Полученное число:".gmp_strval($key2->decrypt($arr[0], $arr[1]))."\n";
