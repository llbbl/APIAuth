<?php

namespace eig\APIAuth\Users;


use eig\APIAuth\Contracts\UserPersistenceInterface;
use eig\APIAuth\Contracts\TokenFieldGeneratorInterface;
use eig\APIAuth\Exceptions\UserException;
use eig\Configurator\Configurator;

class UserRegistrar
{
    protected $persistence;

    protected $tokenGenerator;

    protected $config;


    public function __construct (
        UserPersistenceInterface $persistence,
        TokenFieldGeneratorInterface $tokenGenerator,
        Configurator $config
    )
    {
        $this->persistence = $persistence;
        $this->tokenGenerator = $tokenGenerator;
        $this->config = $config;
    }

    public function register($username, $email = null, $password, $confirmPassword = null)
    {
        try {
            if(
            $this->registrationChecks($username, $email, $password, $confirmPassword)
            ) {
                $token = $this->generateUserToken($username, $email);
                $this->create($username, $email, $password, $token);
                return $token;
            }
        } catch (\Exception $e) {
            throw new UserException('Cannot Register this User', 1, $e);
        }
    }

    public function emailExists($email) {
        return $this->persistence->exists('email', $email);
    }

    public function userExists($username) {
        return $this->persistence->exists('username', $username);
    }

    protected function create($username, $email, $password, $token)
    {
        $this->persistence->create($username, $email, $password, $token);
    }

    protected function identicalPasswords($password, $confirmPassword = null)
    {
        if(!empty($confirmPassword)) {
            if($password === $confirmPassword) {
                return true;
            }
        } else {
            return true;
        }

        throw new UserException('Passwords do not Match', 1);
    }

    protected function passwordMeetsConstraints($password) {
        $error = '';

        if(empty($password)) {
            $error = "Password cannot be empty ";
        }

        if( strlen($password) < 8 ) { //add config vlaue for password len
            $error .= "Password too short! ";
        }

        if( !preg_match("#[0-9]+#", $password) ) {
            $error .= "Password must include at least one number! ";
        }


        if( !preg_match("#[a-z]+#", $password) ) {
            $error .= "Password must include at least one letter! ";
        }


        if( !preg_match("#[A-Z]+#", $password) ) {
            $error .= "Password must include at least one CAPS! ";
        }



        if( !preg_match("#\W+#", $password) ) {
            $error .= "Password must include at least one symbol!";
        }

        if(!empty($error)){
            throw new UserException("Password validation failure:" . $error ." ", 1);
        } else {
            return true;
        }
    }

    protected function isUniuqe($username, $email = null) {
        if($this->userExists($username)) {
            throw new UserException('User Already Exists', 1);
        }

        if(!empty($email)) {
            if($this->emailExists($email))
            {
                throw new UserException('Email Address Already Exists', 1);
            }
        }

        return true;
    }

    protected function registrationChecks($username, $email, $password, $confirmPassword) {
        $return = '';
        try {
            if($this->isUniuqe($username, $email) == true)
            {
                $return = true;
            }
            if($this->identicalPasswords($password, $confirmPassword) == true)
            {
                $return = true;
            }
            if($this->passwordMeetsConstraints($password) == true)
            {
                $return = true;
            }
            return $return;
        } catch (\Exception $e) {
            throw new UserException('Error User Registration Data did not pass checks', 1, $e);
        }
    }

    protected function generateUserToken($username, $email = null) {
        $seed = '';
        if(empty($email)) {
            $seed = $username;
        } else {
            $seed = $username . $email;
        }
        $this->tokenGenerator->generate($seed);
    }

}