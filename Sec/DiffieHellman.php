<?php

require_once __DIR__.'/vendor/autoload.php';

use ProtectData\System;
use ProtectData\Key;

$b = 128;
print "Length: ".$b."\n";
$system = new System($b);
$system->dump();
$key1 = new Key($system->getP(), $system->getG(), $system->getBits());
$key2 = new Key($system->getP(), $system->getG(), $system->getBits());
print "Private 1:".gmp_strval($key1->getPrivate())."\n";
print "Public 1 :".gmp_strval($key1->getPublic())."\n";
print "Private 2:".gmp_strval($key2->getPrivate())."\n";
print "Public 2 :".gmp_strval($key2->getPublic())."\n";
print "z1: ".gmp_strval($key1->genShare($key2->getPublic()))."\n";
print "z2: ".gmp_strval($key2->genShare($key1->getPublic()))."\n";
