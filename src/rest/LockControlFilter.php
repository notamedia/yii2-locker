<?php

namespace notamedia\locker\rest;

use Yii;
use yii\db\ActiveRecordInterface;
use yii\di\Instance;
use yii\base\ActionFilter;
use yii\base\InvalidConfigException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use notamedia\locker\LockManagerInterface;
use notamedia\locker\LockableInterface;
use notamedia\locker\LockAnotherUserException;
use notamedia\locker\LockNotExistException;
use notamedia\locker\LockNotExpiredException;

/**
 * Filter check record lock activity for actions
 *
 * ```php
 *  [
 *      ...
 *      [
 *          'class' => LockControlFilter::class,
 *          'only' => ['update']
 *      ],
 *      ...
 *  ]
 * ```
 */
class LockControlFilter extends ActionFilter
{
    /**
     * @var callable a PHP callable that will be called to return the model corresponding
     * to the specified primary key value. If not set, [[findModel()]] will be used instead.
     * The signature of the callable should be:
     *
     * ```php
     * function ($id, $action) {
     *     // $id is the primary key value. If composite primary key, the key values
     *     // will be separated by comma.
     *     // $action is the action object currently running
     * }
     * ```
     */
    public $findModel;
    /** @var string the name of the GET parameter that specifies the identificator. */
    public $idParam = 'id';
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
     * @inheritdoc
     * @param yii\rest\Action $action
     * @throws NotFoundHttpException
     * @throws InvalidConfigException
     * @throws MethodNotAllowedHttpException
     * @throws LockAnotherUserException
     * @throws LockNotExpiredException
     * @throws LockNotExistException
     * @throws \Exception
     */
    public function beforeAction($action)
    {
        $model = $this->getModel($action);
        if (!$model instanceof LockableInterface) {
            throw new MethodNotAllowedHttpException('Method not supported by the target resource');
        }

        $this->lockManager->checkLockActual($model, true);
        
        return parent::beforeAction($action);
    }

    /**
     * Returns the model
     * @param yii\rest\Action $action
     * @throws NotFoundHttpException if the model cannot be found
     * @return ActiveRecordInterface the model found
     */
    public function getModel(yii\rest\Action $action)
    {
        if ($this->findModel !== null) {
            return call_user_func($this->findModel, $this);
        }

        $request = Yii::$app->getRequest();
        if ($request->getIsGet()) {
            $id = $request->get($this->idParam);
        } else {
            $id = $request->post($this->idParam);
        }

        $model = $action->findModel($id);
        $modelClass = $action->modelClass;

        if (!$model instanceof $modelClass) {
            throw new NotFoundHttpException('Record not found:' . $id);
        }

        return $model;
    }
}