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
     * Construct the exception. Note: The message is NOT binary safe.
     * @param integer $seconds seconds to expired lock
     * @param string $message [optional] The Exception message to throw.
     * @param int $code [optional] The Exception code.
     * @param \Exception $previous [optional] The previous throwable used for the exception chaining.
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