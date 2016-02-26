<?php

namespace eig\APIAuth\Tests;

use Mockery;

class AbstractClientPersistenceTest extends TestAbstract
{

    protected $clientPersistence;

    public function setUp ()
    {
        $this->clientPersistence = Mockery::mock('overload:eig\APIAuth\Abstracts\AbstractClientPersistence');
        parent::setUp();
    }

    public function tearDown ()
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testInstantiation() {
        $this->assertInstanceOf('eig\APIAuth\Abstracts\AbstractClientPersistence', $this->clientPersistence);
    }
}
