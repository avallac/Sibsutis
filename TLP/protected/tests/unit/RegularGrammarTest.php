<?php

/**
 * Created by PhpStorm.
 * User: avallac
 * Date: 16.12.15
 * Time: 22:39
 */
class RegularGrammarTest extends PHPUnit_Framework_TestCase
{
    public function testInputRules()
    {
        $reg = $this->getGrammar();
        $this->assertFalse($reg->addRule("c->b"));
        $this->assertEquals("Неизвестный элемент 'b'.", $reg->getError());
        $this->assertTrue($reg->addRule("c->a"));
        $this->assertTrue($reg->addRule("c->a|d"));
        $this->assertFalse($reg->addRule("a->c|d"));
        $this->assertEquals("Элемент 'a' не нетерминал.", $reg->getError());
        $this->assertFalse($reg->addRule("a-->c|d"));
        $this->assertEquals("Правило 'a-->c|d' не опознано.", $reg->getError());
        $this->assertFalse($reg->addRule("a->a"));
        $this->assertEquals("Правило 'a->a' некоректно.", $reg->getError());
        $this->assertFalse($reg->addRule("c->f"));
        $this->assertEquals("'f': Один нетерминал.", $reg->getError());
        $this->assertFalse($reg->addRule("c->ff"));
        $this->assertEquals("'ff': Больше одного нетерминала.", $reg->getError());
        $this->assertFalse($reg->addRule("c->afa"));
        $this->assertEquals("'afa': Нетерминал находиться не скраю.", $reg->getError());
    }

    public function testConvertRight()
    {
        $reg = $this->getGrammar();
        $this->assertTrue($reg->addRule("c->af"));
        $this->assertTrue($reg->addRule("f->a"));
        $arr = $reg->convertToAutomation();
        $this->assertEquals(
            [
                'rules' =>
                [
                    ['c', 'f', 'a'],
                    ['f', 'Q1', 'a']
                ],
                'states' => 'c, f, Q1',
                'begin' => 'c',
                'end' => 'Q1'
            ],
            $arr
        );
    }

    public function testConvertSplitRight()
    {
        $reg = $this->getGrammar();
        $this->assertTrue($reg->addRule("c->adf"));
        $this->assertTrue($reg->addRule("f->a"));
        $arr = $reg->convertToAutomation();
        $this->assertEquals(
            [
                'rules' =>
                    [
                        ['c', 'Q2', 'a'],
                        ['Q2', 'f', 'd'],
                        ['f', 'Q1', 'a']
                    ],
                'states' => 'c, f, Q1, Q2',
                'begin' => 'c',
                'end' => 'Q1'
            ],
            $arr
        );
    }

    public function testConvertSplitLeft()
    {
        $reg = $this->getGrammar();
        $this->assertTrue($reg->addRule("c->fdt"));
        $this->assertTrue($reg->addRule("f->a"));
        $arr = $reg->convertToAutomation();
        $this->assertEquals(
            [
                'rules' =>
                    [
                        ['f', 'Q2', 'd'],
                        ['Q2', 'c', 't'],
                        ['Q1', 'f', 'a']
                    ],
                'states' => 'c, f, Q1, Q2',
                'begin' => 'Q1',
                'end' => 'c'
            ],
            $arr
        );
    }

    public function testConvertLeft()
    {
        $reg = $this->getGrammar();
        $this->assertTrue($reg->addRule("c->fa"));
        $this->assertTrue($reg->addRule("f->a"));
        $arr = $reg->convertToAutomation();
        $this->assertEquals(
            [
                'rules' =>
                [
                    ['f', 'c', 'a'],
                    ['Q1', 'f', 'a']
                ],
                'states' => 'c, f, Q1',
                'begin' => 'Q1',
                'end' => 'c'
            ],
            $arr
        );
    }

    public function testRG()
    {
        $reg = $this->getGrammar();
        $this->assertTrue($reg->addRule("c->fa"));
        $this->assertTrue($reg->addRule("c->fd"));
        $this->assertFalse($reg->addRule("c->af"));
        $this->assertEquals("'af': леволинейная грамматика", $reg->getError());
    }

    public function testLG()
    {
        $reg = $this->getGrammar();
        $this->assertTrue($reg->addRule("c->af"));
        $this->assertTrue($reg->addRule("c->df"));
        $this->assertFalse($reg->addRule("c->fa"));
        $this->assertEquals("'fa': праволинейная грамматика", $reg->getError());
    }

    public function testGen()
    {
        $reg = $this->getGrammar();
        $this->assertTrue($reg->addRule("c->fa"));
        $this->assertTrue($reg->addRule("f->d"));
        $this->assertTrue($reg->addRule("f->a"));
        $ret = $reg->export(3);
        $this->assertEquals(
            [
                'strings' => [
                    ['c => fa => da'],
                    ['c => fa => aa']
                ],
                'rules' => ['c=>fa', 'f=>d|a'],
                'term' => '',
                'nonterm' => '',
                'target' => 'c'
            ],
            $ret
        );

    }

    private function getGrammar()
    {
        $reg = new RegularGrammar();
        $this->assertTrue($reg->add("a", CFGrammar::TYPE_T));
        $this->assertTrue($reg->add("d", CFGrammar::TYPE_T));
        $this->assertTrue($reg->add("t", CFGrammar::TYPE_T));
        $this->assertTrue($reg->add("c", CFGrammar::TYPE_NT));
        $this->assertTrue($reg->add("f", CFGrammar::TYPE_NT));
        $this->assertTrue($reg->setTarget("c"));
        return $reg;
    }
}