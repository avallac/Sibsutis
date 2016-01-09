<?php

class Lab1Controller extends LabController
{

    public $pageTitle = "Лабораторная 1";
    public $labNum = 1;

    public function actionImg()
    {
        $fileName = $this->getImgName();
        header('Content-type: image/bmp');
        $bmp = BMPReader::loadFromFile("img/" . $fileName);
        $bmp->convertToGrey();
        print $bmp->save();
        exit;
    }
}
