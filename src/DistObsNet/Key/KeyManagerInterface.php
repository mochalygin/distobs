<?php

namespace DistObsNet\Key;

interface KeyManagerInterface
{
    /**
     * @return resource existing private key
     */
    public function getPkey();

    /**
     * @return resource private key (if it is not exists -- it will be created before)
     */
    public function providePkey();
}
