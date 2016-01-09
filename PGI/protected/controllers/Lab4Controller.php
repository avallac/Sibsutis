<?php

class Lab4Controller extends LabController
{

    public $pageTitle = "Лабораторная 4";
    public $labNum = 4;

    public function actionImg()
    {
        $fileName = $this->getImgName();
        header('Content-type: image/bmp');
        $bmp1 = BMPReader::loadFromFile("img/" . $fileName);
        $bmp2 = BMPReader::loadFromFile("img/logo.bmp");
        $bmp1->add($bmp2, rand(0, $bmp1->getMapInfo()['Height']), rand(0, $bmp1->getMapInfo()['Width']));
        print $bmp1->save();
        exit;
    }
}
