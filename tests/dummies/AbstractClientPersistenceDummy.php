<?php

namespace eig\APIAuth\Tests;

use eig\APIAuth\Abstracts\AbstractSessionPersistence;

class AbstractClientPersistenceDummy extends AbstractSessionPersistence
{

    public function exists(array $params)
    {
        return true;
    }

    public function save(array $params = null)
    {
        return true;
    }

    public function get(array $params)
    {
        return true;
    }

    public function all()
    {
        return true;
    }
}
