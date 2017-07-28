<?php

namespace tests\unit;

use yii\rest\UpdateAction;
use notamedia\locker\rest\LockControlFilter;
use notamedia\locker\rest\UnlockAction;
use notamedia\locker\rest\LockAction;

/**
 * @inheritdoc
 */
class Controller extends \yii\rest\ActiveController
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->modelClass = Model::class;
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => LockControlFilter::class,
                'only' => ['update']
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'update' => [
                'class' => UpdateAction::className(),
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess']
            ],
            'lock' => [
                'class' => LockAction::className(),
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess']
            ],
            'unlock' => [
                'class' => UnlockAction::className(),
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
            ]
        ];
    }
}