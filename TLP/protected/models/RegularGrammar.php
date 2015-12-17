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
        $endState = $this->getNextState();
        foreach ($this->rules as $rule) {
            if (strlen($rule['r']) == 1) {
                if ($this->typeRegularGrammar == self::LL) {
                    $tmpRules [] = [$endState, $rule['l'], $rule['r'][0]];
                } else {
                    $tmpRules [] = [$rule['l'], $endState, $rule['r'][0]];
                }
            } else {
                $ls = str_split($rule['r']);
                if ($this->typeRegularGrammar == self::LL) {
                    $from = array_shift($ls);
                    $to = $rule['l'];
                } else {
                    $from = $rule['l'];
                    $to = array_pop($ls);
                }
                $lastE = array_pop($ls);
                foreach ($ls as $e) {
                    $tmpTo = $this->getNextState();
                    $tmpRules [] = [$from, $tmpTo, $e];
                    $from = $tmpTo;
                }
                $tmpRules [] = [$from, $to, $lastE];
            }
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