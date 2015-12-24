<?php


class GoJSGenerator
{
    private $q = [];
    private $next = 0;

    public static function generate($arr)
    {
        $g = new self();
        return $g->export($arr);
    }

    private function export($arr)
    {
        $out = [
            'class' => 'go.GraphLinksModel',
            'nodeDataArray' => [],
            'linkDataArray' => []
        ];
        foreach ($arr as $rule) {
            $from = $this->getQID($rule[0]);
            $to = $this->getQID($rule[1]);
            $out['linkDataArray'][]= ['from' => $from, 'to' => $to, 'text'=> $rule[2]];
        }
        foreach ($this->q as $q => $id) {
            $out['nodeDataArray'][]= ['key' => $id, 'text'=> $q];
        }
        return json_encode($out);
    }

    private function getQID($q)
    {
        if (isset($this->q[$q])) {
            return $this->q[$q];
        } else {
            $this->next++;
            $this->q[$q] = $this->next;
            return $this->next;
        }
    }
}