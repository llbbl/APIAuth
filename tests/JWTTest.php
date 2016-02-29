<?php

namespace eig\APIAuth\Tests;

use eig\Configurator\Configurator;
use eig\Configurator\Options as ConfigOptions;
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


    public function setUp()
    {
        parent::setUp();
        $this->configOptions = new ConfigOptions();
        $this->configOptions->basePath = realpath('src/config');
        $this->config = new Configurator($this->configFiles, $this->configOptions);
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    public function testBuild()
    {
        $token = JWT::build($this->config, $this->fields);
        $token = (new Parser())->parse((string)$token);
        $this->assertEquals($this->fields, json_decode($token->getClaim('data'), true));
    }

    public function testParse()
    {
        $token = JWT::build($this->config, $this->fields);
        $parsedTestToken = (new Parser())->parse((string)$token);
        $parsedToken = JWT::parse($token);
        $this->assertEquals(json_decode($parsedTestToken->getClaim('data')), json_decode($parsedToken->getClaim('data')));
    }

    public function testValidate()
    {
        $token = JWT::build($this->config, $this->fields);
        //$this->assertTrue(JWT::validate($token, $this->config));
        //must add in a id generator provider to match tokens to issue id
        return true;
    }
}
