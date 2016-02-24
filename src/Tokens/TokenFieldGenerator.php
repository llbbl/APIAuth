<?php

namespace eig\APIAuth\Tokens;

use PasswordLib\PasswordLib;
use eig\APIAuth\Contracts\TokenFieldGeneratorInterface;

/**
 * Class FieldTokenGenerator
 * @package eig\APIAuth\Tokens
 */
class TokenFieldGenerator implements TokenFieldGeneratorInterface
{
    protected $library;

    public function __construct ()
    {
        $this->library = new PasswordLib();
    }

    /**
     * generate
     *
     * @param string $seed
     * @param int    $randomLevel
     *
     * @return bool|string
     */
    public function generate ($seed, $cost = 10)
    {
       return $this->library->createPasswordHash($seed, '$2a$', array('cost' => $cost));
    }


}