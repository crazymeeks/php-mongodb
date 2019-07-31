<?php

namespace Tests\Unit\MongoDB\Database;

use Tests\TestCase;
use Nucleus\Databases\Database;
use Nucleus\MongoDB\Database\ConnectionResolver;

class DatabaseTest extends TestCase
{

    /**
     * @test
     * @group unit_positive
     */
    public function it_should_initialize_connection()
    {

        $resolver = new ConnectionResolver([
            'host' => '192.168.1.5',
            'database' => 'nucleus',
            'port'     => '27017', // Optional
            'options' => [ // Optional
                'username' => 'root',
                'password' => 'root'
            ],
        ]);

        $instance = Database::connect($resolver);
        
        $this->assertInstanceOf(\Nucleus\Databases\Contracts\ConnectionInterface::class, $instance);

    }

}