<?php

namespace eig\APIAuth\Tests;

use eig\APIAuth\Exceptions\UserException;
use eig\APIAuth\Tokens\TokenFieldGenerator;
use eig\Configurator\Configurator;
use eig\Configurator\Options;
use eig\APIAuth\Users\UserRegistrar;
use Mockery;

class UserRegistrarTest extends TestAbstract
{
    protected $persistence;

    protected $tokenGenerator;

    protected $userRegistrar;

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

    public function setUp ()
    {
        parent::setUp();
        $this->configOptions = new Options();
        $this->configOptions->basePath = realpath('src/config');
        $this->config = new Configurator($this->configFiles, $this->configOptions);
        $this->tokenGenerator = new TokenFieldGenerator();
        $this->persistence = Mockery::mock('eig\APIAuth\Abstracts\AbstractUserPersistence')->makePartial();
        $this->userRegistrar = new UserRegistrar($this->persistence, $this->tokenGenerator, $this->config);
    }

    public function tearDown ()
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testInstantiation() {
        $this->assertInstanceOf('eig\APIAuth\Users\UserRegistrar', $this->userRegistrar);
    }

    public function testRegister() {
        $this->persistence->shouldReceive('exists')->andReturn(false);
        $this->persistence->shouldReceive('create');

        $this->userRegistrar->register('username', 'email@email.com', 'Password1123&&', 'Password1123&&');
    }
}
