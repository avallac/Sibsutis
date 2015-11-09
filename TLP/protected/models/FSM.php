<?php

class FSM
{
    private $lang = array();
    private $states = array();
    private $begin;
    private $end = array();
    private $rule = array();
    private $error;

    public function setLanguage($language)
    {
        $this->lang = array();
        $lang = CFGrammar::parseInput($language);
        foreach ($lang as $e) {
            if (strlen($e) > 1) {
                $this->error("Буква '$e' слишком длинная.");
                return false;
            }
            if (!isset($this->lang[$e])) {
                $this->lang[$e] = 1;
            } else {
                $this->error("Элемент '$e' повторяется.");
                return false;
            }
        }
        return true;
    }

    public function setStates($states)
    {
        $this->states = array();
        $states = CFGrammar::parseInput($states);
        foreach ($states as $e) {
            if (!isset($this->states[$e])) {
                $this->states[$e] = 1;
            } else {
                $this->error("Состоянме '$e' повторяется.");
                return false;
            }
        }
        return true;
    }

    public function setEnd($end)
    {
        $this->end = array();
        $end = CFGrammar::parseInput($end);
        foreach ($end as $e) {
            if (!isset($this->end[$e])) {
                if ($this->checkState($e)) {
                    $this->end[$e] = 1;
                } else {
                    return false;
                }
            } else {
                $this->error("Конечное состояние '$e' повторяется.");
                return false;
            }
        }
        return true;
    }

    public function setBegin($begin)
    {
        if ($this->checkState($begin)) {
            $this->begin = $begin;
            return true;
        }
        return false;
    }

    public function setRules($rules)
    {
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

    public function getError()
    {
        return $this->error;
    }

    private function error($str)
    {
        $this->error = $str;
    }

    private function checkState($state)
    {
        if (isset($this->states[$state])) {
            return 1;
        } else {
            $this->error("Состояние '$state' не найдено.");
            return 0;
        }
    }

    private function checkLang($a)
    {
        if (isset($this->lang[$a])) {
            return 1;
        } else {
            $this->error("Буква '$a' не найдена.");
            return 0;
        }
    }
}
