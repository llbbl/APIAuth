<?php

namespace eig\APIAuth\Contracts;

/**
 * Interface SessionPersistenceInterface
 * @package eig\APIAuth\Contracts
 */
interface SessionPersistenceInterface
{
    /**
    * exists
    *
    * @param array $params
    *
    * @return boolean
    */
    public function exists(array $params);

    /**
     * save
     *
     * @param array $params
     *
     */
    public function save(array $params = null);

    /**
     * create
     *
     * @param array|null $params
     *
     * @return mixed
     */
    public function create(array $params = null);

    /**
     * get
     *
     * @param array $params
     *
     * @return Object || array
     */
    public function get(array $params);

    /**
     * all
     * @return array
     */
    public function all();

    /**
     * isRevoked
     * @return boolean
     */
    public function isRevoked();

    /**
     * setRevoked
     *
     * @param boolean $revoked
     *
     */
    public function setRevoked($revoked);

    /**
     * token
     *
     * @param null || string $token
     *
     * @return Object || array
     */
    public function token($token = null);

    /**
     * client
     *
     * @param null || string $client
     *
     * @return null || string
     */
    public function client($client = null);


    /**
     * timeout
     *
     * @param null || integer $timeout
     *
     * @return null || integer
     */
    public function timeout($timeout = null);
}
