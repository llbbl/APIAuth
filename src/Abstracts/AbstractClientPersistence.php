<?php

namespace eig\APIAuth\Abstracts;

use eig\APIAuth\Contracts\ClientPersistenceInterface;

/**
 * Class AbstractClientPersistence
 * @package eig\APIAuth\Abstracts
 */
abstract class AbstractClientPersistence implements ClientPersistenceInterface
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
     * fingerprint
     *
     * @param null $fingerprint
     *
     * @return string
     */
    abstract public function fingerprint ($fingerprint = null);

    /**
     * type
     *
     * @param null $type
     *
     * @return string
     */
    abstract public function type ($type = null);

    /**
     * token
     *
     * @param null $token
     *
     * @return string
     */
    abstract public function token ($token = null);

    /**
     * getFingerprint
     * @return string
     */
    abstract protected function getFingerprint();

    /**
     * setFingerprint
     *
     * @param $fingerprint
     *
     */
    abstract protected function setFingerprint($fingerprint);

    /**
     * getType
     * @return string
     */
    abstract protected function getType();

    /**
     * setType
     *
     * @param $type
     *
     */
    abstract protected function setType($type);

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
     * makeNewClientRecord
     */
    abstract public function makeNewClientRecord();

}