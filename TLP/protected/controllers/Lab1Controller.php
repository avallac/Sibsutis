<?php

class Lab1Controller extends Controller
{

    public $pageTitle = "Лабораторная 1";

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

    public function actionSave()
    {
        $model = new Lab1Form;
        $saveModel = new SaveForm;
        if (isset($_POST['SaveForm'])) {
            $saveModel->attributes = $_POST['SaveForm'];
            $model->attributes = json_decode($_POST['SaveForm']['form'], 1);
            if ($saveModel->validate() && $model->validate()) {
                $case = new CaseRecord();
                $case->labNum = 1;
                $case->name = $saveModel->filename;
                $case->rule = $saveModel->form;
                $case->save();
            }
        }
        $this->render('index', array('model'=>$model));
    }

    public function actionLoad($id)
    {
        $case = CaseRecord::model()->find('id=:ID', array(':ID'=>$id));
        $model = new Lab1Form;
        $model->attributes = json_decode($case->rule, 1);
        $this->render('index', array('model'=>$model));
    }
}