<?php

namespace notamedia\locker;

use yii\base\Exception;

/**
 * Represents an exception that is caused when resource lock not exists
 */
class LockNotExistException extends Exception
{
    /**
     * @inheritdoc
     */
    public function __construct(
        $message = 'Resource is not blocked',
        $code = 0,
        \Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Lock Not Exist Exception';
    }
}