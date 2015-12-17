<?php

class CFGrammarTest extends PHPUnit_Framework_TestCase
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

    public function testNoTarget()
    {
        $g = new CFGrammar();
        $this->assertTrue($g->add("c", CFGrammar::TYPE_T));
        $this->assertTrue($g->add("S", CFGrammar::TYPE_NT));
        $this->assertTrue($g->addRule("S->c"));
        $ret = $g->export(3);
        $check = [
            'strings' => false,
            'rules' => [ 'S=>c' ],
            'term' => '',
            'nonterm' => '',
            'target' => ''
        ];
        $this->assertEquals($check, $ret);
    }

    public function testInputRules()
    {
        $g = new CFGrammar();
        $this->assertTrue($g->add("a", CFGrammar::TYPE_T));
        $this->assertTrue($g->add("d", CFGrammar::TYPE_T));
        $this->assertTrue($g->add("c", CFGrammar::TYPE_NT));
        $this->assertTrue($g->setTarget("c"));
        $this->assertFalse($g->addRule("c->b"));
        $this->assertTrue($g->addRule("c->a"));
        $this->assertTrue($g->addRule("c->a|d"));
        $this->assertFalse($g->addRule("a->c|d"));
        $this->assertEquals("Элемент 'a' не нетерминал.", $g->getError());
        $this->assertFalse($g->addRule("a-->c|d"));
        $this->assertEquals("Правило 'a-->c|d' не опознано.", $g->getError());
        $this->assertTrue($g->addRule("a->a"));
    }

    public function testGenerate()
    {
        $g = new CFGrammar();
        $this->assertTrue($g->add("a", CFGrammar::TYPE_T));
        $this->assertTrue($g->add("d", CFGrammar::TYPE_T));
        $this->assertTrue($g->add("S", CFGrammar::TYPE_NT));
        $this->assertTrue($g->add("A", CFGrammar::TYPE_NT));
        $this->assertTrue($g->setTarget("S"));
        $this->assertFalse($g->addRules("S->AA \n a->Sa|#"));
        $this->assertTrue($g->addRules("S->AA \n A->Sa|#"));
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
    public function testCicle()
    {
        $g = new CFGrammar();
        $this->assertTrue($g->add("a", CFGrammar::TYPE_T));
        $this->assertTrue($g->add("b", CFGrammar::TYPE_T));
        $this->assertTrue($g->add("S", CFGrammar::TYPE_NT));
        $this->assertTrue($g->add("A", CFGrammar::TYPE_NT));
        $this->assertTrue($g->setTarget("S"));
        $this->assertTrue($g->addRules("S->A|a|b \n A->S|a"));
        $g->optimize();
        $ret = $g->export(3);
        $check = [
            'strings' => [
                [
                    "S => A => a",
                    "S => a",
                ],
                [
                    "S => b"
                ],
            ],
            'rules' => [
                'S=>A|a|b',
                'A=>S|a'
            ],
            'term' => 'a, b',
            'nonterm' => 'S, A',
            'target' => 'S'
        ];
        $this->assertEquals($check, $ret);
    }
}