<?php

class Lab4Controller extends LabController
{

    public $pageTitle = "Лабораторная 4";
    public $labNum = 4;
    public $formName = 'Lab4Form';

    public function runLab($model)
    {
        $g = new CFTranslate();
        $g->add($model->terminal, CFGrammar::TYPE_T);
        $g->setTrAbc($model->terminalTr, CFGrammar::TYPE_T);
        $g->add($model->nonterminal, CFGrammar::TYPE_NT);
        $g->setTarget($model->target);
        $g->addRules($model->rule);
        $g->optimize();
        return $g->export($model->string);
    }
}
