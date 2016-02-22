<?php

namespace eig\APIAuth\Tests;

use eig\APIAuth\APIAuth;
use eig\APIAuth\Tests\TestAbstract;

class APIAuthTest extends TestAbstract
{
    protected $APIAuth;

    public function setUp ()
    {
        $this->APIAuth = new APIAuth();
        parent::setUp();
    }

    public function testIsInstance() {
        $this->assertInstanceOf('eig\APIAuth\APIAuth', $this->APIAuth);
    }
}
