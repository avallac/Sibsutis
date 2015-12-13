<?php

class DeterministicPushdownAutomatonTest extends PHPUnit_Framework_TestCase
{
    public function testStack()
    {
        $DPDA = new DeterministicPushdownAutomaton();
        $this->assertFalse($DPDA->setStack('a,b,c'));
        $this->assertEquals("Элемент 'a' не найден в алфавите магазина.", $DPDA->getError());
        $this->assertTrue($DPDA->setAbc("a,b,c,d", 'abcStack'));
        $this->assertEquals("#", $DPDA->getStack());
        $this->assertTrue($DPDA->setStack('a,b,c'));
        $this->assertEquals("abc", $DPDA->getStack());
    }

    public function testAddRule()
    {
        $DPDA = new DeterministicPushdownAutomaton();
        $this->assertTrue($DPDA->setAbc(""));
        $rule = '(q0,#,Z)={(q2,Z)}';
        $this->assertFalse($DPDA->addRule($rule));
        $this->assertEquals("Состояние 'q0' не найдено. В правиле номер 1 - ".$rule, $DPDA->getError());
        $this->assertTrue($DPDA->setStates("q0"));
        $this->assertFalse($DPDA->addRule($rule));
        $this->assertEquals("Буква 'Z' не найдена. В правиле номер 1 - ".$rule, $DPDA->getError());
        $this->assertTrue($DPDA->setAbc("Z", 'abcStack'));
        $this->assertFalse($DPDA->addRule($rule));
        $this->assertEquals("Состояние 'q2' не найдено. В правиле номер 1 - ".$rule, $DPDA->getError());
        $this->assertTrue($DPDA->setStates("q0, q1, q2"));
        $this->assertTrue($DPDA->addRule($rule));
        $this->assertFalse($DPDA->setRules("(q0,#,Z,0)={(q2,Z)}"));
        $this->assertEquals("Правило '(q0,#,Z,0)={(q2,Z)}' не опознано.", $DPDA->getError());
        $this->assertTrue($DPDA->setRules("(q1,#,Z)={(q2,Z)}"));
    }

    public function testExport()
    {
        $DPDA = new DeterministicPushdownAutomaton();
        $this->assertTrue($DPDA->setStates("q0, q1, q2"));
        $this->assertTrue($DPDA->setAbc("0,1,d"));
        $this->assertTrue($DPDA->setAbc("Z,0", 'abcStack'));
        $this->assertTrue($DPDA->setBegin("q0"));
        $this->assertTrue($DPDA->setEnd("q0"));
        $this->assertTrue($DPDA->setStack("Z"));
        $this->assertTrue(
            $DPDA->setRules(
                "(q0,#,Z)={(q2,Z)}\n".
                "(q2,d,Z)={(q2,Z)}\n".
                "(q2,0,Z)={(q0,0Z)}\n".
                "(q0,0,Z)={(q0,0Z)}\n".
                "(q0,1,0)={(q1,#)}\n".
                "(q0,0,0)={(q0,00)}\n".
                "(q1,1,0)={(q1,#)}\n".
                "(q1,#,Z)={(q0,#)}"
            )
        );
        $ret = $DPDA->export('dddd0011');
        $check = [
            'output' => [
                "Строчка принята.",
                [
                    "(q0, dddd0011, Z)",
                    "(q2, dddd0011, Z)",
                    "(q2, ddd0011, Z)",
                    "(q2, dd0011, Z)",
                    "(q2, d0011, Z)",
                    "(q2, 0011, Z)",
                    "(q0, 011, 0Z)",
                    "(q0, 11, 00Z)",
                    "(q1, 1, 0Z)",
                    "(q1, #, Z)",
                    "(q0, #, #)"
                ]
            ],
            'lang' => '#, 0, 1, d',
            'states' => 'q0, q1, q2',
        ];
        $this->assertEquals($check, $ret);

        $this->assertTrue($DPDA->setStack("Z"));
        $ret = $DPDA->export('d0d011');
        $check = [
            'output' => [
                "Правил перехода из состояние 'q0' (строка: 'd', стэк: '0') не обнаружено.",
                [
                    "(q0, d0d011, Z)",
                    "(q2, d0d011, Z)",
                    "(q2, 0d011, Z)",
                    "(q0, d011, 0Z)"
                ]
            ],
            'lang' => '#, 0, 1, d',
            'states' => 'q0, q1, q2',
        ];
        $this->assertEquals($check, $ret);

        $this->assertTrue($DPDA->setStack("Z"));
        $this->assertTrue($DPDA->setEnd("q1"));
        $ret = $DPDA->export('d01');
        $check = [
            'output' => [
                "Конечное состояние достигнуто не было",
                [
                    "(q0, d01, Z)",
                    "(q2, d01, Z)",
                    "(q2, 01, Z)",
                    "(q0, 1, 0Z)",
                    "(q1, #, Z)",
                    "(q0, #, #)"
                ]
            ],
            'lang' => '#, 0, 1, d',
            'states' => 'q0, q1, q2',
        ];
        $this->assertEquals($check, $ret);

        $this->assertTrue($DPDA->setRules("(q0,#,Z)={(q0,Z)}"));
        $this->assertTrue($DPDA->setStack("Z"));
        $ret = $DPDA->export('d01');
        $check = [
            'output' => [
                "Обнаружен цикл.",
                [
                    "(q0, d01, Z)",
                    "(q0, d01, Z)"
                ]
            ],
            'lang' => '#, 0, 1, d',
            'states' => 'q0, q1, q2',
        ];
        $this->assertEquals($check, $ret);
    }
}
