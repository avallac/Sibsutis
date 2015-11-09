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
    <div class="row">
        <?php echo $form->labelEx($model,'terminal'); ?>
        <?php echo $form->textField($model,'terminal'); ?>
        <?php echo $form->error($model,'terminal'); ?>
    </div>
    <div class="row">
        <?php echo $form->labelEx($model,'nonterminal'); ?>
        <?php echo $form->textField($model,'nonterminal'); ?>
        <?php echo $form->error($model,'nonterminal'); ?>
    </div>
    <div class="row">
        <?php echo $form->labelEx($model,'target'); ?>
        <?php echo $form->textField($model,'target'); ?>
        <?php echo $form->error($model,'target'); ?>
    </div>
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
<?php if (isset($gModel['rules'])): ?>
    <h2>После оптимизации:</h2>
    <b>Терминалы:</b> {<?= $gModel['term'] ?>}<br>
    <b>Нетерминалы:</b> {<?= $gModel['nonterm'] ?>}<br>
    <b>Целевой символ:</b> <?= $gModel['target'] ?><br>
    <b>Правила:</b><br>
    <?php foreach ($gModel['rules'] as $line): ?>
        &nbsp;&nbsp;<?= $line ?><br>
    <?php endforeach; ?>
    <br><br><br>
<?php endif; ?>
<?php if (isset($gModel['strings'])): ?>
    <table cellspacing="0" class="output">
    <tr><th>Вывод:</th></tr>
    <?php foreach ($gModel['strings'] as $lines): ?>
        <tr><td><?php foreach ($lines as $line): ?>
            <?= $line ?><br>
        <?php endforeach; ?></td></tr>
    <?php endforeach; ?>
    </table>
<?php endif; ?>