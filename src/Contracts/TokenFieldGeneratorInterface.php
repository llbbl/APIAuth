<?php

namespace eig\APIAuth\Contracts;

/**
 * Interface TokenFieldGeneratorInterface
 * @package eig\APIAuth\Contracts
 */
interface TokenFieldGeneratorInterface
{

    /**
     * generate
     *
     * @param string    $seed
     * @param int       $cost
     *
     * @return string
     */
    public function generate($seed, $cost = 10);

}