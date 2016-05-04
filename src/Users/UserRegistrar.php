<?php

namespace eig\APIAuth\Users;


use eig\APIAuth\Contracts\UserPersistenceInterface;
use eig\APIAuth\Contracts\TokenFieldGeneratorInterface;
use eig\APIAuth\Exceptions\UserException;
use eig\Configurator\Configurator;

/**
 * Class UserRegistrar
 * @package eig\APIAuth\Users
 */
class UserRegistrar
{

    /**
     * @var \eig\APIAuth\Contracts\UserPersistenceInterface
     */
    protected $persistence;

    /**
     * @var \eig\APIAuth\Contracts\TokenFieldGeneratorInterface
     */
    protected $tokenGenerator;

    /**
     * @var \eig\Configurator\Configurator
     */
    protected $config;


    /**
     * UserRegistrar constructor.
     *
     * @param \eig\APIAuth\Contracts\UserPersistenceInterface     $persistence
     * @param \eig\APIAuth\Contracts\TokenFieldGeneratorInterface $tokenGenerator
     * @param \eig\Configurator\Configurator                      $config
     */
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

    /**
     * register
     *
     * @param      $username
     * @param null $email
     * @param      $password
     * @param null $confirmPassword
     *
     * @return string
     * @throws \eig\APIAuth\Exceptions\UserException
     */
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

    /**
     * authByToken
     *
     * @param $token
     *
     * @return bool
     */
    public function authByToken($token) {
        if(!empty($this->retrieveByToken($token)))
        {
            return true;
        }
        return false;
    }

    /**
     * authByPassword
     *
     * @param $username
     * @param $password
     */
    public function authByPassword($username, $password) {
        $user = $this->persistence->find(['username' => $username]);
        if(password_verify($password, $user->password)) {
            return $user->token;
        }
        return false;
    }

    /**
     * login
     *
     * @param null $username
     * @param null $password
     * @param null $token
     */
    public function login($username = null, $password = null, $token = null) {
        if(!empty($username) && !empty($password)) {
            return $this->authByPassword($username, $password);
        } else {
            return $this->retrieveByToken($token);
        }
    }

    /**
     * retrieveById
     *
     * @param $identifier
     *
     * @return mixed
     */
    public function retrieveById($identifier) {
        return $this->persistence->find(['id' => $identifier]);
    }


    /**
     * retrieveByToken
     *
     * @param $token
     *
     * @return mixed
     */
    public function retrieveByToken($token) {
        return $this->persistence->find(['token' => $token]);
    }

    /**
     * emailExists
     *
     * @param $email
     *
     * @return mixed
     */
    public function emailExists($email) {
        return $this->persistence->exists('email', $email);
    }

    /**
     * userExists
     *
     * @param $username
     *
     * @return mixed
     */
    public function userExists($username) {
        return $this->persistence->exists('username', $username);
    }

    /**
     * create
     *
     * @param $username
     * @param $email
     * @param $password
     * @param $token
     */
    protected function create($username, $email, $password, $token)
    {
        $password = password_hash($password, PASSWORD_DEFAULT);
        $this->persistence->create($username, $email, $password, $token);
    }

    /**
     * identicalPasswords
     *
     * @param      $password
     * @param null $confirmPassword
     *
     * @return bool
     * @throws \eig\APIAuth\Exceptions\UserException
     */
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

    /**
     * passwordMeetsConstraints
     *
     * @param $password
     *
     * @return bool
     * @throws \eig\APIAuth\Exceptions\UserException
     */
    protected function passwordMeetsConstraints($password) {
        $error = '';

        if(empty($password)) {
            $error = "Password cannot be empty ";
        }

        if( strlen($password) < 6 ) { //add config value for password len
            $error .= "Password too short! ";
        }

        if( !preg_match("#[0-9]+#", $password) ) {
            $error .= "Password must include at least one number! ";
        }


       /* if( !preg_match("#[a-z]+#", $password) ) {
            $error .= "Password must include at least one letter! ";
        }


        if( !preg_match("#[A-Z]+#", $password) ) {
            $error .= "Password must include at least one CAPS! ";
        }



        if( !preg_match("#\W+#", $password) ) {
            $error .= "Password must include at least one symbol!";
        }*/

        if(!empty($error)){
            throw new UserException("Password validation failure:" . $error ." ", 1);
        } else {
            return true;
        }
    }

    /**
     * isUniuqe
     *
     * @param      $username
     * @param null $email
     *
     * @return bool
     * @throws \eig\APIAuth\Exceptions\UserException
     */
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

    /**
     * registrationChecks
     *
     * @param $username
     * @param $email
     * @param $password
     * @param $confirmPassword
     *
     * @return bool|string
     * @throws \eig\APIAuth\Exceptions\UserException
     */
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

    /**
     * generateUserToken
     *
     * @param      $username
     * @param null $email
     *
     * @return string
     */
    protected function generateUserToken($username, $email = null) {
        $seed = '';
        if(empty($email)) {
            $seed = $username;
        } else {
            $seed = $username . $email;
        }
        return $this->tokenGenerator->generate($seed);
    }

}