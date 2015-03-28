#!/usr/bin/perl

use strict;
use Data::Dumper;
my ($i,@com,$l,$key,$j,$tmp,%V,$oldCom,$newCom);
my $N = 7;
#         1    2   3     4    5    6    7
my $m = [[   0,  20,1000,1000,1000,   1,   1],
      [  20,   0,   6,1000,1000,1000,   4],
      [1000,   6,   0,   3,1000,1000,   9],
      [1000,1000,   3,   0,  17,1000,  16],
      [1000,1000,1000,  17,   0,  20,  25],
      [   1,1000,1000,1000,  20,   0,  36],
      [   1,   4,   9,  16,  25,  36,   0]];

for ($i = 0; $i < $N; $i++) {
    for ($j = $i; $j < $N; $j++) {
        $tmp = {'p1'=>$i, 'p2'=>$j};
        push(@{$V{$m->[$i]->[$j]}}, $tmp);
#        print "$i $j ".$m->[$i]->[$j]."\n";
    }
}
for ($i = 0; $i < $N; $i++) {
    $com[$i] = 0;
}
my $nextCom = 1;
my @keys = sort { $a <=> $b } keys %V;
foreach $key (@keys) {
    next if $key == 0 || $key == 1000;
    foreach $l (@{$V{$key}}){
        if($com[$l->{'p1'}] == 0 && $com[$l->{'p2'}] == 0) {
            $com[$l->{'p1'}] = $nextCom;
            $com[$l->{'p2'}] = $nextCom;
            $nextCom ++;
        }elsif($com[$l->{'p1'}] == $com[$l->{'p2'}]) {
            next;
        }elsif ($com[$l->{'p1'}] !=0 && $com[$l->{'p2'}]!=0) {
            if($com[$l->{'p1'}] > $com[$l->{'p2'}]) {
                $newCom = $com[$l->{'p2'}];
                $oldCom = $com[$l->{'p1'}];
            } else {
                $newCom = $com[$l->{'p1'}];
                $oldCom = $com[$l->{'p2'}];
            }
            for ($i = 0; $i < $N; $i++) {
                $com[$i] = $newCom if($com[$i] == $oldCom);
            }
        }else {
            if ($com[$l->{'p1'}]!=0) {
                $com[$l->{'p2'}] = $com[$l->{'p1'}];
            } else {
                $com[$l->{'p1'}] = $com[$l->{'p2'}];
            }
        }
        print "Add $key - ";
        print " from: ".$l->{'p1'}." ";
        print " to: ".$l->{'p2'}."\n";
    }
}