<div class="form">

    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'lab1-form',
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
        <?php echo CHtml::submitButton('Submit'); ?>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- form -->


Вывод:<br>
<?php $count = 1; ?>
<?php foreach ($output as $line): ?>
<?= $count++ ?>) <?= $line ?><br>
<?php endforeach; ?>

