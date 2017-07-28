<?php

namespace tests\unit;

use yii\db\ActiveRecord;
use notamedia\locker\LockableInterface;

/**
 * @inheritdoc
 */
class Model extends ActiveRecord implements LockableInterface
{
    /**
     * @inheritdoc
     */
    public function getLockHash()
    {
        return hash('sha256', $this->getAttribute('id') . static::class);
    }
}