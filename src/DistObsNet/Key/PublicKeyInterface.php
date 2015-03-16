<?php

namespace DistObsNet\Key;

interface PublicKeyInterface
{
    public function setKey($key);
    public function getKey();
    public function crypt($data);
}
