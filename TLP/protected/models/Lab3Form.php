<?php

class Lab3Form extends CFormModel
{
    public $states;
    public $abcLang;
    public $abcStack;
    public $begin;
    public $beginStack;
    public $end;
    public $rule;


    public function rules()
    {
        return array(
            array('states, abcLang, abcStack, begin, beginStack, end, rule', 'required'),
            array('states', 'check', 0),
            array('abcLang', 'check', 1),
            array('abcStack', 'check', 2),
            array('begin', 'check', 3),
            array('beginStack', 'check', 4),
            array('end', 'check', 5),
            array('rule', 'check', 6),
        );
    }

    public function attributeLabels()
    {
        return array(
            'states'=>'Множество состояний',
            'abcLang'=>'Алфавит языка',
            'abcStack' => 'Алфавит магазина',
            'begin' => 'Начальное состояние',
            'beginStack' => 'Начальное содержимое стека',
            'end' => 'Множество заключительных состояний',
            'rule' => 'Правила переходов',
        );
    }

    public function check($attribute, $params)
    {
        $error = 0;
        $DPDA = new DeterministicPushdownAutomaton();
        if (!$DPDA->setStates($this->states)) {
            $this->addError($attribute, $DPDA->getError());
            $error = 1;
        }
        if ($params[0] >= 1) {
            if ($error) {
                return;
            }
            if (!$DPDA->setAbc($this->abcLang)) {
                $error = 1;
            }
        }
        if ($params[0] >= 2) {
            if ($error) {
                return;
            }
            if (!$DPDA->setAbc($this->abcStack, 'abcStack')) {
                $error = 1;
            }
        }
        if ($params[0] >= 3) {
            if ($error) {
                return;
            }
            if (!$DPDA->setBegin($this->begin)) {
                $error = 1;
            }
        }
        if ($params[0] >= 4) {
            if ($error) {
                return;
            }
        }
        if ($params[0] >= 5) {
            if ($error) {
                return;
            }
            if (!$DPDA->setEnd($this->end)) {
                $error = 1;
            }
        }
        if ($params[0] >= 6) {
            if ($error) {
                return;
            }
        }
        if ($error) {
            $this->addError($attribute, $DPDA->getError());
        }

    }
}