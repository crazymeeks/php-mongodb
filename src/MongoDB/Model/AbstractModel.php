<?php

namespace Crazymeeks\MongoDB\Model;

use Exception;
use Crazymeeks\MongoDB\Facades\Connection;
use Crazymeeks\MongoDB\Model\QueryBuilder\Builder;

abstract class AbstractModel
{

    /**
     * Fillable properties of the model
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * Model attributes
     *
     * @var array
     */
    private $attributes = [];

    public function __construct(array $attributes = [])
    {
        $this->map($attributes);
    }


    /**
     * @var \Crazymeeks\MongoDB\Model\QueryBuilder\Builder
     */
    protected $_queryBuilder;

    protected function getFillable()
    {
        return $this->fillable;
    }


    /**
     * Get collection name defined in model
     *
     * @return string
     */
    protected function getModelCollection()
    {
        if (!property_exists($this, 'collection')) {
            throw new Exception(sprintf("Property %s is required for class %s", '$collection', get_class($this)));
        }

        return $this->collection;
    }

    /**
     * Get connected MongoDB client
     *
     * @return \MongoDB\Client|\MongoDB\Database
     */
    private function getMongoClient()
    {
        return Connection::getInstance();
    }

    /**
     * Get default database for connected mongodb client
     *
     * @param \MongoDB\Client|\MongoDB\Database $client
     * 
     * @return \MongoDB\Database
     */
    protected function getDefaultDatabase($client): \MongoDB\Database
    {
        if ($client instanceof \MongoDB\Client) {
            // get defined database from model
            $database = $client->{$this->getDatabaseFromModel()};
            $client = $database;
        }

        return $client;
    }

    /**
     * Get database defined in model
     *
     * @return string
     */
    protected function getDatabaseFromModel()
    {
        if (property_exists($this, 'database') && $this->database) {
            return $this->database;
        }

        throw new \Exception("Fatal error: Database has not been set");
    }

    /**
     * Get one record in MongoDB
     *
     * @return array
     */
    public function first()
    {
        
        $queryBuilder = $this->getQueryBuilder();

        $query = $queryBuilder->get();

        $database = $this->getDefaultDatabase($this->getMongoClient());

        $collection = $database->{$this->getModelCollection()};

        $result = $collection->findOne($query);
            
        return $this->loadAttributeValue($result);

    }

    /**
     * Load attribute and value of the model
     *
     * @param \MongoDB\Model\BSONDocument|null $doc
     * 
     * @return \MongoDB\Model\BSONDocument|$this
     */
    private function loadAttributeValue(\MongoDB\Model\BSONDocument $doc = null)
    {
        $attributes = [];

        if ($doc) {
            $fillables = $this->getFillable();
            if (count($fillables) > 0) {
                foreach($fillables as $attribute){
                    $attributes[$attribute] = $doc->{$attribute};
                    unset($attribute);
                }
            } else {
                return $doc;
            }
        }

        return new static($attributes);
    }

    /**
     * Set query builder
     *
     * @param \Crazymeeks\MongoDB\Model\QueryBuilder\Builder $builder
     * 
     * @return $this
     */
    public function setQueryBuilder(Builder $builder)
    {
        $this->_queryBuilder = $builder;
    }

    /**
     * Get query builder instance
     *
     * @return \Crazymeeks\MongoDB\Model\QueryBuilder\Builder
     */
    public function getQueryBuilder(): \Crazymeeks\MongoDB\Model\QueryBuilder\Builder
    {
        return $this->_queryBuilder;
    }

    /**
     * Map modes attributes
     *
     * @param array $attributes
     * 
     * @return void
     */
    private function map(array $attributes = [])
    {
        foreach($attributes as $attribute => $value){
            $this->{$attribute} = $value;
        }
    }

    /**
     * Dynamically set property
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    /**
     * Dyanmically get property
     *
     * @param string $name
     * 
     * @return mixed
     */
    public function __get($name)
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
    }

    public function __call($name, $args)
    {
        $builder = new Builder($this);

        if (method_exists($builder, $name)) {
            return $builder->{$name}(...$args);
        }

        // Forward calls directly to \MongoDB\Client
        // here, developer my directly use 
        // \MongoDB\Client methods
        $param1 = isset($args[0]) ? $args[0] : [];

        $param2 = isset($args[1]) ? $args[1] : [];
        $param3 = isset($args[2]) ? $args[2] : [];
        $param4 = isset($args[3]) ? $args[3] : [];
        $param5 = isset($args[4]) ? $args[4] : [];

        $database = $this->getDefaultDatabase($this->getMongoClient());

        $collection = $database->{$this->getModelCollection()};

        return $collection->{$name}($param1, $param2, $param3, $param4, $param5);
    }
}