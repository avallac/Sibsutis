<?php

class GoJSParser
{
    private $g;

    public function __construct($str)
    {
        $this->g = json_decode($str, true);
    }

    public function getLang()
    {
        $tmp = array();
        $ret = array();
        foreach ($this->g['linkDataArray'] as $e) {
            if (!isset($tmp[$e['text']])) {
                $tmp[$e['text']] = 1;
                $ret[] = $e['text'];
            }
        }
        return implode(", ", $ret);
    }

    public function getStates()
    {
        $ret = array();
        foreach ($this->g['nodeDataArray'] as $e) {
            $ret[] = $e['text'];
        }
        return implode(", ", $ret);
    }

    public function getRules()
    {
        $nodes = array();
        $ret = array();
        foreach ($this->g['nodeDataArray'] as $e) {
            $nodes[$e['key']] = $e['text'];
        }
        foreach ($this->g['linkDataArray'] as $e) {
            $ret[] = array($nodes[$e['from']], $nodes[$e['to']], $e['text']);
        }
        return $ret;
    }
}