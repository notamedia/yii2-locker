<?php

namespace notamedia\locker;

use yii\web\User;
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
 *     public function getDiff();
 *     {
 *         // return lock time left
 *     }
 * }
 * ```
 *
 * @property string $hash - hash
 * @property string $locked_at - lock time
 * @property mixed $locked_by - lock author
 */
interface LockInterface extends ActiveRecordInterface
{
    /**
     * Get lock
     * @param LockableInterface $resource
     * @return LockInterface
     */
    public static function findOrCreate(LockableInterface $resource);
    /**
     * Set activate attributes
     * @param User $user
     * @param Expression $lockedAtExpression
     */
    public function activate(User $user, Expression $lockedAtExpression);
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
