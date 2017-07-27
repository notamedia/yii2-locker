<?php

namespace notamedia\locker\rest;

use notamedia\locker\LockableInterface;

/**
 * UnlockAction implements the API endpoint for unlocking a resource.
 *
 * ```php
 *  [
 *      //...
 *      'unlock' => [
 *          'class' => UnlockAction::class,
 *          'modelClass' => $this->modelClass,
 *          'checkAccess' => [$this, 'checkAccess']
 *      ],
 *      //...
 *  ]
 * ```
 */
class UnlockAction extends Action
{
    /**
     * @inheritdoc
     */
    protected function manage(LockableInterface $resource)
    {
        $this->lockManager->deactivateLock($resource);
    }
}
