<?php

namespace eig\APIAuth\Exceptions;


/**
 * Class SessionException
 * @package eig\APIAuth\Exceptions
 */
class SessionException extends \Exception
{

    /**
     * SessionException constructor.
     *
     * @param string          $message
     * @param int             $code
     * @param \Exception|null $previous
     */
    public function __construct ($message, $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}