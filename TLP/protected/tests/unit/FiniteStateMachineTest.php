<?php

class FiniteStateMachineTest extends PHPUnit_Framework_TestCase
{
    public function testInputLang()
    {
        $FSM = new FiniteStateMachine();
        $this->assertTrue($FSM->setAbc("a,b,c,d"));
        $this->assertEquals('#, a, b, c, d', $FSM->getLanguage());
        $this->assertFalse($FSM->setAbc("a,b,cc,d"));
        $this->assertEquals("Буква 'cc' слишком длинная.", $FSM->getError());
        $this->assertFalse($FSM->setAbc("a,b,c,d,c"));
        $this->assertEquals("Элемент 'c' повторяется.", $FSM->getError());
    }

    public function testInputStates()
    {
        $FSM = new FiniteStateMachine();
        $this->assertTrue($FSM->setStates("a,b,cc,d"));
        $this->assertFalse($FSM->setStates("a,b,cc,d,cc"));
        $this->assertEquals("Состоянме 'cc' повторяется.", $FSM->getError());
    }

    public function testInputBegin()
    {
        $FSM = new FiniteStateMachine();
        $this->assertTrue($FSM->setStates("a,b,cc,d"));
        $this->assertFalse($FSM->setBegin("f"));
        $this->assertEquals("Состояние 'f' не найдено.", $FSM->getError());
        $this->assertTrue($FSM->setBegin("a"));
    }

    public function testInputEnd()
    {
        $FSM = new FiniteStateMachine();
        $this->assertTrue($FSM->setStates("a,b,cc,d"));
        $this->assertFalse($FSM->setEnd("f"));
        $this->assertEquals("Состояние 'f' не найдено.", $FSM->getError());
        $this->assertTrue($FSM->setEnd("a,b,cc"));
        $this->assertFalse($FSM->setEnd("a,b,cc,a"));
        $this->assertEquals("Конечное состояние 'a' повторяется.", $FSM->getError());
    }

    public function testRules()
    {
        $FSM = new FiniteStateMachine();
        $this->assertTrue($FSM->setStates("a,b,d"));
        $this->assertFalse($FSM->setRule("a", "b", "c"));
        $this->assertEquals("Буква 'c' не найдена.", $FSM->getError());
        $this->assertTrue($FSM->setAbc("c"));
        $this->assertTrue($FSM->setRule("a", "b", "c"));
        $this->assertFalse($FSM->setRule("a", "d", "c"));
        $this->assertEquals("Выход 'c' из 'a' повторяется.", $FSM->getError());
    }

    public function testSetRules()
    {
        $FSM = new FiniteStateMachine();
        $this->assertFalse($FSM->setRules([['q0','q111','a']]));
        $this->assertEquals("Состояние 'q0' не найдено.", $FSM->getError());
    }

    public function testExport()
    {
        $FSM = new FiniteStateMachine();
        $this->assertTrue($FSM->setAbc("a,b,c,d"));
        $this->assertTrue($FSM->setStates("q0,q1,q2"));
        $this->assertTrue($FSM->setBegin("q0"));
        $this->assertTrue($FSM->setEnd("q2"));
        $this->assertTrue($FSM->setRules(
            array(
                array('q0','q1','a'),
                array('q1','q2','b'),
            )
        ));
        $export = $FSM->export('ab');
        $this->assertEquals('Строчка принята.', $export['output'][0]);
        $this->assertEquals('(q0, ab) ├─ (q1, b) ├─ (q2, #)', $export['output'][1]);
        $this->assertEquals('#, a, b, c, d', $export['lang']);
        $this->assertEquals('q0, q1, q2', $export['states']);
        $export = $FSM->export('aab');
        $this->assertEquals("Правила перехода 'a' из состояние 'q1' не обнаружено.", $export['output'][0]);
        $this->assertEquals('(q0, aab) ├─ (q1, ab)', $export['output'][1]);
        $this->assertTrue($FSM->setRules(
            array(
                array('q0','q1','a'),
                array('q1','q1','a'),
                array('q1','q2','b'),
            )
        ));
        $export = $FSM->export('aab');
        $this->assertEquals('Строчка принята.', $export['output'][0]);
        $export = $FSM->export('aa');
        $this->assertEquals('Конечное состояние достигнуто не было', $export['output'][0]);
    }
}