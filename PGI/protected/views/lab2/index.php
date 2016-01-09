<h3> Задание </h3>
<p align="justify">
    Пpебpазовать BMP файл, создав вокpуг него pамку из пикселей случайного цвета.Шиpина рамки - 15 пикселей   (Работа с pастpовыми данными)
</p>

<h3> Загрузите изображение или воспользуйтесь готовым:</h3>
<form enctype="multipart/form-data" action="<?= Yii::app()->createUrl('lab2/upload') ?>" method="POST">
    <input type="hidden" name="MAX_FILE_SIZE" value="50000000" />
    Отправить этот файл: <input name="userfile" type="file" />
    <input type="submit" value="Send File" />
</form>

<?php $this->widget('ImageWidget', array('lab' => 2, 'type' => 'BMP')); ?>

<?php if (isset($name)): ?>
<table><tr>
    <td>
        <H2>Исходный файл:</H2>
        <img src="<?= Yii::app()->createUrl('/img'); ?>/<?= $name ?>">
        <H2>Выходной файл:</H2>
        <img src="<?= Yii::app()->createUrl('lab2/img', ['img' => $id]) ?>">
    </td>
</tr></table>
<?php endif; ?>