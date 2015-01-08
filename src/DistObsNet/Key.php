<?php

namespace DistObsNet;

class Key
{
    public function __construct() 
    {
        echo "Object KEY initialization<br>\n";
    }
    
    public function isInit()
    {
        return (bool)$this->getPublicKey();
    }
    
    protected function getPublicKey()
    {
        return 'keykeykey';
    }
    
    protected function getPrivateKey()
    {
        
    }
    
}
