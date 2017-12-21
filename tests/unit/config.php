<?php

namespace tests\unit;

use yii\web\User;
use yii\db\Connection;
use notamedia\locker\LockManager;
use notamedia\locker\config\Bootstrap;

return [
    'id' => 'app-tests',
    'language' => 'ru',
    'basePath' => __DIR__,
    'controllerMap' => [
        'test' => Controller::class,
    ],
    'bootstrap' => [
        Bootstrap::class
    ],
    'components' => [
        'i18n' => [
            'translations' => [
                'notamedia/locker/*' => [
                    'class' => \yii\i18n\PhpMessageSource::className(),
                    'basePath' => '@resources/messages',
                    'sourceLanguage' => 'en',
                    'fileMap' => [
                        'notamedia/locker/labels' => 'labels.php',
                        'notamedia/locker/errors' => 'errors.php',
                    ],
                ]
            ],
        ],
        'user' => [
            'class' => User::class,
            'identityClass' => \tests\unit\User::class,
        ],
        'db' => [
            'class' => Connection::class,
            'dsn' => 'sqlite:tests/_output/db.sq3',
        ],
        'lockManager' => [
            'class' => LockManager::class,
            'lockTime' => [
                'default' => 900
            ],
            'initTimeExpressionValue' => "date('now', '+%s second')",
            'diffExpressionValue' => 'CAST(strftime([[locked_at]], CURRENT_TIMESTAMP) as integer)',
        ],
        'urlManager' => [
            'enablePrettyUrl'     => true,
            'enableStrictParsing' => false,
            'showScriptName'      => false,
            'rules' => [
                'POST test/<id:\d+>' => 'test/update',
                'LOCK,UNLOCK test/<id:\d+>' => 'test/lock',
            ]
        ]
    ]
];