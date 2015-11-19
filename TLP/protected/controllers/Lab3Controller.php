<?php

class Lab3Controller extends LabController
{

    public $pageTitle = "Лабораторная 3";
    public $labNum = 3;
    public $formName = 'Lab3Form';

    public function runLab($model)
    {
        $DPDA = new DeterministicPushdownAutomaton();
        $DPDA->setStates($model->states);
        $DPDA->setAbc($model->abcLang);
        $DPDA->setAbc($model->abcStack, 'abcStack');
        $DPDA->setBegin($model->begin);
        $DPDA->setStack($model->beginStack);
        $DPDA->setEnd($model->end);
        $DPDA->setRules($model->rule);
        return $DPDA->export($model->check);
    }
}
