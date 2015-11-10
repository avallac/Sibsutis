<?php
$baseUrl = Yii::app()->baseUrl;
$cs = Yii::app()->getClientScript();
$cs->registerScriptFile($baseUrl . '/js/go.js');
$cs->registerScriptFile($baseUrl . '/js/graph.js');
?>

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
                    <?php echo $form->labelEx($model,'check'); ?>
                    <?php echo $form->textField($model,'check'); ?>
                    <?php echo $form->error($model,'check'); ?>
                </div>
                <div class="row">
                    <?php echo $form->labelEx($model,'begin'); ?>
                    <?php echo $form->textField($model,'begin'); ?>
                    <?php echo $form->error($model,'begin'); ?>
                </div>
                <div class="row">
                    <?php echo $form->labelEx($model,'end'); ?>
                    <?php echo $form->textField($model,'end'); ?>
                    <?php echo $form->error($model,'end'); ?>
                </div>
                <div class="row buttons">
                    <?php echo $form->hiddenField($model, 'graph'); ?>
                    <?php echo CHtml::submitButton('Проверить', array('submit' => Yii::app()->createUrl('lab2/index'))); ?>
                </div>
                <?php $this->endWidget(); ?>
            </td><td>
                <?php $this->widget('SaveFormWidget', array('lab' => 2)); ?>
            </td></tr>
    </table>
    <div id="myDiagram" style="background-color: whitesmoke; border: solid 1px black; width: 100%; height: 400px"></div>
    <div id="footer"></div>
    <?php if (isset($FSMModel['lang'])): ?>
        <h2> Информация:</h2>
        <b> Алфавит:</b> {<?= $FSMModel['lang'] ?>}<br>
        <b> Список состояний:</b> {<?= $FSMModel['states'] ?>}<br>
        <br><br><br>
    <?php endif; ?>
    <?php if (isset($FSMModel['output'])): ?>
        <table cellspacing="0" class="output">
            <tr><th colspan="2">Вывод:</th></tr>
            <tr><td width="100">Результат</td><td><?= $FSMModel['output'][0]; ?></td></tr>
            <tr><td width="100">Последовательность</td><td><?= $FSMModel['output'][1]; ?></td></tr>
        </table>
    <?php endif; ?>
</div>

<script>
    $(document).ready(function() {
        init();
        myDiagram.model = go.Model.fromJson(document.getElementById("Lab2Form_graph").value);
    });
    $('#lab-form').submit(function(){
        $("#Lab2Form_graph").val(myDiagram.model.toJson());
    });
</script>