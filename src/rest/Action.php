<?php

namespace notamedia\locker\rest;

use yii\di\Instance;
use yii\rest as yiirest;
use yii\base\InvalidConfigException;
use yii\web\MethodNotAllowedHttpException;
use notamedia\locker\LockableInterface;
use notamedia\locker\LockManagerInterface;

/**
 * Action implements the API endpoint for manage lock a resource.
 */
abstract class Action extends yiirest\Action
{
    /** @var LockManagerInterface|string lock manager component name */
    public $lockManager = 'lockManager';

    /**
     * @inheritdoc
     * @throws InvalidConfigException if lock manager not found
     */
    public function init()
    {
        parent::init();

        $this->lockManager = Instance::ensure($this->lockManager, LockManagerInterface::class);
        if (!$this->lockManager instanceof LockManagerInterface) {
            throw new InvalidConfigException(
                'Not found correct lock manager component implementing `LockManagerInterface`'
            );
        }
    }

    /**
     * Manage lock an existing model.
     * @param string $id the primary key of the model.
     * @throws \Exception
     */
    public function run($id)
    {
        $model = $this->findModel($id);
        if (!$model instanceof LockableInterface) {
            throw new MethodNotAllowedHttpException('Method not supported by the target resource');
        }

        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id, $model);
        }

        $this->manage($model);

        \Yii::$app->getResponse()->setStatusCode(204);
    }

    /**
     * Manage lock an existing resource.
     * @param LockableInterface $resource
     * @throws \Exception
     * @return mixed
     */
    abstract protected function manage(LockableInterface $resource);
}
