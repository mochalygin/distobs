<?php

namespace DistObsNet\Console;

use Silex\Application;
use Symfony\Component\Console\Command\Command;

class ContainerAwareCommand extends Command
{
    private $container;

    public static function create()
    {
        return new static;
    }

    public function setContainer(Application $container)
    {
        $this->container = $container;

        return $this;
    }

    protected function getContainer()
    {
        return $this->container;
    }

}
