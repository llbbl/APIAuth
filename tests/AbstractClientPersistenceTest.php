<?php

namespace eig\APIAuth\Tests;

use Mockery;

class AbstractClientPersistenceTest extends TestAbstract
{

    protected $clientPersistence;

    protected $model;


    public function setUp()
    {
        $this->model = Mockery::mock('overload:Illuminate\Database\Eloquent\Model');
        $this->clientPersistence = Mockery::mock('eig\APIAuth\Abstracts\AbstractClientPersistence')->makePartial();
        parent::setUp();
    }

    public function tearDown()
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testTrue()
    {
        return true;
    }

    public function testInstantiation() {
        //$this->assertTrue(is_subclass_of($this->clientPersistence, 'eig\APIAuth\Abstracts\AbstractClientPersistence'));
        $this->assertInstanceOf('eig\APIAuth\Abstracts\AbstractClientPersistence', $this->clientPersistence);
    }

    public function testExpired() {
        // maybe make a test concrete implementation that returns true for any abstract methods
        $this->model->shouldReceive('isExpired')->andReturn(true);
        $this->model->shouldReceive('setExpired');
        $this->clientPersistence->setExpired(true);
        $this->assertTrue($this->clientPersistence->isExpired());
    }
}
