<?php

namespace Helpers;

class UUID
{
    /**
     * @return string UUID
     * @throws UUIDException
     */
    public static function generate()
    {
        //1. добавить проверку на наличие uuidgen
        //2. вариант для винды
        if (PHP_OS === 'Linux')
            return exec('uuidgen');
        else
            throw new UUIDException('Generating UUIDs in your OS not supported yet');
    }
}

class UUIDException extends \Exception {}