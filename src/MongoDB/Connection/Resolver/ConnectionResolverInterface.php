<?php

namespace Crazymeeks\MongoDB\Connection\Resolver;

interface ConnectionResolverInterface
{

    /**
     * Set mongodb server address for connection
     *
     * @param string $server Could be an ip
     * 
     * @return $this
     */
    public function setServerAddress(string $server);

    /**
     * Set server options
     *
     * @param array $options
     * 
     * @return $this
     */
    public function setServerOptions(array $options = []);

    /**
     * Set driver options
     *
     * @param array $options
     * 
     * @return $this
     */
    public function setDriverOptions(array $options = []);

    /**
     * Get mongodb server address
     *
     * @return string
     */
    public function getServerAddress();

    /**
     * Get server options
     *
     * @return array
     */
    public function getServerOptions(): array;

    /**
     * Get driver options
     *
     * @return array
     */
    public function getDriverOptions(): array;

    /**
     * Resolve connection
     *
     * @return \MongoDB\Client
     */
    public function resolve();

}