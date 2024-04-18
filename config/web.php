<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'defaultRoute' => 'site/index',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            'cookieValidationKey' => 'AsmXcRvr0JV5YEbMzsMuZ6yhpA0w7MqP',
            'enableCsrfValidation' => YII_ENV_PROD,
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        // 'response' => [
        //     'format' => yii\web\Response::FORMAT_JSON,
        //     'charset' => 'UTF-8',
        // ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'user/request-password-reset' => 'user/request-password-reset',
                'person/view/<id:\d+>' => 'person/view',
                'person/update/<id:\d+>' => 'person/update',
                'person/delete/<id:\d+>' => 'person/delete',
                'cage/view/<id:\d+>' => 'cage/view',
                'cage/update/<id:\d+>' => 'cage/update',
                'cage/delete/<id:\d+>' => 'cage/delete',
                'cage/get-cages' => 'cage/get-cages',
                'livestock/view/<id:\d+>' => 'livestock/view',
                'livestock/update/<id:\d+>' => 'livestock/update',
                'livestock/delete/<id:\d+>' => 'livestock/delete',
                'livestock/search/<vid:[^\/]+>' => 'livestock/search',
                'livestock/upload-image/<id:\d+>' => 'livestock/upload-image',
                'note/view/<id:\d+>' => 'note/view',
                'note/update/<id:\d+>' => 'note/update',
                'note/delete/<id:\d+>' => 'note/delete',
                'note/upload-documentation/<id:\d+>' => 'note/upload-documentation',
                'dashboard/dashboard/<userId:\d+>' => 'dashboard/dashboard',
            ],
        ],
        // 'formatter' => [
        //     'dateFormat' => 'Y-m-d',
        //     'datetimeFormat' => 'Y-m-d H:i:s',
        //     'timeFormat' => 'H:i:s',
        //     'locale' => 'id_ID', 
        //     'timeZone' => 'Asia/Jakarta',
        // ],        
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'debug';
    if (class_exists('yii\debug\Module')) {
        $config['modules']['debug'] = [
            'class' => 'yii\debug\Module',
        ];
    }

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;