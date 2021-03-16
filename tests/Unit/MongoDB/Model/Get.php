<?php

namespace Tests\Unit\MongoDB\Model;

trait Get
{

    public function testGetCollections()
    {
        $results = $this->_user->whereEq('name', 'john')
                    ->whereEq('email', 'test@email.com')
                    ->get();
        $this->assertInstanceOf(\Tests\User::class, $results[0]);

    }
}