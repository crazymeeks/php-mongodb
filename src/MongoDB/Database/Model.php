<?php

namespace Nucleus\MongoDB\Database;

use MongoDB\BSON\ObjectId;
use Nucleus\Databases\Support\ConnectedClientTrait;
use Nucleus\MongoDB\Exceptions\ModelCollectionNotSetException;

abstract class Model
{


    use ConnectedClientTrait;
    
    /**
     * Model's attributes
     *
     * @var array
     */
    public $attributes = [];

    /**
     * The executed operation
     *
     * @var string
     */
    protected $executedOperation;

    /**
     * Write concern ACKNOWLEDGE
     * 
     * @var const
     */
    const ACKNOWLEDGE = 1;
    
    /**
     * Write concern JOURNALED
     * 
     * @var const
     */
    const JOURNALED = true;


    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
    }

    /**
     * Fill attributes
     *
     * @param array $attributes
     * 
     * @return void
     */
    public function fill(array $attributes = [])
    {

        if (count($this->attributes) > 0) {
            return;
        }

        if (count($attributes) > 0) {
            foreach($attributes as $fieldname => $value){
                $this->{$fieldname} = $value;
                unset($value);
            }
        } else {
            $this->attributes = $attributes;
        }
        
    }

    /**
     * Find model
     *
     * @param string $id
     * 
     * @return $this
     * 
     * @throws \MongoDB\Driver\Exception\InvalidArgumentException
     */
    public function find(string $id)
    {

        $result = $this->runQuery(['_id' => new ObjectId($id)], 'findOne');
        
        if ($result) {
            $results = $result->jsonSerialize();
            $this->map($results);
        }

        return $this;

    }

    /**
     * Save
     *
     * @param array $data
     * 
     * @return $this
     */
    public function insertOne(array $document)
    {
        $result = $this->runQuery($document, 'insertOne');

        $document['_id'] = $result->getInsertedId();

        $this->map($document);

        return $this;
    }

    /**
     * Insert many document in a collection
     *
     * @param array $documents
     * 
     * @return $this
     */
    public function insertMany(array $documents)
    {

        $result = $this->runQuery($documents, 'insertMany');

        $insertedIds = $result->getInsertedIds();
        $_documents = [];
        foreach($documents as $key => $values){
            foreach($values as $fieldname => $value){
                $_documents[$key]['_id'] = $insertedIds[$key]->__toString();
                $_documents[$key][$fieldname] = $value;
                unset($value);
            }
            unset($values);
        }
        
        $this->map($_documents);

        return $this;

    }

    /**
     * Map model fields and values
     *
     * @param array|object $data
     * 
     * @return void
     */
    private function map($documents)
    {
        foreach($documents as $fieldname => $value){

            if ($value instanceof ObjectId) {                    
                $this->_id = $value->__toString();
            } else {
                $this->{$fieldname} = $value;
            }

            unset($value);

        }

    }

    /**
     * Run a query
     *
     * @param array $data
     * @param string $operation MongoDB operation e.g 'insertOne', 'insertMany'
     * 
     * @return new static
     */
    private function runQuery(array $documents, string $operation)
    {

        $this->executedOperation = $operation;

        return $this->execute(
            $operation,
            $documents
        );

        // return new static($data, $operation);
    }

    /**
     * Execute query
     *
     * @param string $operation
     * @param array $data
     * 
     * @return true
     */
    private function execute($operation, array $documents = [])
    {

        $client = $this->getConnectedClient();

        try{
            return $client->{$this->getModelCollection()}->{$operation}($documents, $this->getWriteConcerns());
        }catch(\MongoCursorException $e){
            throw $e;
        }

    }

    /**
     * Get write concerns
     *
     * @return array
     */
    public function getWriteConcerns()
    {
        return [
            'w' => static::ACKNOWLEDGE,
            'j' => static::JOURNALED,
        ];
    }

    

    /**
     * Get model defined collection name
     *
     * @return string Model collection
     */
    protected function getModelCollection()
    {
        if (!property_exists($this, 'collection')) {
            throw new ModelCollectionNotSetException('Property {$collection} not set in ' . \get_class($this));
        }

        return $this->collection;
    }

    /**
     * Get model attributes
     *
     * @return array
     */
    public function get()
    {
        return json_decode(json_encode($this->attributes));
    }

    /**
     * Dynamically set property
     *
     * @param string $name
     * @param mixed $value
     * 
     * @return void
     */
    public function __set($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    /**
     * Dynamically get property
     *
     * @param string $name
     * 
     * @return mixed
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->attributes)) {
            return $this->attributes[$name];
        }
        throw new \Exception(sprintf("Property {%s} does not exist on %s", $name, get_class($this)));
    }

}