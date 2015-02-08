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

    public function load($VM)
    {
        $export = array(
            'memory' => $VM->memory->export(),
            'console'=> $VM->console->get(),
            'instructionCounter' => $VM->getInstructionCounter(),
            'acc' => $VM->getAcc(),
            'command' => $VM->getCurrentCommand(),
            'flags' => $VM->getFlags()
        );
        return json_encode($export);
    }

    public function cmd($VM, $params)
    {
        $VM->console->cmd($params['cmd'], 1);
    }

    public function loadProgram($VM, $params)
    {
        $VM->program(json_decode($params['prog']));
    }
}
