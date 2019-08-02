<?php

/*
 * This file is part of the Nucleus package.
 *
 * (c) Jefferson Claud <jefferson.claud@nuworks.ph>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


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