<?php

namespace notamedia\locker;

/**
 * Represents an exception that is caused when record locked by another user
 */
class LockAnotherUserException extends LockException
{
    /**
     * @inheritdoc
     */
    public function __construct(
        $data = null,
        $message = 'Resource is blocked by the another user',
        $code = 0,
        \Exception $previous = null
    ) {
        parent::__construct($data, $message, $code, $previous);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Lock Another User Exception';
    }
}