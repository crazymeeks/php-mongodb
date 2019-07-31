<?php

namespace Tests\Unit\MongoDB\Database;

use Tests\TestCase;

class SetupTest extends TestCase
{


    /**
     * @test
     * @group unit_positive
     */
    public function it_should_setup_database_configuration()
    {
        \Nucleus\MongoDB\Database\Setup::createConfiguration([
            'host' => '192.168.1.5',
            'database' => 'nucleus',
            'port'     => '27017', // Optional
            'options' => [ // Optional
                'username' => 'root',
                'password' => 'root'
            ],
        ]);

        
        $this->assertEquals([
            'host' => '192.168.1.5',
            'database' => 'nucleus',
            'port'     => '27017', // Optional
            'options' => [ // Optional
                'username' => 'root',
                'password' => 'root'
            ],
        ], \Nucleus\MongoDB\Database\Setup::getConfiguration());
    }
}