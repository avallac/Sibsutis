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
    <?php foreach(array('length', 'terminal', 'nonterminal', 'target', 'empty') as $e):?>
        <div class="row">
            <?php echo $form->labelEx($model, $e); ?>
            <?php echo $form->textField($model, $e); ?>
            <?php echo $form->error($model, $e); ?>
        </div>
    <?php endforeach;?>
    <div class="row">
        <?php echo $form->labelEx($model,'rule'); ?>
        <?php echo $form->textArea($model,'rule',array('rows'=>6, 'cols'=>50)); ?>
        <?php echo $form->error($model,'rule'); ?>
    </div>
    <div class="row buttons">
        <?php echo CHtml::submitButton('Построить', array('submit' => Yii::app()->createUrl('lab1/index'))); ?>
    </div>
    <?php $this->endWidget(); ?>
</td><td>
        <?php $this->widget('SaveFormWidget', array('lab' => 1)); ?>
</td></tr></table>
</div>
<div id="footer"></div>
<?php if (isset($labModel['rules'])): ?>
    <h2>После оптимизации:</h2>
    <b>Терминалы:</b> {<?= $labModel['term'] ?>}<br>
    <b>Нетерминалы:</b> {<?= $labModel['nonterm'] ?>}<br>
    <b>Целевой символ:</b> <?= $labModel['target'] ?><br>
    <b>Правила:</b><br>
    <?php foreach ($labModel['rules'] as $line): ?>
        &nbsp;&nbsp;<?= $line ?><br>
    <?php endforeach; ?>
    <br><br><br>
<?php endif; ?>
<?php if (isset($labModel['strings'])): ?>
    <table cellspacing="0" class="output">
    <tr><th>Вывод:</th></tr>
    <?php foreach ($labModel['strings'] as $lines): ?>
        <tr><td><?php foreach ($lines as $line): ?>
            <?= $line ?><br>
        <?php endforeach; ?></td></tr>
    <?php endforeach; ?>
    </table>
<?php endif; ?>