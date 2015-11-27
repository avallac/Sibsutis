<?php

class CFGParseTest extends CTestCase
{

    public function testInputTerm1()
    {
        $g = new CFGrammar();
        $g->add("0", CFGrammar::TYPE_T);
        $g->add("1", CFGrammar::TYPE_T);
        $g->add("A", CFGrammar::TYPE_NT);
        $g->add("B", CFGrammar::TYPE_NT);
        $g->add("S", CFGrammar::TYPE_NT);
        $g->setTarget("S");
        $g->addRule("A->00");
        $g->addRule("B->0A");
        $g->addRule("S->B1");
        $this->assertEmpty($g->getError());
        $this->assertEquals("S=>B1=>0A1=>0001", $g->parse('0001'));
    }

    public function testInputTerm2()
    {
        $g = new CFGrammar();
        $g->add("0", CFGrammar::TYPE_T);
        $g->add("1", CFGrammar::TYPE_T);
        $g->add("S", CFGrammar::TYPE_NT);
        $g->setTarget("S");
        $g->addRule("S->0S");
        $g->addRule("S->1S");
        $g->addRule("S->#");
        $this->assertEquals('', $g->getError());
        $g->optimize();
        $this->assertEquals("S=>0S=>00S=>000S=>0001", $g->parse('0001'));
    }

}