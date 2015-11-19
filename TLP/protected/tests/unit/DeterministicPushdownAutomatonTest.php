<?php

class DeterministicPushdownAutomatonTest extends CTestCase
{
    public function testStack()
    {
        $DPDA = new DeterministicPushdownAutomaton();
        $this->assertFalse($DPDA->setStack('a,b,c'));
        $this->assertEquals("Элемент 'a' не найден в алфавите магазина.", $DPDA->getError());
        $this->assertTrue($DPDA->setAbc("a,b,c,d", 'abcStack'));
        $this->assertTrue($DPDA->setStack('a,b,c'));
    }
}
