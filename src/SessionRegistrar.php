<?php

namespace eig\APIAuth;

use eig\APIAuth\Contracts\SessionPersistenceInterface;
use eig\APIAuth\Contracts\TokenGeneratorInterface;
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
     * @var \eig\APIAuth\Contracts\TokenGeneratorInterface
     */
    protected $tokenGenerator;


    /**
     * SessionRegistrar constructor.
     *
     * @param \eig\APIAuth\Contracts\SessionPersistenceInterface $persistence
     * @param \eig\APIAuth\Contracts\TokenGeneratorInterface     $tokenGenerator
     */
    public function __construct (SessionPersistenceInterface $persistence, TokenGeneratorInterface $tokenGenerator)
    {
        $this->persistence = $persistence;
        $this->tokenGenerator = $tokenGenerator;
    }


    /**
     * register
     *
     * @param $fingerprint
     * @param $clientToken
     *
     * @return Object
     * @throws \eig\APIAuth\Exceptions\SessionException
     */
    public function register($fingerprint, $clientToken){
        try
        {
            $this->validateClientFingerprint($fingerprint);
            $this->persistence = new $this->persistence();
            $this->persistence->fingerprint($fingerprint);
        } catch (SessionException $e){
            // log exception
            throw $e;
        }
        if( $this->validateClientToken($clientToken) )
        {
            $this->persistence->clientToken($clientToken);
        } else {
            // log exception
            throw new SessionException('Client Token cannont be null or empty and must be a hash', 1);
        }
        $this->persistence->token($this->generateToken($fingerprint, $clientToken));
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
            if ($this->persistence->exists(['fingerprint' => $fingerprint]))
            {
                throw new SessionException('Client Session already exists', 1);
            } else {
                return true;
            }
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
     */
    protected function validateClientToken($clientToken) {
        if (!empty($clientToken) && $this->clientTokenIsHash($clientToken)) {
            return true;
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
        return preg_match('/^[a-f0-9]{32}$/', $clientToken);
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