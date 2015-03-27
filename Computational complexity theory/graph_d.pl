#!/usr/bin/perl

$N = 7;
$m = [[   0,   3,1000,1000,  25,  20,  10],
      [   3,   0,   6,  10,   4,1000,1000],
      [1000,   6,   0,   2,   7,1000,1000],
      [1000,  10,   2,   0,   4,1000,1000],
      [  25,   4,   7,   4,   0,   8,  16],
      [  20,1000,1000,1000,   8,   0,  4],
      [  10,1000,1000,1000,  16,   4,  0]];

$start = 0;

foreach $i (1..($N-1)){
    $D{$i}=1000;
}
$D{-1} = 1000;
$D{$start} = 0;
$S{$start} = 1;
$pos = $start;
sub printS
{
    $ret = "";
    foreach $i (0..($N-1)){
        $ret.=($i+1)." " if $S{$i};
    }
    $ret = $ret." "x(15 - length($ret));
    return $ret;
}
foreach(0..($N-1)){
    print printS()."|";
    print ($pos+1);
    print "|";
    print " " if $D{$pos}<10;
    print $D{$pos}. "|";
    foreach $i (0..($N-1)){
        next if $S{$i};
        if(($D{$pos} + $m->[$pos][$i]) < $D{$i} ){
            $D{$i} = $D{$pos} + $m->[$pos][$i];
        }
    }
    foreach $i (0..($N-1)){
        print " " if $D{$i}<10;
        print " " if $D{$i}<100;
        print " " if $D{$i}<1000;
        print "$D{$i}|";
    }
    print "\n";
    $minV=-1;
    foreach $i (0..($N-1)){
        next if $S{$i};
        if($D{$minV} > $D{$i}){
            $minV = $i;
        }
    }
    $pos = $minV;
    last if $minV==-1;
    $S{$pos}=1;
}