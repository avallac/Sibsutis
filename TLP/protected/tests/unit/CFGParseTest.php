<?php

class CFGParseTest extends PHPUnit_Framework_TestCase
{

    public function testRMNull()
    {
        $g = new CFTranslate();
        $this->assertTrue($g->setTrAbc("a,b,c"));
        $this->assertTrue($g->add("S", CFGrammar::TYPE_NT));
        $this->assertTrue($g->add("A", CFGrammar::TYPE_NT));
        $this->assertTrue($g->add("B", CFGrammar::TYPE_NT));
        $this->assertTrue($g->add("C", CFGrammar::TYPE_NT));
        $this->assertTrue($g->add("a", CFGrammar::TYPE_T));
        $this->assertTrue($g->addRule("S->aA,aA"));
        $this->assertTrue($g->addRule("A->BC,BC"));
        $this->assertTrue($g->addRule("B->#,b"));
        $this->assertTrue($g->addRule("B->a,b"));
        $this->assertTrue($g->addRule("C->#,c"));
        $this->assertTrue($g->addRule("C->a,c"));
        $this->assertTrue($g->setTarget('S'));
        $g->optimize();
        $this->assertEquals(
            ["S=>aA,aA", "A=>BC,BC", "B=>a,b", "C=>a,c", "S=>a,abc", "A=>C,bC", "A=>B,Bc"],
            $g->getRules()
        );
        $this->assertEquals(["(S,S)", "(aA,aA)", "(aBC,aBC)", "(aaC,abC)", "(aaa,abc)"], $g->parse('aaa'));
        $this->assertEquals(["(S,S)", "(a,abc)"], $g->parse('a'));
    }

    public function testMet()
    {
        $g = new CFTranslate();
        $this->assertTrue($g->setTrAbc("a, +, *, (, )"));
        $this->assertTrue($g->add("S", CFGrammar::TYPE_NT));
        $this->assertTrue($g->add("P", CFGrammar::TYPE_NT));
        $this->assertTrue($g->add("A", CFGrammar::TYPE_NT));
        $this->assertTrue($g->add("C", CFGrammar::TYPE_NT));
        $this->assertTrue($g->add("a", CFGrammar::TYPE_T));
        $this->assertTrue($g->add("+", CFGrammar::TYPE_T));
        $this->assertTrue($g->add("*", CFGrammar::TYPE_T));
        $this->assertTrue($g->add("(", CFGrammar::TYPE_T));
        $this->assertTrue($g->add(")", CFGrammar::TYPE_T));
        $this->assertTrue($g->addRule("S->(S),S"));
        $this->assertTrue($g->addRule("S->S+S,S+S"));
        $this->assertTrue($g->addRule("S->P,P"));
        $this->assertTrue($g->addRule("P->(P),P"));
        $this->assertTrue($g->addRule("P->A*A,A*A"));
        $this->assertTrue($g->addRule("P->a,a"));
        $this->assertTrue($g->addRule("A->(S+S),(S+S)"));
        $this->assertTrue($g->addRule("A->P,P"));
        $this->assertTrue($g->setTarget('S'));
        $g->optimize();
        $this->assertEquals(
            [
                "(S,S)",
                "((S),S)",
                "((P),P)",
                "((A*A),A*A)",
                "(((S+S)*A),(S+S)*A)",
                "(((P+S)*A),(P+S)*A)",
                "(((a+S)*A),(a+S)*A)",
                "(((a+(S))*A),(a+S)*A)",
                "(((a+(P))*A),(a+P)*A)",
                "(((a+(A*A))*A),(a+A*A)*A)",
                "(((a+(P*A))*A),(a+P*A)*A)",
                "(((a+(a*A))*A),(a+a*A)*A)",
                "(((a+(a*P))*A),(a+a*P)*A)",
                "(((a+(a*a))*A),(a+a*a)*A)",
                "(((a+(a*a))*P),(a+a*a)*P)",
                "(((a+(a*a))*a),(a+a*a)*a)"
            ],
            $g->parse('((a+(a*a))*a)')
        );
    }

    public function testInputTerm1()
    {
        $g = new CFGrammar();
        $this->assertTrue($g->add("0", CFGrammar::TYPE_T));
        $this->assertTrue($g->add("1", CFGrammar::TYPE_T));
        $this->assertTrue($g->add("A", CFGrammar::TYPE_NT));
        $this->assertTrue($g->add("B", CFGrammar::TYPE_NT));
        $this->assertTrue($g->add("S", CFGrammar::TYPE_NT));
        $this->assertTrue($g->setTarget("S"));
        $this->assertTrue($g->addRule("A->00"));
        $this->assertTrue($g->addRule("B->0A"));
        $this->assertTrue($g->addRule("S->B1"));
        $this->assertEmpty($g->getError());
        $this->assertEquals("Последовательность не распознана.", $g->parse('000a'));
        $this->assertEquals("Последовательность не распознана.", $g->parse('000'));
        $this->assertEquals("S=>B1=>0A1=>0001", $g->parse('0001'));
    }

    public function testInputTerm2()
    {
        $g = new CFGrammar();
        $this->assertTrue($g->add("0", CFGrammar::TYPE_T));
        $this->assertTrue($g->add("1", CFGrammar::TYPE_T));
        $this->assertTrue($g->add("S", CFGrammar::TYPE_NT));
        $this->assertTrue($g->setTarget("S"));
        $this->assertTrue($g->addRule("S->0S"));
        $this->assertTrue($g->addRule("S->1S"));
        $this->assertTrue($g->addRule("S->#"));
        $this->assertEquals('', $g->getError());
        $g->optimize();
        $this->assertEquals("S=>0S=>00S=>000S=>0001", $g->parse('0001'));
    }

    public function testRule()
    {
        $g = new CFTranslate();
        $this->assertTrue($g->add("0", CFGrammar::TYPE_T));
        $this->assertTrue($g->add("1", CFGrammar::TYPE_T));
        $this->assertTrue($g->add("S", CFGrammar::TYPE_NT));
        $this->assertTrue($g->add("A", CFGrammar::TYPE_NT));
        $this->assertTrue($g->setTrAbc("0"));
        $this->assertTrue($g->setTarget("S"));
        $this->assertTrue($g->addRule("S->S,S"));
        $this->assertTrue($g->addRule("S->A,A"));
        $this->assertFalse($g->addRule("S->A,A"));
        $this->assertEquals("Правило дублируется 'S->A'.", $g->getError());
        $this->assertFalse($g->addRule("S->A0,A01"));
        $this->assertEquals("Неопределенный элемент '1'.", $g->getError());
        $this->assertFalse($g->addRule("S-->0,01"));
        $this->assertEquals("Правило 'S-->0,01' не опознано.", $g->getError());
        $this->assertFalse($g->addRule("0->SS,SS0"));
        $this->assertEquals("Элемент '0' не нетерминал.", $g->getError());
    }

    public function testMultiNT()
    {
        $g = new CFTranslate();
        $this->assertTrue($g->add("0", CFGrammar::TYPE_T));
        $this->assertTrue($g->add("1", CFGrammar::TYPE_T));
        $this->assertTrue($g->add("S", CFGrammar::TYPE_NT));
        $this->assertTrue($g->add("A", CFGrammar::TYPE_NT));
        $this->assertTrue($g->setTrAbc("0"));
        $this->assertTrue($g->setTarget("S"));
        $this->assertTrue($g->addRule("S->AA,AA"));
        $this->assertTrue($g->addRule("A->0|1|#,0"));
        $this->assertEquals('', $g->getError());
        $g->optimize();
        $this->assertEquals(["(S,S)", "(AA,AA)", "(0A,0A)", "(01,00)"], $g->parse('01'));
    }

    public function testInputTerm3()
    {
        $g = new CFTranslate();
        $this->assertTrue($g->add("a", CFGrammar::TYPE_T));
        $this->assertTrue($g->add("B", CFGrammar::TYPE_NT));
        $this->assertFalse($g->setTrAbc("aa"));
        $this->assertEquals("Буква 'aa' слишком длинная.", $g->getError());
        $this->assertTrue($g->setTrAbc("a"));
        $this->assertFalse($g->setTrAbc("a, a"));
        $this->assertEquals("Элемент 'a' повторяется.", $g->getError());
        $this->assertTrue($g->setTrAbc("b"));
        $this->assertFalse($g->setTrAbc("B"));
        $this->assertEquals("Элемент 'B' уже определен как нетерминал.", $g->getError());
    }

    public function testBadTranslate()
    {
        $g = new CFTranslate();
        $this->assertFalse($g->addRule("A->a,b"));
        $this->assertEquals("Неопределенный элемент 'b'.", $g->getError());
    }

    public function testTranslateWithAlt()
    {
        $g = new CFTranslate();
        $g->setTrAbc("b");
        $this->assertFalse($g->addRule("A->a,b|a"));
        $this->assertEquals("Перевод должен быть однозначным.", $g->getError());
    }

    public function testBadNT()
    {
        $g = new CFTranslate();
        $g->setTrAbc("b");
        $this->assertFalse($g->addRule("A->Aa,bA"));
        $this->assertEquals("Неопределенный элемент 'A'.", $g->getError());
    }

    public function testBadTerm()
    {
        $g = new CFTranslate();
        $g->setTrAbc("b");
        $g->add("A", CFGrammar::TYPE_NT);
        $this->assertFalse($g->addRule("A->Aa,bA"));
        $this->assertEquals("Неизвестный элемент 'a'.", $g->getError());
        $this->assertFalse($g->addRule("b->b,b"));
        $this->assertEquals("Элемент 'b' не нетерминал.", $g->getError());
    }


    public function testGoodRule()
    {
        $g = new CFTranslate();
        $this->assertTrue($g->setTrAbc("b"));
        $this->assertTrue($g->add("A", CFGrammar::TYPE_NT));
        $this->assertTrue($g->add("B", CFGrammar::TYPE_NT));
        $this->assertTrue($g->add("a", CFGrammar::TYPE_T));
        $this->assertTrue($g->addRule("A->a,b"));
        $this->assertTrue($g->addRule("A->Aa,Ab"));
        $this->assertTrue($g->addRule("A->Aaa|Aaaa,Ab"));
        $this->assertTrue($g->addRule("A->AB,BA"));
        $this->assertEquals(["A=>a,b", "A=>Aa,Ab", "A=>Aaa,Ab", "A=>Aaaa,Ab", "A=>AB,BA"], $g->getRules());
    }

    public function testNTNotEQ()
    {
        $g = new CFTranslate();
        $this->assertTrue($g->setTrAbc("b"));
        $this->assertTrue($g->add("A", CFGrammar::TYPE_NT));
        $this->assertTrue($g->add("B", CFGrammar::TYPE_NT));
        $this->assertTrue($g->add("a", CFGrammar::TYPE_T));
        $this->assertFalse($g->addRule("A->Aa,b"));
        $this->assertEquals("Нетерминалы не совпадают 'Aa, b'.", $g->getError());
        $this->assertFalse($g->addRule("A->Aa,Bb"));
        $this->assertEquals("Нетерминалы не совпадают 'Aa, Bb'.", $g->getError());
        $this->assertFalse($g->addRule("A->Aa|Bb,Bb"));
        $this->assertEquals("Нетерминалы не совпадают 'Aa, Bb'.", $g->getError());
    }

    public function testDup()
    {
        $g = new CFTranslate();
        $this->assertTrue($g->setTrAbc("b"));
        $this->assertTrue($g->add("A", CFGrammar::TYPE_NT));
        $this->assertTrue($g->add("a", CFGrammar::TYPE_T));
        $this->assertTrue($g->addRule("A->a,b"));
        $this->assertFalse($g->addRule("A->a,b"));
        $this->assertEquals("Правило дублируется 'A->a'.", $g->getError());
    }
}