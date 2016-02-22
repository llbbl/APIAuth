<?php

namespace eig\APIAuth\Contracts;

/**
 * Interface TokenGeneratorInterface
 * @package eig\APIAuth\Contracts
 */
interface TokenGeneratorInterface
{

    /**
     * generate
     *
     * @param string    $seed
     * @param int       $randomLevel
     *
     * @return string
     */
    public function generate($seed, $randomLevel = 256);

}