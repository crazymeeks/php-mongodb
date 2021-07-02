<?php

namespace Tests\Unit\MongoDB\Facades;

use Crazymeeks\MongoDB\Facades\Connection;
use Tests\TestCase as AbstractTestCase;
use Crazymeeks\MongoDB\Connection\Resolver\Exceptions\ConnectionException;

class ConnectionTest extends AbstractTestCase
{
    public function testCreateConnect()
    {


        Connection::disconnect();

        Connection::setUpConnection('172.28.5.1', ['username' => 'root', 'password' => 'root'], [])
                  ->setDefaultDatabase('testing_mytestdb_crzymix')
                  ->connect();
        
        $this->assertInstanceOf(\MongoDB\Database::class, Connection::getInstance());

    }

    public function testThrowWhenConnectionNotInitialized()
    {
        $this->expectException(ConnectionException::class);
        Connection::disconnect();
        Connection::getInstance();
    }

    public function testThrowWhenUnableToConnectToMongoDB()
    {
        $this->expectException(ConnectionException::class);
        Connection::disconnect();
        Connection::getInstance();
    }
}