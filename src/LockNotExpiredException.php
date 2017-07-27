<?php

namespace notamedia\locker;

/**
 * Represents an exception that is caused when resource lock is actual
 */
class LockNotExpiredException extends LockException
{
    /**
     * @inheritdoc
     */
    public function __construct(
        $data = null,
        $message = 'Resource lock time has not expired yet',
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
        return 'Lock Not Expired Exception';
    }
}