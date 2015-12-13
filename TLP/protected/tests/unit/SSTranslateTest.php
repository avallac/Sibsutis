<?php

class SSTranslateTest extends PHPUnit_Framework_TestCase
{
    public function testAddRule()
    {
        $DPDA = new SSTranslate();
        $this->assertTrue($DPDA->setAbc(""));
        $rule = '(q0,#,Z)={(q2,Z,a)}';
        $this->assertFalse($DPDA->addRule($rule));
        $this->assertEquals("Буква 'a' не найдена. В правиле номер 1 - ".$rule, $DPDA->getError());
        $this->assertTrue($DPDA->setAbc("a", 'abcTr'));
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
        $this->assertTrue($DPDA->setRules("(q1,#,Z)={(q2,Z,a)}"));
    }

    public function testTranslate()
    {
        $DPDA = new SSTranslate();
        $this->assertTrue($DPDA->setStates("q0, q1, q2"));
        $this->assertTrue($DPDA->setAbc("0,1"));
        $this->assertTrue($DPDA->setAbc("Z,0", 'abcStack'));
        $this->assertTrue($DPDA->setAbc("a,b", 'abcTr'));
        $this->assertTrue($DPDA->setBegin("q0"));
        $this->assertTrue($DPDA->setEnd("q2"));
        $this->assertTrue($DPDA->setStack("Z"));
        $this->assertTrue(
            $DPDA->setRules(
                "(q0,0,Z)={(q0,0Z,aa)}\n" .
                "(q0,0,0)={(q0,00,aa)}\n" .
                "(q0,1,0)={(q1,#,bb)}\n" .
                "(q1,1,0)={(q1,#,bb)}\n" .
                "(q1,#,Z)={(q2,#,#)}"
            )
        );
        $this->assertEquals("", $DPDA->getError());
        $ret = $DPDA->export('0011');
        $check = [
            'output' => [
                "Строчка принята.",
                [
                    "(q0, 0011, Z, #)",
                    "(q0, 011, 0Z, aa)",
                    "(q0, 11, 00Z, aaaa)",
                    "(q1, 1, 0Z, aaaabb)",
                    "(q1, #, Z, aaaabbbb)",
                    "(q2, #, #, aaaabbbb)",
                ]
            ],
            'lang' => '#, 0, 1',
            'states' => 'q0, q1, q2',
        ];
        $this->assertEquals($check, $ret);
    }
}