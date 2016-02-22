<?php

namespace eig\APIAuth\Facades;

use eig\APIAuth\Contracts\ClientPersistenceInterface;
use eig\APIAuth\Exceptions\ClientException;
use eig\Configurator\Configurator;
use eig\APIAuth\ClientRegistrar as ClientRegistrarObject;


class ClientRegistrar
{
    protected static $clientRegistrar;

    protected static $persistence;

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

    public static function initialize($persistence){
        self::$config = new Configurator(self::$configFile);
        if ($persistence != null && $persistence instanceof ClientPersistenceInterface)
        {
            self::$persistence = $persistence;
        } else {
            self::$persistence = self::$config['APIAuth']['Client Persistence']();
        }
        self::$clientRegistrar = new ClientRegistrarObject($persistence);
    }



        public static function register($fingerprint, $type, $persistence){
            self::initialize($persistence);
            return self::$clientRegistrar->register($fingerprint, $type);
        }

}