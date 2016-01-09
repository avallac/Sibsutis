<?php

class Lab2Controller extends LabController
{

    public $pageTitle = "Лабораторная 2";
    public $labNum = 2;

    public function actionImg()
    {
        $fileName = $this->getImgName();
        header('Content-type: image/bmp');
        $bmp = BMPReader::loadFromFile("img/" . $fileName);
        $bmp->drawBorder();
        print $bmp->save();
        exit;
    }
}
