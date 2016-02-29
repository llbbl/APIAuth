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


    public function setUp ()
    {
        parent::setUp();
        $this->configOptions = new ConfigOptions();
        $this->configOptions->basePath = realpath('src/config');
        $this->config = new Configurator($this->configFiles, $this->configOptions);
    }

    public function tearDown ()
    {
        parent::tearDown();
    }

    public function testBuild() {
        JWT::build($this->config, $this->fields);
    }


}
