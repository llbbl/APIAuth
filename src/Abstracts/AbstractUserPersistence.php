<?php

namespace eig\APIAuth\Abstracts;


use eig\APIAuth\Contracts\UserPersistenceInterface;

class AbstractUserPersistence implements UserPersistenceInterface
{
    protected $userModel;

    public function __construct ($model)
    {
        $this->userModel = $model;
    }

    public function create ($username, $email, $password, $token)
    {
        $this->userModel->create(['username' => $username, 'email' => $email, 'password' => $password, 'token' => $token]);
    }

    public function exists ($key, $value)
    {
        $exists = $this->userModel->where($key, '=', $value)->first();
        if(is_null($exists)) {
            return false;
        } else {
            return true;
        }
    }

    public function find (array $params)
    {
        return $this->userModel->whereNested(function($query, $params)
        {
            foreach ($params as $key => $value)
            {
                $query->where($key, '=', $value);
            }
        })->get();
    }


}