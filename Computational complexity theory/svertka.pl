#!/usr/bin/perl

@a = (1,2);
@b = (3,4,5);

$lenA = int(@a);
$lenB = int(@b);

$lenC = $lenA + $lenB - 1;
for($i = 0; $i < $lenC; $i++) {
    for($j = 0; $j <= $i; $j++) {
        $c[$i] += $a[$j]*$b[$i - $j];
    }
}
for($i = 0; $i < $lenC; $i++) {
    print $c[$i]." ";
}
print "\n";