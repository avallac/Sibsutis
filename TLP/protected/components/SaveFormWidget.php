<?php

class SaveFormWidget extends CWidget
{
    public function run()
    {
        $model = new SaveForm();
        if (isset($_POST['SaveForm'])) {
            $model->attributes = $_POST['SaveForm'];
        }
        $cases=CaseRecord::model()->findAll('labNum=:labID', array(':labID'=>1));
        $this->render('index', array('saveModel' => $model, 'cases'=> $cases));
    }
}
