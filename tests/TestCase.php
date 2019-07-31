<?php

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    

    public function setUp(): void
    {
        parent::setUp();
        
        \Nucleus\MongoDB\Database\Setup::createConfiguration([
            'host' => '192.168.1.5',
            'database' => 'nucleus',
            'port'     => '27017', // Optional
            'options' => [ // Optional
                'username' => 'root',
                'password' => 'root'
            ],
        ]);
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }
}
