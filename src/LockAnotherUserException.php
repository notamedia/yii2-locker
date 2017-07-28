<?php

namespace notamedia\locker;

use yii\base\Exception;

/**
 * Represents an exception that is caused when record locked by another user
 */
class LockAnotherUserException extends Exception
{
    /** @var integer locked user id */
    protected $userId;

    /**
     * @inheritdoc
     */
    public function __construct(
        $userId,
        $message = 'Resource is blocked by the another user',
        $code = 0,
        \Exception $previous = null
    ) {
        $this->userId = $userId;
        parent::__construct($message, $code, $previous);
    }

    /**
     * Return locked user id
     * @return int|string
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Lock Another User Exception';
    }
}