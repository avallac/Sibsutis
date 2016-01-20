<?php

class RegularGrammar extends CFGrammar
{
    protected $allowEqualLR = 0;
    protected $typeRegularGrammar = 0;
    protected $nextState = 0;

    protected $ndt = [];


    const LL = 1;
    const PL = 2;

    public function __construct($t = '#')
    {
        $this->setEmpty($t);
        $this->add($this->getEmpty(), self::TYPE_EMPTY, 1);
    }


    protected function checkShortRule($arr, $e)
    {
        if (sizeof($arr) == 1) {
            if ($arr[0] == CFGrammar::TYPE_T) {
                return true;
            } else {
                $this->error("'$e': Один нетерминал.");
                return false;
            }
        }
        return true;
    }

    protected function checkNTInRule($arr, $e)
    {
        $exist = 0;
        for ($i = 0; isset($arr[$i]); $i++) {
            if ($arr[$i] == CFGrammar::TYPE_NT) {
                if ($exist) {
                    $this->error("'$e': Больше одного нетерминала.");
                    return false;
                } else {
                    $exist = 1;
                }
            }
        }
        if ($exist) {
            if (($arr[0] != CFGrammar::TYPE_NT) && ($arr[$i-1] != CFGrammar::TYPE_NT)) {
                $this->error("'$e': Нетерминал находиться не скраю.");
                return false;
            } else {
                if ($arr[0] == CFGrammar::TYPE_NT) {
                    if ($this->typeRegularGrammar == self::PL) {
                        $this->error("'$e': праволинейная грамматика");
                        return false;
                    }
                    $this->typeRegularGrammar = self::LL;
                } else {
                    if ($this->typeRegularGrammar == self::LL) {
                        $this->error("'$e': леволинейная грамматика");
                        return false;
                    }
                    $this->typeRegularGrammar = self::PL;
                }
            }
        }
        return true;
    }

    protected function existNT($str)
    {
        $arr = str_split($str);
        for ($i = 0; isset($arr[$i]); $i++) {
            if ($this->V[$arr[$i]]['type'] == CFGrammar::TYPE_NT) {
                return true;
            }
        }
        return false;
    }

    protected function validateRightPart($e)
    {
        $arr = [];
        $count = 0;
        foreach (str_split($e) as $eV) {
            if (!$this->checkExists($eV)) {
                $this->error("Неизвестный элемент '$eV'.");
                return false;
            }
            $arr[$count] = $this->V[$eV]['type'];
            $count++;
        }
        if (!$this->checkShortRule($arr, $e) || !$this->checkNTInRule($arr, $e)) {
            return false;
        }
        return true;
    }

    public function getNextState()
    {
        $this->nextState ++;
        $this->addElement('Q'.$this->nextState, CFGrammar::TYPE_NT);
        return 'Q'.$this->nextState;
    }

    private function genPair($i, $exist, $step)
    {
        $tmpArr = $this->ndt;
        rsort($tmpArr);
        if (sizeof($tmpArr) == $i) {
            $ret = [];
        } else {
            $ret = [[]];
        }
        if ($i <= 0) {
            return [[]];
        }
        for ($s = $step; isset($tmpArr[$s]); $s ++) {
            $e = $tmpArr[$s];
            $new = array_merge($exist, [$e]);
            if (!in_array($e, $exist, true)) {
                $tmp = $this->genPair($i - 1, $new, $s);
                foreach ($tmp as $k => $v) {
                    $tmp[$k][] = $e;
                }
                $ret = array_merge($tmp, $ret);
            }
        }
        return $ret;
    }


    public function convertToAutomation()
    {
        $this->removeUnavailable();
        $this->removeOrphan();
        $this->nextState = 0;
        $tmpRules = [];
        $cash = [];
        $endState = $this->getNextState();
        foreach ($this->rules as $rule) {
            $ls = str_split($rule['r']);
            if ($this->typeRegularGrammar == self::LL) {
                if ($this->existNT($rule['r'])) {
                    $from = array_shift($ls);
                } else {
                    $from = $endState;
                }
                $to = $rule['l'];
            } else {
                $from = $rule['l'];
                if ($this->existNT($rule['r'])) {
                    $to = array_pop($ls);
                } else {
                    $to = $endState;
                }
            }
            $lastE = array_pop($ls);
            foreach ($ls as $e) {
                if (!isset($cash[$from][$e])) {
                    $tmpTo = $this->getNextState();
                    $cash[$from][$e] = $tmpTo;
                    $tmpRules [] = [$from, $tmpTo, $e];
                    $from = $tmpTo;
                } else {
                    $from = $cash[$from][$e];
                }
            }
            $cash[$from][$lastE] = $to;
            $tmpRules [] = [$from, $to, $lastE];
        }
        if ($this->typeRegularGrammar == self::LL) {
            $target = $endState;
            $end = $this->target;
        } else {
            $target = $this->target;
            $end = $endState;
        }
        $out = [];
        foreach ($tmpRules as $rule) {
            $out[$rule[0]][$rule[2]][] = $rule[1];
        }
        foreach ($out as $name => $e) {
            $this->ndt[] = $name;
        }
        if (!in_array($end, $this->ndt, 1)) {
            $this->ndt[] = $end;
        }
        if (!in_array($target, $this->ndt, 1)) {
            $this->ndt[] = $target;
        }
        $ret = $this->genPair(sizeof($this->ndt), [], 0);
        foreach ($ret as $v) {
            sort($v);
            $name = implode($v, '|');
            $dt[$name] = ['terms' => [], 'in' => $v];
            foreach ($this->getTerm(1, 1) as $term) {
                $eStates = [];
                foreach ($v as $states) {
                    if (isset($out[$states][$term])) {
                        $eStates = array_unique(array_merge($eStates, $out[$states][$term]));
                    }
                }
                sort($eStates);
                $dt[$name]['terms'][$term] = $eStates;
            }
        }
        $updated = 1;
        $dt[$target]['good'] = 1;
        while ($updated) {
            $updated = 0;
            foreach ($dt as $name => $e) {
                if (isset($dt[$name]['good'])) {
                    foreach ($e['terms'] as $t) {
                        if (sizeof($t)) {
                            if (!isset($dt[implode($t, '|')])) {
                                //var_dump($dt);
                                var_dump(implode($t, '|'));
                                exit;
                            }
                            if (!isset($dt[implode($t, '|')]['good'])) {
                                $dt[implode($t, '|')]['good'] = 1;
                                $updated = 1;
                            }
                        }
                    }
                }
            }
        }
        $newEnd = [];
        $tmpRules = [];
        foreach ($dt as $name => $e) {
            if (isset($dt[$name]['good'])) {
                foreach ($e['terms'] as $term => $t) {
                    if (sizeof($t)) {
                        $tmpRules [] = [$name, implode($t, '|'), $term];
                    }
                }
                if (in_array($end, $dt[$name]['in'])) {
                    $newEnd[] = $name;
                }
            }
        }

        return [
            'ndt' => $out,
            'dt' => $dt,
            'term' => $this->getTerm(1, 1),
            'rules' => $tmpRules,
            'states' => $this->getNonTerm(1),
            'begin' => $target,
            'end' => implode($newEnd, ',')
        ];
    }
}