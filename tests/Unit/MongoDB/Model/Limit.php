<?php

namespace Tests\Unit\MongoDB\Model;

trait Limit
{

    public function testAddLimitQuery()
    {
        $results = $this->_user
                    ->orWhereIn('age', [10, 8])
                    ->limit(1)
                    ->get();

        $this->assertSame(1, count($results));
        $this->assertInstanceOf(\Tests\User::class, $results[0]);
    }
}