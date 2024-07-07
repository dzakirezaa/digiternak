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
                [
                    'class' => UrlRule::class,
                    'controller' => 'cage',
                    'pluralize' => false,
                    'extraPatterns' => [
                        // 'GET,HEAD' => 'index',
                        'POST' => 'create',
                        'GET <id:\d+>' => 'view',
                        'PUT <id:\d+>' => 'update',
                        'DELETE <id:\d+>' => 'delete',
                        'GET' => 'get-cages',
                    ],
                ],
                [
                    'class' => UrlRule::class,
                    'controller' => 'user',
                    'pluralize' => false,
                    'extraPatterns' => [
                        'POST register' => 'register',
                        'POST login' => 'login',
                        'POST logout' => 'logout',
                        'POST password-reset' => 'request-password-reset',
                        'GET verify-email/<token>' => 'verify-email',
                        'GET' => 'profile',
                        'PUT' => 'edit-profile',
                    ],
                ],
                [
                    'class' => UrlRule::class,
                    'controller' => 'livestock',
                    'pluralize' => false,
                    'extraPatterns' => [
                        'POST' => 'create',
                        'GET <id:\d+>' => 'view',
                        'PUT <id:\d+>' => 'update',
                        'DELETE <id:\d+>' => 'delete',
                        'GET vid/<vid:[^\/]+>' => 'search',
                        'GET uid/<user_id:\d+>' => 'get-livestocks',
                        'POST upload-image/<id:\d+>' => 'upload-image',
                    ],
                ],
                [
                    'class' => UrlRule::class,
                    'controller' => 'note',
                    'pluralize' => false,
                    'extraPatterns' => [
                        'POST <livestock_id:\d+>' => 'create',
                        'GET <id:\d+>' => 'view',
                        'PUT <id:\d+>' => 'update',
                        'DELETE <id:\d+>' => 'delete',
                        'GET livestock/<livestock_id:\d+>' => 'get-note-by-livestock-id',
                        'GET' => 'index',
                        'POST upload-note/<id:\d+>' => 'upload-documentation',
                    ],
                ],
                [
                    'class' => UrlRule::class,
                    'controller' => 'bcs',
                    'pluralize' => false,
                    'extraPatterns' => [
                        'POST <livestock_id:\d+>' => 'create',
                        'GET <id:\d+>' => 'view',
                        'PUT <id:\d+>' => 'update',
                        'DELETE <id:\d+>' => 'delete',
                        'GET livestock/<livestock_id:\d+>' => 'get-bcs-by-livestock-id',
                        'POST upload-bcs/<id:\d+>' => 'upload-bcs',
                    ],
                ],
                'dashboard/<userId:\d+>' => 'dashboard/user-overview',
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