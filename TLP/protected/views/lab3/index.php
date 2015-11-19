<div class="form">
    <table border="0">
        <tr><td>
                <?php $form=$this->beginWidget('CActiveForm', array(
                    'id'=>'lab-form',
                    'enableClientValidation'=>true,
                    'clientOptions'=>array(
                        'validateOnSubmit'=>true,
                    ),
                )); ?>
                <?php echo CHtml::errorSummary($model); ?>
                <?php foreach(array('check', 'states', 'abcLang', 'abcStack', 'begin', 'beginStack', 'end') as $e):?>
                <div class="row">
                    <?php echo $form->labelEx($model, $e); ?>
                    <?php echo $form->textField($model, $e); ?>
                    <?php echo $form->error($model, $e); ?>
                </div>
                <?php endforeach;?>
                <div class="row">
                    <?php echo $form->labelEx($model, 'rule'); ?>
                    <?php echo $form->textArea($model, 'rule', array('rows'=>6, 'cols'=>50)); ?>
                    <?php echo $form->error($model, 'rule'); ?>
                </div>
                <div class="row buttons">
                    <?php echo CHtml::submitButton('Построить', array('submit' => Yii::app()->createUrl('lab3/index'))); ?>
                </div>
                <?php $this->endWidget(); ?>
            </td><td>
                <?php $this->widget('SaveFormWidget', array('lab' => 3)); ?>
            </td></tr></table>
</div>
<div id="footer"></div>
<?php if (isset($labModel['output'])): ?>
    <table cellspacing="0" class="output">
        <tr><th colspan="2">Вывод:</th></tr>
        <tr><td width="100">Результат</td><td><?= $labModel['output'][0]; ?></td></tr>
        <tr><td width="100">Последовательность</td><td><?= $labModel['output'][1]; ?></td></tr>
    </table>
<?php endif; ?>
</div>