<?php

class Lab1Form extends CFormModel
{
    public $terminal;
    public $nonterminal;
    public $target;
    public $rule;
    public $length;

    public function rules()
    {
        return array(
            array('terminal, nonterminal, target, rule, length', 'required'),
            array('length', 'numerical', 'integerOnly' => true, 'min' => 1),
            array('terminal', 'checkG', 0),
            array('nonterminal', 'checkG', 1),
            array('target', 'checkG', 2),
            array('rule', 'checkG', 3),
        );
    }

    public function attributeLabels()
    {
        return array(
            'length'=>'Максимальная длина',
            'terminal'=>'Терминалы',
            'nonterminal'=>'Нетерминалы',
            'target' => 'Целевой символ',
            'rule' => 'Правила',
        );
    }

    public function checkG($attribute, $params)
    {
        $g = $this->getGrammar();
        $g->add($this->terminal, CFGrammar::TYPE_T);
        if ($params[0] >= 1) {
            if (strlen($g->getError())) {
                return;
            }
            $g->add($this->nonterminal, CFGrammar::TYPE_NT);
        }
        if ($params[0] >= 2) {
            if (strlen($g->getError())) {
                return;
            }
            $g->setTarget($this->target);
        }
        if ($params[0] >= 3) {
            if (strlen($g->getError())) {
                return;
            }
            $g->addRules($this->rule);
            if (!strlen($g->getError())) {
                $g->removeE();
                $g->removeOrphan();
                $g->removeUnavailable();
            }
        }
        if (strlen($g->getError())) {
            $this->addError($attribute, $g->getError());
        }
    }

    protected function getGrammar()
    {
        return new CFGrammar();
    }
}
