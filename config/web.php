<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            'cookieValidationKey' => 'AsmXcRvr0JV5YEbMzsMuZ6yhpA0w7MqP',
            'parsers' => [
                'enableCsrfValidation' => YII_ENV_PROD,
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'response' => [
            'format' => yii\web\Response::FORMAT_JSON,
            'charset' => 'UTF-8',
        ],
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
                'person/view/<id:\d+>' => 'person/view',
                'person/update/<id:\d+>' => 'person/update',
                'person/delete/<id:\d+>' => 'person/delete',
            ],
        ],
        // 'modules' => [
        //     'api' => [
        //         'class' => '\app\modules\api\Module',
        //         'as authenticator' => [
        //             'class' => 'yii\filters\auth\HttpBearerAuth',
        //             'only' => ['profile'],  // Daftar action yang memerlukan otentikasi
        //         ],
        //         'as access' => [
        //             'class' => 'yii\filters\AccessControl',
        //             'only' => ['profile'],  // Daftar action yang memerlukan otentikasi
        //             'rules' => [
        //                 [
        //                     'actions' => ['profile'],
        //                     'allow' => true,
        //                     'roles' => ['@'],  // Hanya user yang sudah login yang dapat mengakses action ini
        //                 ],
        //             ],
        //         ],
        //     ],
        // ],
        'formatter' => [
            'dateFormat' => 'Y-m-d',
            'datetimeFormat' => 'Y-m-d H:i:s',
            'timeFormat' => 'H:i:s',
            'locale' => 'id_ID', 
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
