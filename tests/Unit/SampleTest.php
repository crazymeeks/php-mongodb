<?php

namespace Tests\Unit;

class SampleTest extends \Tests\TestCase
{

    public function testInsert()
    {
        $collection = (new \MongoDB\Client('mongodb://192.168.1.5/', ['username' => 'root', 'password' => 'root']))->hehe->users;

$insertOneResult = $collection->insertOne([
    'username' => 'admin',
    'email' => 'admin@example.com',
    'name' => 'Admin User',
]);
    $this->assertTrue(true);
    }
}