<?php

namespace Tests\Unit\MongoDB\Connection\Resolver;

use Tests\TestCase as AbstractTestCase;
use Crazymeeks\MongoDB\Connection\Resolver\ConnectionResolver;

class ConnectionResolverTest extends AbstractTestCase
{

    private $_resolver;
    public function setUp(): void
    {
        parent::setUp();

        $this->_resolver = new ConnectionResolver();
    }

    public function testInitiateConnection()
    {
        $client = $this->_resolver->setServerAddress("172.28.5.1")
                        ->setServerOptions(['username' => 'root', 'password' => 'root'])
                        ->setDriverOptions()
                        ->resolve();
        $this->assertInstanceOf(\MongoDB\Client::class, $client);
    }
}