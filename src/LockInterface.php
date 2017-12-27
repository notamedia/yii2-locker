<?php

namespace notamedia\locker;

use yii\web\IdentityInterface;
use yii\db\ActiveRecordInterface;
use yii\db\Expression;

/**
 * LockInterface is the interface that should be implemented by a lock model.
 */
interface LockInterface extends ActiveRecordInterface
{
    /**
     * Get a new or existing lock by hash
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
     * Get the remaining lock time in seconds
     * @param Expression $diffExpression
     * @return int
     */
    public function getLockTimeLeft(Expression $diffExpression);
}
