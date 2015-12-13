<?php

class Lab5Controller extends LabController
{

    public $pageTitle = "Лабораторная 5";
    public $labNum = 5;
    public $formName = 'Lab5Form';

    public function runLab($model)
    {
        $SST = new SSTranslate();
        $SST->setStates($model->states);
        $SST->setAbc($model->abcLang);
        $SST->setAbc($model->abcStack, 'abcStack');
        $SST->setAbc($model->abcTr, 'abcTr');
        $SST->setBegin($model->begin);
        $SST->setStack($model->beginStack);
        $SST->setEnd($model->end);
        $SST->setRules($model->rule);
        return $SST->export($model->check);
    }
}
