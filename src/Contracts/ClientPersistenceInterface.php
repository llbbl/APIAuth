<?php

namespace eig\APIAuth\Contracts;

/**
 * Interface ClientPersistenceInterface
 * @package eig\APIAuth\Contracts
 */
interface ClientPersistenceInterface
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
    public function save(array $params);

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
     * fingerprint
     *
     * @param null || string $fingerprint
     *
     * @return Object || array
     */
    public function fingerprint($fingerprint = null);

    /**
     * type
     *
     * @param null || string $type
     *
     * @return Object || array
     */
    public function type($type = null);

    /**
     * token
     *
     * @param null || string $token
     *
     * @return Object || array
     */
    public function token($token = null);


}