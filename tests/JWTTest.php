<?php

namespace eig\APIAuth\Tests;

use eig\Configurator\Configurator;
use eig\Configurator\Options as ConfigOptions;
use Mockery;
use Lcobucci\JWT\Parser;
use eig\APIAuth\Facades\JWT;

/**
 * Class JWTTest
 * @package eig\APIAuth\Tests
 */
class JWTTest extends TestAbstract
{

    /**
     * @var array
     */
    protected $configFiles = [
        [
           'source' => 'APIAuth.php',
           'path' => 'src/config/',
           'pathType' => 'relative',
           'type' => 'array',
           'alias' => 'APIAuth'
        ]
    ];

    /**
     * @var
     */
    protected $configOptions;

    /**
     * @var
     */
    protected $config;

    /**
     * @var array
     */
    protected $fields = [
        'test' => 'value',
        'iam' => 'a field',
        'token' => 'token'
    ];

    /**
     * @var
     */
    protected $persistence;


    /**
     * setUp
     */
    public function setUp()
    {
        parent::setUp();
        $this->persistence = Mockery::mock('eig\APIAuth\Abstracts\AbstractJWTPersistence');
        $this->configOptions = new ConfigOptions();
        $this->configOptions->basePath = realpath('src/config');
        $this->config = new Configurator($this->configFiles, $this->configOptions);
        JWT::initialize($this->config, $this->persistence);
    }

    /**
     * tearDown
     */
    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * testBuild
     * @throws \eig\APIAuth\Exceptions\JWTException
     */
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

    /**
     * testParse
     * @throws \eig\APIAuth\Exceptions\JWTException
     */
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

    /**
     * testValidate
     * @return bool
     * @throws \eig\APIAuth\Exceptions\JWTException
     */
    public function testValidate()
    {
        $this->persistence->shouldReceive('get');
        $this->persistence->shouldReceive('id')->andReturn('123456543');
        $this->persistence->shouldReceive('find')->andReturn('123456543');
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

    /**
     * testAdd
     * @throws \eig\APIAuth\Exceptions\JWTException
     */
    public function testAdd()
    {
        $newData = ['my' => 'new data'];
        $this->persistence->shouldReceive('get');
        $this->persistence->shouldReceive('find')->andReturn('123456543');
        $this->persistence->shouldReceive('id')->andReturn('123456543');
        $this->persistence->shouldReceive('issued');
        $this->persistence->shouldReceive('expiration');
        $this->persistence->shouldReceive('notBefore');
        $this->persistence->shouldReceive('create');
        $this->persistence->shouldReceive('token');
        $this->persistence->shouldReceive('save');
        $token = JWT::build($this->fields);
        $token = JWT::add($token, $newData);
        $stringToken = (string)$token;
        $parsedToken = JWT::parse($stringToken);
        $this->assertArraySubset($newData, json_decode($token->getClaim('data'), true));
        $this->assertArraySubset($this->fields, json_decode($token->getClaim('data'), true));
        $this->assertArraySubset($newData, json_decode($parsedToken->getClaim('data'), true));
        $this->assertArraySubset($this->fields, json_decode($parsedToken->getClaim('data'), true));
    }

    public function testRemove()
    {
        $this->persistence->shouldReceive('get');
        $this->persistence->shouldReceive('find')->andReturn('123456543');
        $this->persistence->shouldReceive('id')->andReturn('123456543');
        $this->persistence->shouldReceive('issued');
        $this->persistence->shouldReceive('expiration');
        $this->persistence->shouldReceive('notBefore');
        $this->persistence->shouldReceive('create');
        $this->persistence->shouldReceive('token');
        $this->persistence->shouldReceive('save');
        $token = JWT::build($this->fields);
        $token = JWT::remove($token, 'token');
        $stringToken = (string)$token;
        $parsedToken = JWT::parse($stringToken);
        $this->assertArrayNotHasKey('token', json_decode($parsedToken->getClaim('data'), true));
    }
}
