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
     * @return booleans
     */
    public function exists(array $params);
}