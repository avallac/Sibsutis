<?php

class Lab1Controller extends LabController
{

    public $pageTitle = "Лабораторная 1";
    public $labNum = 1;
    public $formName = 'Lab1Form';

    public function runLab($model)
    {
        $g = new CFGrammar();
        $g->add($model->terminal, CFGrammar::TYPE_T);
        $g->add($model->nonterminal, CFGrammar::TYPE_NT);
        $g->setTarget($model->target);
        $g->addRules($model->rule);
        $g->optimize();
        return $g->export($model->length);
    }
}
