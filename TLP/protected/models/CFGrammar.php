<?php

class CFGrammar
{
    const TYPE_T = 1;
    const TYPE_NT = 2;
    const TYPE_EMPTY = 3;

    private $target = '';
    private $V = array();
    private $rules = array();
    private $error = '';

    public function getError()
    {
        return $this->error;
    }

    public function __construct()
    {
        $this->add("#", self::TYPE_EMPTY, 1);
    }

    public function parseInput($str)
    {
        return explode(',', str_replace(" ", "", $str));
    }

    public function add($VT, $type, $empty = 0)
    {
        $VT = $this->parseInput($VT);
        foreach ($VT as $e) {
            if (strlen($e) > 1 && $e!=='S1') {
                $this->error("Элемент '$e' длинее одного символа.");
            }
            if (!isset($this->V[$e])) {
                $this->V[$e] = array();
                $this->V[$e]['type'] = $type;
                $this->V[$e]['empty'] = $empty;
                $this->V[$e]['used'] = 0;
                $this->V[$e]['head'] = 0;
                if ($type == self::TYPE_T || $type == self::TYPE_EMPTY) {
                    $this->V[$e]['used'] = 1;
                }
            } else {
                $this->error("Элемент '$e' повторяется.");
            }
        }
    }

    public function setTarget($target)
    {
        if ($this->checkNT($target)) {
            $this->target = $target;
            $this->V[$target]['head'] = 1;
        }
    }

    public function checkExists($e)
    {
        if (isset($this->V[$e])) {
            if ($this->V[$e]['type'] == self::TYPE_EMPTY) {
                $this->error("Беда, пролезла пустота.");
            }
            return 1;
        } else {
            $this->error("Элемент '$e' не найден.");
        }
    }

    public function checkNT($nt)
    {
        if ($this->checkExists($nt)) {
            if ($this->V[$nt]['type'] == self::TYPE_NT) {
                return 1;
            } else {
                $this->error("Терминал '$nt' не может быть в левой части.");
            }
        }
    }

    public function addRules($rules)
    {
        foreach (explode("\n", $rules) as $rule) {
            $this->addRule(trim($rule));
        }
    }

    public function addRule($rule)
    {
        if (preg_match('/^(\S)->(.+)$/', $rule, $m)) {
            if ($m[1] === $m[2]) {
                return;
            }
            foreach ($this->rules as $rule) {
                if ($rule['l'] === $m[1] && $rule['r'] === $m[2]) {
                    return;
                }
            }
            $this->checkNT($m[1]);
            foreach (explode('|', $m[2]) as $e) {
                if ($e === '#') {
                    $this->V[$m[1]]['empty'] = 1;
                } else {
                    $e = str_replace('#', '', $e);
                    $this->rules [] = array('l' => $m[1], 'r' => $e);
                    foreach (str_split($e) as $eV) {
                        $this->checkExists($eV);
                    }
                }
            }
        } else {
            $this->error("Правило '$rule' не опознано.");
        }
    }

    public function removeE()
    {
        $this->rCheck('empty');
        foreach ($this->V as $t => $v) {
            if (($v['type'] == self::TYPE_NT) && ($v['empty'] == 1)) {
                foreach ($this->rules as $rule) {
                    $count = substr_count($rule['r'], $t);
                    $str = str_split($rule['r']);
                    if ($count) {
                        for ($i = 0; $i < (pow(2, $count) - 1); $i++) {
                            $j = 0;
                            $newRule = '';
                            foreach ($str as $a) {
                                if ($a == $t) {
                                    if ($i & pow(2, $j)) {
                                        $newRule .= $a;
                                    }
                                    $j++;
                                } else {
                                    $newRule .= $a;
                                }
                            }
                            if ($newRule !== '') {
                                $this->addRule($rule['l'] . "->" . $newRule);
                            }
                        }
                    }

                }
            }
        }
        if ($this->V[$this->target]['empty']) {
            $this->add("S1", self::TYPE_NT);
            $this->rules [] = array('l' => 'S1', 'r' => '#');
            $this->rules [] = array('l' => 'S1', 'r' => $this->target);
            $this->setTarget('S1');
        }
    }

    public function export($len)
    {
        return array(
            'strings' => $this->generate($len),
            'rules' => $this->getRules(),
            'term' => $this->getTerm(),
            'nonterm' => $this->getNonTerm(),
            'target' => $this->target,
        );
    }

    private function returnPrev($i, $queue, $exist)
    {
        $ret = array();
        if ($i < 0) {
            return array(array());
        }
        if (in_array($queue[$i][0], $exist)) {
            return array();
        }
        $new = array_merge($exist, array($queue[$i][0]));
        foreach ($queue[$i][1] as $old) {
            $ret = array_merge($this->returnPrev($old, $queue, $new), $ret);
        }
        foreach ($ret as $k => $v) {
            $ret[$k][]= $queue[$i][0];
        }
        return $ret;
    }

    public function generate($len)
    {
        $return = array();
        $exists = array($this->target);
        $queue = array(array($this->target, array(-1)));
        for ($i = 0; isset($queue[$i]); $i++) {
            if ($queue[$i][0] === 'S1') {
                $str = array('S1');
            } else {
                $str = str_split($queue[$i][0]);
            }
            for ($j = 0; isset($str[$j]); $j++) {
                if ($this->V[$str[$j]]['type'] == self::TYPE_NT) {
                    $queue[$i][2] = 0;
                    $rep = $str[$j];
                    foreach ($this->rules as $rule) {
                        if ($rule['l'] == $rep) {
                            $str[$j] = $rule['r'];
                            if (strlen(implode($str)) <= $len) {
                                if (!in_array(implode($str), $exists)) {
                                    $queue[] = array(implode($str), array($i), 1);
                                    $exists[] = implode($str);
                                } else {
                                    foreach ($queue as $k => $v) {
                                        if ($v[0] === implode($str)) {
                                            $queue[$k][1][] = $i;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    break;
                }
            }
        }
        for ($i = 0; isset($queue[$i]); $i++) {
            if ($queue[$i][2]) {
                $ret = $this->returnPrev($i, $queue, array());
                $arr = array();
                foreach ($ret as $e) {
                     $arr[]= implode(" => ", $e);
                }
                $return[] = $arr;
            }
        }
        return $return;
    }

    public function removeOrphan()
    {
        $this->rCheck('used');
        $this->clean('used');
    }

    public function removeUnavailable()
    {
        $this->lCheck('head');
        $this->clean('head');
    }

    public function getTerm()
    {
        $ret = array();
        foreach ($this->V as $t => $v) {
            if ($v['type'] == self::TYPE_T && $v['head'] == 1) {
                $ret []= $t;
            }
        }
        return implode(", ", $ret);
    }

    public function getNonTerm()
    {
        $ret = array();
        foreach ($this->V as $t => $v) {
            if ($v['type'] == self::TYPE_NT && $v['used'] == 1) {
                $ret []= $t;
            }
        }
        return implode(", ", $ret);
    }

    public function getRules()
    {
        $ret = array();
        $out = array();
        foreach ($this->rules as $rule) {
            if (isset($out[$rule['l']])) {
                $out[$rule['l']] .= '|' . $rule['r'];
            } else {
                $out[$rule['l']] = $rule['l'] . '=>' . $rule['r'];
            }
        }
        foreach ($out as $rule) {
            $ret [] = $rule;
        }
        return $ret;
    }

    private function rCheck($param)
    {
        $mod = 1;
        while ($mod) {
            $mod = 0;
            foreach ($this->rules as $rule) {
                $change = 1;
                foreach (str_split($rule['r']) as $e) {
                    if (!$this->V[$e][$param]) {
                        $change = 0;
                    }
                }
                if ($change && !$this->V[$rule['l']][$param]) {
                    $this->V[$rule['l']][$param] = 1;
                    $mod = 1;
                }
            }
        }
    }

    private function lCheck($param)
    {
        $mod = 1;
        while ($mod) {
            $mod = 0;
            foreach ($this->rules as $rule) {
                if ($this->V[$rule['l']][$param]) {
                    foreach (str_split($rule['r']) as $e) {
                        if (!$this->V[$e][$param]) {
                            $this->V[$e][$param] = 1;
                            $mod = 1;
                        }
                    }
                }
            }
        }
    }

    private function clean($param)
    {
        foreach ($this->V as $t => $v) {
            if (!$v[$param]) {
                $tmp = array();
                foreach ($this->rules as $rule) {
                    if (!substr_count($rule['r'], $t) && !substr_count($rule['l'], $t)) {
                        $tmp[] = $rule;
                    }
                }
                $this->rules = $tmp;
            }
        }
    }

    private function error($str)
    {
        $this->error = $str;
    }
}