<?php

namespace eig\APIAuth\Exceptions;

/**
 * Class UserException
 * @package eig\APIAuth\Exceptions
 */
class UserException extends \Exception
{

    /**
     * UserException constructor.
     *
     * @param string          $message
     * @param int             $code
     * @param \Exception|null $previous
     */
    public function __construct($message, $code = 0, \Exception $previous = null)
    {
        parent::__construct($message . '' . $previous->message, $code, $previous);
    }
}