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
    public function it_should_insert_single_data_to_mongodb()
    {
        $sample_model = new SampleModel();
        
        $t = $sample_model->insertOne([
            'firstname' => 'Jane',
            'lastname'  => 'Doe'
        ]);
        
        $this->assertInstanceOf(SampleModel::class, $t);
    }

    /**
     * @test
     * @group unit_positive
     */
    public function it_should_insert_many_record()
    {
        $sample_model = new SampleModel();
        
        $t = $sample_model->insertMany([
            [
                'firstname' => 'sarah Jane',
                'lastname'  => 'Doe'
            ],
            [
                'firstname' => 'John',
                'lastname'  => 'Doe'
            ]
        ]);
       
        $this->assertObjectHasAttribute('_id', $t->get()[0]);
        $this->assertObjectHasAttribute('firstname', $t->get()[0]);
        $this->assertObjectHasAttribute('lastname', $t->get()[0]);
        $this->assertInstanceOf(SampleModel::class, $t);
    }

    /**
     * @test
     * @group unit_positive
     */
    public function it_should_find_one_record()
    {
        $sample_model = new SampleModel();
        $this->assertInstanceOf(SampleModel::class, $sample_model->find('5d31af4abd695506fd3d1752'));
    }

    /**
     * test
     */
    public function it_should_chain_where_query()
    {
        $sample_model = new SampleModel();
        $sample_model->where('id', new ObjectId('5d31af4abd695506fd3d1752'));
    }

}