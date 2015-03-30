#!/usr/bin/perl
use strict;

my $currCommand = 0;

sub reversePolishNotation {
    my ($str) = @_;
    my $out = '';
    my %prior = (
        '*' => 3,
        '/' => 3,
        '+' => 2,
        '-' => 2,
        '<' => 1,
        '>' => 1,
    );
    my @stack = ();
    $str =~ s/ //g;
    foreach my $e (split(//, $str)){
        if($e eq ')') {
            while($stack[int(@stack)-1] ne '(') {
                $out .= pop(@stack);
            }
            pop(@stack);
        } elsif($e eq '(') {
            push(@stack, $e);
        } elsif($prior{$e}) {
            while($prior{$stack[int(@stack)-1]} >= $prior{$e}) {
                $out .= pop(@stack);
            }
            push(@stack, $e);
        } else {
            $out.=$e;
        }
    }
    while(int(@stack)) {
        $out .= pop(@stack);
    }
    return $out;
}

sub polishToAsm {
    my ($str) = @_;
    my $next = 1;
    my $oldNext = -1;
    my %use = ();
    while($str =~ s/(\S\S[*+-\/\<\>])/$next/) {
        my $e = $1;
        if(my ($f, $s, $op) = $e=~/(\S)(\S)([*+-\/\<\>])/){
            if($oldNext != $f) {
                if (($oldNext == $s) && ($op eq '*') || ($op eq '+')) {
                    my $tmp = $s;
                    $s = $f;
                    $f = $tmp;
                } else {
                    addCommand("STORE ".mapValue($oldNext)) if $oldNext!=-1;
                    addCommand("LOAD ".mapValue($f));
                }
            }
            $use{$f} = 0;
            $use{$s} = 0;
            addCommand("MUL ".mapValue($s)) if($op eq '*');
            addCommand("ADD ".mapValue($s)) if($op eq '+');
            addCommand("SUB ".mapValue($s)) if($op eq '-');
            addCommand("DIVIDE ".mapValue($s)) if($op eq '/');
            if ($op eq '>') {
                addCommand("SUB ".mapValue($s));
                addCommand("JNEG ".($currCommand+3));
                addCommand("LOAD ".mapValue('STATIC_1'));
                addCommand("JUMP ".($currCommand+2));
                addCommand("LOAD ".mapValue('STATIC_0'));
            }
            if ($op eq '<') {
                addCommand("SUB ".mapValue($s));
                addCommand("JNEG ".($currCommand+3));
                addCommand("LOAD ".mapValue('STATIC_0'));
                addCommand("JUMP ".($currCommand+2));
                addCommand("LOAD ".mapValue('STATIC_1'));
            }
        }
        $use{$next} = 1;
        $oldNext = $next;
        $next = 0;
        foreach my $i (1..9) {
             if(!$use{$i}) {
                $next = $i;
                last;
            }
        }
    }
}

sub addCommand {
    my ($str) = @_;
    print $currCommand . " " . $str . "\n";
    $currCommand++;
}

my %values = ();
my $usedValues = 0;
my %prev = ();
sub mapValue {
    my ($value)=@_;
    #return $value;
    return $values{$value} if $values{$value};
    $values{$value} = 127 - $usedValues;
    if($value=~/STATIC_(\d+)/) {
        $prev{$values{$value}} = $1;
    } else {
        $prev{$values{$value}} = 0;
    }
    $usedValues ++;
    return $values{$value};
}

#polishToAsm(reversePolishNotation('a + ( b - c ) * d + f'));

open(IN, 'input.txt');
while(my $str = <IN>) {
    my ($com, $param) = $str =~/^\d+\s(\S+)\s(.+?)\n/;
    next if($com eq 'REM');
    if($com eq 'INPUT') {
        addCommand('READ '.mapValue($param));
    } elsif($com eq 'PRINT') {
        addCommand('WRITE '.mapValue($param));
    } elsif($com eq 'LET') {
        my ($to, $exp) = $param =~/^(\S+)\s+=\s+(.+)/;
        polishToAsm(reversePolishNotation($exp));
        addCommand('STORE '.mapValue($to));
    } else {
        #print $com."\n";
    }
}
close(IN);
for(my $i = 128 - $usedValues;$i < 128 ;$i ++) {
    print $i." = ".$prev{$i}."\n";
}