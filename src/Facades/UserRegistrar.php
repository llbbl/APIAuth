<?php

namespace eig\APIAuth\Facades;

use eig\APIAuth\Exceptions\JWTException;
use eig\APIAuth\Exceptions\UserException;
use eig\APIAuth\Users\UserRegistrar as Registrar;
use eig\APIAuth\Tokens\TokenFieldGenerator;

/**
 * Class UserRegistrar
 * @package eig\APIAuth\Facades
 */
class UserRegistrar
{

    /**
     * @var
     */
    protected $registrar;

    /**
     * @var
     */
    protected $persistence;

    /**
     * @var
     */
    protected $config;


    /**
     * initialize
     *
     * @param      $config
     * @param null $persistence
     *
     * @throws \eig\APIAuth\Exceptions\UserException
     */
    public static function initialize($config, $persistence = null)
    {
        if(!empty($config))
        {
            if (is_a($config, 'eig\Configurator\Configurator'))
            {
                self::$config = $config;
            }
            elseif (is_array($config))
            {
                try
                {
                    self::$config = new Configurator($config, new Options());
                } catch (\Exception $e)
                {
                    throw new UserException(
                        'Incorrect config file supplied, must be a Configurator Config File Array', 1, $e
                    );
                }
            }
            else
            {
                throw new UserException('Error, a config file or Configurator Object must be supplied', 1);
            }
        } else {
            throw new UserException('Error, a config file or Configurator Object must be supplied', 1);
        }

        self::$persistence = new $config['APIAuth']['User Persistence']();

        self::$registrar = new Registrar(self::$persistence, new TokenFieldGenerator(), self::$config);
    }

    /**
     * register
     *
     * @param      $username
     * @param null $email
     * @param      $password
     * @param null $confirmPassword
     *
     * @return mixed
     */
    public static function register($username, $email = null, $password, $confirmPassword = null)
    {
        return self::$registrar->register($username, $email, $password, $confirmPassword);
    }

    /**
     * authByToken
     *
     * @param $token
     *
     * @return mixed
     */
    public function authByToken($token) {
        return self::$registrar->authByToken($token);
    }

    /**
     * authByPassword
     *
     * @param $username
     * @param $password
     *
     * @return mixed
     */
    public function authByPassword($username, $password) {
        return self::$registrar->authByPassword($username, $password);
    }

    /**
     * login
     *
     * @param null $username
     * @param null $password
     * @param null $token
     *
     * @return mixed
     */
    public function login($username = null, $password = null, $token = null) {
        return self::$registrar->login($username, $password, $token);
    }

    /**
     * retrieveById
     *
     * @param $identifier
     *
     * @return mixed
     */
    public function retrieveById($identifier) {
        return self::$registrar->retrieveById($identifier);
    }

    /**
     * retrieveByToken
     *
     * @param $token
     *
     * @return mixed
     */
    public function retrieveByToken($token) {
        return self::$registrar->retrieveByToken($token);
    }

    /**
     * emailExists
     *
     * @param $email
     *
     * @return mixed
     */
    public function emailExists($email) {
        return self::$registrar->emailExists($email);
    }

    /**
     * userExists
     *
     * @param $username
     *
     * @return mixed
     */
    public function userExists($username) {
        return self::$registrar->userExists($username);
    }

}