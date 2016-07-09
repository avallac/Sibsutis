#!/usr/bin/perl

use POSIX;

sub dMask {
    my ($mask) = @_;
    $out = 0;
    foreach $c (1..32) {
        $out = $out << 1;
        if ($c <= $mask) {
            $out++;
        }
    }
    $p1 = int($out/pow(2,24));
    $p2 = int(($out % pow(2,24) )/ pow(2,16));
    $p3 = int(($out % pow(2,16) )/ pow(2,8));
    $p4 = int($out % pow(2,8));
    return $p1.".".$p2.".".$p3.".".$p4;
}

sub bin8 {
    my ($in) = @_;
    $out = sprintf("%b", $in);
    while (length($out) < 8) {
        $out = '0' . $out;
    }
    return $out;
}

sub binString {
    my ($val) = @_;
    $val =~/(\d+)\.(\d+)\.(\d+)\.(\d+)/;
    return bin8($1).".".bin8($2).".".bin8($3).".".bin8($4);
}

$network = '136.125.0.0';
$mask = 16;
$subnet = 119 + 10;

$bitsForNet = ceil(log($subnet)/log(2));

print "Сеть          " . binString($network)." [".$network."]\n";
print "Маска         " . binString(dMask($mask))." [".dMask($mask)." - ".$mask."]\n";
print "Битов на все подсети:".$bitsForNet."\n";
print "Маска подсети " . binString(dMask($mask + $bitsForNet))." [".dMask($mask + $bitsForNet)." - ".($mask + $bitsForNet )."]\n";
print "Хостов в подсети: " . (pow(2, (32 - ($mask + $bitsForNet))) - 2) . "\n";
