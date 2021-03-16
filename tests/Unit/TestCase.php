<?php

namespace Tests;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{

    protected $_user;

    
}

class User extends \Crazymeeks\MongoDB\Model\AbstractModel
{

    protected $collection = 'users';

    protected $fillable = [
        '_id',
        'name',
        'email',
        'age'
    ];
}