<?php

namespace DistObsNet\Key;

class PublicKeyDummy implements PublicKeyInterface
{
    private $key;

    public function crypt($data)
    {
        return $data;
    }

    public function setKey($key)
    {
        $this->key = $key;
    }

    public function getKey()
    {
        return $this->key;
    }

}
