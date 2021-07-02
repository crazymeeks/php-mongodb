<?php

namespace Tests\Unit\MongoDB\Model;

use Tests\User;
use Tests\Unit\MongoDB\Model\Get;
use Tests\Unit\MongoDB\Model\Sort;
use Tests\Unit\MongoDB\Model\First;
use Tests\Unit\MongoDB\Model\Limit;
use Tests\Unit\MongoDB\Model\OrWhere;
use Tests\Unit\MongoDB\Model\Insert;
use Tests\Unit\MongoDB\Model\Update;
use Tests\Unit\MongoDB\Model\Delete;
use Crazymeeks\MongoDB\Facades\Connection;


class ModelTest extends \Tests\TestCase
{

    use First,
    Get,
    Sort,
    OrWhere,
    Limit,
    Insert,
    Update,
    Delete;

    public function setUp(): void
    {
        parent::setUp();
    
        Connection::setUpConnection('172.28.5.1', ['username' => 'root', 'password' => 'root'], [])
                  ->setDefaultDatabase('testing_mytestdb_crzymix')
                  ->connect();

        $this->_user = new User();
        $this->_user->insertMany([
            [
                'name' => 'John',
                'email' => 'test@email.com',
                'age' => 10,
                'years' => 10
            ],
            [
                'name' => 'XXX',
                'email' => 'xxx@email.com',
                'age' => 8,
                'years' => 10
            ],
        ]);
    }

    public function tearDown(): void
    {
        $user = new User();
        $user->deleteMany();
        parent::tearDown();
    }
    
}