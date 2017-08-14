<?php

namespace notamedia\locker;

use yii\db\Expression;
use yii\base\Component;
use yii\base\InvalidConfigException;

/**
 * Resource lock manager
 *
 * ```php
 *
 *  'components' => [
 *      //...
 *      'lockManager' => [
 *          'class' => LockManager::class,
 *          'lockTime' => [
 *              LockManager::DEFAULT_LOCK_TIME_KEY => 900,
 *          ]
 *      ],
 *      //...
 *  ]
 * //```
 */
class LockManager extends Component implements LockManagerInterface
{
    /** @var string lock time key for default value */
    const DEFAULT_LOCK_TIME_KEY = 'default';
    /** @var array lock time in seconds for each record class, key `default` for default value */
    public $lockTime = [
        self::DEFAULT_LOCK_TIME_KEY => 900 // 15 minutes
    ];
    /** @var string expression for add seconds to current date, where %s is a needed seconds */
    public $initTimeExpressionValue = 'DATE_ADD(NOW(), INTERVAL %s SECOND)';
    /** @var string expression for getting past time */
    public $diffExpressionValue = 'TIMESTAMPDIFF(SECOND, NOW(), [[locked_at]])';

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();

        if (!\Yii::$app->getUser()) {
            throw new InvalidConfigException('Not found correct user component');
        }

        if (!is_array($this->lockTime) || !isset($this->lockTime[self::DEFAULT_LOCK_TIME_KEY])) {
            throw new InvalidConfigException('Incorrect lockTime value');
        }
    }

    /**
     * @inheritdoc
     */
    public function activateLock(LockableInterface $resource)
    {
        $lock = $this->getResourceLock($resource);
        $this->checkLockAuthor($lock, true);

        $lockSeconds = $this->getResourceLockTime($resource);
        $lock->setAttributes([
            'locked_by' => \Yii::$app->getUser()->getId(),
            'locked_at' => new Expression(
                sprintf($this->initTimeExpressionValue, $lockSeconds)
            )
        ]);

        if (!$lock->save()) {
            throw new \RuntimeException('Failed to create or update the object');
        }
    }
    
    /**
     * @inheritdoc
     */
    public function deactivateLock(LockableInterface $resource)
    {
        $this->checkLockActual($resource, true);

        $lock = $this->getResourceLock($resource);
        $lock->setAttributes([
            'locked_at' => new Expression('NOW()')
        ]);

        if (!$lock->save()) {
            throw new \RuntimeException('Failed to update the object');
        }
    }

    /**
     * Create new or return exist lock for resource
     * @param LockableInterface $resource
     * @throws InvalidConfigException
     * @return Lock
     */
    public function getResourceLock($resource)
    {
        $hash = $resource->getLockHash();

        $lock = Lock::findOne(['hash' => $hash]);
        if(!$lock instanceof Lock) {
            $lock = \Yii::createObject(Lock::class);
            $lock->setAttributes([
                'hash'      => $hash,
                'locked_at' => new Expression('NOW()'),
                'locked_by' => \Yii::$app->getUser()->getId()
            ]);
        }

        return $lock;
    }

    /**
     * @inheritdoc
     */
    public function checkLockActual(LockableInterface $resource, $throw = false)
    {
        $lock = $this->getResourceLock($resource);
        $active = false;

        if (!$lock->getIsNewRecord()) {
            $active = $this->checkLockAuthor($lock, $throw) && $this->checkLockTime($resource, $lock, $throw);
        } elseif($throw) {
            throw new LockNotExistException();
        }

        return $active;
    }

    /**
     * Check lock author
     * @param Lock $lock
     * @param boolean $throw
     * @throws LockAnotherUserException
     * @return boolean
     */
    public function checkLockAuthor(Lock $lock, $throw = false)
    {
        $active = \Yii::$app->getUser()->getId() !== $lock->locked_by;
        if ($active && $throw) {
            throw new LockAnotherUserException($lock->locked_by);
        }

        return $active;
    }

    /**
     * Check actual lock time
     * @param LockableInterface $resource
     * @param Lock $lock
     * @param boolean $throw
     * @throws LockNotExpiredException
     * @return integer
     */
    public function checkLockTime(LockableInterface $resource, Lock $lock, $throw = false)
    {
        $lockSeconds = $this->getResourceLockTime($resource);
        $diffExpression = new Expression($this->diffExpressionValue);
        $diffSeconds = Lock::find()
            ->select($diffExpression)
            ->where(['hash' => $lock->getAttribute('hash')])
            ->scalar();

        if ($diffSeconds > $lockSeconds && $throw) {
            throw new LockNotExpiredException($diffSeconds);
        }

        return $diffSeconds;
    }

    /**
     * Get lock time
     * @param LockableInterface $resource
     * @return integer
     */
    public function getResourceLockTime(LockableInterface $resource)
    {
        $class = get_class($resource);
        return isset($this->lockTime[$class]) ?
            $this->lockTime[$class] : $this->lockTime[self::DEFAULT_LOCK_TIME_KEY];
    }
}