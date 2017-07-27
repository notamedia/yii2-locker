<?php

namespace notamedia\locker;

use yii\base\Exception;

/**
 * Represents an exception that is caused by lock operations.
 */
class LockException extends Exception
{
    /** @var mixed extra data */
    protected $data;

    /**
     * Construct the exception.
     * @param mixed $data [optional]
     * @param string $message [optional]
     * @param int $code [optional]
     * @param \Exception $previous [optional]
     */
    public function __construct($data = null, $message = '', $code = 500, \Exception $previous = null)
    {
        $this->data = $data;
        parent::__construct($message, $code, $previous);
    }

    /**
     * Extra exception data
     * @return mixed|null
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Lock Exception';
    }
}