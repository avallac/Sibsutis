#!/usr/bin/perl

sub printM
{
    my ($p1,$p2)=@_;
    if ($p1 == $p2) {
        return "M".$p1
    }
    my $s1 = $return[$p1][$p2]->[0];
    my $e1 = $return[$p1][$p2]->[1];
    my $s2 = $return[$p1][$p2]->[2];
    my $e2 = $return[$p1][$p2]->[3];
    return "(".printM($s1,$e1)."*".printM($s2,$e2).")";
}

@input = ( 10, 20, 5, 4, 30, 6);
$n = int(@input) - 1;

for ($i = 1; $i <= $n; $i++) {
    $dp[$i][$i] = 0;
}
 
for ($l = 2; $l <= $n; $l++) {
    for ($i = 1; $i <= $n - l + 1; $i++) {
        $j = $i + $l - 1;
        $dp[$i][$j] = 100000000;
        for ($k = $i; $k <= $j - 1; $k++) {
            $val = $dp[$i][$k] + $dp[$k + 1][$j] + $input[$i - 1] * $input[$k] * $input[$j];
            if($dp[$i][$j] > $val) {
                $dp[$i][$j] = $val;
                $return[$i][$j] = [$i,$k,$k+1,$j];
            }
        }
    }
}
print "Result:".$dp[1][$n]."\n";
print printM(1,$n)."\n";
