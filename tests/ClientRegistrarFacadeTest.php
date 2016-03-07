<?php

namespace eig\APIAuth\Tests;

use eig\APIAuth\Facades\ClientRegistrar;
use eig\Configurator\Configurator;
use eig\Configurator\Options as ConfigOptions;
use Lcobucci\JWT\Parser;
use PasswordLib\PasswordLib;
use eig\APIAuth\Facades\JWT;
use Mockery;

class ClientRegistrarFacadeTest extends TestAbstract
{
    protected $clientPersistence;

    protected $sessionPersistence;

    protected $tokenGenerator;

    protected $library;

    protected $fingerprint;

    protected $jwtPersistence;

    /**
     * @var
     */
    protected $type;

    protected $configFiles = [
        [
            'source' => 'APIAuth.php',
            'path' => 'src/config/',
            'pathType' => 'relative',
            'type' => 'array',
            'alias' => 'APIAuth'
        ]
    ];

    protected $configOptions;

    protected $config;


    public function setUp()
    {
        $this->configOptions = new ConfigOptions();
        $this->configOptions->basePath = realpath('src/config');
        $this->config = new Configurator($this->configFiles, $this->configOptions);
        $this->fingerprint = md5('this is the clients guid');
        $this->type = 'Android';
        $this->library = new PasswordLib();
        $this->clientPersistence = Mockery::mock('eig\APIAuth\Contracts\ClientPersistenceInterface');
        $this->sessionPersistence = Mockery::mock('eig\APIAuth\Contracts\SessionPersistenceInterface');
        $this->jwtPersistence = Mockery::mock('eig\APIAuth\Abstracts\AbstractJWTPersistence');
        $this->jwtPersistence->shouldReceive('issued');
        $this->jwtPersistence->shouldReceive('expiration');
        $this->jwtPersistence->shouldReceive('notBefore');
        $this->jwtPersistence->shouldReceive('token');
        $this->jwtPersistence->shouldReceive('save');
        JWT::initialize($this->config, $this->jwtPersistence);

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
        $this->jwtPersistence->shouldReceive('create');
        $this->jwtPersistence->shouldReceive('id')->andReturn('123456543');
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

        ClientRegistrar::initialize($this->clientPersistence, $this->sessionPersistence, null, $this->jwtPersistence);
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
