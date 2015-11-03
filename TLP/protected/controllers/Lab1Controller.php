<?php

class Lab1Controller extends Controller
{

    public function actionIndex()
    {
        $this->pageTitle="Лабораторная 1";
        $model = new Lab1Form;
        $strings = array();
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
                $strings = $g->generate(3);
            }
        }
        $this->render('index', array('output' => $strings, 'model'=>$model));
    }
}
