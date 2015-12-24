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
                    <?php echo CHtml::submitButton('Построить', array('submit' => Yii::app()->createUrl('lab6/index'))); ?>
                </div>
                <?php $this->endWidget(); ?>
            </td><td>
                <?php $this->widget('SaveFormWidget', array('lab' => 6)); ?>
            </td></tr></table>
</div>
<div id="myDiagram" style="background-color: whitesmoke; border: solid 1px black; width: 100%; height: 400px"></div>
<div id="footer"></div>
<?php if (isset($labModel['convert'])): ?>
    <h2>Автомат:</h2>
    <b>Список состояний:</b> {<?= $labModel['convert']['states'] ?>}<br>
    <b>Список начальных состояний:</b> {<?= $labModel['convert']['begin'] ?>}<br>
    <b>Список конечных состояний:</b> {<?= $labModel['convert']['end'] ?>}<br>
    <br><br><br>
<?php endif; ?>
<?php if (isset($labModel['export']['strings'])): ?>
    <table cellspacing="0" class="output">
        <tr><th>Вывод:</th></tr>
        <?php foreach ($labModel['export']['strings'] as $lines): ?>
            <tr><td><?php foreach ($lines as $line): ?>
                        <?= $line ?>
<?php preg_match('/.+ => (.*?)$/', $line, $m); ?>
 (<a target="_blank" href="<?= Yii::app()->createUrl('lab2/import', ['import' => json_encode([
'check' => $m[1],
'begin' => $labModel['convert']['begin'],
'end' => $labModel['convert']['end'],
'graph' => GoJSGenerator::generate($labModel['convert']['rules'])
                        ])]) ?>">Проверить строку</a>)
                        <br>
                    <?php endforeach; ?></td></tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>
<input name="Lab2Form[graph]" id="Lab2Form_graph" type="hidden" value='
<?php
    if (isset($labModel['convert'])) {
        print GoJSGenerator::generate($labModel['convert']['rules']);
    }
?>
' >
<?php if (isset($labModel['convert'])): ?>
<script>
    $(document).ready(function() {
        init();
        myDiagram.model = go.Model.fromJson(document.getElementById("Lab2Form_graph").value);
    });
    $('#lab-form').submit(function(){
        $("#Lab2Form_graph").val(myDiagram.model.toJson());
    });
</script>
<?php endif; ?>