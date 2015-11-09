<?php

class SaveFormWidget extends CWidget
{
    public $lab;

    public function run()
    {
        $model = new SaveForm();
        if (isset($_POST['SaveForm'])) {
            $model->attributes = $_POST['SaveForm'];
        }
        $cases=CaseRecord::model()->findAll('labNum=:labID', array(':labID'=>$this->lab));
        $this->render('index', array('saveModel' => $model, 'cases'=> $cases, 'lab' => $this->lab));
    }
}
