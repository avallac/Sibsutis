#!/usr/bin/perl

@m = (3,7,8);
@c = (8,19,22);
$len = int(@m)-1;

foreach $j (0..$len){
    print "Item$j m:".int($m[$j])." c:".int($c[$j])."\n";
}

$f[0] = 0;
foreach $i (1..100){
    $mx = 0;
    foreach $j (0..$len){
        if(($i-$m[$j])>=0){
            $mx = $f[$i-$m[$j]]+$c[$j] if ($f[$i-$m[$j]]+$c[$j]) > $mx;
        }
    }
    $f[$i] = $mx;
    $i = "0$i" if $i<10;
    $mx = "0$mx" if $mx<10;
    print "$i $mx - ";
    my @basket=();
    print "inventory: ";
    while($i>=0 && $f[$i]){
        foreach $j (0..$len){
            if($f[$i-$m[$j]] + $c[$j] == $f[$i]){
                $i-= $m[$j];
                $basket[$j]++;
            }
        }
    }
    foreach $j (0..$len){
        print "Item$j:".int($basket[$j])." ";
    }
    print "\n";
}
