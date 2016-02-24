<?php

namespace eig\APIAuth\Session;

use eig\APIAuth\Contracts\SessionPersistenceInterface;
use eig\APIAuth\Contracts\TokenFieldGeneratorInterface;
use eig\APIAuth\Exceptions\SessionException;

/**
 * Class SessionRegistrar
 * @package eig\APIAuth
 */
class SessionRegistrar
{


    /**
     * @var \eig\APIAuth\Contracts\SessionPersistenceInterface
     */
    protected $persistence;

    /**
     * @var \eig\APIAuth\Contracts\TokenFieldGeneratorInterface
     */
    protected $tokenGenerator;


    /**
     * SessionRegistrar constructor.
     *
     * @param \eig\APIAuth\Contracts\SessionPersistenceInterface $persistence
     * @param \eig\APIAuth\Contracts\TokenFieldGeneratorInterface     $tokenGenerator
     */
    public function __construct (SessionPersistenceInterface $persistence, TokenFieldGeneratorInterface $tokenGenerator)
    {
        $this->persistence = $persistence;
        $this->tokenGenerator = $tokenGenerator;
    }


    /**
     * register
     *
     * @param $clientToken
     * @param $fingerprint
     *
     * @return Object
     * @throws \eig\APIAuth\Exceptions\SessionException
     */
    public function register($clientToken, $fingerprint){
        try
        {
            $this->validateClientFingerprint($fingerprint);
        } catch (SessionException $e){

            throw $e;
        }
        if( $this->validateClientToken($clientToken) )
        {
            $this->persistence = new $this->persistence();
            $this->persistence->client($clientToken);
        } else {
            throw new SessionException('Client Token cannot be null or empty and must be a hash', 1);
        }
        $this->persistence->token($this->generateToken($fingerprint, $clientToken));
        $this->persistence->setRevoked(false);
        $this->persistence->save();
        return $this->persistence->token();
    }


    /**
     * validateClientFingerprint
     *
     * @param $fingerprint
     *
     * @return bool
     * @throws \eig\APIAuth\Exceptions\SessionException
     */
    protected function validateClientFingerprint($fingerprint) {
        if ( !empty($fingerprint) && strlen($fingerprint) >= 10 )
        {
            return true;
        } else {
            throw new SessionException('Client Fingerprint submitted cannot be null or empty, and length must be >= 10 char', 1);
        }
    }


    /**
     * validateClientToken
     *
     * @param $clientToken
     *
     * @return bool
     * @throws \eig\APIAuth\Exceptions\SessionException
     */
    protected function validateClientToken($clientToken) {
        if (!empty($clientToken) && $this->clientTokenIsHash($clientToken) == true) {
            if ($this->persistence->exists(['client' => $clientToken]) == true)
            {
                throw new SessionException('Client Session already exists', 1);
            } else
            {
                return true;
            }
        }
        return false;
    }

    /**
     * clientTokenIsHash
     *
     * @param $clientToken
     *
     * @return int
     */
    protected function clientTokenIsHash($clientToken) {
        /*
        return preg_match('/^[0-9a-f]{40}$/i', $clientToken);

        TODO: needs reworked
        */
        return true;
    }

    /**
     * generateToken
     *
     * @param $fingerprint
     * @param $clientToken
     *
     * @return string
     */
    protected function generateToken($fingerprint, $clientToken) {
        return $this->tokenGenerator->generate($clientToken . $fingerprint);
    }


}