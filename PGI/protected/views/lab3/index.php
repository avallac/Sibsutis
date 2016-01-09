<h3> Задание </h3>
<p align="justify">
    Вывести на экpан 16-цветный, 256-цветный и True Color BMP файл с помощью библиотеки wingraph.h
</p>

<h3> Загрузите изображение или воспользуйтесь готовым:</h3>
<form enctype="multipart/form-data" action="<?= Yii::app()->createUrl('lab3/upload') ?>" method="POST">
    <input type="hidden" name="MAX_FILE_SIZE" value="50000000" />
    Отправить этот файл: <input name="userfile" type="file" />
    <input type="submit" value="Send File" />
</form>

<?php $this->widget('ImageWidget', array('lab' => 3, 'type' => 'BMP')); ?>

<?php if (isset($name)): ?>
<table><tr>
    <td>
        <H2>Исходный файл:</H2>
        <img src="<?= Yii::app()->createUrl('/img'); ?>/<?= $name ?>">
        <H2>Выходной файл:</H2>
        <img src="<?= Yii::app()->createUrl('lab3/img', ['img' => $id]) ?>">
    </td>
</tr></table>
<?php endif; ?>