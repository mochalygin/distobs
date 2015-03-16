<?php

namespace DistObsNet\Key;

class PublicKey implements PublicKeyInterface
{
    private $publicKey = null;
    private $crc32 = null;

    /**
     * @param string $publicKey
     */
    public function setKey($publicKey)
    {
        assert((bool)$publicKey);
        $this->publicKey = openssl_pkey_get_public($publicKey);
    }

    public function crypt($data)
    {
        $crypted = '';
        if (! openssl_public_encrypt($data, $crypted, $this->publicKey) ) {
            throw new KeyException("Can't crypt data");
        }

        return $crypted;
    }

    public function crc32()
    {
        if (null === $this->crc32) {
            $this->crc32 = crc32($this->publicKey);
        }

        return $this->crc32;
    }

}
