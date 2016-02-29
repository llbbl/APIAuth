<?php

namespace eig\APIAuth\Tests;

use eig\APIAuth\Tokens\TokenFieldGenerator;

/**
 * Class TokenFieldGeneratorTest
 * @package eig\APIAuth\Tests
 */
class TokenFieldGeneratorTest extends TestAbstract
{

    /**
     * @var
     */
    protected $tokenGenerator;

    /**
     * @var
     */
    protected $seed;

    /**
     * @var
     */
    protected $cost;


    /**
     * setUp
     */
    public function setUp()
    {
        $this->tokenGenerator = new TokenFieldGenerator();
        $this->seed = (string)bin2hex(openssl_random_pseudo_bytes(24));
        $this->cost = 10;
        parent::setUp();
    }

    /**
     * testGenerate
     */
    public function testGenerate()
    {
        $expected = password_hash($this->cost, PASSWORD_DEFAULT, ['cost' => $this->cost]);
        $this->assertNotEquals($expected, $this->tokenGenerator->generate($this->seed, $this->cost));
    }
}
