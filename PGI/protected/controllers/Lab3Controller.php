<?php

class Lab3Controller extends LabController
{

    public $pageTitle = "Лабораторная 3";
    public $labNum = 3;

    public function actionImg()
    {
        $fileName = $this->getImgName();
        header('Content-type: image/png');
        $bmp = BMPReader::loadFromFile("img/" . $fileName);
        $bmp->show();
        exit;
    }
}
