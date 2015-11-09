<?php

class Lab2Controller extends LabController
{

    public $pageTitle = "Лабораторная 2";
    public $labNum = 2;
    public $formName = 'Lab2Form';

    public function actionIndex()
    {
        $model = new Lab2Form;
        if (isset($_POST['Lab2Form'])) {
            $model->attributes = $_POST['Lab2Form'];
            if ($model->validate()) {

            }
        }
        $this->render('index', array('model'=>$model));
    }
}