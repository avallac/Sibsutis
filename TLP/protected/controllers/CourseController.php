<?php

class CourseController extends LabController
{

    public $pageTitle = "Курсовая работа";
    public $labNum = 6;
    public $formName = 'CourseForm';

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
