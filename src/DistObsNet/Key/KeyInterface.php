<?php

namespace DistObsNet\Key;

interface KeyInterface
{
    public function __construct(KeyManagerInterface $keyManager);
    public function publicKey();
    public function decrypt($data);
//    public function signOff($data);
}
