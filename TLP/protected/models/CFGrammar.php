<?php

class CFGrammar
{
    const TYPE_T = 1;
    const TYPE_NT = 2;
    const TYPE_EMPTY = 3;

    protected $target = '';
    protected $V = array();
    protected $rules = array();
    protected $error = '';
    protected $allowEqualLR = 1;

    public function getError()
    {
        return $this->error;
    }

    public function __construct()
    {
        $this->add("#", self::TYPE_EMPTY, 1);
    }

    public static function parseInput($str)
    {
        return explode(',', str_replace(" ", "", $str));
    }

    protected function addElement($e, $type, $empty = 0)
    {
        $this->V[$e] = array();
        $this->V[$e]['type'] = $type;
        $this->V[$e]['empty'] = $empty;
        $this->V[$e]['used'] = 0;
        $this->V[$e]['head'] = 0;
    }

    public function add($VT, $type, $empty = 0)
    {
        $VT = self::parseInput($VT);
        foreach ($VT as $e) {
            if (strlen($e) > 1 && $e!=='S`') {
                $this->error("Элемент '$e' длинее одного символа.");
                return false;
            }
            if (!isset($this->V[$e])) {
                $this->addElement($e, $type, $empty);
                if ($type == self::TYPE_T || $type == self::TYPE_EMPTY) {
                    $this->V[$e]['used'] = 1;
                }
            } else {
                $this->error("Элемент '$e' повторяется.");
                return false;
            }
        }
        return true;
    }

    public function setTarget($target)
    {
        if ($this->checkNT($target)) {
            $this->target = $target;
            $this->V[$target]['head'] = 1;
            return true;
        }
        return false;
    }

    public function checkExists($e)
    {
        if (isset($this->V[$e])) {
            return true;
        } else {
            $this->error("Элемент '$e' не найден.");
        }
        return false;
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
        $this->rules = [];
        foreach (explode("\n", $rules) as $rule) {
            if (!$this->addRule(trim($rule))) {
                return false;
            }
        }
        return true;
    }

    public function optimize()
    {
        $this->removeE();
        $this->removeOrphan();
        $this->removeUnavailable();
    }

    public function checkExistRule($m)
    {
        foreach ($this->rules as $rule) {
            if ($rule['l'] === $m[1] && $rule['r'] === $m[2]) {
                return true;
            }
        }
        return false;
    }

    protected function validateRightPart($e)
    {
        foreach (str_split($e) as $eV) {
            if (!$this->checkExists($eV)) {
                $this->error("Неизвестный элемент '$eV'.");
                return false;
            }
        }
        return true;
    }

    public function addRule($rule)
    {
        if (preg_match('/^(\S)->(.+)$/', $rule, $m)) {
            if ($m[1] === $m[2]) {
                if ($this->allowEqualLR) {
                    return true;
                } else {
                    $this->error("Правило '$rule' некоректно.");
                    return false;
                }
            }
            if ($this->checkExistRule($m)) {
                return true;
            }
            if ($this->checkNT($m[1])) {
                foreach (explode('|', $m[2]) as $e) {
                    if ($e === '#') {
                        $this->V[$m[1]]['empty'] = 1;
                    } else {
                        $e = str_replace('#', '', $e);
                    }
                    if ($this->validateRightPart($e, $m)) {
                        $this->rules [] = array('l' => $m[1], 'r' => $e);
                    } else {
                        return false;
                    }
                }
                return true;
            } else {
                $this->error("Элемент '$m[1]' не нетерминал.");
                return false;
            }
        } else {
            $this->error("Правило '$rule' не опознано.");
            return false;
        }
    }

    protected function makeEmptyAlt()
    {
    }

    protected function replaceNT($rule, $t)
    {
        $count = substr_count($rule['r'], $t);
        if ($count) {
            $exp = explode($t, $rule['r']);
            for ($i = 0; $i < (pow(2, $count) - 1); $i++) {
                $newRule = '';
                $j = 0;
                foreach ($exp as $k => $e) {
                    $newRule .= $e;
                    if ($i & pow(2, $j)) {
                        $newRule .= $t;
                    }
                    $j++;
                }
                if ($newRule !== '') {
                    $this->addRule($rule['l'] . "->" . $newRule);
                }
            }
        }
    }

    public function removeE()
    {
        $this->rCheck('empty');
        $this->makeEmptyAlt();
        foreach ($this->V as $t => $v) {
            if (($v['type'] == self::TYPE_NT) && ($v['empty'] == 1)) {
                foreach ($this->rules as $rule) {
                    $this->replaceNT($rule, $t);
                }
            }
        }
        $tmp = [];
        foreach ($this->rules as $rule) {
            if ($rule['r'] !=='#') {
                $tmp[] = $rule;
            }
        }
        $this->rules = $tmp;
        if ($this->V[$this->target]['empty']) {
            $this->add("S`", self::TYPE_NT);
            $this->rules [] = array('l' => 'S`', 'r' => '#');
            $this->rules [] = array('l' => 'S`', 'r' => $this->target);
            $this->setTarget('S`');
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
        if ($this->target === '') {
            return false;
        }
        $return = array();
        $exists = array($this->target);
        $queue = array(array($this->target, array(-1)));
        for ($i = 0; isset($queue[$i]); $i++) {
            if ($queue[$i][0] === 'S`') {
                $str = array('S`');
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

    public function translate($queue, $i)
    {
        $ret = array();
        $next = $queue[$i];
        while ($next[1] != -1) {
            $ret [] = $next[0];
            $next = $queue[$next[1]];
        }
        $ret [] = $queue[0][0];
        return join("=>", $ret);
    }

    public function searchFirstNT($str)
    {
        $arr = str_split($str);
        for ($j = 0; $j < sizeof($arr); $j ++) {
            if ($this->V[$arr[$j]]['type'] == self::TYPE_NT) {
                return $j;
            }
        }
        return sizeof($arr);
    }

    public function checkInput($str)
    {
        $arr = str_split($str);
        for ($j = 0; $j < sizeof($arr); $j ++) {
            if (!isset($this->V[$arr[$j]])) {
                return false;
            }
        }
        return true;
    }

    public function parse($str)
    {
        $exists = array();
        $queue = [[$str, -1]];
        if ($this->checkInput($str)) {
            for ($i = 0; isset($queue[$i]); $i++) {
                foreach ($this->rules as $rule) {
                    if (($queue[$i][0] === $this->target) && ($this->target !== 'S`') ||
                        (($rule['l'] === 'S`') && $rule['r'] === $queue[$i][0])
                    ) {
                        return $this->translate($queue, $i);
                    }
                    if ($rule['l'] === 'S`') {
                        continue;
                    }
                    if (($pos = strrpos($queue[$i][0], $rule['r'])) !== false) {
                        if ($pos > $this->searchFirstNT($queue[$i][0])) {
                            continue;
                        }
                        $tmp = substr_replace($queue[$i][0], $rule['l'], $pos, strlen($rule['r']));
                        if (!isset($exists[$tmp])) {
                            $queue[] = [$tmp, $i, $rule, $pos];
                            $exists[$tmp] = 1;
                        }
                    }
                }
            }
        }
        return 'Последовательность не распознана.';
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

    public function getNonTerm($all = 0)
    {
        $ret = array();
        foreach ($this->V as $t => $v) {
            if ($v['type'] == self::TYPE_NT && (($v['used'] == 1) || $all)) {
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

    protected function error($str)
    {
        $this->error = $str;
    }
}