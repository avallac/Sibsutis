<?php

class DeterministicPushdownAutomaton extends Automaton
{
    protected $pattern = '/\(([^,]+),(.),(.)\)=\{\(([^,]+),(.+)()\)\}()/';
    public function setRules($rules)
    {
        $this->rule = array();
        foreach (explode("\n", $rules) as $rule) {
            if (!$this->addRule(trim($rule))) {
                return false;
            }
        }
        return true;
    }

    public function checkRule($m)
    {

        if (!$this->checkState($m[1]) ||
        !$this->checkAbc($m[2]) ||
        !$this->checkAbc($m[3], 'abcStack') ||
        !$this->checkState($m[4])) {
            return true;
        }

        foreach (str_split($m[5]) as $e) {
            if (!$this->checkAbc($e, 'abcStack')) {
                return true;
            }
        }

        return false;
    }

    public function addRule($rule)
    {

        if (preg_match($this->pattern, $rule, $m)) {
            if ($this->checkRule($m)) {
                $count = count($this->rule) + 1;
                $this->error($this->getError() . " В правиле номер " . $count . " - " . $rule);
                return false;
            }
            $this->rule[$m[1]][$m[2]][$m[3]] = array($m[4], array_reverse(str_split($m[5])), 0, $m[7]);
        } else {
            $this->error("Правило '$rule' не опознано.");
            return false;
        }
        return true;
    }

    public function mapEmpty($str)
    {
        if (strlen($str)) {
            return $str;
        } else {
            return '#';
        }
    }

    public function printOut($state, $input, $stack, $out)
    {
        return "(" . $state . ", " . $input . ", " . $stack .")";
    }

    public function check($str)
    {
        $cur = $this->begin;
        $states = [$this->printOut($cur, $str, $this->getStack(), '')];
        $str = str_split($str);
        $oldState = [0, '', ''];
        $out = '';
        while (!empty($str) || !empty($this->stack)) {
            if (
                $oldState[0] === $cur &&
                $oldState[1] === implode($str) &&
                $oldState[2] === $this->getStack()
            ) {
                return array("Обнаружен цикл.", $states);
            } else {
                $oldState = [$cur, implode($str), $this->getStack()];
            }
            $a = array_shift($str);
            $b = array_shift($this->stack);
            if (!isset($this->rule[$cur][$a][$b])) {
                $realA = $a;
                if ($a !== null) {
                    array_unshift($str, $a);
                }
                $a = '#';
            }
            if (!isset($this->rule[$cur][$a][$b])) {
                $b = $this->mapEmpty($b);
                return array(
                    "Правил перехода из состояние '$cur' (строка: '$realA', стэк: '$b') не обнаружено.",
                    $states
                );
            } else {
                if ($this->rule[$cur][$a][$b][1][0] !== '#') {
                    foreach ($this->rule[$cur][$a][$b][1] as $e) {
                        array_unshift($this->stack, $e);
                    }
                }
                if ($this->rule[$cur][$a][$b][3] !== '#') {
                    $out .= $this->rule[$cur][$a][$b][3];
                }
                $cur = $this->rule[$cur][$a][$b][0];
                $states[] = $this->printOut($cur, $this->mapEmpty(implode($str)), $this->getStack(), $out);
            }
        }
        if (!isset($this->end[$cur])) {
            return array("Конечное состояние достигнуто не было", $states);
        }
        return array("Строчка принята.", $states);
    }

}