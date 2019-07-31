<?php

namespace Tests\Unit\MongoDB\Models;

use Nucleus\MongoDB\Database\Model;

/**
 * Test model only
 * 
 */
class SampleModel extends Model
{

    /**
     * Collection name where the document
     * will be saved.
     *
     * @var string
     */
    protected $collection = 'sample_model';

}