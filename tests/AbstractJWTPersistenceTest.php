<?php

namespace eig\APIAuth\Tests;

use Mockery;
use eig\APIAuth\Abstracts\AbstractJWTPersistence;

class AbstractJWTPersistenceTest extends TestAbstract
{
    protected  $model;

    protected $jwtPersistence;

    public function setUp()
    {
        $this->model = Mockery::mock('overload:Illuminate\Database\Eloquent\Model');
        $this->jwtPersistence = Mockery::mock('eig\APIAuth\Abstracts\AbstractJWTPersistence')->makePartial();
        parent::setUp();
    }

    public function tearDown()
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testConstructor() {
        $this->assertInstanceOf('eig\APIAuth\Abstracts\AbstractJWTPersistence', $this->jwtPersistence);
    }
}
