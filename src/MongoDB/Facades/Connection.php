<?php

namespace Crazymeeks\MongoDB\Facades;

use Crazymeeks\MongoDB\Connection\Resolver\ConnectionResolver;
use Crazymeeks\MongoDB\Connection\Exceptions\ConnectionException;

class Connection
{


    /**
     * @var \MongoDB\Client|null
     */
    protected static $client = null;

    protected static $database = null;

    protected $uri;
    protected $uriOptions = [];
    protected $driverOptions = [];
    protected $default_database = null;

    public function __construct(
        $uri = '127.0.0.1',
        $uriOptions = [],
        $driverOptions = [],
        $default_database = null
    )
    {
        $this->uri = $uri;
        $this->uriOptions = $uriOptions;
        $this->driverOptions = $driverOptions;
        $this->default_database = $default_database;
    }


    /**
     * Connect to MongoDB
     *
     * @param string $uri
     * @param array $uriOptions
     * @param array $driverOptions
     * 
     * @return void
     */
    public function connect()
    {
        $resolver = new ConnectionResolver();
        $client = $resolver->setServerAddress($this->uri)
                           ->setServerOptions($this->uriOptions)
                           ->setDriverOptions($this->driverOptions)
                           ->resolve();
        try {            
            $client->listDatabases();
            if ($db = $this->default_database) {
                $database = $client->{$db};
                self::$client = $database;
            } else {
                self::$client = $client;
            }
        } catch (\MongoDB\Driver\Exception\ConnectionTimeoutException $e) {
            throw ConnectionException::connectionFailed($this->uri);
        }
    }

    /**
     * Disconnect to mongodb
     *
     * @return void
     */
    public static function disconnect()
    {
        self::$client = null;
    }

    /**
     * Get mongodb client instance
     *
     * @return \MongoDB\Client|\MongoDB\Database
     */
    public static function getInstance()
    {
        if (!self::$client) {
            throw ConnectionException::connectNotInitialized();
        }

        return self::$client;
    }

    /**
     * Set default database will connect to
     *
     * @param string $database
     * 
     * @return void
     */
    public function setDefaultDatabase(string $database)
    {
        $this->default_database = $database;

        return $this;
    }


    public static function __callStatic($name, $arguments)
    {

        if (self::$client) {
            return new \Crazymeeks\MongoDB\Connection\Resolver\ResolvedConnection();
        }
        if ($name === 'setUpConnection') {
            if (!self::$client) {
                list($uri, $uriOptions, $driverOptions) = $arguments;
                return new static($uri, $uriOptions, $driverOptions);
            }
        }

        throw new \BadMethodCallException(sprintf("Calling undefined static method %s()", $name));
    }

}