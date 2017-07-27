<?php

namespace tests\_data\fixtures;

use yii\test\ActiveFixture;
use tests\unit\Model;

/**
 * Test model fixture
 */
class ModelFixture extends ActiveFixture
{
    /**
     * @inheritdoc
     */
    public function __construct($config = [])
    {
        $this->modelClass = Model::class;
        parent::__construct($config);
    }
}