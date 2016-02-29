<?php

namespace eig\APIAuth\Tests;

use Mockery;

class AbstractClientPersistenceTest extends TestAbstract
{

    protected $clientPersistence;

    protected $model;

    protected $mockery;

    public function setUp ()
    {
        $this->mockery = new Mockery\Mock();
        $this->model = Mockery::mock('overload:Illuminate\Database\Eloquent\Model');
        $this->clientPersistence = Mockery::mock('stdClass, eig\APIAuth\Abstracts\AbstractClientPersistence');
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

    public function testExpired() {
        // maybe make a test conrete implementation that returns true for any abstract methods
        $this->clientPersistence->setExpired(true);
        $this->assertTrue($this->clientPersistence->isExpired);
    }
}
