<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'save-form',
    'enableClientValidation'=>true,
    'clientOptions'=>array(
        'validateOnSubmit'=>true,
    ),
)); ?>
<div class="row">
    <?php echo $form->labelEx($saveModel,'filename'); ?>
    <?php echo $form->textField($saveModel,'filename'); ?>
    <?php echo $form->error($saveModel,'filename'); ?>
    <?php echo $form->hiddenField($saveModel, 'form'); ?>
</div>
<div class="row buttons">
    <?php echo CHtml::submitButton('Сохранить', array('submit' => Yii::app()->createUrl('lab' . $lab . '/save'))); ?>
</div>
<script>
    $('#save-form').submit(function(e){
        var data = JSON.stringify($("#lab-form").serializeArray().reduce(function(a, x) {
            var str = x.name;
            var found = str.match(/\[(.+)\]/);
            a[found[1]] = x.value;
            return a;
        }, {}));
        $("#SaveForm_form").val(data);
    });
</script>
<?php $this->endWidget(); ?>
<b>Сохраненные тесты:</b><br>
<?php foreach ($cases as $e): ?>
    <a href="<?= Yii::app()->createUrl('lab' . $lab . '/load', array('id' => $e->id)) ?>"><?= $e->name ?></a>
    (<a href="<?= Yii::app()->createUrl('lab' . $lab . '/delete', array('id' => $e->id)) ?>">Удалить</a>)<br>
<?php endforeach; ?>

