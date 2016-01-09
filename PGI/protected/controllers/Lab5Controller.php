<?php

class Lab5Controller extends LabController
{

    public $pageTitle = "Лабораторная 5";
    public $labNum = 5;
    public $ext = 'PCX';

    public function actionImg()
    {
        $fileName = $this->getImgName();
        header('Content-type: image/png');
        $pcx = new PCX();
        $pcx->load("img/" . $fileName);
        $pcx->show();
        exit;
    }

    public function actionPre()
    {
        if (isset($_REQUEST['img'])) {
            $fileName = $this->getImgName();
            $pcx = new PCX();
            $pcx->load("img/" . $fileName, 0);
            $this->render('index', ['image' => $pcx, 'name' => $fileName, 'id' => (int)$_REQUEST['img']]);
        } else {
            $this->render('index');
        }
    }
}
