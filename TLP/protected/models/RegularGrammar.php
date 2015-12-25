<?php

class RegularGrammar extends CFGrammar
{
    protected $allowEqualLR = 0;
    protected $typeRegularGrammar = 0;
    protected $nextState = 0;

    const LL = 1;
    const PL = 2;


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

    public function convertToAutomation()
    {
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
        return [
            'rules' => $tmpRules,
            'states' => $this->getNonTerm(1),
            'begin' => $target,
            'end' => $end
        ];
    }
}