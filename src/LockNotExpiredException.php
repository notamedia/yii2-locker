<?php

namespace notamedia\locker;

use yii\base\Exception;

/**
 * Represents an exception that is caused when resource lock is actual
 */
class LockNotExpiredException extends Exception
{
    /** @var integer seconds to expired lock */
    protected $seconds;

    /**
     * @inheritdoc
     */
    public function __construct(
        $seconds,
        $message = 'Resource lock time has not expired yet',
        $code = 0,
        \Exception $previous = null
    ) {
        $this->seconds = $seconds;
        parent::__construct($message, $code, $previous);
    }

    /**
     * Return seconds to expired lock
     * @return int|string
     */
    public function getSeconds()
    {
        return $this->seconds;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Lock Not Expired Exception';
    }
}