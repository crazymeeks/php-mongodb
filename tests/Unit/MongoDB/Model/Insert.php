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
        User::create([
            'name' => 'C',
            'email' => 'php@email.com'
        ]);

        $find = $this->_user->whereEq('name', 'c')->first();
        $this->assertSame('C', $find->name);
        $this->assertInstanceOf(\Tests\User::class, $find);
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