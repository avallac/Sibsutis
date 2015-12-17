<?php
$baseUrl = Yii::app()->baseUrl;
$cs = Yii::app()->getClientScript();
$cs->registerScriptFile($baseUrl . '/js/go.js');
$cs->registerScriptFile($baseUrl . '/js/graph.js');
?>

<div id="myDiagram" style="background-color: whitesmoke; border: solid 1px black; width: 100%; height: 400px"></div>
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
                <?php foreach(array('terminal', 'nonterminal', 'target') as $e):?>
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
                <?php $this->widget('SaveFormWidget', array('lab' => 6)); ?>
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


<input name="Lab2Form[graph]" id="Lab2Form_graph" type="hidden" value='
{
"class": "go.GraphLinksModel",
"nodeDataArray": [
    {"text":"q1", "key":-1},
    {"text":"q2", "key":-2},
    {"text":"q3", "key":-3},
    {"text":"q4", "key":-4}
    ],
"linkDataArray": [
    {"from":-1, "to":-2, "text":"a"},
    {"from":-2, "to":-3, "text":"a"},
    {"from":-2, "to":-4, "text":"a"},
    {"from":-1, "to":-4, "text":"a"}
    ]
}'>

<script>
    $(document).ready(function() {
        init();
        myDiagram.model = go.Model.fromJson(document.getElementById("Lab2Form_graph").value);
    });
    $('#lab-form').submit(function(){
        $("#Lab2Form_graph").val(myDiagram.model.toJson());
    });
</script>
