<?php

namespace Tests\Unit\MongoDB\Connection\Resolver;

use Crazymeeks\MongoDB\Connection\Resolver\ConnectionResolver;

class ConnectionResolverTest extends \Tests\TestCase
{

    private $_resolver;
    public function setUp(): void
    {
        parent::setUp();

        $this->_resolver = new ConnectionResolver();
    }

    public function testInitiateConnection()
    {
        $client = $this->_resolver->setServerAddress("192.168.1.5")
                        ->setServerOptions(['username' => 'root', 'password' => 'root'])
                        ->setDriverOptions()
                        ->resolve();
        $this->assertInstanceOf(\MongoDB\Client::class, $client);
    }
}