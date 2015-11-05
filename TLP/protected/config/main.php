<?php

return array(
    'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
    'name'=>'Теория языков трансляции',
    'preload'=>array('log'),
    'import'=>array(
        'application.models.*',
        'application.components.*',
    ),
    'components'=>array(
        'db'=>array(
            'class'=>'system.db.CDbConnection',
            'connectionString' => 'sqlite:'.dirname(__FILE__).'/../data/storage.db',
        ),
        'urlManager'=>array(
            'showScriptName'=>false,
            'urlFormat'=>'path',
            'rules'=>array(
                '<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
            ),
        ),
        'errorHandler'=>array(
            'errorAction'=>'site/error',
        ),
        'log'=>array(
            'class'=>'CLogRouter',
            'routes'=>array(
                array(
                    'class'=>'CFileLogRoute',
                    'levels'=>'error, warning',
                ),
                //array('class'=>'CWebLogRoute'),
            ),
        ),

    )
);
