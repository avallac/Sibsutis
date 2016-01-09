<?php

abstract class LabController extends Controller
{
    public $labNum;
    public $ext = 'BMP';

    public function actionIndex()
    {
        $this->render('index');
    }

    public function getImgName()
    {
        $imgId = (int)$_REQUEST['img'];
        $files = FileManager::getImages($this->ext);
        return $files[$imgId];
    }

    public function actionPre()
    {
        if (isset($_REQUEST['img'])) {
            $fileName = $this->getImgName();
            $bmp = BMPReader::loadFromFile("img/" . $fileName, 0);
            $this->render('index', ['image' => $bmp, 'name' => $fileName, 'id' => (int)$_REQUEST['img']]);
        } else {
            $this->render('index');
        }
    }

    public function actionUpload()
    {
        if (move_uploaded_file($_FILES['userfile']['tmp_name'], 'img/tmp_file.'.$this->ext)) {
            $files = FileManager::getImages($this->ext);
            $this->redirect(
                Yii::app()->createUrl(
                    'lab' . $this->labNum . '/pre',
                    ['img' => array_search('tmp_file.' . $this->ext, $files)]
                )
            );
        } else {
            echo 'При загрузке вознкли проблемы'; exit;
        }
    }

}


