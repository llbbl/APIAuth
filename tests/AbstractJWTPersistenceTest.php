<?php

namespace eig\APIAuth\Tests;

use Mockery;
use eig\APIAuth\Abstracts\AbstractJWTPersistence;

/**
 * Class AbstractJWTPersistenceTest
 * @package eig\APIAuth\Tests
 */
class AbstractJWTPersistenceTest extends TestAbstract
{

    /**
     * @var
     */
    protected $model;

    /**
     * @var
     */
    protected $jwtPersistence;

    /**
     * setUp
     */
    public function setUp()
    {
        $this->model = Mockery::mock('overload:Illuminate\Database\Eloquent\Model');
        $this->jwtPersistence = Mockery::mock('eig\APIAuth\Abstracts\AbstractJWTPersistence')->makePartial();
        parent::setUp();
    }

    /**
     * tearDown
     */
    public function tearDown()
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * testConstructor
     * @test
     */
    public function testConstructor() {
        $this->assertInstanceOf('eig\APIAuth\Abstracts\AbstractJWTPersistence', $this->jwtPersistence);
    }

    /**
     * testSignature
     * @test
     */
    public function testSignature() {
        $this->jwtPersistence->signature('this is a signature');
        $this->assertEquals('this is a signature', $this->jwtPersistence->signature());
    }

    /**
     * testIssued
     * @test
     */
    public function testIssued() {
        $this->jwtPersistence->issued('now');
        $this->assertEquals('now', $this->jwtPersistence->issued());
    }

    /**
     * testNotBefore
     * @test
     */
    public function testNotBefore() {
        $this->jwtPersistence->notBefore('now');
        $this->assertEquals('now', $this->jwtPersistence->notBefore());
    }

    /**
     * testExpiration
     * @test
     */
    public function testExpiration() {
        $this->jwtPersistence->expiration('later');
        $this->assertEquals('later', $this->jwtPersistence->expiration());
    }

    /**
     * testToken
     * @test
     */
    public function testToken() {
        $this->jwtPersistence->token('this is a token');
        $this->assertEquals('this is a token', $this->jwtPersistence->token());
    }
}
