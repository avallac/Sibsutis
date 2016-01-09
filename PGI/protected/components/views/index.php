<table style="width: 0;">
    <?php foreach ($images as $e): ?>
    <tr>
        <?php if ($type == 'BMP'): ?>
        <td>
            <?php if ($e['fileName'] == 'tmp_file.BMP'): ?>
                Последний загруженный файл<br>
            <?php else: ?>
                Название файла: <?= $e['fileName'] ?><br>
            <?php endif; ?>
            Разрешение: <?= $e['mapInfo']['Width'] ?>x<?= $e['mapInfo']['Height'] ?><br>
            Цвет: <?= $e['mapInfo']['BitCount'] ?>
            <?php if ($e['mapInfo']['BitCount'] == 8): ?>Бит<?php else: ?>Бита<?php endif; ?>
            <br>
            <a href="<?= Yii::app()->createUrl('lab' . $lab . '/pre', array('img' => $e['id'])) ?>">Использовать</a>

        </td>
        <td><img src="<?= Yii::app()->createUrl('/img'); ?>/<?= $e['fileName'] ?>" width="100"></td>
        <?php else: ?>
        <td>
            <?php if ($e['fileName'] == 'tmp_file.PCX'): ?>
                Последний загруженный файл<br>
            <?php else: ?>
                Название файла: <?= $e['fileName'] ?><br>
            <?php endif; ?>
            Разрешение: <?= $e['header']['XMax'] - $e['header']['XMin'] + 1 ?>x<?= $e['header']['YMax'] - $e['header']['YMin'] + 1 ?><br>
            Цвет: <?= $e['header']['BitPerPixel'] ?> * <?= $e['header']['Planes'] ?>  Бит
            <br>
            <a href="<?= Yii::app()->createUrl('lab' . $lab . '/pre', array('img' => $e['id'])) ?>">Использовать</a>
        </td>
        <?php endif; ?>
    </tr>
    <?php endforeach; ?>
</table>