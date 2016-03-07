<?php

namespace eig\APIAuth\Facades;

use eig\APIAuth\Contracts\JWTPersistenceInterface;
use eig\APIAuth\Exceptions\JWTException;
use eig\Configurator\Configurator;
use Lcobucci\JWT\ValidationData;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;

class JWT
{
    protected static $persistence, $config, $signer;

    public static function initialize (Configurator $config, JWTPersistenceInterface $persistence = null)
    {
        self::$config = $config;
        if(empty($persistence)) {
            self::$persistence = new self::$config['APIAuth']['JWT']['Storage'];
        } else
        {
            self::$persistence = $persistence;
        }
        $signatureMethod = self::$config['APIAuth']['JWT']['Signature'];
        self::$signer = new self::$config['APIAuth']['JWT']['Signature Methods'][$signatureMethod];

    }

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
                ->sign(self::$signer, self::$persistence->id())
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

    public static function parse($token)
    {
        try {
            return (new Parser())->parse((string)$token);
        } catch (\Exception $e) {
            throw new JWTException('Unable to parse token', 1, $e);
        }
    }

    public static function validate($token)
    {
        self::$persistence->get(['id' => $token->getClaim('jti')]);
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

    protected static function persistenceCreate()
    {
        self::$persistence->create();
    }
}
