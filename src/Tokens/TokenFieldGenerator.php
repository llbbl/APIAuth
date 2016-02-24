<?php

namespace eig\APIAuth\Tokens;


use eig\APIAuth\Contracts\TokenFieldGeneratorInterface;

/**
 * Class FieldTokenGenerator
 * @package eig\APIAuth\Tokens
 */
class TokenFieldGenerator implements TokenFieldGeneratorInterface
{

    /**
     * generate
     *
     * @param string $seed
     * @param int    $randomLevel
     *
     * @return bool|string
     */
    public function generate ($seed, $randomLevel = 10)
    {
       return password_hash($seed, PASSWORD_DEFAULT);
    }


}