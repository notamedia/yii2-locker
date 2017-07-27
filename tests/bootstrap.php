<?php

// This is global bootstrap for autoloading
defined('YII_ENV')   or define('YII_ENV', 'test');
defined('YII_DEBUG') or define('YII_DEBUG', true);

require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');

Yii::setAlias('@resources', __DIR__ . '/../resources/');
Yii::setAlias('@src', __DIR__ . '/../src/');
Yii::setAlias('@tests', __DIR__);