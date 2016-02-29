<?php

namespace eig\APIAuth\Facades;

use eig\APIAuth\Exceptions\JWTException;
use eig\Configurator\Configurator;
use Lcobucci\JWT\ValidationData;
use PasswordLib\PasswordLib;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;

class JWT
{
    public static function build(Configurator $config, array $params)
    {
        $library = new PasswordLib();
        try
        {
            $token = (new Builder())
                ->setIssuer($config['APIAuth']['JWT']['Issuer'])
                ->setAudience($config['APIAuth']['JWT']['Audience'])
                ->setId($library->getRandomToken(16), true)
                ->setIssuedAt(time())
                ->setNotBefore(time() + 60)
                ->setExpiration(time() + 3600)
                ->set('data', json_encode($params))
                ->getToken();


            return $token;
        } catch (\Exception $e) {
            throw new JWTException('Unable to build a JWT token', 1, $e);
        }


    }

    public static function parse($token) {
        try {
            return (new Parser())->parse((string)$token);
        } catch (\Exception $e) {
            throw new JWTException('Unable to parse token', 1, $e);
        }
    }

    public static function validate($token, Configurator $config) {
        $library = new PasswordLib();
        $data = new ValidationData();
        $data->setIssuer($config['APIAuth']['JWT']['Issuer']);
        $data->setAudience($config['APIAuth']['JWT']['Audience']);
        $data->setId($library->getRandomToken(16), true);
        try {
            return $token->validate($data);
        } catch (\Exception $e) {
            throw new JWTException('$token is not a JWT token', 1, $e);
        }


    }

    public static function add($token) {

    }
}