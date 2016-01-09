<h3> Задание </h3>
<p align="justify">
    Вывести на экpан 256-цветный PCX файл с помощью библиотеки wingraph.h
</p>


<h3> Загрузите изображение или воспользуйтесь готовым:</h3>
<form enctype="multipart/form-data" action="<?= Yii::app()->createUrl('lab5/upload') ?>" method="POST">
    <input type="hidden" name="MAX_FILE_SIZE" value="50000000" />
    Отправить этот файл: <input name="userfile" type="file" />
    <input type="submit" value="Send File" />
</form>

<?php $this->widget('ImageWidget', array('lab' => 5, 'type' => 'PCX')); ?>

<?php if (isset($name)): ?>
<table><tr>
    <td>
        <H2>Файл:</H2>
        <img src="<?= Yii::app()->createUrl('lab5/img', ['img' => $id]) ?>">
    </td>
    <td>
        <H2>Заголовок файла:</H2>
        <?php var_dump($image->getHeader()); ?>
    </td>
</tr></table>
<?php endif; ?>