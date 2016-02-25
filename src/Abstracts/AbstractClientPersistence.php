<?php

namespace eig\APIAuth\Abstracts;

use eig\APIAuth\Contracts\ClientPersistenceInterface;
use eig\APIAuth\Exceptions\ClientException;

/**
 * Class AbstractClientPersistence
 * @package eig\APIAuth\Abstracts
 */
abstract class AbstractClientPersistence implements ClientPersistenceInterface
{


    /**
     * @var
     */
    protected $fingerprint, $type, $token, $status, $clientModel;

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
     * AbstractClientPersistence constructor.
     *
     * @param $clientModel
     */
    public function __construct ($clientModel)
    {
        $this->clientModel = $clientModel;
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
    abstract public function save (array $params);
    // saves the fields of the repository to the
    // client token model

    /**
     * get
     *
     * @param array $params
     *
     * @return Object || array
     */
    abstract public function get (array $params);
    // retrieves a single client token record by any specified paramaters
    // and loads it into the repository fields


    /**
     * all
     * @return array
     */
    abstract public function all ();
    // returns an array or collection of ClientToken Records


    /**
     * create
     *
     * @param array|null $params
     *
     * @throws \eig\APIAuth\Exceptions\ClientException
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
     * fingerprint
     *
     * @param null $fingerprint
     *
     * @return string
     */
    public function fingerprint ($fingerprint = null) {
        if(empty($fingerprint))
        {
            return $this->getFingerprint();
        } else {
            $this->setFingerprint($fingerprint);
        }
    }

    /**
     * type
     *
     * @param null $type
     *
     * @return string
     */
    public function type ($type = null) {
        if(empty($type))
        {
            return $this->getType();
        } else {
            $this->setType($type);
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
     * getFingerprint
     * @return string
     */
    protected function getFingerprint()
    {
        return $this->fingerprint;
    }

    /**
     * setFingerprint
     *
     * @param $fingerprint
     *
     */
    protected function setFingerprint($fingerprint) {
        if(!empty($fingerprint)) {
            $this->fingerprint = $fingerprint;
        }
    }

    /**
     * getType
     * @return string
     */
    protected function getType() {
        return $this->type;
    }

    /**
     * setType
     *
     * @param $type
     *
     */
    protected function setType($type) {
        if(!empty($type)) {
            $this->type = $type;
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
     * getToken
     * @return string
     */
    protected function getToken() {
        return $this->token;
    }

    /**
     * setToken
     *
     * @param $token
     *
     */
    protected function setToken($token) {
        if(!empty($token)) {
            $this->token = $token;
        }
    }

    /**
     * setDefaultsOnNew
     */
    protected function setDefaultsOnNew() {
        $this->fingerprint = null;
        $this->token = null;
        $this->type = null;
        $this->setStatus(self::VALID);
    }

    /**
     * canSave
     * @return bool
     */
    protected function canSave(){
        if(empty($this->fingerprint)){
           return false;
        }

        if(empty($this->type)){
            return false;
        }

        if(empty($this->token)) {
            return false;
        }

        return true;

    }

    /**
     * loadFields
     *
     * @param $params
     *
     * @throws \eig\APIAuth\Exceptions\ClientException
     */
    protected function loadFields($params) {
        if(is_array($params)) {
            foreach($params as $key => $value) {
                try {
                    $this->$key($value);
                } catch (\Exception $e) {
                    throw new ClientException('Paramanter Key not found as a field', 1, $e);
                }
            }
        } else {
            throw new ClientException('Supplied paramter must be an Array' , 1);
        }
    }

}