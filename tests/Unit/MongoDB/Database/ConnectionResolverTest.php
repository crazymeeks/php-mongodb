<?php

namespace Tests\Unit\MongoDB\Database;

use Tests\TestCase;
use Nucleus\MongoDB\Database\ConnectionResolver;

class ConnectionResolverTest extends TestCase
{


    /**
     * @test
     * @group unit_positive
     */
    public function it_should_prepare_mongodb_connection_string()
    {
        $con = new ConnectionResolver([
            'host' => '192.168.1.5',
            'database' => 'nucleus',
            'port'     => '27017', // Optional
            'options' => [ // Optional
                'username' => 'root',
                'password' => 'root'
            ],
        ]);

        $constr = $con->constructConnectionString();

        $this->assertEquals('mongodb://192.168.1.5:27017/', $constr);
    }

    /**
     * @test
     * @group unit_positive
     */
    public function it_should_prepare_mongodb_connection_string_even_host_is_array()
    {
        $con = new ConnectionResolver([
            'host' => ['192.168.1.5', '127.0.0.1'],
            'database' => 'nucleus',
            'port'     => '27017', // Optional
            'options' => [ // Optional
                'username' => 'root',
                'password' => 'root'
            ],
        ]);
        
        $constr = $con->constructConnectionString();

        $this->assertEquals('mongodb://192.168.1.5:27017/,mongodb://127.0.0.1:27017/', $constr);
    }

    /**
     * Actual connection to mongodb
     *
     * @test
     * @group unit_positive
     */
    public function it_should_resolve_mongodb_connection()
    {
        $con = new ConnectionResolver([
            'host' => '192.168.1.5',
            'database' => 'nucleus',
            'port'     => '27017', // Optional
            'options' => [ // Optional
                'username' => 'root',
                'password' => 'root'
            ],
        ]);
        
        $client = $con->resolve();
        
        $this->assertInstanceOf(\Nucleus\Databases\Contracts\ConnectionInterface::class, $client);
    }
    
}