<?php

class SaveForm extends CFormModel
{
    public $filename;
    public $form;

    public function rules()
    {
        return array(
            array('filename, form', 'required'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'filename'=>'Имя'
        );
    }
}
