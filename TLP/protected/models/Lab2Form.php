<?php

class Lab2Form extends CFormModel
{
    public $check;
    public $begin;
    public $end;
    public $graph;

    public function rules()
    {
        return array(
            array('check, begin, end, graph', 'required'),
            array('graph', 'check', 0),
            array('begin', 'check', 1),
            array('ends', 'check', 2),
        );
    }

    public function attributeLabels()
    {
        return array(
            'check'=>'Строка для проверки',
            'begin'=>'Начальное состоение',
            'end' => 'Множество конечных состояний',
            'graph' => 'Граф',
        );
    }

    public function check($attribute, $params)
    {
        $error = 0;
        $FSM = new FiniteStateMachine();
        $parser = new GoJSParser($this->graph);
        if (!$FSM->setAbc($parser->getLang())) {
            $this->addError($attribute, $FSM->getError());
            $error = 1;
        }
        if (!$FSM->setStates($parser->getStates())) {
            $this->addError($attribute, $FSM->getError());
            $error = 1;
        }
        if (!$FSM->setRules($parser->getRules())) {
            $error = 1;
        }
        if ($params[0] >= 1) {
            if ($error) {
                return;
            }
            if (!$FSM->setBegin($this->begin)) {
                $error = 1;
            }
        }
        if ($params[0] >= 2) {
            if ($error) {
                return;
            }
            if (!$FSM->setEnd($this->end)) {
                $error = 1;
            }
        }
        if ($error) {
            $this->addError($attribute, $FSM->getError());
        }

    }
}

