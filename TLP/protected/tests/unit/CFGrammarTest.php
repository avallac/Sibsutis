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
}