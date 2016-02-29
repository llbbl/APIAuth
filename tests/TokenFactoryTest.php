<?php

namespace eig\APIAuth\Tests;

use eig\Configurator\Configurator;
use eig\Configurator\Options as ConfigOptions;
use eig\APIAuth\Facades\TokenFactory;
use Lcobucci\JWT\Parser;

class TokenFactoryTest extends TestAbstract
{
    public function testBuild() {
        $configFiles = [
            [
            'source' => 'APIAuth.php',
            'path' => 'src/config/',
            'pathType' => 'relative',
            'type' => 'array',
            'alias' => 'APIAuth'
            ]
        ];
        $configOptions = new ConfigOptions();
        $configOptions->basePath = realpath('src/config');
        $config = new Configurator($configFiles, $configOptions);
        $fields = [
            'test' => 'value',
            'iam' => 'a field',
            'token' => 'token'
        ];
        $token = TokenFactory::build($config, $fields);
        //echo $token . "\n";
        $token = (new Parser())->parse((string) $token);
        //print_r($token->getClaims());
        //print_r($token->getClaim('data'));
    }
}
