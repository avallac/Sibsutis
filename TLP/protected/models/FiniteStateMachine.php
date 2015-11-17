<?php


class FiniteStateMachine extends Automaton
{
    private $rule = array();

    public function setRules($rules)
    {
        $this->rule = array();
        foreach ($rules as $e) {
            if (!$this->setRule($e[0], $e[1], $e[2])) {
                return false;
            }
        }
        return true;
    }

    public function setRule($from, $to, $a)
    {
        if ($this->checkState($from) && $this->checkState($to) && $this->checkLang($a)) {
            if (!isset($this->rule[$from])) {
                $this->rule[$from] = array();
            }
            if (isset($this->rule[$from][$a])) {
                $this->error("Выход '$a' из '$from' повторяется.");
                return false;
            }
            $this->rule[$from][$a] = $to;
            return true;
        }
        return false;
    }

    public function export($str)
    {
        return array(
            'output' => $this->check($str),
            'lang' => $this->getLanguage(),
            'states' => $this->getStates()
        );
    }

    public function check($str)
    {
        $cur = $this->begin;
        $states = "($cur, $str)";
        $str = str_split($str);
        while ($a = array_shift($str)) {
            if (!isset($this->rule[$cur][$a])) {
                return array("Правила перехода '$a' из состояние '$cur' не обнаружено.", $states);
            } else {
                $cur = $this->rule[$cur][$a];
                if (count($str)) {
                    $states .= " ├─ (" . $cur . ", " . implode($str) . ")";
                } else {
                    $states .= " ├─ (" . $cur . ", #)";
                }
            }
        }
        if (!isset($this->end[$cur])) {
            return array("Конечное состояние достигнуто не было", $states);
        }
        return array("Строчка принята.", $states);
    }

}
