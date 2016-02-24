<?php

namespace eig\APIAuth\Facades;

use eig\Configurator\Options as ConfigOptions;
use eig\APIAuth\Contracts\ClientPersistenceInterface;
use eig\APIAuth\Exceptions\ClientException;
use eig\APIAuth\Exceptions\SessionException;
use eig\APIAuth\Session\SessionRegistrar;
use eig\APIAuth\Contracts\SessionPersistenceInterface;
use eig\Configurator\Configurator;
use eig\APIAuth\Client\ClientRegistrar as ClientRegistrarObject;


class ClientRegistrar
{
    protected static $clientRegistrar;

    protected static $sessionRegistrar;

    protected static $tokenGenerator;

    protected static $clientPersistence;

    protected static $sessionPersistence;

    protected static $config;

    protected static $configOptions;

    protected static $configFile = [
        [
            'source' => 'APIAuth.php',
            'path' => 'src/config/',
            'pathType' => 'relative',
            'type' => 'array',
            'alias' => 'APIAuth'
        ],
    ];

    public static function initialize($clientPersistence = null, $sessionPersistence = null){
        self::$configOptions = new ConfigOptions();
        self::$configOptions->basePath = realpath('src/config');
        try {
            self::$config = new Configurator(self::$configFile, self::$configOptions);
        } catch (\Exception $exception) {
            throw new ClientException('unable to load APIAUth Options', 1, $exception);
        }

        self::initializeTokenGenerator();
        self::initializeClient($clientPersistence);
        self::initializeSession($sessionPersistence);
    }



    public static function register($fingerprint, $type){
      if (
           is_a(self::$clientRegistrar, 'eig\APIAuth\Client\ClientRegistrar') == false
           ||
           is_a(self::$sessionRegistrar, 'eig\APIAuth\Session\SessionRegistrar') == false
      )
      {
          self::initialize();
      }
        return self::$clientRegistrar->register($fingerprint, $type);
        //next do session registration
        //then call a token builder
        // set claims on token of client and session
        // return token
    }

    protected static function initializeTokenGenerator(){
        self::$tokenGenerator = new self::$config['APIAuth']['Token Field Generator']();
    }

    protected static function initializeClient($persistence = null) {
        if (!empty($persistence) && $persistence instanceof ClientPersistenceInterface)
        {
            self::$clientPersistence = $persistence;
        } else {
             self::$clientPersistence = new self::$config['APIAuth']['Client Persistence']();
        }
        self::$clientRegistrar = new ClientRegistrarObject(self::$clientPersistence, self::$tokenGenerator);
    }

    protected static function initializeSession($persistence = null) {
        if ($persistence != null && $persistence instanceof SessionPersistenceInterface)
        {
            self::$sessionPersistence = $persistence;
        } else {
            self::$sessionPersistence = new self::$config['APIAuth']['Session Persistence']();
        }
        self::$sessionRegistrar = new SessionRegistrar(self::$sessionPersistence, self::$tokenGenerator);
    }

    public static function getResitrar() {
        return self::$clientRegistrar;
    }
}