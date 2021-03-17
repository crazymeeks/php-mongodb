<?php

namespace Tests\Unit\MongoDB\Model;

trait First
{

    public function testAddWhereQuery()
    {
        $result = $this->_user->whereEq('name', 'john')
                    ->whereEq('email', 'test@email.com')
                    ->first();

        $this->assertSame('John', $result->name);
        $this->assertSame('test@email.com', $result->email);
    }

    public function testCallWhereQueriesAsStatic()
    {
        $result = \Tests\User::whereEq('name', 'john')->first();
        $this->assertInstanceOf(\Tests\User::class, $result);
    }

    public function testAddWhereNotQuery()
    {
        $result = $this->_user->whereEq('name', 'john')
                              ->whereNotEq('email', 'test1@email.com')
                              ->first();

        $this->assertSame('John', $result->name);
        $this->assertSame('test@email.com', $result->email);
    }

    public function testAddWhereInQuery()
    {
        $result = $this->_user->whereEq('name', 'john')
                              ->whereNotEq('email', 'test1@email.com')
                              ->whereIn('name', ['John', 'Jane'])
                              ->first();

        $this->assertSame('John', $result->name);
        $this->assertSame('test@email.com', $result->email);   
    }

    public function testAddWhereNotInQuery()
    {
        $result = $this->_user->whereEq('name', 'john')
                              ->whereNotEq('email', 'test1@email.com')
                              ->whereNotIn('name', ['test'])
                              ->first();

        $this->assertSame('John', $result->name);
        $this->assertSame('test@email.com', $result->email); 
    }

    public function testAddWhereGreaterThanQuery()
    {
        $result = $this->_user->whereEq('name', 'john')
                              ->whereNotEq('email', 'test1@email.com')
                              ->whereGreater('age', 9)
                              ->first();

        $this->assertSame('John', $result->name);
        $this->assertSame('test@email.com', $result->email); 
    }

    public function testAddWhereGreaterThanOrEqualQuery()
    {
        $result = $this->_user->whereEq('name', 'john')
                              ->whereNotEq('email', 'test1@email.com')
                              ->whereGreaterOrEq('age', 10)
                              ->first();

        $this->assertSame('John', $result->name);
        $this->assertSame('test@email.com', $result->email);
    }

    public function testAddWhereLessThanOrEqualQuery()
    {
        $result = $this->_user->whereEq('name', 'john')
                              ->whereNotEq('email', 'test1@email.com')
                              ->whereGreaterOrEq('age', 10)
                              ->whereLessThanOrEq('years', 10)
                              ->first();

        $this->assertSame('John', $result->name);
        $this->assertSame('test@email.com', $result->email);
        $this->assertSame(1, count($result));
    }

    public function testAddWhereLessThanQuery()
    {
        $result = $this->_user->whereEq('name', 'john')
                              ->whereNotEq('email', 'test1@email.com')
                              ->whereGreaterOrEq('age', 10)
                              ->whereLessThan('years', 11)
                              ->first();

        $this->assertSame('John', $result->name);
        $this->assertSame('test@email.com', $result->email);
    }
}