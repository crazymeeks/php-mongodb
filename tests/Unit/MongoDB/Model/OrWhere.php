<?php

namespace Tests\Unit\MongoDB\Model;

trait OrWhere
{
    
    public function testOrWhereQuery()
    {
        $results = $this->_user->whereEq('name', 'john')
                    ->orWhere('email', 'test@email.com')
                    ->orWhere('age', 10)
                    ->get();
        $this->assertInstanceOf(\Tests\User::class, $results[0]);
    }

    public function testOrWhereNotEqQuery()
    {
        $results = $this->_user->orWhereNotEq('email', 'test@email.com')
                               ->get();
        foreach($results as $result){
            $this->assertEquals($result->email, 'xxx@email.com');
        }
        $this->assertInstanceOf(\Tests\User::class, $results[0]);
    }

    public function testOrWhereInQuery()
    {
        $results = $this->_user->orWhereIn('email', ['test@email.com'])
                               ->get();
        $emails = [];
        foreach($results as $result){
            $emails[] = $result->email;
        }
        $this->assertTrue(in_array('test@email.com', $emails));
        $this->assertFalse(in_array('xxx@email.com', $emails));
        $this->assertInstanceOf(\Tests\User::class, $results[0]);
    }
    
    public function testOrWhereNotInQuery()
    {
        $results = $this->_user->orWhereNotIn('email', ['test@email.com'])
                               ->get();
        $emails = [];
        foreach($results as $result){
            $emails[] = $result->email;
        }

        $this->assertFalse(in_array('test@email.com', $emails));
        $this->assertTrue(in_array('xxx@email.com', $emails));
        $this->assertInstanceOf(\Tests\User::class, $results[0]);
    }
}