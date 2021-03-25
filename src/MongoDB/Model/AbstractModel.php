<?php

namespace Crazymeeks\MongoDB\Model;

use Countable;
use Exception;
use Crazymeeks\MongoDB\Facades\Connection;
use Crazymeeks\MongoDB\Model\QueryBuilder\Builder;

abstract class AbstractModel implements Countable
{

    const ATT_CREATED_AT = 'created_at';
    const ATT_UPDATED_AT = 'updated_at';

    /**
     * Fillable properties of the model
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * Auto insert/update 'created_at' and 'updated_at'
     * field on the model
     *
     * @var boolean
     */
    protected $timestamps = true;

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
    protected $_queryBuilder = null;

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

        $query = $queryBuilder->getQueries();

        $collection = $this->getCollection();

        $result = $collection->findOne($query);
            
        return $this->loadAttributeValue($result);

    }

    /**
     * Get collection
     *
     * @return \MongoDB\Collection
     */
    private function getCollection(): \MongoDB\Collection
    {

        $database = $this->getDefaultDatabase($this->getMongoClient());
        
        $collection = $database->{$this->getModelCollection()};

        return $collection;
    }

    /**
     * Get records in MongoDB
     *
     * @return array
     */
    public function get()
    {
        $queryBuilder = $this->getQueryBuilder();

        $query = $queryBuilder->getQueries();
        $options = $queryBuilder->getQueryOptions();

        $options['batchSize'] = 10;

        $collection = $this->getCollection();
        
        $collections = $collection->find($query, $options);
        
        return $this->loadCollections($collections);
    }

    /**
     * Get timestamp fields of the model
     *
     * @return array
     */
    protected function getTimestamps()
    {
        return [
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
    }

    /**
     * Indicate whether the fields 'created_at' and 'updated_at'
     * are automatically inserted/updated on the model
     *
     * @return boolean
     */
    private function isTimestamps()
    {
        return $this->timestamps;
    }

    /**
     * Save data
     *
     * @return $this
     * 
     * @throws \Exception|\Error
     */
    public function save()
    {

        $attributes = $this->getAttributes();
        if (count($attributes) === 0) {
            throw new \Exception("Error. Data not defined.");
        }

        if ($this->isTimestamps()) {

            $timestamps = $this->getTimestamps();

            if (!isset($attributes[self::ATT_CREATED_AT])) {
                $attributes[self::ATT_CREATED_AT] = $timestamps[self::ATT_CREATED_AT];
            }

            if (!isset($attributes[self::ATT_UPDATED_AT])) {
                $attributes[self::ATT_UPDATED_AT] = $timestamps[self::ATT_UPDATED_AT];
            }
        }
        
        $collection = $this->getCollection();

        try {
            $result = $collection->insertOne($attributes);
            $attributes['_id'] = $result->getInsertedId();
            $this->map($attributes);
            return $this;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            throw $e;
        } catch (\Error $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    /**
     * Update document
     *
     * @param $attributes Array of attribute-value
     * 
     * @return mixed
     */
    public function update(array $attributes)
    {
        return $this->doUpdate($attributes);
    }

    /**
     * Execute update on document
     *
     * @param array $attributes
     * @param string $operation
     * 
     * @return mixed
     */
    private function doUpdate(array $attributes, string $operation = 'updateOne')
    {

        $queryBuilder = $this->getQueryBuilder();
        
        $query = $queryBuilder->getQueries();

        $collection = $this->getCollection();

        $timestamps = $this->getTimestamps();
        unset($timestamps[self::ATT_CREATED_AT]);
        $attributes = array_merge($attributes, $timestamps);
        
        try {
            $collection->{$operation}($query, [
                '$set' => $attributes
            ]);

            if ($operation === 'updateOne') {
                $find = $this->first();
            } else {
                $find = true;
            }

            $queryBuilder->dehydrateConditions();

            return $find;
            
        } catch (\Exception $e) {
            error_log($e->getMessage());
            throw $e;
        } catch (\Error $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    /**
     * Update many documents
     *
     * @param array $attributes
     * 
     * @return mixed
     */
    public function updateMany(array $attributes)
    {
        return $this->doUpdate($attributes, 'updateMany');
    }

    /**
     * Delete document
     *
     * @return boolean
     */
    public function delete()
    {
        return $this->doDelete();
    }

    /**
     * Delete document
     *
     * @return boolean
     */
    public function deleteMany()
    {
        return $this->doDelete('deleteMany');
    }

    /**
     * Execute deletion on document
     *
     * @param string $operation
     * 
     * @return boolean
     */
    private function doDelete(string $operation = 'deleteOne')
    {
        $queryBuilder = $this->getQueryBuilder();
        
        $query = $queryBuilder->getQueries();

        $collection = $this->getCollection();

        try {
            $collection->{$operation}($query);
            return true;
        } catch (\Exception $e) {
            error_log($e->getMessage());
        } catch (\Error $e) {
            error_log($e->getMessage());
        }

        return false;
    }

    /**
     * Load collections
     *
     * @param \MongoDB\Driver\Cursor $cursor
     * 
     * @return \Crazymeeks\MongoDB\Model\AbstractModel[]
     */
    private function loadCollections(\MongoDB\Driver\Cursor $cursor)
    {
        
        $models = [];
        foreach($cursor as $collection){
            $attribute_value = [];
            foreach($collection as $field => $value){
                $attribute_value[$field] = $value;
                unset($value);
            }
            $models[] = new static($attribute_value);
            unset($collection);
        }
        
        return $models;
        
    }

    /**
     * Load attribute and value of the model
     *
     * @param \MongoDB\Model\BSONDocument|null $doc
     * 
     * @return mixed
     */
    private function loadAttributeValue(\MongoDB\Model\BSONDocument $doc = null)
    {
        $attributes = [];
        
        if ($doc instanceof \MongoDB\Model\BSONDocument) {
            $fillables = $this->getFillable();
            if (count($fillables) > 0) {
                foreach($fillables as $attribute){
                    $attributes[$attribute] = property_exists($doc, $attribute) ? $doc->{$attribute} : null;
                    unset($attribute);
                }
            } else {
                return $doc;
            }

        }

        return new static($attributes);
    }

    /**
     * @implement
     */
    public function count()
    {
        $attributes = $this->getAttributes();
        if (count($attributes) == count($attributes, COUNT_RECURSIVE)) {
            return count($attributes) >= 1 ? 1 : 0;
        }
        return count($this->getAttributes());
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
        return !$this->_queryBuilder ? new \Crazymeeks\MongoDB\Model\QueryBuilder\Builder($this) : $this->_queryBuilder;
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

    /**
     * Get loaded attributes
     *
     * @return array
     */
    private function getAttributes()
    {
        return $this->attributes;
    }

    public function __call($name, $args)
    {
        $builder = new Builder($this);
        
        if (method_exists($builder, $name)) {
            return $builder->{$name}(...$args);
        }

        if (method_exists($this, $name)) {
            return $this->{$name}($args);
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

        if (method_exists($collection, $name)) {
            return $collection->{$name}($param1, $param2, $param3, $param4, $param5);
        }

        throw new \BadMethodCallException(sprintf("Calling undefined method %s() in %s.", $name, get_class($this)));
        
    }

    public static function __callStatic($name, $args)
    {
        $builder = new Builder(new static());

        if (method_exists($builder, $name)) {
            return $builder->{$name}(...$args);
        }

        $me = new static();
        if (method_exists($me, $name)) {
            return $me->{$name}($args);
        }

        if ($name === 'create') {
            list($attributes) = $args;
            $me->map($attributes);
            return $me->save();
        }

        return $me->{$name}(...$args);
    }
}