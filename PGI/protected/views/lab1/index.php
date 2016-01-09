<h3> Задание </h3>
<p align="justify">
    Пpеобpазование цветного BMP файла в чеpно-белый (найти в файле   палитpу, пpеобpазовать ее, усpеднив по тpойкам RGB цветов и   записать получившийся файл под новым именем) Вывести основные характеристики BMP изображения.
</p>

<h3> Загрузите изображение или воспользуйтесь готовым:</h3>
<form enctype="multipart/form-data" action="<?= Yii::app()->createUrl('lab1/upload') ?>" method="POST">
    <input type="hidden" name="MAX_FILE_SIZE" value="50000000" />
    Отправить этот файл: <input name="userfile" type="file" />
    <input type="submit" value="Send File" />
</form>
<?php $this->widget('ImageWidget', array('lab' => 1, 'type' => 'BMP')); ?>

<?php if (isset($name)): ?>
<table><tr>
    <td>
        <H2>Исходный файл:</H2>
        <img src="<?= Yii::app()->createUrl('/img'); ?>/<?= $name ?>">
        <H2>Выходной файл:</H2>
        <img src="<?= Yii::app()->createUrl('lab1/img', ['img' => $id]) ?>">
    </td>
    <td width="100%" style="vertical-align:top;">
        <H2>Заголовок исходного файла:</H2>
        <?php var_dump($image->getMapInfo(), $image->getHeader()); ?>
    </td>
</tr></table>
<?php endif; ?>