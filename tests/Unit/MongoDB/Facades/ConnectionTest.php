<?php

namespace Tests\Unit\MongoDB\Facades;

use Crazymeeks\MongoDB\Facades\Connection;
use Crazymeeks\MongoDB\Connection\Exceptions\ConnectionException;

class ConnectionTest extends \Tests\TestCase
{
    public function testCreateConnect()
    {

        Connection::setUpConnection('192.168.1.5', ['username' => 'root', 'password' => 'root'], [])
                  ->connect();
        
        $this->assertInstanceOf(\MongoDB\Client::class, Connection::getInstance());

        Connection::disconnect();

        Connection::setUpConnection('192.168.1.5', ['username' => 'root', 'password' => 'root'], [])
                  ->setDefaultDatabase('mytestdb')
                  ->connect();
        
        $this->assertInstanceOf(\MongoDB\Database::class, Connection::getInstance());

    }

    public function testThrowWhenConnectionNotInitialized()
    {
        $this->expectException(ConnectionException::class);
        Connection::getInstance();
    }

    public function testThrowWhenUnableToConnectToMongoDB()
    {
        $this->expectException(ConnectionException::class);
        Connection::setUpConnection('192.168.1.55', ['username' => 'root', 'password' => 'root'], [])
                  ->setDefaultDatabase('mytestdb')
                  ->connect();
    }
}