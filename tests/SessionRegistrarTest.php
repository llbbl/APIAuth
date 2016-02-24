<?php

namespace eig\APIAuth\Tests;

use eig\APIAuth\Session\SessionRegistrar;
use eig\APIAuth\Exceptions\SessionException;
use Mockery;

class SessionRegistrarTest extends TestAbstract
{
    /**
     * @var
     */
    protected $sessionRegistrar;

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
    protected $clientToken;


    protected $sessionToken;

    /**
     * setUp
     */
    public function setUp ()
    {
        $this->sessionToken = sha1('this is the session token');
        $this->fingerprint = md5('this is the clients guid');
        $this->clientToken = sha1('This is the Client Token');
        $this->persistence = Mockery::mock('overload:eig\APIAuth\Contracts\SessionPersistenceInterface');
        $this->tokenGenerator = Mockery::mock('eig\APIAuth\Contracts\TokenFieldGeneratorInterface');

        $this->persistence->shouldReceive('client')->andReturn(true);
        $this->persistence->shouldReceive('setRevoked');
        $this->persistence->shouldReceive('token')->andReturn($this->sessionToken);
        $this->persistence->shouldReceive('save')->andReturn(true);
        $this->tokenGenerator->shouldReceive('generate')->andReturn($this->sessionToken);
        $this->sessionRegistrar = new SessionRegistrar($this->persistence, $this->tokenGenerator);

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
        $this->assertInstanceOf('eig\APIAuth\Session\SessionRegistrar', $this->sessionRegistrar);
    }

    public function testRegister() {
        $this->persistence->shouldReceive('exists')->andReturn(false);
        $this->assertEquals($this->sessionToken, $this->sessionRegistrar->register($this->clientToken, $this->fingerprint));
    }

    /**
     * testFingerprintNull
     * @expectedException eig\APIAuth\Exceptions\SessionException
     */
    public function testFingerprintNull() {
        $this->sessionRegistrar->register($this->clientToken, null);
        $this->setExpectedExceptionFromAnnotation();
    }

    /**
     * testFingerprintEmpty
     * @expectedException eig\APIAuth\Exceptions\SessionException
     */
    public function testFingerprintEmpty() {
        $this->sessionRegistrar->register($this->clientToken, '');
        $this->setExpectedExceptionFromAnnotation();
    }

    /**
     * testFingerprintShort
     * @expectedException eig\APIAuth\Exceptions\SessionException
     */
    public function testFingerprintShort() {
        $this->sessionRegistrar->register($this->clientToken, 'ABCDEF');
        $this->setExpectedExceptionFromAnnotation();
    }

    /**
     * testClientTokenNull
     * @expectedException eig\APIAuth\Exceptions\SessionException
     */
    public function testClientTokenNull() {
        $this->sessionRegistrar->register(null, $this->fingerprint);
        $this->setExpectedExceptionFromAnnotation();
    }

    /**
     * testClientTokenEmpty
     * @expectedException eig\APIAuth\Exceptions\SessionException
     */
    public function testClientTokenEmpty() {
        $this->sessionRegistrar->register('', $this->fingerprint);
        $this->setExpectedExceptionFromAnnotation();
    }

    /**
     * testClientTokenShort
     * @expectedException eig\APIAuth\Exceptions\SessionException
     */
    public function testClientTokenShort() {
        $this->sessionRegistrar->register('ABCDEF', $this->fingerprint);
        $this->setExpectedExceptionFromAnnotation();
    }

    /**
     * testClientTokenExists
     * @expectedException eig\APIAuth\Exceptions\SessionException
     */
    public function testClientTokenExists() {
        $this->persistence->shouldReceive('exists')->andReturn(true);
        $this->sessionRegistrar->register($this->clientToken, $this->fingerprint);
        $this->setExpectedExceptionFromAnnotation();
    }
}
