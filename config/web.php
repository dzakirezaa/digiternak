<?php

require __DIR__ . '/../vendor/autoload.php';
use yii\rest\UrlRule;

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
            ],
        ],
        'assetManager' => [
            'forceCopy' => !YII_ENV_PROD,
            'linkAssets' => true,
            'appendTimestamp' => true,
        ],
        'view' => [
            'theme' => [
                'pathMap' => [
                    '@app/views' => [
                        '@app/views',
                        '@vendor/anovsiradj/yii2-theme-mazer/views',
                    ],
                ],
            ],
        ],
        'response' => [
            'format' => yii\web\Response::FORMAT_HTML,
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
                // ... other URL rules
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
