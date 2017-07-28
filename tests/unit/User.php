<?php

namespace tests\unit;

use yii\web\IdentityInterface;

/**
 * @inheritdoc
 */
class User implements IdentityInterface
{
    /** @var integer */
    private $_id;

    /**
     * @inheritdoc
     */
    public function __construct($id)
    {
        $this->_id = $id;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return new User(1);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return new User(1);
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->_id === $authKey;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->_id;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->_id;
    }
}