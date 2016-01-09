<?php

class ImageWidget extends CWidget
{
    public $lab;
    public $type;

    public function run()
    {
        $img = [];
        $files = FileManager::getImages($this->type);
        foreach ($files as $id => $fileName) {
            if ($this->type == 'BMP') {
                $bmp = BMPReader::loadFromFile("img/" . $fileName, 0);
                $img[] = ['fileName' => $fileName, 'id' => $id, 'mapInfo' => $bmp->getMapInfo()];
            } else {
                $pcx = new PCX();
                $pcx->load("img/" . $fileName, 0);
                $img[] = ['fileName' => $fileName, 'id' => $id, 'header' => $pcx->getHeader()];
            }
        }
        $this->render('index', ['images' => $img, 'lab' => $this->lab, 'type' => $this->type]);
    }
}
