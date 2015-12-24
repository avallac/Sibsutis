<?php /* @var $this Controller */ ?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="language" content="en">

	<!-- blueprint CSS framework -->
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/screen.css" media="screen, projection">
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css" media="print">
	<!--[if lt IE 8]>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/ie.css" media="screen, projection">
	<![endif]-->

	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css">
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css">

	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body>

<div class="container" id="page">

	<div id="header">
		<div id="logo"><?php echo CHtml::encode(Yii::app()->name); ?></div>
	</div><!-- header -->

	<div id="mainmenu">
		<?php $this->widget('zii.widgets.CMenu',array(
			'items'=>array(
				array('label'=>'Лабораторная 1', 'url'=>array('/lab1')),
				array('label'=>'Лабораторная 2', 'url'=>array('/lab2')),
				array('label'=>'Лабораторная 3', 'url'=>array('/lab3')),
				array('label'=>'Лабораторная 4', 'url'=>array('/lab4')),
				array('label'=>'Лабораторная 5', 'url'=>array('/lab5')),
				array('label'=>'Курсовая работа', 'url'=>array('/lab6')),

			),
		)); ?>
	</div>
	<div style="margin: 0 50px 0 50px;">
	<br><?php echo $content; ?>
	</div>

	<div class="clear"></div>

	<div id="footer">
		Copyright &copy; <?php echo date('Y'); ?> by Петр Петренко.<br/>
		Just for fun.<br/>
	</div><!-- footer -->

</div><!-- page -->

</body>
</html>
