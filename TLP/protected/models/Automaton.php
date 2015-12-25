<?php

abstract class Automaton
{

    protected $abc = array();
    protected $abcStack = array();
    protected $abcTr = array();
    protected $error;
    protected $states = array();
    protected $begin;
    protected $end = array();
    protected $stack = array();
    protected $rule = array();

    public function setAbc($language, $type = 'abc')
    {
        $arr = &$this->$type;
        $arr = array('#' => 1);
        $lang = CFGrammar::parseInput($language);
        foreach ($lang as $e) {
            if (strlen($e) > 1) {
                $this->error("Буква '$e' слишком длинная.");
                return false;
            }
            if ($e !== '#') {
                if (!isset($arr[$e])) {
                    $arr[$e] = 1;
                } else {
                    $this->error("Элемент '$e' повторяется.");
                    return false;
                }
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

    public function export($str)
    {
        return array(
            'output' => $this->check($str),
            'lang' => $this->getLanguage(),
            'states' => $this->getStates()
        );
    }

    public function getStack()
    {
        if (empty($this->stack)) {
            return '#';
        } else {
            return implode($this->stack);
        }
    }

    public function setStack($stack)
    {
        $this->stack = array();
        $inputStack = CFGrammar::parseInput($stack);
        foreach ($inputStack as $e) {
            if ($e !== '#') {
                if (isset($this->abcStack[$e])) {
                    $this->stack[] = $e;
                } else {
                    $this->error("Элемент '$e' не найден в алфавите магазина.");
                    return false;
                }
            }
        }
        return true;
    }

    protected function checkState($state)
    {
        if (isset($this->states[$state])) {
            return 1;
        } else {
            $this->error("Состояние '$state' не найдено.");
            return 0;
        }
    }

    protected function checkAbc($a, $type = 'abc')
    {
        $arr = &$this->$type;
        if (isset($arr[$a])) {
            return 1;
        } else {
            $this->error("Буква '$a' не найдена.");
            return 0;
        }
    }

    public function getLanguage()
    {
        return implode(', ', array_keys($this->abc));
    }

    public function getStates()
    {
        return implode(', ', array_keys($this->states));
    }

    public function getError()
    {
        return $this->error;
    }

    protected function error($str)
    {
        $this->error = $str;
    }
}
