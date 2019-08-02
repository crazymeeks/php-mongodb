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

use Nucleus\MongoDB\Database\Connection;
use Nucleus\Databases\Contracts\ConnectionInterface;
use Nucleus\Databases\Contracts\Resolver\ConnectionResolverInterface;

class ConnectionResolver implements ConnectionResolverInterface
{
    
    const CONNECTION_PREFIX = 'mongodb://';

    /**
     * MongoDB database connection config
     * 
     * @var array
     */
    private $config = [];

    /**
     * Constructor
     * 
     * @param array{host:string,database:string,port:string|null,options:array{username:string,password:string}|[]} $config  Mongodb config connections
     * 
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Build/prepare connection
     *
     * @return string
     */
    public function constructConnectionString(): string
    {
        $config = $this->config;

        $port = $config['port'] ?? '27017';

        $conArray = [];
        if (is_array($config['host'])) {
            foreach($config['host'] as $host){
                $conArray[] = self::CONNECTION_PREFIX . $host . ":{$port}/";
                unset($host);
            }
        } else {
            $conArray = [
                self::CONNECTION_PREFIX . $config['host'] . ":{$port}/",
            ];
        }

        return implode(',', $conArray);
    }

    /**
     * Resolve connection
     *
     * @return \MongoDB\Client
     */
    public function resolve(): ConnectionInterface
    {
        $uri = $this->constructConnectionString();
        $uriOptions = $this->config['options'] ?? [];
        $driverOptions = [];

        $client = new Connection($uri, $uriOptions, $driverOptions);

        $client->listDatabases();
        
        return $client;
    }
}