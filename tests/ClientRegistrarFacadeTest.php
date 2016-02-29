<?php

namespace eig\APIAuth\Tests;

use eig\APIAuth\Facades\ClientRegistrar;
use Lcobucci\JWT\Parser;
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


    public function setUp()
    {
        $this->fingerprint = md5('this is the clients guid');
        $this->type = 'Android';
        $this->library = new PasswordLib();
        $this->clientPersistence = Mockery::mock('overload:eig\APIAuth\Contracts\ClientPersistenceInterface');
        $this->sessionPersistence = Mockery::mock('eig\APIAuth\Contracts\SessionPersistenceInterface');


        parent::setUp();
    }

    public function tearDown()
    {
        \Mockery::close();
        parent::tearDown();
    }

    public function testRegister()
    {
        $ctoken = $this->library->createPasswordHash($this->fingerprint . $this->type, '$2a$', array('cost' => 10));
        $this->clientPersistence->shouldReceive('fingerprint')->andReturn(true);
        $this->clientPersistence->shouldReceive('exists')->andReturn(false);
        $this->clientPersistence->shouldReceive('type');
        $this->clientPersistence->shouldReceive('create');
        $this->clientPersistence->shouldReceive('token')->andReturn($ctoken);
        $this->clientPersistence->shouldReceive('save')->andReturn(true);
        $this->sessionPersistence->shouldReceive('client')->andReturn(true);
        $this->sessionPersistence->shouldReceive('setRevoked');
        $this->sessionPersistence->shouldReceive('create');
        $this->sessionPersistence->shouldReceive('timeout');
        $this->sessionPersistence->shouldReceive('exists')->andReturn(false);
        $this->sessionPersistence->shouldReceive('token')->andReturn(
            $this->library->createPasswordHash($ctoken . $this->fingerprint, '$2a$', array('cost' => 10))
        );
        $this->sessionPersistence->shouldReceive('save')->andReturn(true);

        ClientRegistrar::initialize($this->clientPersistence, $this->sessionPersistence);
        $data = ClientRegistrar::register($this->fingerprint, $this->type);
        $data = (new Parser())->parse((string) $data);
        //$data->getHeaders();
        //$data->getClaims();
        $registrarData = (array)json_decode($data->getClaim('data'));
        //print_r($registrarData);
        $clientToken = $registrarData['ClientToken'];
        $sessionToken = $registrarData['SessionToken'];
        $this->assertTrue($this->library->verifyPasswordHash($this->fingerprint . $this->type, $clientToken));
        $this->assertTrue($this->library->verifyPasswordHash($ctoken . $this->fingerprint, $sessionToken));
    }
}
