<?php

namespace notamedia\locker;

/**
 * LockableInterface is the interface that should be implemented by a lockable resource.

 * ```php
 * class Record extends ActiveRecord implements LockableInterface
 * {
 *     public function getLockHash()
 *     {
 *          return hash('sha256', $this->getAttribute('id') . static::class);
 *     }
 * }
 * ```
 */
interface LockableInterface
{
    /**
     * Return hash that uniquely identifies a resource.
     * @return mixed because format is depend by model and database table field type
     */
    public function getLockHash();
}
