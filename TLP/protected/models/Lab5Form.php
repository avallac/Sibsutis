<?php

class Lab5Form extends CFormModel
{
    public $check;
    public $states;
    public $abcLang;
    public $abcTr;
    public $abcStack;
    public $begin;
    public $beginStack;
    public $end;
    public $rule;


    public function rules()
    {
        return array(
            array('check, states, abcLang, abcStack, begin, beginStack, end, rule', 'required'),
            array('states', 'check', 0),
            array('abcLang', 'check', 1),
            array('abcTr', 'check', 2),
            array('abcStack', 'check', 3),
            array('begin', 'check', 4),
            array('beginStack', 'check', 5),
            array('end', 'check', 6),
            array('rule', 'check', 7),
        );
    }

    public function attributeLabels()
    {
        return array(
            'check' => 'Строка для проверки',
            'states' => 'Множество состояний',
            'abcLang' => 'Алфавит входного языка',
            'abcTr' => 'Алфавит выходного языка',
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
        $SST = new SSTranslate();
        if (!$SST->setStates($this->states)) {
            $this->addError($attribute, $SST->getError());
            $error = 1;
        }
        if ($params[0] >= 1) {
            if ($error) {
                return;
            }
            if (!$SST->setAbc($this->abcLang)) {
                $error = 1;
            }
        }
        if ($params[0] >= 2) {
            if ($error) {
                return;
            }
            if (!$SST->setAbc($this->abcStack, 'abcStack')) {
                $error = 1;
            }
        }
        if ($params[0] >= 3) {
            if ($error) {
                return;
            }
            if (!$SST->setAbc($this->abcTr, 'abcTr')) {
                $error = 1;
            }
        }
        if ($params[0] >= 4) {
            if ($error) {
                return;
            }
            if (!$SST->setBegin($this->begin)) {
                $error = 1;
            }
        }
        if ($params[0] >= 5) {
            if ($error) {
                return;
            }
            if (!$SST->setStack($this->beginStack)) {
                $error = 1;
            }
        }
        if ($params[0] >= 6) {
            if ($error) {
                return;
            }
            if (!$SST->setEnd($this->end)) {
                $error = 1;
            }
        }
        if ($params[0] >= 7) {
            if ($error) {
                return;
            }
            if (!$SST->setRules($this->rule)) {
                $error = 1;
            }
        }
        if ($error) {
            $this->addError($attribute, $SST->getError());
        }

    }
}