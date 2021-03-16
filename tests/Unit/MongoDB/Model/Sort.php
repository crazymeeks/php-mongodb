<?php

namespace Tests\Unit\MongoDB\Model;

trait Sort
{
    public function testSortResult()
    {
        // mongodb's sort not properly working with number as string("1000").
        $numericOrdering = true;
        // end
        
        $results = $this->_user
                    ->sortBy('age', 'asc', $numericOrdering)
                    ->get();
        $this->assertEquals(8, $results[0]->age);
    }
}