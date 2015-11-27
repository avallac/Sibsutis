<?php

class CFGrammarTest extends CTestCase
{

    public function testInputTerm()
    {
        $g = new CFGrammar();
        $this->assertTrue($g->add("a", CFGrammar::TYPE_T));
        $this->assertTrue($g->add("b", CFGrammar::TYPE_T));
        $this->assertFalse($g->add("a", CFGrammar::TYPE_T));
        $this->assertFalse($g->add("aa", CFGrammar::TYPE_T));
    }

    public function testInputNTerm()
    {
        $g = new CFGrammar();
        $this->assertFalse($g->setTarget("c"));
        $this->assertTrue($g->add("c", CFGrammar::TYPE_NT));
        $this->assertTrue($g->setTarget("c"));
    }

    public function testInputRules()
    {
        $g = new CFGrammar();
        $g->add("a", CFGrammar::TYPE_T);
        $g->add("d", CFGrammar::TYPE_T);
        $g->add("c", CFGrammar::TYPE_NT);
        $g->setTarget("c");
        $this->assertFalse($g->addRule("c->b"));
        $this->assertTrue($g->addRule("c->a"));
        $this->assertTrue($g->addRule("c->a|d"));
    }

    public function testGenerate()
    {
        $g = new CFGrammar();
        $g->add("a", CFGrammar::TYPE_T);
        $g->add("d", CFGrammar::TYPE_T);
        $g->add("S", CFGrammar::TYPE_NT);
        $g->add("A", CFGrammar::TYPE_NT);
        $g->setTarget("S");
        $g->addRule("S->AA");
        $g->addRule("A->Sa|#");
        $g->optimize();
        $ret = $g->export(3);
        $check = [
            'strings' => [
                [
                    "S` => #"
                ],
                [
                    "S` => S => A => a"
                ],
                [
                    'S` => S => A => Sa => Aa => aa',
                    'S` => S => AA => aA => aa'
                ],
                [
                    'S` => S => A => Sa => Aa => Saa => Aaa => aaa',
                    'S` => S => A => Sa => AAa => aAa => aaa',
                    'S` => S => AA => aA => aSa => aAa => aaa',
                    'S` => S => AA => SaA => AaA => aaA => aaa'
                ]
            ],
            'rules' => [
                'S=>AA|A',
                'A=>Sa|a',
                'S`=>#|S'
            ],
            'term' => 'a',
            'nonterm' => 'S, A, S`',
            'target' => 'S`'
        ];
        $this->assertEquals($check, $ret);
    }
}