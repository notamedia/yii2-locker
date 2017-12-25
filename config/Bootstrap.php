<?php

namespace notamedia\locker\config;

use yii\base\BootstrapInterface;
use notamedia\locker\Lock;
use notamedia\locker\LockInterface;

/**
 * A bootstrapping component as well as an initializator.
 * The following code shows how you can use this class as a bootstrapping component.
 *
 * ```php
 * "extra": {
 *     "bootstrap": "notamedia\\locker\\config\\I18nBootstrap"
 * }
 * ```
 */
class Bootstrap implements BootstrapInterface
{
    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        if (!isset($app->i18n->translations['notamedia*']) &&
            !isset($app->i18n->translations['notamedia/locker/*']) &&
            !isset($app->i18n->translations['notamedia-locker'])
        ) {
            $app->i18n->translations['notamedia/locker/*'] = [
                'class' => \yii\i18n\PhpMessageSource::class,
                'sourceLanguage' => 'en',
                'basePath' => '@vendor/yii2-locker/resources/messages',
                'fileMap' => [
                    'notamedia/locker/labels' => 'labels.php',
                    'notamedia/locker/errors' => 'errors.php',
                ]
            ];
        }

        if (!\Yii::$container->has(LockInterface::class)) {
            \Yii::$container->set(
                LockInterface::class,
                new Lock()
            );
        }
    }
}
