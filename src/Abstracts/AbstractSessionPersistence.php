<?php

namespace eig\APIAuth\Abstracts;

use eig\APIAuth\Contracts\SessionPersistenceInterface;
use eig\APIAuth\Exceptions\SessionException;

/**
 * Class AbstractSessionPersistence
 * @package eig\APIAuth\Abstracts
 */
abstract class AbstractSessionPersistence implements SessionPersistenceInterface
{

    /**
     * @var
     */
    /**
     * @var
     */
    /**
     * @var
     */
    /**
     * @var
     */
    /**
     * @var
     */
    protected $clientToken, $sessionToken, $status, $timeout, $sessionModel;

    /**
     *
     */
    const VALID   = 'VALID';
    /**
     *
     */
    const INVALID = 'INVALID';
    /**
     *
     */
    const EXPIRED = 'EXPIRED';
    /**
     *
     */
    const REVOKED = 'REVOKED';

    /**
     * AbstractSessionPersistence constructor.
     *
     * @param $sessionModel
     */
    public function __construct ($sessionModel)
    {
        $this->sessionModel = $sessionModel;
    }

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
    abstract public function save (array $params = null);

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
     * create
     *
     * @param array|null $params
     *
     * @throws \eig\APIAuth\Exceptions\SessionException
     */
    public function create(array $params = null) {
        if(!empty($params)) {
            $this->loadFields($params);
        } else {
            $this->setDefaultsOnNew();
        }
        if ($this->canSave()) {
            $this->save();
        }
    }

    /**
     * isExpired
     * @return bool
     */
    public function isExpired() {
        if($this->getStatus() == self::EXPIRED) {
            return true;
        }
        return false;
    }

    /**
     * setExpired
     *
     * @param $expired
     */
    public function setExpired($expired) {
        if(is_bool($expired)) {
            if($expired == true) {
                $this->setStatus(self::EXPIRED);
            } else {
                $this->setStatus(self::VALID);
            }
        }
    }

    /**
     * isValid
     * @return bool
     */
    public function isValid() {
        if($this->getStatus() == self::VALID) {
            return true;
        }
        return false;
    }

    /**
     * setValid
     *
     * @param $valid
     */
    public function setValid($valid) {
        if(is_bool($valid)) {
            if($valid == true) {
                $this->setStatus(self::VALID);
            } else {
                $this->setStatus(self::INVALID);
            }
        }
    }

    /**
     * isRevoked
     * @return boolean
     */
    public function isRevoked () {
        if($this->getStatus() == self::REVOKED) {
            return true;
        }
        return false;
    }

    /**
     * setRevoked
     *
     * @param bool $revoked
     *
     */
    public function setRevoked ($revoked) {
        if(is_bool($revoked)) {
            if($revoked == true) {
                $this->setStatus(self::REVOKED);
            } else {
                $this->setStatus(self::VALID);
            }
        }
    }

    /**
     * token
     *
     * @param null $token
     *
     * @return string
     */
    public function token ($token = null) {
        if(empty($token))
        {
            return $this->getToken();
        } else {
            $this->setToken($token);
        }
    }


    /**
     * client
     *
     * @param null || string $client
     *
     * @return null || string
     */
    public function client($client = null) {
        if(empty($client))
        {
            return $this->getClient();
        } else {
            $this->setClient($client);
        }
    }


    /**
     * timeout
     *
     * @param null || integer $timeout
     *
     * @return integer || null
     */
    public function timeout ($timeout = null) {
        if(empty($timeout))
        {
            return $this->getTimeoout();
        } else {
            $this->setTimeout($timeout);
        }
    }

    /**
     * getToken
     * @return string
     */
    protected function getToken() {
        return $this->sessionToken;
    }

    /**
     * setToken
     *
     * @param $token
     *
     */
    protected function setToken($token) {
        if(!empty($token)) {
            $this->sessionToken = $token;
        }
    }

    /**
     * getTimeoout
     * @return integer
     */
    protected function getTimeoout() {
        return $this->timeout;
    }

    /**
     * setTimeout
     *
     * @param $timeout
     */
    protected function setTimeout($timeout) {
        if(!empty($timeout)) {
            $this->timeout = $timeout;
        }
    }

    /**
     * getClient
     * @return string
     */
    protected function getClient() {
        return $this->clientToken;
    }

    /**
     * setClient
     *
     * @param string $client
     */
    protected function setClient($client) {
        if(!empty($client)) {
            $this->clientToken = $client;
        }
    }

    /**
     * setStatus
     *
     * @param $status
     */
    protected function setStatus($status) {
        if (!empty($status)) {
            $this->status = $status;
        }
    }

    /**
     * getStatus
     * @return mixed
     */
    protected function getStatus() {
        return $this->status;
    }

    /**
     * setDefaultsOnNew
     */
    protected function setDefaultsOnNew() {
        $this->clientToken = null;
        $this->sessionToken = null;
        $this->timeout = null;
        $this->setStatus(self::VALID);
    }

    /**
     * canSave
     * @return bool
     */
    protected function canSave(){
        if(empty($this->clientToken)){
            return false;
        }

        if(empty($this->sessionToken)){
            return false;
        }

        if(empty($this->timeout)) {
            return false;
        }

        return true;

    }


    /**
     * loadFields
     *
     * @param $params
     *
     * @throws \eig\APIAuth\Exceptions\SessionException
     */
    protected function loadFields($params) {
        if(is_array($params)) {
            foreach($params as $key => $value) {
                try {
                    $this->$key($value);
                } catch (\Exception $e) {
                    throw new SessionException('Parameter Key not found as a field', 1, $e);
                }
            }
        } else {
            throw new SessionException('Supplied parameter must be an Array' , 1);
        }
    }

}