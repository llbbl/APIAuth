<?php

namespace eig\APIAuth\Tests;

use eig\APIAuth\Facades\ClientRegistrar;
use PasswordLib\PasswordLib;
use Mockery;

class ClientRegistrarFacadeTest extends TestAbstract
{
    protected $clientPersistence;

    protected $sessionPersistence;

    protected $tokenGenerator;

    protected $library;

    protected $fingerprint;

    /**
     * @var
     */
    protected $type;


    public function setUp ()
    {
        $this->fingerprint = md5('this is the clients guid');
        $this->type = 'Android';
        $this->library = new PasswordLib();
        $this->clientPersistence = Mockery::mock('overload:eig\APIAuth\Contracts\ClientPersistenceInterface');
        $this->sessionPersistence = Mockery::mock('overload:eig\APIAuth\Contracts\SessionPersistenceInterface');


        parent::setUp();
    }

    public function tearDown ()
    {
        \Mockery::close();
        parent::tearDown();
    }

    public function testRegister() {
        $this->clientPersistence->shouldReceive('fingerprint')->andReturn(true);
        $this->clientPersistence->shouldReceive('exists')->andReturn(false);
        $this->clientPersistence->shouldReceive('type');
        $this->clientPersistence->shouldReceive('token')->andReturn(
            $this->library->createPasswordHash($this->fingerprint . $this->type, '$2a$', array('cost' => 10))
        );
        $this->clientPersistence->shouldReceive('save')->andReturn(true);

        ClientRegistrar::initialize($this->clientPersistence, $this->sessionPersistence);
        $token = ClientRegistrar::register($this->fingerprint, $this->type);

        $this->assertTrue($this->library->verifyPasswordHash($this->fingerprint . $this->type, $token));
    }
}
