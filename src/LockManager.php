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
    /** @var string lock model class */
    public $lockClass = Lock::class;
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
        /** @var LockInterface $classLock */
        $classLock = $this->lockClass;
        $lock = $classLock::findOrCreate($resource);
        $this->checkLockAuthor($lock, true);

        $lockSeconds = $this->getResourceLockTime($resource);
        $lock->activate(\Yii::$app->getUser(), new Expression(sprintf($this->initTimeExpressionValue, $lockSeconds)));

        if (!$lock->save()) {
            throw new \RuntimeException('Failed to create or update the object');
        }
    }

    /**
     * @inheritdoc
     */
    public function deactivateLock(LockableInterface $resource)
    {
        /** @var LockInterface $classLock */
        $classLock = $this->lockClass;
        
        $this->checkLockActual($resource, true);

        $lock = $classLock::findOrCreate($resource);
        $lock->deactivate();
        if (!$lock->save()) {
            throw new \RuntimeException('Failed to update the object');
        }
    }

    /**
     * @inheritdoc
     */
    public function checkLockActual(LockableInterface $resource, $throw = false)
    {
        $active = false;
        /** @var LockInterface $classLock */
        $classLock = $this->lockClass;

        $lock = $classLock::findOrCreate($resource);
        if (!$lock->getIsNewRecord()) {
            $active = $this->checkLockAuthor($lock, $throw) && $this->checkLockTime($resource, $lock, $throw);
        } elseif($throw) {
            throw new LockNotExistException();
        }

        return $active;
    }

    /**
     * Check lock author
     * @param LockInterface $lock
     * @param boolean $throw
     * @throws LockAnotherUserException
     * @return boolean
     */
    public function checkLockAuthor(LockInterface $lock, $throw = false)
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
     * @param LockInterface $lock
     * @param boolean $throw
     * @throws LockNotExpiredException
     * @return integer
     */
    public function checkLockTime(LockableInterface $resource, LockInterface $lock, $throw = false)
    {
        $lockSeconds = $this->getResourceLockTime($resource);
        $diffExpression = new Expression($this->diffExpressionValue);
        $diffSeconds = $lock->getLockTimeLeft($diffExpression);

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