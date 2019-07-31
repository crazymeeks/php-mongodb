<?php

namespace Nucleus\Databases\Support;

use Nucleus\Databases\Database;
use Nucleus\MongoDB\Database\ConnectionResolver;


trait ConnectedClientTrait
{


    /**
     * Get connection
     *
     * @return \MongoDB\Client
     */
    protected function getConnectedClient()
    {
        
        $config = \Nucleus\MongoDB\Database\Setup::getConfiguration();

        $resolver = new ConnectionResolver($config);

        $mongoclient = Database::connect($resolver)->{$config['database']};
        
        return $mongoclient;

    }
}