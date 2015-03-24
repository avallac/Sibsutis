#!/usr/bin/perl

foreach $N (10,50,100,200,500,1000){
    for ($i = 0; $i < $N; $i ++) {
        $arr[$i] = int(rand(1000));
    }
    $C = 0;
    $M = 0;
    for($i=0;$i<($N-1);$i++){
        $lastChange = $N-1;
        for($j=$N-1;$j>$i;$j--){
            $C++;
            if($arr[$j] < $arr[$j-1]){
                $M+=3;
                $tmp = $arr[$j];
                $arr[$j] = $arr[$j-1];
                $arr[$j-1] = $tmp;
                $lastChange = $j-1;
            }
        }
        $i = $lastChange;
    }
#    exit;
    $n2 = $N * $N;
    print "Тест для $N [n*n = $n2 n = $N] элементов. Сравнений: $C, пересылок: $M\n";
}
