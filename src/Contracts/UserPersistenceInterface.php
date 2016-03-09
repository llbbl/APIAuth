<?php

namespace eig\APIAuth\Contracts;


interface UserPersistenceInterface
{
    public function create($username, $email, $password, $token);

    public function exists($key, $value);

    public function find(array $params);
}