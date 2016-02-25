<?php

namespace eig\APIAuth\Abstracts;

use eig\APIAuth\Contracts\SessionPersistenceInterface;

/**
 * Class AbstractSessionPersistence
 * @package eig\APIAuth\Abstracts
 */
abstract class AbstractSessionPersistence implements SessionPersistenceInterface
{
    /**
     * exists
     *
     * @param array $params
     *
     * @return boolean
     */
    abstract public function exists (array $params);

    /**
     * save
     *
     * @param array $params
     *
     * @return mixed
     */
    abstract public function save (array $params);

    /**
     * get
     *
     * @param array $params
     *
     * @return Object || array
     */
    abstract public function get (array $params);

    /**
     * all
     * @return array
     */
    abstract public function all ();

    /**
     * isRevoked
     * @return boolean
     */
    abstract public function isRevoked ();

    /**
     * setRevoked
     *
     * @param bool $revoked
     *
     */
    abstract public function setRevoked ($revoked);

    /**
     * token
     *
     * @param null $token
     *
     * @return string
     */
    abstract public function token ($token = null);


    /**
     * client
     *
     * @param null || string $client
     *
     * @return null || string
     */
    abstract public function client($client = null);


    /**
     * timeout
     *
     * @param null || integer $timeout
     *
     * @return integer || null
     */
    abstract public function timeout ($timeout = null);

    /**
     * getToken
     * @return string
     */
    abstract protected function getToken();

    /**
     * setToken
     *
     * @param $token
     *
     */
    abstract protected function setToken($token);

    /**
     * getTimeoout
     * @return integer
     */
    abstract protected function getTimeoout();

    /**
     * setTimeout
     *
     * @param $timeout
     */
    abstract protected function setTimeout($timeout);

    /**
     * getClient
     * @return string
     */
    abstract protected function getClient();

    /**
     * setClient
     *
     * @param string $client
     */
    abstract protected function setClient($client);

}