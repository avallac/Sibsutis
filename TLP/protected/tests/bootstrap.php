<?php

// change the following paths if necessary
$yiit=dirname(__FILE__).'/../../../libs/yii/framework/yiit.php';
$config=dirname(__FILE__).'/../config/main.php';
require_once($yiit);
Yii::createWebApplication($config);
