#!/usr/bin/perl


foreach $N (10,50,100,200,500,1000){
    for ($i = 0; $i < $N; $i ++) {
        $arr[$i] = int(rand(1000));
    }
    $C = 0;
    $M = 0;
    for ($i=0;$i<($N-1);$i++) {
        $min=$i;
        for($j=$i+1;$j<$N;$j++){
            $C++;
            $min=$j if($arr[$min] > $arr[$j]);
        }
        $M+=3;
        $tmp=$arr[$min];
        $arr[$min]=$arr[$i];
        $arr[$i]=$tmp;

    }
    $n2 = $N * $N;
    print "Тест для $N [n*n = $n2] элементов. Сравнений: $C, пересылок: $M\n";
}