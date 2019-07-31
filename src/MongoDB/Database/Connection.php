<?php

namespace Nucleus\MongoDB\Database;

use MongoDB\Client as MongoClient;
use Nucleus\Databases\Contracts\ConnectionInterface;

class Connection extends MongoClient implements ConnectionInterface
{
 
    
    
    public function __construct($uri, array $uriOptions = [], array $driverOptions = [])
    {
        parent::__construct($uri, $uriOptions, $driverOptions);
    }


}