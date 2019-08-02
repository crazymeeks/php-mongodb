<?php

namespace Tests\Unit\MongoDB\Database\Grammar;

/**
 * Query grammar for where(), whereIn(), whereBetween, etc
 */


use Tests\TestCase;
use MongoDB\BSON\ObjectId;
use Nucleus\Databases\Grammar\QueryGrammar;

/**
 * @covers \Nucleus\Databases\Grammar\QueryGrammar
 */
class QueryGrammarTest extends TestCase
{

    private $grammar;

    public function setUp(): void
    {
        parent::setUp();
        $this->grammar = new QueryGrammar('sample_model');
    }

    /**
     * @test
     * @group unit_positive
     */
    public function it_should_construct_where_query_with_field_value_parameter()
    {
        $collection = $this->grammar->where('_id', new ObjectId('5d368b98bd9cdf00c53dbf62'))
                      ->where('lastname', 'Doe')
                      ->get();

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $collection);
    }

    /**
     * @test
     * @group unit_positive
     */
    public function it_should_construct_where_query_with_closure_parameter()
    {
        $collection = $this->grammar->where(function($query){
                            $query->where('firstname', ['$ne' => 'sarah Jane']);
                      })->where('lastname', 'Doe')
                      ->get();
        $this->assertInstanceOf(\MongoDB\Model\BSONDocument::class, $collection[0]);
    }

    /**
     * @test
     * @covers \Nucleus\Databases\Grammar\QueryGrammar::whereNotEqual
     * @group unit_positive
     */
    public function it_should_construct_wherenot_query_with_field_value_parameter()
    {
        $collections = $this->grammar->whereNotEqual('firstname', 'sarah Jane')
                      ->get();
        $records = [];
        foreach($collections as $collection){
            $records[] = $collection->firstname . ' ' . $collection->lastname;
            unset($collection);
        }
        $this->assertTrue(!in_array('sarah Jane Doe', $records));
        $this->assertInstanceOf(\MongoDB\Model\BSONDocument::class, $collections[0]);
    }

    /**
     * @test
     * @group unit_positive
     */
    public function it_should_construct_where_query_with_field_operator_and_value_as_parameter()
    {
        $collections = $this->grammar->whereNotEqual(function($query){
                                        $query->where('lastname', 'Doe');
                                    })
                                     ->get();
        
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $collections);

    }

    /**
     * @test
     * @group unit_positive
     */
    public function it_should_construct_order_by_query()
    {
        $collection = $this->grammar->orderBy('firstname', 'desc')
                                     ->last();
        $this->assertEquals('Anthony', $collection->firstname);
        $this->assertInstanceOf(\MongoDB\Model\BSONDocument::class, $collection);
    }

    /**
     * @test
     * @group unit_positive
     */
    public function it_should_construct_limit_query()
    {
        $collection = $this->grammar->orderBy('firstname', 'desc')
                                     ->limit(1)
                                     ->get();
        $this->assertEquals(1, $collection->count());
    }

    /**
     * @test
     * @group unit_positive
     */
    public function it_should_construct_skip_query()
    {
        $collections = $this->grammar->skip(1)
                                     ->get();
        
        $names = [];
        foreach($collections as $collection){
            $names[] = $collection->firstname . ' ' . $collection->lastname;
        }
        $this->assertTrue((!in_array('Anthony Davis', $names)));
    }

}