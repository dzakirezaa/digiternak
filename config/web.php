<?php

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

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
            'transport' => [
                'class' => 'Symfony\Component\Mailer\Transport',
                'dsn' => 'smtp://digiternak@gmail.com:ltfs%20ducm%20mbaa%20siwj@smtp.gmail.com:587',
            ],
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
                'user/register' => 'user/register',
                'user/login' => 'user/login',
                'user/logout' => 'user/logout',
                'user/' => 'user/handle-request',
                'user/request-password-reset' => 'user/request-password-reset',
                'user/verify-email/<token>' => 'user/verify-email',
                'cage/' => 'cage/handle-request',
                'cage/<id:\d+>' => 'cage/handle-request',
                'livestock/' => 'livestock/handle-request',
                'livestock/<id:\d+>' => 'livestock/handle-request',
                'livestock/vid/<vid:[^\/]+>' => 'livestock/search',
                'livestock/uid/<user_id:\d+>' => 'livestock/get-livestocks',
                'livestock/upload-image/<id:\d+>' => 'livestock/upload-image',
                'note/<id:\d+>' => 'note/handle-request',
                'note/create/<livestock_id:\d+>' => 'note/create',
                'note/livestock/<livestock_id:\d+>' => 'note/get-note-by-livestock-id',
                'note/upload-note/<id:\d+>' => 'note/upload-documentation',
                'dashboard/<userId:\d+>' => 'dashboard/user-overview',
                'bcs/create/<livestock_id:\d+>' => 'bcs/create',
                'bcs/<id:\d+>' => 'bcs/handle-request',
                'bcs/livestock/<livestock_id:\d+>' => 'bcs/get-bcs-by-livestock-id',
                'bcs/upload-bcs/<id:\d+>' => 'bcs/upload-bcs',
            ],
        ],     
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