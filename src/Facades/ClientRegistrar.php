<?php

namespace eig\APIAuth\Facades;

use eig\APIAuth\Contracts\JWTPersistenceInterface;
use eig\Configurator\Options as ConfigOptions;
use eig\APIAuth\Contracts\ClientPersistenceInterface;
use eig\APIAuth\Exceptions\ClientException;
use eig\APIAuth\Exceptions\SessionException;
use eig\APIAuth\Session\SessionRegistrar;
use eig\APIAuth\Contracts\SessionPersistenceInterface;
use eig\Configurator\Configurator;
use eig\APIAuth\Client\ClientRegistrar as ClientRegistrarObject;


/**
 * Class ClientRegistrar
 * @package eig\APIAuth\Facades
 */
class ClientRegistrar
{

    /**
     * @var
     */
    protected static $clientRegistrar;

    /**
     * @var
     */
    protected static $sessionRegistrar;

    /**
     * @var
     */
    protected static $tokenGenerator;

    /**
     * @var
     */
    protected static $clientPersistence;

    /**
     * @var
     */
    protected static $sessionPersistence;

    /**
     * @var
     */
    protected static $config;

    /**
     * @var
     */
    protected static $configOptions;

    /**
     * @var array
     */
    protected static $configFile = [
        [
            'source' => 'APIAuth.php',
            'path' => 'src/config/',
            'pathType' => 'relative',
            'type' => 'array',
            'alias' => 'APIAuth'
        ],
    ];


    /**
     * initialize
     *
     * @param null                                $clientPersistence
     * @param null                                $sessionPersistence
     * @param \eig\Configurator\Configurator|null $config
     * @param null                                $jwtPersistence
     *
     * @throws \eig\APIAuth\Exceptions\ClientException
     */
    public static function initialize(
        $clientPersistence = null,
        $sessionPersistence = null,
        Configurator $config = null,
        $jwtPersistence = null
    )
    {
        self::initializeConfig($config);
        self::initializeTokenGenerator();
        self::initializeClient($clientPersistence);
        self::initializeSession($sessionPersistence);
        self::initializeJWT($jwtPersistence);
    }


    /**
     * register
     *
     * @param $fingerprint
     * @param $type
     *
     * @return \Lcobucci\JWT\Token
     */
    public static function register($fingerprint, $type){
        $data = [];
        if (
           is_a(self::$clientRegistrar, 'eig\APIAuth\Client\ClientRegistrar') == false
           ||
           is_a(self::$sessionRegistrar, 'eig\APIAuth\Session\SessionRegistrar') == false
          )
          {
              self::initialize();
          }
        $data['ClientToken'] = self::$clientRegistrar->register($fingerprint, $type);
        $data['SessionToken'] = self::$sessionRegistrar->register($data['ClientToken'], $fingerprint);

        return JWT::build($data);

    }

    /**
     * initializeTokenGenerator
     */
    protected static function initializeTokenGenerator(){
        self::$tokenGenerator = new self::$config['APIAuth']['Token Field Generator']();
    }

    /**
     * initializeClient
     *
     * @param null $persistence
     */
    protected static function initializeClient($persistence = null) {
        if (!empty($persistence) && $persistence instanceof ClientPersistenceInterface)
        {
            self::$clientPersistence = $persistence;
        } else {
             self::$clientPersistence = new self::$config['APIAuth']['Client Persistence']();
        }
        self::$clientRegistrar = new ClientRegistrarObject(self::$clientPersistence, self::$tokenGenerator);
    }

    /**
     * initializeSession
     *
     * @param null $persistence
     */
    protected static function initializeSession($persistence = null) {
        if ($persistence != null && $persistence instanceof SessionPersistenceInterface)
        {
            self::$sessionPersistence = $persistence;
        } else {
            self::$sessionPersistence = new self::$config['APIAuth']['Session Persistence']();
        }
        self::$sessionRegistrar = new SessionRegistrar(self::$sessionPersistence, self::$tokenGenerator);
    }

    /**
     * initializeConfig
     *
     * @param \eig\Configurator\Configurator|null $config
     *
     * @throws \eig\APIAuth\Exceptions\ClientException
     */
    protected static function initializeConfig(Configurator $config = null) {
        if(empty($config)) {
            self::$configOptions = new ConfigOptions();
            self::$configOptions->basePath = realpath('src/config');
            try {
                self::$config = new Configurator(self::$configFile, self::$configOptions);
            } catch (\Exception $exception) {
                throw new ClientException('unable to load APIAUth Options', 1, $exception);
            }
        } else {
            self::$config = $config;
        }
    }

    /**
     * initializeJWT
     *
     * @param \eig\APIAuth\Contracts\JWTPersistenceInterface|null $jwtPersistence
     */
    protected static function initializeJWT(JWTPersistenceInterface $jwtPersistence = null)
    {
        if(!empty($jwtPersistence) && $jwtPersistence instanceof JWTPersistenceInterface)
        {
            JWT::initialize(self::$config, $jwtPersistence);
        } else {
            JWT::initialize(self::$config);
        }

    }


    /**
     * getRegistrar
     * @return mixed
     */
    public static function getRegistrar()
    {

        return self::$clientRegistrar;
    }
}