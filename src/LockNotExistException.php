<?php

namespace notamedia\locker;

/**
 * Represents an exception that is caused when resource lock not exists
 */
class LockNotExistException extends LockException
{
    /**
     * @inheritdoc
     */
    public function __construct(
        $data = null,
        $message = 'Resource is not blocked',
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
        return 'Lock Not Exist Exception';
    }
}