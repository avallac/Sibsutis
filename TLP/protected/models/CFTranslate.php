<?php

class CFTranslate extends CFGrammar
{
    protected $trAbc = array();
    protected $altRule = array();

    public function setTrAbc($abc)
    {
        $this->trAbc = array('#' => 1);
        $lang = CFGrammar::parseInput($abc);
        foreach ($lang as $e) {
            if (strlen($e) > 1) {
                $this->error("Буква '$e' слишком длинная.");
                return false;
            }
            if (isset($this->V[$e])) {
                if ($this->V[$e]['type'] == self::TYPE_NT) {
                    $this->error("Элемент '$e' уже определен как нетерминал.");
                    return false;
                }
            }
            if (!isset($this->trAbc[$e])) {
                $this->trAbc[$e] = 1;
            } else {
                $this->error("Элемент '$e' повторяется.");
                return false;
            }
        }
        return true;
    }

    public function getNTList($r)
    {
        $ret = array();
        foreach (str_split($r) as $e) {
            if ($this->checkExists($e)) {
                if ($this->V[$e]['type'] == self::TYPE_NT) {
                    $ret[]= $e;
                }
            }
        }
        sort($ret);
        return $ret;
    }

    public function checkEqNT($r1, $r2)
    {
        if ($this->getNTList($r1) === $this->getNTList($r2)) {
            return true;
        } else {
            return false;
        }
    }

    protected function makeEmptyAlt()
    {
        $this->altRule = [];
        $change = 1;
        while ($change) {
            $change = 0;
            foreach ($this->rules as $rule) {
                if (!isset($this->altRule[$rule['l']])) {
                    if ($rule['r'] == '#') {
                        $this->altRule[$rule['l']] = $rule['tr'];
                        $change = 1;
                        break;
                    }
                    $good = 1;
                    $str = '';
                    foreach (str_split($rule['r']) as $e) {
                        if (($this->V[$e]['type'] == self::TYPE_NT) && isset($this->altRule[$e])) {
                            $str .= $this->altRule[$e];
                        } else {
                            $good = 0;
                            break;
                        }
                    }
                    if ($good) {
                        $this->altRule[$rule['l']] = $str;
                        $change = 1;
                    }
                }
            }
        }
    }

    public function addRule($rule)
    {
        if (preg_match('/^(\S)->(.+),(.+)$/', $rule, $m)) {
            if ($m[1] === $m[2]) {
                return true;
            }
            foreach ($this->rules as $rule) {
                if ($rule['l'] === $m[1] && $rule['r'] === $m[2]) {
                    $this->error("Правило дублируется '".$m[1]."->".$m[2]."'.");
                    return false;
                }
            }
            foreach (str_split($m[3]) as $e) {
                if ($e === '|') {
                    $this->error("Перевод должен быть однозначным.");
                    return false;
                }
                if (!isset($this->trAbc[$e])) {
                    if (!$this->checkExists($e)) {
                        $this->error("Неопределенный элемент '$e'.");
                        return false;
                    } elseif ($this->V[$e]['type'] == self::TYPE_T) {
                        $this->error("Неопределенный элемент '$e'.");
                        return false;
                    }
                }
            }

            if ($this->checkNT($m[1])) {
                foreach (explode('|', $m[2]) as $e) {
                    if ($e === '#') {
                        $this->V[$m[1]]['empty'] = 1;
                    } else {
                        $e = str_replace('#', '', $e);
                    }
                    foreach (str_split($e) as $eV) {
                        if (!$this->checkExists($eV)) {
                            $this->error("Неизвестный элемент '$eV'.");
                            return false;
                        }
                    }
                    if (!$this->checkEqNT($m[3], $e)) {
                        $this->error("Нетерминалы не совпадают '$e, $m[3]'.");
                        return false;
                    }
                    $this->rules [] = array('l' => $m[1], 'r' => $e, 'tr' => $m[3]);
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

    public function translate($queue, $i)
    {
        $ret = array();
        $next = $queue[$i];
        $translate = $next[2]['l'];
        while ($next[1] != -1) {
            $ret [] = "(".$next[0].",".$translate.")";
            $pos = strpos($translate, $next[2]['l']);
            $translate = substr_replace($translate, $next[2]['tr'], $pos, strlen($next[2]['l']));
            $next = $queue[$next[1]];
        }
        $ret [] = "(".$next[0].",".$translate.")";
        return join("=>", $ret);
    }

    protected function replaceNT($rule, $t)
    {
        $count = substr_count($rule['r'], $t);
        if ($count) {
            $exp = explode($t, $rule['r']);
            $exp2 = explode($t, $rule['tr']);
            for ($i = 0; $i < (pow(2, $count) - 1); $i++) {
                $newRule = '';
                $newTr = '';
                $j = 0;
                foreach ($exp as $k => $e) {
                    $newTr .= $exp2[$k];
                    $newRule .= $e;
                    if ($i & pow(2, $j)) {
                        $newRule .= $t;
                        $newTr .= $t;
                    } else {
                        if ($j < $count) {
                            $newTr .= $this->altRule[$t];
                        }
                    }
                    $j++;
                }
                if ($newRule !== '') {
                    $this->addRule($rule['l'] . "->" . $newRule .",". $newTr);
                }
            }
        }
    }

    public function getRules()
    {
        $out = array();
        foreach ($this->rules as $rule) {
            $out[] = $rule['l'] . '=>' . $rule['r'] . "," . $rule['tr'];
        }
        return $out;
    }
}