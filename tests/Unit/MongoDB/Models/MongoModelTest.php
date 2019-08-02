<?php

namespace Tests\Unit\MongoDb;

use Tests\TestCase;
use MongoDB\BSON\ObjectId;
use Tests\Unit\MongoDB\Models\SampleModel;

/**
 * @covers \Tests\Unit\MongoDB\Models\SampleModel
 */
class MongoModelTest extends TestCase
{


    /**
     * @test
     * @group unit_positive
     */
    public function it_should_find_one_record()
    {
        $sample_model = new SampleModel();

        $class = $sample_model->where('firstname', 'Jeff')
                              ->first();
        $this->assertInstanceOf(\MongoDB\Model\BSONDocument::class, $class);
        
    }

    /**
     * @test
     * @group unit_positive
     */
    public function it_should_also_use_static_calls()
    {
        $result = SampleModel::where('firstname', 'Jeff')
                             ->first();

        $this->assertInstanceOf(\MongoDB\Model\BSONDocument::class, $result);
        $this->assertEquals('Jeff', $result->firstname);

    }

    /**
     * @test
     * @group unit_positive
     */
    public function it_should_chain_where_query()
    {
        $sample_model = new SampleModel();
        $collections = $sample_model->where('_id', new ObjectId('5d43c69abd9cdf022c6e9d22'))
                                    ->where('lastname', 'Claud')
                                    ->get();
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $collections);
    }

    /**
     * @test
     * @group unit_positive
     */
    public function it_should_throw_when_calling_non_existing_method_on_the_model()
    {
        $this->expectException(\BadMethodCallException::class);

        $sample_model = new SampleModel();
        $collections = $sample_model->where('_id', new ObjectId('5d43c69abd9cdf022c6e9d22'))
                                    ->where('lastname', 'Claud')
                                    ->nonExistingMethod();
    }

}