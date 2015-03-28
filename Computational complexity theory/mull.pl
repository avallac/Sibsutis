#!/usr/bin/perl

@a = (9,1);
@b = (9,5,1);

$lenA = int(@a);
$lenB = int(@b);

$lenC = $lenA + $lenB;
for($i = 0; $i < $lenC; $i++) {
    for($j = 0; $j <= $i; $j++) {
        $c[$i] += $a[$j]*$b[$i - $j];
    }
}
for($i = 0; $i < $lenC; $i++) {
    print int($c[$i]/10);
    $c[$i+1] += $c[$i] % 10 * 10;
}
print "\n";