<?php

namespace eig\APIAuth;

use eig\APIAuth\Contracts\ClientPersistenceInterface;
use eig\APIAuth\Contracts\TokenGeneratorInterface;
use eig\APIAuth\Exceptions\ClientException;
use eig\Configurator\Configurator;

class ClientRegistrar
{
    protected $persistence;

    protected $tokenGenerator;

    public function __construct (ClientPersistenceInterface $persistence, TokenGeneratorInterface $tokenGenerator)
    {
        $this->persistence = $persistence;
        $this->tokenGenerator = $tokenGenerator;
        // setup the encryption system
    }

    public function register($fingerprint, $type){
        try
        {
            $this->validateClientFingerprint($fingerprint);
            $this->persistence = new $this->persistence();
            $this->persistence->fingerprint($fingerprint);
        } catch (ClientException $e){
            // log exception
            throw $e;
        }
        if( $this->validateClientType($type))
        {
            $this->persistence->type($type);
        } else {
           // log exception
           throw new ClientException('Client Type cannont be null or empty', 1);
        }
        $this->persistence->token($this->generateToken($fingerprint, $type));
        $this->persistence->save();
        return $this->persistence->token();
    }

    protected function validateClientFingerprint($fingerprint) {
        if ($fingerprint != '' && $fingerprint != null )
        {
            if ($this->persistence->exists(['fingerprint' => $fingerprint]))
            {
                 throw new ClientException('Client already exists', 1);
            } else {
                return true;
            }
        } else {
            throw new ClientException('Client Fingerprint submitted cannot be null or empty', 1);
        }
    }

    protected function validateClientType($type) {
        if ($type != '' && $type != null)
        {
            return true;
        }
        return false;
    }

    protected function generateToken($fingerprint, $type) {
        return $this->tokenGenerator->generate($fingerprint . $type);
    }

}