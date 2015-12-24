<?php

class Lab6Controller extends LabController
{

    public $pageTitle = "Курсовая работа";
    public $labNum = 6;
    public $formName = 'Lab6Form';

    public function runLab($model)
    {
        $g = new RegularGrammar();
        $g->add($model->terminal, CFGrammar::TYPE_T);
        $g->add($model->nonterminal, CFGrammar::TYPE_NT);
        $g->setTarget($model->target);
        $g->addRules($model->rule);
        return ['export' => $g->export(3), 'convert' => $g->convertToAutomation()];
    }
}
