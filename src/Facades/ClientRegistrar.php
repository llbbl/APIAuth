<?php

namespace eig\APIAuth\Facades;

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

    protected static $configFile = [
        [
            'source' => 'APIAuth.php',
            'path' => '/src/config/',
            'pathType' => 'relative',
            'type' => 'array',
            'alias' => 'APIAuth'
        ],
    ];

    public static function initialize(){
        self::$config = new Configurator(self::$configFile);
        self::initializeTokenGenerator();
        self::initializeClient();
        self::initializeSession();
    }



    public static function register($fingerprint, $type){
        if (self::$clientRegistrar = null || self::$sessionRegistrar = null) {
            self::initialize();
        }
        self::initializeTokenGenerator();

        return self::$clientRegistrar->register($fingerprint, $type);
        //next do session registration
        //then call a token builder
        // set claims on token of client and session
        // return token
    }

    protected static function initializeTokenGenerator(){
        self::$tokenGenerator = new self::$config['APIAuth']['Token Generator']();
    }

    protected static function initializeClient($persistence = null) {
        if ($persistence != null && $persistence instanceof ClientPersistenceInterface)
        {
            self::$clientPersistence = $persistence;
        } else {
            self::$clientPersistence = new self::$config['APIAuth']['Client Persistence']();
        }
        self::$clientRegistrar = new ClientRegistrarObject(self::$clientPersistence, self::$tokenGenerator);
    }

    protected static function initializeSession($peristence = null) {

    }
}