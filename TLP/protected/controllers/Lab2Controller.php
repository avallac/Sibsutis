<?php

class Lab2Controller extends LabController
{

    public $pageTitle = "Лабораторная 2";
    public $labNum = 2;
    public $formName = 'Lab2Form';

    public function actionIndex()
    {
        $FSMModel = array();
        $model = new Lab2Form;
        if (isset($_POST['Lab2Form'])) {
            $model->attributes = $_POST['Lab2Form'];
            if ($model->validate()) {
                $FSM = new FSM();
                $parser = new GoJSParser($model->graph);
                $FSM->setLanguage($parser->getLang());
                $FSM->setStates($parser->getStates());
                $FSM->setBegin($model->begin);
                $FSM->setEnd($model->end);
                $FSM->setRules($parser->getRules());
                $FSMModel = $FSM->export($model->check);
            }
        }
        $this->render('index', array('model'=>$model, 'FSMModel' => $FSMModel));
    }
}