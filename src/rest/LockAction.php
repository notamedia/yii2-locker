<?php

namespace notamedia\locker\rest;

use notamedia\locker\LockableInterface;

/**
 * LockAction implements the API endpoint for locking a resource.
 *
 * ```php
 *  [
 *      ...
 *      'lock' => [
 *          'class' => LockAction::class,
 *          'modelClass' => $this->modelClass,
 *          'checkAccess' => [$this, 'checkAccess']
 *      ],
 *      ...
 *  ]
 * ```
 */
class LockAction extends Action
{
    /**
     * @inheritdoc
     */
    protected function manage(LockableInterface $resource)
    {
        $this->lockManager->activateLock($resource);
    }
}
