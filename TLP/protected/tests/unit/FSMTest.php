<?php

class FSMTest extends CTestCase
{
    public function testInputLang()
    {
        $FSM = new FSM();
        $this->assertTrue($FSM->setLanguage("a,b,c,d"));
        $this->assertEquals('a, b, c, d', $FSM->getLanguage());
        $this->assertFalse($FSM->setLanguage("a,b,cc,d"));
        $this->assertEquals("Буква 'cc' слишком длинная.", $FSM->getError());
        $this->assertFalse($FSM->setLanguage("a,b,c,d,c"));
        $this->assertEquals("Элемент 'c' повторяется.", $FSM->getError());
    }

    public function testInputStates()
    {
        $FSM = new FSM();
        $this->assertTrue($FSM->setStates("a,b,cc,d"));
        $this->assertFalse($FSM->setStates("a,b,cc,d,cc"));
        $this->assertEquals("Состоянме 'cc' повторяется.", $FSM->getError());
    }

    public function testInputBegin()
    {
        $FSM = new FSM();
        $this->assertTrue($FSM->setStates("a,b,cc,d"));
        $this->assertFalse($FSM->setBegin("f"));
        $this->assertEquals("Состояние 'f' не найдено.", $FSM->getError());
        $this->assertTrue($FSM->setBegin("a"));
    }

    public function testInputEnd()
    {
        $FSM = new FSM();
        $this->assertTrue($FSM->setStates("a,b,cc,d"));
        $this->assertFalse($FSM->setEnd("f"));
        $this->assertEquals("Состояние 'f' не найдено.", $FSM->getError());
        $this->assertTrue($FSM->setEnd("a,b,cc"));
        $this->assertFalse($FSM->setEnd("a,b,cc,a"));
        $this->assertEquals("Конечное состояние 'a' повторяется.", $FSM->getError());
    }

    public function testRules()
    {
        $FSM = new FSM();
        $FSM->setStates("a,b,d");
        $this->assertFalse($FSM->setRule("a", "b", "c"));
        $this->assertEquals("Буква 'c' не найдена.", $FSM->getError());
        $FSM->setLanguage("c");
        $this->assertTrue($FSM->setRule("a", "b", "c"));
        $this->assertFalse($FSM->setRule("a", "d", "c"));
        $this->assertEquals("Выход 'c' из 'a' повторяется.", $FSM->getError());
    }
}