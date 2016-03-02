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
    public function save(array $params = null);

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
     * create
     *
     * @param array|null $params
     *
     * @return mixed
     */
    public function create(array $params = null);

    /**
     * isExpired
     * @return bool
     */
    public function isExpired();

    /**
     * setExpired
     *
     * @param $expired
     */
    public function setExpired($expired);

    /**
     * isValid
     * @return bool
     */
    public function isValid();

    /**
     * setValid
     *
     * @param $valid
     */
    public function setValid($valid);

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
