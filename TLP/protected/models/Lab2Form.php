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
            //array('terminal', 'checkG', 0),
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

}
