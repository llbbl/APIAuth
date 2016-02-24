<?php

namespace eig\APIAuth\Client;

use eig\APIAuth\Contracts\ClientPersistenceInterface;
use eig\APIAuth\Contracts\TokenFieldGeneratorInterface;
use eig\APIAuth\Exceptions\ClientException;

/**
 * Class ClientRegistrar
 * @package eig\APIAuth
 */
class ClientRegistrar
{

    /**
     * @var \eig\APIAuth\Contracts\ClientPersistenceInterface
     */
    protected $persistence;

    /**
     * @var \eig\APIAuth\Contracts\TokenFieldGeneratorInterface
     */
    protected $tokenGenerator;

    /**
     * ClientRegistrar constructor.
     *
     * @param \eig\APIAuth\Contracts\ClientPersistenceInterface $persistence
     * @param \eig\APIAuth\Contracts\TokenFieldGeneratorInterface    $tokenGenerator
     */
    public function __construct (ClientPersistenceInterface $persistence, TokenFieldGeneratorInterface $tokenGenerator)
    {
        $this->persistence = $persistence;
        $this->tokenGenerator = $tokenGenerator;
    }

    /**
     * register
     *
     * @param $fingerprint
     * @param $type
     *
     * @return Object
     * @throws \eig\APIAuth\Exceptions\ClientException
     */
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
        if( $this->validateClientType($type) )
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

    /**
     * validateClientFingerprint
     *
     * @param $fingerprint
     *
     * @return bool
     * @throws \eig\APIAuth\Exceptions\ClientException
     */
    protected function validateClientFingerprint($fingerprint) {
        if ( !empty($fingerprint) && strlen($fingerprint) >= 10 )
        {
            if ($this->persistence->exists(['fingerprint' => $fingerprint]))
            {
                 throw new ClientException('Client already exists', 1);
            } else {
                return true;
            }
        } else {
            throw new ClientException('Client Fingerprint submitted cannot be null or empty, and length must be >= 10 char', 1);
        }
    }

    /**
     * validateClientType
     *
     * @param $type
     *
     * @return bool
     */
    protected function validateClientType($type) {
        if ( !empty($type) && strlen($type) >= 3)
        {
            return true;
        }
        return false;
    }

    /**
     * generateToken
     *
     * @param $fingerprint
     * @param $type
     *
     * @return string
     */
    protected function generateToken($fingerprint, $type) {
        return $this->tokenGenerator->generate($fingerprint . $type);
    }

}