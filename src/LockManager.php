<?php

namespace notamedia\locker;

use yii\db\Expression;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\base\InvalidValueException;
use yii\web\IdentityInterface;

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
    /** @var LockInterface empty lock class object */
    protected $lockModel;

    /**
     * @inheritdoc
     */
    public function __construct(LockInterface $lockModel, array $config = [])
    {
        $this->lockModel = $lockModel;

        parent::__construct($config);

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
        $userIdentity = $this->getUserIdentity();

        $lock = $this->getLock($resource);
        $this->checkLockAuthor($lock, true);

        $lockSeconds = $this->getResourceLockTime($resource);
        $lock->activate($userIdentity, new Expression(sprintf($this->initTimeExpressionValue, $lockSeconds)));

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

        $lock = $this->getLock($resource);
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
        $lock = $this->getLock($resource);
        if ($lock->getIsNewRecord() && $throw) {
            throw new LockNotExistException();
        }

        return $this->checkLockAuthor($lock, $throw) && $this->checkLockTime($resource, $lock, $throw);
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
        $userIdentity = $this->getUserIdentity();
        $active = $userIdentity->getId() !== $lock->locked_by;

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
        $diffExpression = new Expression($this->diffExpressionValue);
        $diffSeconds = $lock->getLockTimeLeft($diffExpression);
        $lockSeconds = $this->getResourceLockTime($resource);

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

    /**
     * Get resource lock
     * @param LockableInterface $resource
     * @return LockInterface
     */
    protected function getLock(LockableInterface $resource)
    {
        $userIdentity = $this->getUserIdentity();
        return call_user_func([$this->lockModel, 'findOrCreate'], $userIdentity, $resource);
    }

    /**
     * Get user identity
     * @return IdentityInterface
     */
    protected function getUserIdentity()
    {
        $user = \Yii::$app->getUser();
        $userIdentity = $user->getIdentity();
        if (!$userIdentity instanceof IdentityInterface) {
            throw new InvalidValueException('The identity object must be IdentityInterface.');
        }

        return $userIdentity;
    }
}