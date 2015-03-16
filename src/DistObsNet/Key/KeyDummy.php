<?php

namespace DistObsNet\Key;

class KeyDummy implements KeyInterface
{
    private $publicKey = null;

    public function __construct(KeyManagerInterface $keyManager)
    {
        $this->publicKey = $keyManager->getPkey();
    }

    public function decrypt($data)
    {
        return $data;
    }

    public function publicKey()
    {
        return $this->publicKey;
    }

}
