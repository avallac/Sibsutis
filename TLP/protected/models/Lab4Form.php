<?php

class Lab4Form extends CFormModel
{
    public $terminal;
    public $terminalTr;
    public $nonterminal;
    public $target;
    public $rule;
    public $string;

    public function rules()
    {
        return array(
            array('string, terminal, terminalTr, nonterminal, target, rule', 'required'),
            array('terminal', 'checkG', 0),
            array('terminalTr', 'checkG', 1),
            array('nonterminal', 'checkG', 2),
            array('target', 'checkG', 3),
            array('rule', 'checkG', 4),
        );
    }

    public function attributeLabels()
    {
        return array(
            'string' => 'Строчка',
            'terminal'=>'Алфавит входного языка',
            'terminalTr'=>'Алфавит выходного языка',
            'nonterminal'=>'Нетерминалы',
            'target' => 'Целевой символ',
            'rule' => 'Правила',
        );
    }

    public function checkG($attribute, $params)
    {
        $g = new CFTranslate();
        $g->add($this->terminal, CFGrammar::TYPE_T);
        if ($params[0] >= 1) {
            if (strlen($g->getError())) {
                return;
            }
            $g->setTrAbc($this->terminalTr);
        }
        if ($params[0] >= 2) {
            if (strlen($g->getError())) {
                return;
            }
            $g->add($this->nonterminal, CFGrammar::TYPE_NT);
        }
        if ($params[0] >= 3) {
            if (strlen($g->getError())) {
                return;
            }
            $g->setTarget($this->target);
        }
        if ($params[0] >= 4) {
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
}
