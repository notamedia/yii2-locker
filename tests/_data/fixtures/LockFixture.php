<?php

namespace tests\_data\fixtures;

use yii\test\ActiveFixture;
use notamedia\locker\Lock;

/**
 * Lock model fixture
 */
class LockFixture extends ActiveFixture
{
    /**
     * @inheritdoc
     */
    public function __construct($config = [])
    {
        $this->modelClass = Lock::class;
        parent::__construct($config);
    }
}