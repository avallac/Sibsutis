<?php

$pub = gmp_init($argv[1]);
$P = gmp_init($argv[2]);
$G = gmp_init($argv[3]);

$s = gmp_intval(gmp_sqrt($P)) + 1;
for ($i = 1; $i < $s; $i ++) {
    $result1 = gmp_strval(gmp_mod(gmp_mul($pub, gmp_powm($G, ($i-1), $P)), $P));
    $result2 = gmp_strval(gmp_powm($G, $s * $i, $P));
    $m1[$result1] = $i - 1;
    $m2[$result2] = $i;
    if (isset($m2[$result1])) {
        $J = $i - 1;
        $I = $m2[$result1];
        $x = $I * $s - $J;
        print "$x ($I $J)\n";
        exit;
    }
    if (isset($m1[$result2])) {
        $I = $i;
        $J = $m1[$result2];
        $x = $I * $s - $J;
        print "$x ($I $J)\n";
        exit;
    }
}