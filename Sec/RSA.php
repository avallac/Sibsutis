<?php

require_once __DIR__.'/vendor/autoload.php';

use ProtectData\RSA;


$n = 16;
print "Система: ".$n." бит\n";
$rsa = new RSA($n);
print "N:".$rsa->getN()."\n";
print "C:".$rsa->getC()."\n";
print "D:".$rsa->getD()."\n";
$mIn = gmp_mod(gmp_random(), gmp_pow(2, $n));
print "Отправленное случайное число:".gmp_strval($mIn)."\n";
$mOut = $rsa->decode($rsa->code($mIn));
print "Полученное число:".gmp_strval($mOut)."\n";
