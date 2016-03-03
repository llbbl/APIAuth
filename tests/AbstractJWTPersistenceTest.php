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

    public function testCreateNew ()
    {
        $this->jwtPersistence->create();
        $this->assertNull($this->jwtPersistence->signature());
        $this->assertNull($this->jwtPersistence->token());
        $this->assertNull($this->jwtPersistence->expiration());
        $this->assertNull($this->jwtPersistence->notBefore());
        $this->assertNull($this->jwtPersistence->issued());
    }

    public function testCreateWithParams()
    {
        $this->jwtPersistence->shouldReceive('save');
        $params = [
            'signature' => 'signature',
            'token' => 'token',
            'notBefore' => 'now',
            'expiration' => 'later',
            'issued' => 'now'
        ];
        $this->jwtPersistence->create($params);
        $this->assertNotNull($this->jwtPersistence->signature());
        $this->assertNotNull($this->jwtPersistence->token());
        $this->assertNotNull($this->jwtPersistence->notBefore());
        $this->assertNotNull($this->jwtPersistence->expiration());
        $this->assertNotNull($this->jwtPersistence->issued());
    }

    public function testGetID()
    {
        $this->assertNull($this->jwtPersistence->id());
    }

    /**
     * testErrorLoadFields
     * @test
     * @expectedException eig\APIAUth\Exceptions\JWTException
     */
    public function testErrorLoadFields ()
    {
        $this->jwtPersistence->shouldNotReceive('save');
        $params = [
            'hello' => 'world',
            'token' => 'token',
            'notBefore' => 'now',
            'expiration' => 'later',
            'issued' => 'now'
        ];
        $this->jwtPersistence->create($params);
        $this->setExpectedExceptionFromAnnotation();
    }

    public function testCreateWithoutSignature()
    {
        $this->jwtPersistence->shouldNotReceive('save');
        $params = [
            'token'      => 'token',
            'notBefore'  => 'now',
            'expiration' => 'later',
            'issued'     => 'now'
        ];
        $this->jwtPersistence->create($params);
        $this->assertNull($this->jwtPersistence->signature());
    }

    public function testCreateWithoutToken ()
    {
        $params = [
            'signature'  => 'signature',
            'notBefore'  => 'now',
            'expiration' => 'later',
            'issued'     => 'now'
        ];
        $this->jwtPersistence->create($params);
        $this->assertNull($this->jwtPersistence->token());
    }
    public function testCreateWithoutNotBefore()
    {
        $params = [
            'signature'  => 'signature',
            'token'      => 'token',
            'expiration' => 'later',
            'issued'     => 'now'
        ];
        $this->jwtPersistence->create($params);
        $this->assertNull($this->jwtPersistence->notBefore());
    }
    public function testCreateWithoutExpiration()
    {
        $params = [
            'signature' => 'signature',
            'token'     => 'token',
            'notBefore' => 'now',
            'issued'    => 'now'
        ];
        $this->jwtPersistence->create($params);
        $this->assertNull($this->jwtPersistence->expiration());
    }
    public function testCreateWithoutIssued()
    {
        $params = [
            'signature' => 'signature',
            'token' => 'token',
            'notBefore' => 'now',
            'expiration' => 'later'
        ];
        $this->jwtPersistence->create($params);
        $this->assertNull($this->jwtPersistence->issued());

    }
}
