<?php

class Lab1Controller extends LabController
{

    public $pageTitle = "Лабораторная 1";
    public $labNum = 1;
    public $formName = 'Lab1Form';

    public function actionIndex()
    {
        $model = new Lab1Form;
        $gModel = array();
        if (isset($_POST['Lab1Form'])) {
            $model->attributes = $_POST['Lab1Form'];
            if ($model->validate()) {
                $g = new CFGrammar();
                $g->add($model->terminal, CFGrammar::TYPE_T);
                $g->add($model->nonterminal, CFGrammar::TYPE_NT);
                $g->setTarget($model->target);
                $g->addRules($model->rule);
                $g->removeE();
                $g->removeOrphan();
                $g->removeUnavailable();
                $gModel = $g->export(3);
            }
        }
        $this->render('index', array('gModel' => $gModel, 'model'=>$model));
    }
}