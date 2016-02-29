<?php

namespace eig\APIAuth\Facades;

use eig\Configurator\Configurator;
use PasswordLib\PasswordLib;
use Lcobucci\JWT\Builder;

class JWT
{
    public static function build(Configurator $config, array $params)
    {
        $library = new PasswordLib();
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

    }
}