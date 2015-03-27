#!/usr/bin/perl

use Math::Complex;

sub insert_to_main_queue{
    my ($val)=@_;
    my $new_rec;
    $new_rec->{'val'}=$val;
    $new_rec->{'next'}=0;
    unless($Q{-1}->{'head'}){
        $Q{-1}->{'head'}=$new_rec;
        $Q{-1}->{'tail'}=$new_rec;
    }else{
        $Q{-1}->{'tail'}->{'next'}=$new_rec;
        $Q{-1}->{'tail'}=$new_rec;
    }
}

sub ser{
    my ($head)=@_;
    $tmp = $head;
    $summ=1;
    do{
        if($tmp->{'val'}>$tmp->{'next'}->{'val'}){
            $summ++;
        }
        $tmp = $tmp->{'next'};
    }while($tmp->{'next'});
    return $summ;
}

sub move{
    my ($rec,$i)=@_;
    $rec->{'next'} = 0;
    unless($Q{$i}->{'head'}){
        $Q{$i}->{'head'}=$rec;
        $Q{$i}->{'tail'}=$rec;
    }else{
        $Q{$i}->{'tail'}->{'next'}=$rec;
        $Q{$i}->{'tail'}=$rec;
    }
}


foreach $N (10,50,100,200,500,1000,10000){
    $Q{-1}->{'head'} = 0;
    $Q{0}->{'head'} = 0;
    $Q{1}->{'head'} = 0;
    $Q{2}->{'head'} = 0;
    $Q{3}->{'head'} = 0;
    $M = 0 ;
    $C = 0;
    for($i=0;$i<$N;$i++){
        insert_to_main_queue(int(rand(10000)));
    }
    $q = 0;
    while($e = $Q{-1}->{'head'}){
        $tmp = $Q{-1}->{'head'}->{'next'};
        move($Q{-1}->{'head'},$q);
        $M++;
        $Q{-1}->{'head'} = $tmp;
        $q = 1 - $q;
    }
    $p = 1;
    $q = 0;
    while($p<$N){
        while(($Q{0}->{'head'}) || ($Q{1}->{'head'})){
            $a = 0;
            $b = 0;
            while((($a + $b) < $p*2) && (($Q{0}->{'head'}) || ($Q{1}->{'head'}))){
                while(($a < $p) && ($b < $p) && ($Q{0}->{'head'}) && ($Q{1}->{'head'})){
                    $C++;
                    if($Q{0}->{'head'}->{'val'} < $Q{1}->{'head'}->{'val'}){
                        $from = 0;
                        $a++;
                    }else{
                        $from = 1;
                        $b++;
                    }
                    $tmp = $Q{$from}->{'head'}->{'next'};
                    move($Q{$from}->{'head'},2+$q);
                    $M++;
                    $Q{$from}->{'head'} = $tmp;
                }
                while(($a < $p) && ($Q{0}->{'head'})){
                    $tmp = $Q{0}->{'head'}->{'next'};
                    move($Q{0}->{'head'},2+$q);
                    $M++;
                    $Q{0}->{'head'} = $tmp;
                    $a++;
                }
                while(($b < $p) && ($Q{1}->{'head'})){
                    $tmp = $Q{1}->{'head'}->{'next'};
                    move($Q{1}->{'head'},2+$q);
                    $M++;
                    $Q{1}->{'head'} = $tmp;
                    $b++;
                }
            }
            $q = 1 - $q;
        }
        $Q{0}->{'head'} = $Q{2}->{'head'};
        $Q{1}->{'head'} = $Q{3}->{'head'};
        $Q{2}->{'head'} = 0;
        $Q{3}->{'head'} = 0;
        $p*=2;
    }
    $nlogn = $N * logn($N, 2);
    print "Тест для $N [nlog(n) = $nlogn] элементов. Сравнений: $C, пересылок: $M\n";
}