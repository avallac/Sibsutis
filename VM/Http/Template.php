<?php

namespace Http;

class Template
{

    public function addVariant($name, $var)
    {
        $this->$name = $var;
    }

    public function templateWOLayout($TplFile)
    {
        ob_start();
        require('Tmpl' . DIRECTORY_SEPARATOR . $TplFile . '.php');
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }
}
