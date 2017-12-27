<?php

namespace notamedia\locker;

use yii\base\InvalidConfigException;

/**
 * LockManagerInterface is the interface that should be implemented by a class that manages locks.
 */
interface LockManagerInterface
{
    /**
     * Activate or update lock
     * @param LockableInterface $resource
     * @throws InvalidConfigException
     * @throws LockAnotherUserException
     */
    public function activateLock(LockableInterface $resource);
        
    /**
     * Deactivate lock
     * @param LockableInterface $resource
     * @throws InvalidConfigException
     * @throws LockAnotherUserException
     * @throws LockNotExpiredException
     * @throws LockNotExistException
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
