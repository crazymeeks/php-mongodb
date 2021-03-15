<?php

namespace Tests\Unit\MongoDB\Model;

use Crazymeeks\MongoDB\Facades\Connection;

class ModelTest extends \Tests\TestCase
{

    private $_user;

    public function setUp(): void
    {
        parent::setUp();
        $this->_user = new User();
        
        Connection::setUpConnection('192.168.1.5', ['username' => 'root', 'password' => 'root'], [])
                  ->setDefaultDatabase('mytestdb')
                  ->connect();
    }

    public function testAddWhereQuery()
    {
        $result = $this->_user->whereEq('name', 'john')
                    ->whereEq('email', 'test@email.com')
                    ->first();

        $this->assertSame('John', $result->name);
        $this->assertSame('test@email.com', $result->email);

    }

    public function testAddWhereNotQuery()
    {
        $result = $this->_user->whereEq('name', 'john')
                              ->whereEq('email', 'test@email.com')
                              ->whereNotEq('age', 12)
                              ->first();

        $this->assertSame('John', $result->name);
        $this->assertSame('test@email.com', $result->email);
    }
    
    public function testInsert()
    {
        $this->_user->insertOne([
            'name' => 'John',
            'email' => 'Doe'
        ]);   
    }
    
}

class User extends \Crazymeeks\MongoDB\Model\AbstractModel
{

    protected $collection = 'users';

    protected $fillable = [
        '_id',
        'name',
        'email',
    ];
}