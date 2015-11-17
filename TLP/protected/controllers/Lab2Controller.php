<?php

class Lab2Controller extends LabController
{

    public $pageTitle = "Лабораторная 2";
    public $labNum = 2;
    public $formName = 'Lab2Form';

    public function runLab($model)
    {
        $FSM = new FiniteStateMachine();
        $parser = new GoJSParser($model->graph);
        $FSM->setAbc($parser->getLang());
        $FSM->setStates($parser->getStates());
        $FSM->setBegin($model->begin);
        $FSM->setEnd($model->end);
        $FSM->setRules($parser->getRules());
        return $FSM->export($model->check);
    }
}
