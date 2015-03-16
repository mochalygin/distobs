<?php

namespace DistObsNet\Key;

class KeyManager implements KeyManagerInterface
{
    const PRIVATE_KEY_FILE = '../var/SECRET_KEY';

    public static function getErrors()
    {
        $errors = array();
        while ($error = openssl_error_string())
            $errors[] = $error;

        return $errors;
    }

    public function getPkey()
    {
        if (! $res = openssl_pkey_get_private('file://' . self::PRIVATE_KEY_FILE))
            throw new KeyException("Can't open key file");

        return $res;
    }

    public function providePkey()
    {
        try {
            return $this->getPkey();
        } catch (KeyException $e) {
            $this->generate();

            return $this->getPkey();
        }

    }

    protected function generate()
    {
        $config = array(
            'private_key_bits' => PRIVATE_KEY_BITS,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        );

        if (! $res = openssl_pkey_new($config))
            throw new KeyException("Can't generate new key");

        $this->store($res);
    }

    protected function store($res)
    {
        if (! openssl_pkey_export_to_file($res, self::PRIVATE_KEY_FILE))
            throw new KeyException("Can't store new key to file");
    }

}