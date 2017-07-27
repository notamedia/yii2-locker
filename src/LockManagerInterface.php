<?php

namespace notamedia\locker;

use yii\base\InvalidConfigException;

/**
 * LockManagerInterface is the interface that should be implemented by a class that manages locks.
 *
 * ```php
 * class LockManager implements LockManagerInterface
 * {
 *     public static function activateLock($resource)
 *     {
 *         // check lock and create or reactivate
 *     }
 *
 *     public static function deactivateLock($resource)
 *     {
 *         // check and deactivate lock
 *     }
 *
 *     public function checkLockActual($resource, $throw = false);
 *     {
 *         // check lock actuality
 *     }
 * }
 * ```
 */
interface LockManagerInterface
{
    /**
     * Activate or update lock
     * @param LockableInterface $resource
     * @throws InvalidConfigException
     * @throws LockAnotherUserException
     * @throws LockException
     */
    public function activateLock(LockableInterface $resource);
        
    /**
     * Deactivate lock
     * @param LockableInterface $resource
     * @throws InvalidConfigException
     * @throws LockAnotherUserException
     * @throws LockNotExpiredException
     * @throws LockNotExistException
     * @throws LockException
     */
    public function deactivateLock(LockableInterface $resource);
    
    /**
     * Check lock actuality
     * @param LockableInterface $resource
     * @param boolean $throw [optional] throw exception if lock is actual
     * @throws InvalidConfigException
     * @throws LockAnotherUserException
     * @throws LockNotExpiredException
     * @throws LockNotExistException
     * @return bool
     */
    public function checkLockActual(LockableInterface $resource, $throw = false);
}
