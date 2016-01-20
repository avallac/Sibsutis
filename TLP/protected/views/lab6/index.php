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
                <?php foreach(array('length', 'terminal', 'nonterminal', 'target') as $e):?>
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
    <table class="output">
        <tr><th colspan="<?= (sizeof($labModel['convert']['term']) + 1) ?>">Недетерминироанный автомат:</th></tr>
        <tr>
            <td></td>
            <?php foreach ($labModel['convert']['term'] as $term): ?>
                <td><b><?= $term ?></b></td>
            <?php endforeach; ?>
        </tr>
        <?php foreach ($labModel['convert']['ndt'] as $name =>  $lines): ?>
        <tr>
            <td><?= $name ?></td>
            <?php foreach ($labModel['convert']['term'] as $term): ?>
                <td> &nbsp;
                    <?php if (isset($labModel['convert']['ndt'][$name][$term])):  ?>
                    <?= implode($labModel['convert']['ndt'][$name][$term], ',') ?>
                    <?php endif; ?>
                </td>
            <?php endforeach; ?>
        </tr>
        <?php endforeach; ?>
    </table><br><br>
    <table class="output">
        <tr><th colspan="<?= (sizeof($labModel['convert']['term']) + 1) ?>">Детерминированный автомат:</th></tr>
        <tr>
            <td></td>
            <?php foreach ($labModel['convert']['term'] as $term): ?>
                <td><b><?= $term ?></b></td>
            <?php endforeach; ?>
        </tr>
        <?php foreach ($labModel['convert']['dt'] as $name =>  $lines): ?>
            <tr>
                <td <?php if (isset($labModel['convert']['dt'][$name]['good'])): ?> style="background-color: #cee2d3"<?php endif; ?>>
                    <?= $name ?>
                </td>
                <?php foreach ($labModel['convert']['term'] as $term): ?>
                    <td <?php if (isset($labModel['convert']['dt'][$name]['good'])): ?> style="background-color: #cee2d3"<?php endif; ?>> &nbsp;
                        <?php if (isset($labModel['convert']['dt'][$name]['terms'][$term])):  ?>
                            <?= implode($labModel['convert']['dt'][$name]['terms'][$term], ',') ?>
                        <?php endif; ?>
                    </td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

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

<form action="<?= Yii::app()->createUrl('lab2/index') ?>" method="post">
    <input name="Lab2Form[check]" type="hidden" value="<?= $m[1] ?>">
    <input name="Lab2Form[begin]" type="hidden" value="<?= $labModel['convert']['begin'] ?>">
    <input name="Lab2Form[end]" type="hidden" value="<?= $labModel['convert']['end'] ?>">
    <input name="Lab2Form[graph]" type="hidden" value='<?= GoJSGenerator::generate($labModel['convert']['rules']) ?>'>
    <input type="submit" value="Проверить">
</form>

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
</script>
<?php endif; ?>