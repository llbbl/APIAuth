<?php

namespace eig\APIAuth\Tests;

use eig\Configurator\Configurator;
use eig\Configurator\Options as ConfigOptions;
use Mockery;
use Lcobucci\JWT\Parser;
use eig\APIAuth\Facades\JWT;

class JWTTest extends TestAbstract
{

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

    protected $fields = [
        'test' => 'value',
        'iam' => 'a field',
        'token' => 'token'
    ];

    protected $persistence;


    public function setUp()
    {
        parent::setUp();
        $this->persistence = Mockery::mock('eig\APIAuth\Abstracts\AbstractJWTPersistence');
        $this->configOptions = new ConfigOptions();
        $this->configOptions->basePath = realpath('src/config');
        $this->config = new Configurator($this->configFiles, $this->configOptions);
        JWT::initialize($this->config, $this->persistence);
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    public function testBuild()
    {
        $this->persistence->shouldReceive('id')->andReturn('123456543');
        $this->persistence->shouldReceive('issued');
        $this->persistence->shouldReceive('expiration');
        $this->persistence->shouldReceive('notBefore');
        $this->persistence->shouldReceive('token');
        $this->persistence->shouldReceive('create');
        $this->persistence->shouldReceive('save');
        $token = JWT::build($this->fields);
        $token = (new Parser())->parse((string)$token);
        $this->assertEquals($this->fields, json_decode($token->getClaim('data'), true));
    }

    public function testParse()
    {
        $this->persistence->shouldReceive('create');
        $this->persistence->shouldReceive('issued');
        $this->persistence->shouldReceive('expiration');
        $this->persistence->shouldReceive('notBefore');
        $this->persistence->shouldReceive('token');
        $this->persistence->shouldReceive('save');
        $this->persistence->shouldReceive('id')->andReturn('123456543');
        $token = JWT::build($this->fields);
        $parsedTestToken = (new Parser())->parse((string)$token);
        $parsedToken = JWT::parse($token);
        $this->assertEquals(json_decode($parsedTestToken->getClaim('data')), json_decode($parsedToken->getClaim('data')));
    }

    public function testValidate()
    {
        $this->persistence->shouldReceive('get');
        $this->persistence->shouldReceive('id')->andReturn('123456543');
        $this->persistence->shouldReceive('issued');
        $this->persistence->shouldReceive('expiration');
        $this->persistence->shouldReceive('notBefore');
        $this->persistence->shouldReceive('create');
        $this->persistence->shouldReceive('token');
        $this->persistence->shouldReceive('save');
        $token = JWT::build($this->fields);
        sleep(31);
        $this->assertTrue(JWT::validate($token));
        return true;
    }

    public function testAdd()
    {
        $newData = ['my' => 'new data'];
        $this->persistence->shouldReceive('get');
        $this->persistence->shouldReceive('id')->andReturn('123456543');
        $this->persistence->shouldReceive('issued');
        $this->persistence->shouldReceive('expiration');
        $this->persistence->shouldReceive('notBefore');
        $this->persistence->shouldReceive('create');
        $this->persistence->shouldReceive('token');
        $this->persistence->shouldReceive('save');
        $token = JWT::build($this->fields);
        $token = JWT::add($token, $newData);
        $this->assertArraySubset($newData, json_decode($token->getClaim('data'), true));
        $this->assertArraySubset($this->fields, json_decode($token->getClaim('data'), true));
    }
}
