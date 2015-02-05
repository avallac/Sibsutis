<?php

namespace Http;

class MainController
{
    public function index($VM)
    {
        $template = new Template();
        $template->addVariant('memory', $VM->memory->export());
        return $template->templateWOLayout('MainWindow');
    }

    public function changeMemory($VM, $params)
    {
        $cel = substr($params['id'], 1);
        $VM->memory->set($cel, $params['value']);
        $val = 0;
        $VM->memory->get($cel, $val);
        return $val;
    }

    public function reset($VM, $params)
    {
        $VM->init();
    }

    public function e404()
    {
        return "404 Not Found";
    }
}
