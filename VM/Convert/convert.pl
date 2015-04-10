#!/usr/bin/perl
use strict;

my $currCommand = 0;
my $goAnchors;
my $DEBUG = 0;

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
    my $startBlock = 1;
    foreach my $e (split(//, $str)){
        if($e eq ')') {
            if(!$startBlock) {
                $out.='}'; $startBlock = 1;
            }
            while($stack[int(@stack)-1] ne '(') {
                $out .= pop(@stack);
            }
            pop(@stack);
        } elsif($e eq '(') {
            if(!$startBlock) {
                $out.='}'; $startBlock = 1;
            }
            push(@stack, $e);
        } elsif($prior{$e}) {
            if(!$startBlock) {
                $out.='}'; $startBlock = 1;
            }
            while($prior{$stack[int(@stack)-1]} >= $prior{$e}) {
                $out .= pop(@stack);
            }
            push(@stack, $e);
        } else {
            $out.='{' if $startBlock;            
            $out.=$e;
            $startBlock = 0;
        }
    }
    if(!$startBlock) {
        $out.='}'; $startBlock = 1;
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
    $str =~s/{(\d+)}/{STATIC_$1}/g;
    print "$str\n" if $DEBUG;
    while($str =~ s/({\S+?}{\S+?}[*+-\/\<\>])/{$next}/) {
        print "$str\n" if $DEBUG;
        my $e = $1;
        if(my ($f, $s, $op) = $e=~/{(\S+?)}{(\S+?)}([*+-\/\<\>])/){
            print "$f!$s!$op\n" if $DEBUG;
            if($oldNext != $f) {
                if (($oldNext == $s) && (($op eq '*') || ($op eq '+'))) {
                    print "$oldNext == $s\n";
                    my $tmp = $s;
                    $s = $f;
                    $f = $tmp;
                } else {
                    addCommand("STORE ".mapValue($oldNext)) if $oldNext!=-1;
                    addCommand("LOAD ".mapValue($f));
                }
            }else {
                print "$oldNext == $f\n" if $DEBUG;
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
    print "Get $value - $values{$value}\n" if $DEBUG;
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

sub parseCommand {
    my($id, $com, $param) = @_;
    $goAnchors->{$id} = $currCommand;
    if($com eq 'INPUT') {
        addCommand('READ '.mapValue($param));
    } elsif($com eq 'PRINT') {
        addCommand('WRITE '.mapValue($param));
    } elsif($com eq 'GOTO') {
        addCommand('JUMP '.$goAnchors->{$param});
    } elsif($com eq 'END') {
        addCommand('HALT 0');
    } elsif($com eq 'LET') {
        my ($to, $exp) = $param =~/^(\S+)\s+=\s+(.+)/;
        polishToAsm(reversePolishNotation($exp));
        addCommand('STORE '.mapValue($to));
    } else {
        die $com;
    }
}
sub parseIf {
    my($id, $if, $com, $param) = @_;
    polishToAsm(reversePolishNotation($if));
    addCommand("JZ ".($currCommand+2));
    parseCommand($id, $com, $param);
    
}
die 'Bad input file' unless -e $ARGV[0];
my ($e,$c,$p,$id);
open(IN, $ARGV[0]);
while(my $str = <IN>) {
    my ($com, $param);
    print "Parse command: $str" if $DEBUG;
    if($str =~/^(\d+)\s+END/) {
        parseCommand($1,'END',0)
    } elsif(($id, $e, $c, $p) =  $str =~/^(\d+)\s+IF\s+(.+)\s+(\S+)\s(\d+)/) {
        parseIf($id, $e,$c,$p);
    } else {
        ($id, $com, $param) = $str =~/^(\d+)\s(\S+)\s(.+?)\n/;
        parseCommand($id,$com,$param) if($com ne 'REM');;
    }
}
close(IN);
for(my $i = 128 - $usedValues;$i < 128 ;$i ++) {
    print $i." = ".$prev{$i}."\n";
}