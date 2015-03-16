<?php
//http://stackoverflow.com/questions/7143514/how-to-encrypt-a-large-file-in-openssl-using-public-key

namespace DistObsNet\Key;

class Key implements KeyInterface
{
    private $pkey = null;
    private $privateKey = null;
    private $publicKey = null;
    private $crc32 = null;

    public function __construct(KeyManagerInterface $keyManager)
    {
        $this->pkey = $keyManager->getPkey();
    }

    public function publicKey()
    {
        if (null === $this->publicKey) {
            if (! $details = openssl_pkey_get_details($this->pkey))
                throw new KeyException("Can't extract public key from pkey resource");
            $this->publicKey = $details["key"];
        }

        return $this->publicKey;
    }

    public function crc32()
    {
        if (null === $this->crc32) {
            $this->crc32 = crc32($this->publicKey());
        }

        return $this->crc32;
    }

    public function decrypt($data)
    {
        $decrypted = '';
        if (! openssl_private_decrypt($data, $decrypted, $this->privateKey()))
            throw new KeyException("Can't decrypt data");

        return $decrypted;
    }

    private function privateKey()
    {
        if (null === $this->privateKey) {
            if (! openssl_pkey_export($this->pkey, $this->privateKey))
                throw new KeyException("Can't extract private key from pkey resource");
        }

        return $this->privateKey;
    }

}