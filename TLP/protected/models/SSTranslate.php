<?php

/**
 * Created by PhpStorm.
 * User: avallac
 * Date: 12.12.15
 * Time: 5:55
 */
class SSTranslate extends DeterministicPushdownAutomaton
{
    protected $pattern = '/\(([^,]+),(.),(.)\)=\{\(([^,]+),(.)(.?),(.+)\)\}/';

    public function checkRule($m)
    {
        $str = str_split($m[7]);
        foreach ($str as $e) {
            if (!$this->checkAbc($e, 'abcTr')) {
                return true;
            }
        }
        $parent = parent::checkRule($m);
        return $parent;
    }

    public function printOut($state, $input, $stack, $out)
    {
        if ($out == '') {
            $out = '#';
        }
        return "(" . $state . ", " . $input . ", " . $stack .", " . $out . ")";
    }
}