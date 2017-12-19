<?php

namespace notamedia\locker;

use yii\web\IdentityInterface;
use yii\db\ActiveRecordInterface;
use yii\db\Expression;

/**
 * LockInterface is the interface that should be implemented by a lock model.
 *
 * ```php
 * class Lock extends ActiveRecord implements LockInterface
 * {
 *     public static function findOrCreate(LockableInterface $resource)
 *     {
 *         // get lock
 *     }
 *     public function activate(User $user, Expression $lockedAtExpression);
 *     {
 *         // set activate params
 *     }
 *
 *     public function deactivate();
 *     {
 *         // set deactivate params
 *     }
 *
 *     public function getLockTimeLeft();
 *     {
 *         // return lock time left
 *     }
 * }
 * ```
 */
interface LockInterface extends ActiveRecordInterface
{
    /**
     * Get lock
     * @param IdentityInterface $user
     * @param LockableInterface $resource
     * @return LockInterface
     */
    public static function findOrCreate(IdentityInterface $user, LockableInterface $resource);
    /**
     * Set activate attributes
     * @param IdentityInterface $user
     * @param Expression $lockedAtExpression
     */
    public function activate(IdentityInterface $user, Expression $lockedAtExpression);
    /**
     * Set deactivate attributes
     */
    public function deactivate();
    /**
     * Get lock time left
     * @param Expression $diffExpression
     * @return int
     */
    public function getLockTimeLeft(Expression $diffExpression);
}
