<?php

namespace DistObsNet\Key;

class KeyManagerDummy implements KeyManagerInterface
{

    const PRIVATE_KEY_FILE = '/../../../var/DUMMY_SECRET_KEY';

    public function getPkey()
    {
        if (! is_file(__DIR__ . self::PRIVATE_KEY_FILE))
            return false;

        $f = fopen(__DIR__ . self::PRIVATE_KEY_FILE, 'r');
        $key = fread($f, 32);
        fclose($f);

        return $key;
    }

    public function providePkey()
    {
        if (! $ret = $this->getPkey()) {
            $this->generate();
        }

        return $this->getPkey();
    }

    protected function generate()
    {
        $key = md5(time() . rand());
        $this->store($key);
    }

    protected function store($key)
    {
        $f = fopen(__DIR__ . self::PRIVATE_KEY_FILE, 'w');
        fwrite($f, $key);
        fclose($f);
    }

}
