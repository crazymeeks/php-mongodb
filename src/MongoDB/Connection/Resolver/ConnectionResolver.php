<?php

namespace Crazymeeks\MongoDB\Connection\Resolver;

use MongoDB\Client;
use Crazymeeks\MongoDB\Connection\Resolver\ConnectionResolverInterface;

class ConnectionResolver implements ConnectionResolverInterface
{

    private $_serverAddress;
    private $_serverOptons = [];
    private $_driverOptions = [];

    /**
     * @implement
     */
    public function setServerAddress(string $server)
    {
        $server = str_replace("mongodb://", "", (rtrim($server, "/")));

        $this->_serverAddress = "mongodb://$server/";

        return $this;
    }

    /**
     * @implement
     */
    public function setServerOptions(array $options = [])
    {
        $this->_serverOptons = $options;
        return $this;
    }


    /**
     * @implement
     */
    public function setDriverOptions(array $options = [])
    {
        $this->_driverOptions = $options;
        return $this;
    }

    /**
     * @implement
     */
    public function getServerAddress()
    {
        return $this->_serverAddress;
    }

    /**
     * @implement
     */
    public function getServerOptions(): array
    {
        return $this->_serverOptons;
    }

    /**
     * @implement
     */
    public function getDriverOptions(): array
    {
        return $this->_driverOptions;
    }

    /**
     * @implement
     */
    public function resolve()
    {
        $client = new Client(
            $this->getServerAddress(),
            $this->getServerOptions(),
            $this->getDriverOptions()
        );

        return $client;
    }
}