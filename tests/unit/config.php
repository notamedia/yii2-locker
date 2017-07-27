<?php

namespace tests\unit;

use yii\web\User;
use yii\db\Connection;
use notamedia\locker\LockManager;

return [
    'id' => 'app-tests',
    'language' => 'ru',
    'basePath' => __DIR__,
    'controllerMap' => [
        'test' => Controller::class,
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
            'dsn' => 'mysql:host=127.0.0.1;dbname=yii2locker;port=3306',
            'username' => 'root',
            'password' => '1234',
        ],
        'lockManager' => [
            'class' => LockManager::class,
            'lockTime' => [
                'default' => 900
            ]
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