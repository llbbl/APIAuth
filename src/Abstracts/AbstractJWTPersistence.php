<?php

namespace eig\APIAuth\Abstracts;


use eig\APIAuth\Contracts\booleans;
use eig\APIAuth\Contracts\JWTPersistenceInterface;
use eig\APIAuth\Exceptions\JWTException;

abstract class AbstractJWTPersistence implements JWTPersistenceInterface
{

    protected $id, $signature, $issued, $notBefore, $expiration, $token, $jwtModel;

    public function __construct ($jwtModel)
    {
        $this->jwtModel = $jwtModel;
    }


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

    abstract public function save (array $params = null);

    abstract public function get (array $params);

    abstract public function all ();

    abstract public function exists (array $params);

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

    protected function canSave()
    {
        if (empty($this->token)) {
            return false;
        }

        if (empty($this->signature)) {
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

    protected function setDefaultsOnNew()
    {
        $this->signature = null;
        $this->token = null;
        $this->issued = null;
        $this->notBefore = null;
        $this->expiration = null;
    }

    public function id() {
        return $this->getId();
    }

    public function signature($signature = null) {
        if(empty($signature)) {
            return $this->getSignature();
        } else {
            $this->setSignature($signature);
        }
    }

    public function issued($issued = null) {
        if(empty($issued)) {
            return $this->getIssued();
        } else {
            $this->setIssued($issued);
        }
    }

    public function notBefore($notBefore = null) {
        if(empty($notBefore)) {
            return $this->getBefore();
        } else {
            $this->setNotBefore($notBefore);
        }
    }

    public function expiration($expiration = null) {
        if(empty($expiration)) {
            return $this->getExpiration();
        } else {
            $this->setExpiration($expiration);
        }
    }

    public function token($token = null) {
        if(empty($token)) {
            return $this->getToken();
        } else {
            $this->setToken($token);
        }
    }

    protected function getId ()
    {
        return $this->id;
    }


    protected function setId ($id)
    {
        if(!empty($id)) {
            $this->id = $id;
        }

    }


    protected function getSignature ()
    {
        return $this->signature;
    }


    protected function setSignature ($signature)
    {
        if(!empty($signature)) {
            $this->signature = $signature;
        }
    }


    protected function getIssued ()
    {
        return $this->issued;
    }


    protected function setIssued ($issued)
    {
        if(!empty($issued)) {
            $this->issued = $issued;
        }
    }


    protected function getNotBefore ()
    {
        return $this->notBefore;
    }


    protected function setNotBefore ($notBefore)
    {
        if(!empty($notBefore)) {
            $this->notBefore = $notBefore;
        }
    }


    protected function getExpiration ()
    {
        return $this->expiration;
    }


    protected function setExpiration ($expiration)
    {
        if(!empty($expiration)) {
            $this->expiration = $expiration;
        }

    }


    protected function getToken ()
    {
        return $this->token;
    }


    protected function setToken ($token)
    {
        if(!empty($token)) {
            $this->token = $token;
        }

    }



}