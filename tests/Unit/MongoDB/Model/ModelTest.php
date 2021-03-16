<?php

namespace Tests\Unit\MongoDB\Model;

use Tests\Unit\MongoDB\Model\First;
use Crazymeeks\MongoDB\Facades\Connection;

class ModelTest extends \Tests\TestCase
{

    use First;

    private $_user;

    public function setUp(): void
    {
        parent::setUp();
        $this->_user = new User();
        
        Connection::setUpConnection('192.168.1.5', ['username' => 'root', 'password' => 'root'], [])
                  ->setDefaultDatabase('mytestdb')
                  ->connect();
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