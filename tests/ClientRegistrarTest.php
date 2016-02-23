<?php

namespace eig\APIAuth\Tests;

use eig\APIAuth\ClientRegistrar;
use eig\APIAuth\Exceptions\ClientException;
use Mockery;

class ClientRegistrarTest extends TestAbstract
{

    /**
     * @var
     */
    protected $clientRegistrar;

    /**
     * @var
     */
    protected $tokenGenerator;

    /**
     * @var
     */
    protected $persistence;

    /**
     * @var
     */
    protected $fingerprint;

    /**
     * @var
     */
    protected $type;

    /**
     * setUp
     */
    public function setUp ()
    {
        $this->persistence = Mockery::mock('overload:eig\APIAuth\Contracts\ClientPersistenceInterface');
        $this->tokenGenerator = Mockery::mock('eig\APIAuth\Contracts\TokenGeneratorInterface');
        $this->persistence->shouldReceive('fingerprint')->andReturn(true);
        $this->persistence->shouldReceive('type');
        $this->persistence->shouldReceive('token')->andReturn(sha1('this is the token'));
        $this->persistence->shouldReceive('save')->andReturn(true);
        $this->tokenGenerator->shouldReceive('generate')->andReturn(sha1('this is the token'));
        $this->clientRegistrar = new ClientRegistrar($this->persistence, $this->tokenGenerator);
        $this->fingerprint = md5('this is the clients guid');
        $this->type = 'Android';
        parent::setUp();
    }

    /**
     * tearDown
     */
    public function tearDown ()
    {
        \Mockery::close();
        parent::tearDown();
    }

    /**
     * testConstructor
     */
    public function testConstructor() {
        $this->assertInstanceOf('eig\APIAuth\ClientRegistrar', $this->clientRegistrar);
    }

    /**
     * testRegister
     */
    public function testRegister() {
        $this->persistence->shouldReceive('exists')->andReturn(false);
        $this->assertEquals(sha1('this is the token'), $this->clientRegistrar->register($this->fingerprint, $this->type));
    }


    /**
     * testFingerprintNull
     * @expectedException eig\APIAuth\Exceptions\ClientException
     */
    public function testFingerprintNull() {
        $this->clientRegistrar->register(null, $this->type);
        $this->setExpectedExceptionFromAnnotation();
    }

    /**
     * testFingerprintEmpty
     * @expectedException eig\APIAuth\Exceptions\ClientException
     */
    public function testFingerprintEmpty() {
        $this->clientRegistrar->register('', $this->type);
        $this->setExpectedExceptionFromAnnotation();
    }

    /**
     * testFingerprintShort
     * @expectedException eig\APIAuth\Exceptions\ClientException
     */
    public function testFingerprintShort() {
        $this->clientRegistrar->register('ABCDEF', $this->type);
        $this->setExpectedExceptionFromAnnotation();
    }

    /**
     * testFingerprintExists
     * @expectedException eig\APIAuth\Exceptions\ClientException
     */
    public function testFingerprintExistsShort() {
        $this->persistence->shouldReceive('exists')->andReturn(true);
        $this->clientRegistrar->register($this->fingerprint, $this->type);
        $this->setExpectedExceptionFromAnnotation();
    }

    /**
     * testTypeNull
     * @expectedException eig\APIAuth\Exceptions\ClientException
     */
    public function testTypeNull() {
        $this->persistence->shouldReceive('exists')->andReturn(false);
        $this->clientRegistrar->register($this->fingerprint, null);
        $this->setExpectedExceptionFromAnnotation();
    }

    /**
     * testTypeEmpty
     * @expectedException eig\APIAuth\Exceptions\ClientException
     */
    public function testTypeEmpty() {
        $this->persistence->shouldReceive('exists')->andReturn(false);
        $this->clientRegistrar->register($this->fingerprint, '');
        $this->setExpectedExceptionFromAnnotation();
    }

    /**
     * testTypeShort
     * @expectedException eig\APIAuth\Exceptions\ClientException
     */
    public function testTypeShort() {
        $this->persistence->shouldReceive('exists')->andReturn(false);
        $this->clientRegistrar->register($this->fingerprint, 'AB');
        $this->setExpectedExceptionFromAnnotation();
    }

}
