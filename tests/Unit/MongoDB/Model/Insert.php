<?php

namespace Tests\Unit\MongoDB\Model;

use Tests\User;

trait Insert
{
    
    public function testCreateOneUsingSave()
    {

        $user = new User([
            'name' => 'PHP',
            'email' => 'php@email.com'
        ]);

        $user->save();
        $this->assertEquals($user->name, 'PHP');
    }

    public function testCreateOneUsingCreateMethod()
    {
        $user = User::create([
            'name' => 'C',
            'email' => 'php@email.com'
        ]);

        $this->assertSame('C', $user->name);
        $this->assertInstanceOf(\Tests\User::class, $user);
    }

    public function testInsertMany()
    {
        $result = User::insertMany([
            [
                'name' => 'C',
                'email' => 'php@email.com',
            ],
            [
                'name' => 'D',
                'email' => 'd@email.com',
            ],
        ]);

        $this->assertInstanceOf(\MongoDB\InsertManyResult::class, $result);
    }
}