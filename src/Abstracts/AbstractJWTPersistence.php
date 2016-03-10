<?php

namespace eig\APIAuth\Abstracts;


use eig\APIAuth\Contracts\booleans;
use eig\APIAuth\Contracts\JWTPersistenceInterface;
use eig\APIAuth\Exceptions\JWTException;

/**
 * Class AbstractJWTPersistence
 * @package eig\APIAuth\Abstracts
 */
abstract class AbstractJWTPersistence implements JWTPersistenceInterface
{

    /**
     * @var
     */
    protected $id, $issued, $notBefore, $expiration, $token, $jwtModel;

    /**
     * AbstractJWTPersistence constructor.
     *
     * @param $jwtModel
     */
    public function __construct ($jwtModel)
    {
        $this->jwtModel = $jwtModel;
    }


    /**
     * create
     *
     * @param array|null $params
     *
     * @throws \eig\APIAuth\Exceptions\JWTException
     */
    public function create (array $params = null)
    {
        if (!empty($params)) {
            $this->loadFields($params);
        } else {
            $this->setDefaultsOnNew();
        }
        if ($this->canSave()) {
            $this->save();
        }
    }

    /**
     * save
     *
     * @param array|null $params
     */
    abstract public function save (array $params = null);

    /**
     * get
     *
     * @param array $params
     *
     * @return mixed
     */
    abstract public function get (array $params);

    /**
     * all
     * @return mixed
     */
    abstract public function all ();

    /**
     * exists
     *
     * @param array $params
     *
     * @return boolean
     */
    abstract public function exists (array $params);

    /**
     * id
     * @return mixed
     */
    public function id() {
        return $this->getId();
    }

    /**
     * issued
     *
     * @param null $issued
     *
     * @return mixed
     */
    public function issued($issued = null) {
        if(empty($issued)) {
            return $this->getIssued();
        } else {
            $this->setIssued($issued);
        }
    }

    /**
     * notBefore
     *
     * @param null $notBefore
     *
     * @return mixed
     */
    public function notBefore($notBefore = null) {
        if(empty($notBefore)) {
            return $this->getNotBefore();
        } else {
            $this->setNotBefore($notBefore);
        }
    }

    /**
     * expiration
     *
     * @param null $expiration
     *
     * @return mixed
     */
    public function expiration($expiration = null) {
        if(empty($expiration)) {
            return $this->getExpiration();
        } else {
            $this->setExpiration($expiration);
        }
    }

    /**
     * token
     *
     * @param null $token
     *
     * @return mixed
     */
    public function token($token = null) {
        if(empty($token)) {
            return $this->getToken();
        } else {
            $this->setToken($token);
        }
    }

    /**
     * find
     *
     * @param $id
     *
     * @return mixed
     */
    public function find($id) {
        return $this->jwtModel->find($id);
    }

    /**
     * loadFields
     *
     * @param $params
     *
     * @throws \eig\APIAuth\Exceptions\JWTException
     */
    protected function loadFields($params)
    {
        if (is_array($params)) {
            foreach ($params as $key => $value) {
                try {
                    $this->$key($value);
                } catch (\Exception $e) {
                    throw new JWTException('Parameter Key not found as a field', 1, $e);
                }
            }
        } else {
            throw new JWTException('Supplied parameter must be an Array', 1);
        }
    }

    /**
     * canSave
     * @return bool
     */
    protected function canSave()
    {
        if (empty($this->token)) {
            return false;
        }

        if (empty($this->issued)) {
            return false;
        }

        if (empty($this->notBefore)) {
            return false;
        }

        if (empty($this->expiration)) {
            return false;
        }

        return true;
    }

    /**
     * setDefaultsOnNew
     */
    protected function setDefaultsOnNew()
    {
        $this->token = null;
        $this->issued = null;
        $this->notBefore = null;
        $this->expiration = null;
    }

    /**
     * getId
     * @return mixed
     */
    protected function getId ()
    {
        return $this->id;
    }


    /**
     * setId
     *
     * @param $id
     */
    protected function setId ($id)
    {
        if(!empty($id)) {
            $this->id = $id;
        }

    }


    /**
     * getIssued
     * @return mixed
     */
    protected function getIssued ()
    {
        return $this->issued;
    }


    /**
     * setIssued
     *
     * @param $issued
     */
    protected function setIssued ($issued)
    {
        if(!empty($issued)) {
            $this->issued = $issued;
        }
    }


    /**
     * getNotBefore
     * @return mixed
     */
    protected function getNotBefore ()
    {
        return $this->notBefore;
    }


    /**
     * setNotBefore
     *
     * @param $notBefore
     */
    protected function setNotBefore ($notBefore)
    {
        if(!empty($notBefore)) {
            $this->notBefore = $notBefore;
        }
    }


    /**
     * getExpiration
     * @return mixed
     */
    protected function getExpiration ()
    {
        return $this->expiration;
    }


    /**
     * setExpiration
     *
     * @param $expiration
     */
    protected function setExpiration ($expiration)
    {
        if(!empty($expiration)) {
            $this->expiration = $expiration;
        }

    }


    /**
     * getToken
     * @return mixed
     */
    protected function getToken ()
    {
        return $this->token;
    }


    /**
     * setToken
     *
     * @param $token
     */
    protected function setToken ($token)
    {
        if(!empty($token)) {
            $this->token = $token;
        }

    }



}