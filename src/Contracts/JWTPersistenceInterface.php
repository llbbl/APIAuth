<?php

namespace eig\APIAuth\Contracts;


/**
 * Interface JWTPersistenceInterface
 * @package eig\APIAuth\Contracts
 */
interface JWTPersistenceInterface
{

    /**
     * create
     *
     * @param array|null $params
     *
     * @return object
     */
    public function create(array $params = null);

    /**
     * save
     *
     * @param array $params
     */
    public function save(array $params);

    /**
     * get
     *
     * @param array $params
     *
     * @return mixed
     */
    public function get(array $params);

    /**
     * all
     * @return mixed
     */
    public function all();

    /**
     * exists
     *
     * @param array $params
     *
     * @return boolean
     */
    public function exists(array $params);

    /**
     * id
     * @return string
     */
    public function id();

    /**
     * signature
     *
     * @param null $signature
     *
     * @return mixed
     */
    public function signature($signature = null);

    /**
     * issued
     *
     * @param null $issued
     *
     * @return mixed
     */
    public function issued($issued = null);

    /**
     * notBefore
     *
     * @param null $notBefore
     *
     * @return mixed
     */
    public function notBefore($notBefore = null);

    /**
     * expiration
     *
     * @param null $expiration
     *
     * @return mixed
     */
    public function expiration($expiration = null);

    /**
     * token
     *
     * @param null $token
     *
     * @return mixed
     */
    public function token ($token = null);


}