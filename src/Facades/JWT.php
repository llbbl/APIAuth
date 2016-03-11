<?php

namespace eig\APIAuth\Facades;

use eig\APIAuth\Contracts\JWTPersistenceInterface;
use eig\APIAuth\Exceptions\JWTException;
use eig\Configurator\Configurator;
use eig\Configurator\Options;
use Lcobucci\JWT\ValidationData;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;

/**
 * Class JWT
 * @package eig\APIAuth\Facades
 */
class JWT
{

    /**
     * @var
     */
    protected static $persistence, $config, $signer;

    /**
     * initialize
     *
     * @param \eig\Configurator\Configurator || array                  $config
     * @param \eig\APIAuth\Contracts\JWTPersistenceInterface|null $persistence
     *
     * @throws JWTException
     */
    public static function initialize ($config, JWTPersistenceInterface $persistence = null)
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
                    $options = new Options();
                    $options->basePath = realpath('src/config');
                    self::$config = new Configurator($config, $options);
                } catch (\Exception $e)
                {
                    throw new JWTException(
                        'Incorrect config file supplied, must be a Configurator Config File Array', 1, $e
                    );
                }
            }
            else
            {
                throw new JWTException('Error, a config file or Configurator Object must be supplied', 1);
            }
        } else {
            throw new JWTException('Error, a config file or Configurator Object must be supplied', 1);
        }

        if(empty($persistence)) {
            self::$persistence = new self::$config['APIAuth']['JWT']['Storage']();
        } else
        {
            self::$persistence = $persistence;
        }
        $signatureMethod = self::$config['APIAuth']['JWT']['Signature'];
        self::$signer = new self::$config['APIAuth']['JWT']['Signature Methods'][$signatureMethod]();

    }

    /**
     * build
     *
     * @param array $params
     *
     * @return \Lcobucci\JWT\Token
     * @throws \eig\APIAuth\Exceptions\JWTException
     */
    public static function build(array $params)
    {
        self::persistenceCreate();
        try {
            $token = (new Builder())
                ->setIssuer(self::$config['APIAuth']['JWT']['Issuer'])
                ->setAudience(self::$config['APIAuth']['JWT']['Audience'])
                ->setId(self::$persistence->id(), true)
                ->setIssuedAt(time())
                ->setNotBefore(time() + self::$config['APIAuth']['JWT']['NotBefore'])
                ->setExpiration(time() + self::$config['APIAuth']['JWT']['Timeout'])
                ->set(self::$config['APIAuth']['JWT']['Fields'], json_encode($params))
                ->sign(self::$signer, (string)self::$persistence->id())
                ->getToken();

            self::$persistence->issued($token->getClaim('iat'));
            self::$persistence->expiration($token->getClaim('exp'));
            self::$persistence->notBefore($token->getClaim('nbf'));
            self::$persistence->token($token->getPayload());
            self::$persistence->save();
            return $token;
        } catch (\Exception $e) {

            throw new JWTException('Unable to build a JWT token', 1, $e);
        }
    }

    /**
     * parse
     *
     * @param $token
     *
     * @return \Lcobucci\JWT\Token
     * @throws \eig\APIAuth\Exceptions\JWTException
     */
    public static function parse($token)
    {
        try {
            return (new Parser())->parse((string)$token);
        } catch (\Exception $e) {
            throw new JWTException('Unable to parse token', 1, $e);
        }
    }

    /**
     * validate
     *
     * @param $token
     *
     * @return mixed
     * @throws \eig\APIAuth\Exceptions\JWTException
     */
    public static function validate($token)
    {
        self::$persistence->find($token->getClaim('jti'));
        $data = new ValidationData();
        $data->setIssuer(self::$config['APIAuth']['JWT']['Issuer']);
        $data->setAudience(self::$config['APIAuth']['JWT']['Audience']);
        $data->setId(self::$persistence->id());
        $data->setCurrentTime(time());
        try {
            //dd($token, $data);
            //dd($token->validate($data));
            return $token->validate($data);
        } catch (\Exception $e) {
            throw new JWTException('$token is not a valid JWT token', 1, $e);
        }
    }

    /**
     * add
     *
     * @param $token
     * @param $data
     *
     * @return \Lcobucci\JWT\Token
     * @throws \eig\APIAuth\Exceptions\JWTException
     */
    public static function add($token, $data)
    {
        $oldToken = self::parse($token);
        $oldData = json_decode(
            $oldToken->getClaim(self::$config['APIAuth']['JWT']['Fields']),
            true
        );
        self::$persistence->get(['id' => $oldToken->getClaim('jti')]);
        $data = array_merge($oldData, $data);
        try {
            $token = (new Builder())
                ->setIssuer(self::$config['APIAuth']['JWT']['Issuer'])
                ->setAudience(self::$config['APIAuth']['JWT']['Audience'])
                ->setId(self::$persistence->id(), true)
                ->setIssuedAt(self::$persistence->issued())
                ->setNotBefore(self::$persistence->notBefore())
                ->setExpiration(self::$persistence->expiration())
                ->set(self::$config['APIAuth']['JWT']['Fields'], json_encode($data))
                ->sign(self::$signer, self::$persistence->id())
                ->getToken();

            self::$persistence->token($token->getPayload());
            self::$persistence->save();
            return $token;
        } catch (\Exception $e) {

            throw new JWTException('Unable to add to the JWT token', 1, $e);
        }

    }

    /**
     * renew
     *
     * @param $token
     *
     * @return \Lcobucci\JWT\Token
     * @throws \eig\APIAuth\Exceptions\JWTException
     */
    public static function renew($token)
    {
        $oldToken = self::parse($token);
        $data = json_decode(
            $oldToken->getClaim(self::$config['APIAuth']['JWT']['Fields']),
            true
        );
        self::$persistence->get(['id' => $oldToken->getClaim('jti')]);
        try {
            $token = (new Builder())
                ->setIssuer(self::$config['APIAuth']['JWT']['Issuer'])
                ->setAudience(self::$config['APIAuth']['JWT']['Audience'])
                ->setId(self::$persistence->id(), true)
                ->setIssuedAt(time())
                ->setNotBefore(time() + self::$config['APIAuth']['JWT']['NotBefore'])
                ->setExpiration(time() + self::$config['APIAuth']['JWT']['Timeout'])
                ->set(self::$config['APIAuth']['JWT']['Fields'], json_encode($data))
                ->sign(self::$signer, self::$persistence->id())
                ->getToken();

            self::$persistence->token($token->getPayload());
            self::$persistence->save();
            return $token;
        } catch (\Exception $e) {

            throw new JWTException('Unable to renew the JWT token', 1, $e);
        }

    }

    /**
     * persistenceCreate
     */
    protected static function persistenceCreate()
    {
        self::$persistence->create();
    }
}
