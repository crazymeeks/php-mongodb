<?php

namespace Crazymeeks\MongoDB\Model\QueryBuilder;

use MongoDB\BSON\Regex as MongoRegex;
use Crazymeeks\MongoDB\Model\AbstractModel;

class Builder
{

    const WHERE = 'where';
    const WHERENOT = 'wherenot';
    const WHEREIN = 'wherein';
    const WHERENOTIN = 'wherenotin';
    const WHEREGREATER = 'wheregt';
    const WHEREGTE = 'wheregte';
    const WHERELTE = 'wherelte';
    const WHERELT = 'wherelt';

    protected $_conditions = [
        self::WHERE => [],
        self::WHERENOT => [],
        self::WHEREIN => [],
        self::WHERENOTIN => [],
        self::WHEREGREATER => [],
        self::WHERELTE => [],
        self::WHERELT => [],
    ];

    const OPERATORS = [
        'ne' => '$ne',
        'gte' => '$gte',
        'gt' => '$gt',
        'lte' => '$lte',
        'lt' => '$lt',
        'in' => '$in',
        'nin' => '$nin',
    ];

    protected $_model;

    /**
     * Constructor
     *
     * @param \Crazymeeks\MongoDB\Model\AbstractModel $model
     */
    public function __construct(AbstractModel $model)
    {
        $this->_model = $model;
    }

    /**
     * Construct a where equal query
     * 
     * @param array $args
     *             [
     *                   'field',
     *                   'value'
     *             ]
     *
     * @return $this
     */
    public function whereEq()
    {
        
        list($field, $value) = $this->extractQueryToFieldAndValue(func_get_args());
        $this->_conditions[self::WHERE][$field] = new MongoRegex(preg_quote($value), 'i');
        return $this;
    }

    /**
     * Construct a where not query
     *
     * @return $this
     */
    public function whereNotEq()
    {
        
        list($field, $value) = $this->extractQueryToFieldAndValue(func_get_args());
        $this->_conditions[self::WHERENOT][$field] = [self::OPERATORS['ne'] => $value];
        return $this;
    }

    /**
     * Construct a where in query
     *
     * @return $this
     */
    public function whereIn()
    {
        list($field, $value) = $this->extractQueryToFieldAndValue(func_get_args());
        $this->_conditions[self::WHEREIN][$field] = [self::OPERATORS['in'] => $value];
        return $this;
    }

    /**
     * Construct where not in query
     *
     * @return $this
     */
    public function whereNotIn()
    {
        list($field, $value) = $this->extractQueryToFieldAndValue(func_get_args());
        $this->_conditions[self::WHERENOTIN][$field] = [self::OPERATORS['nin'] => $value];
        return $this;
    }

    /**
     * Construct where greater query
     *
     * @return $this
     */
    public function whereGreater()
    {
        list($field, $value) = $this->extractQueryToFieldAndValue(func_get_args());
        $this->_conditions[self::WHEREGREATER][$field] = [self::OPERATORS['gt'] => $value];
        return $this;
    }

    /**
     * Construct where greater than or equal query
     *
     * @return $this
     */
    public function whereGreaterOrEq()
    {
        list($field, $value) = $this->extractQueryToFieldAndValue(func_get_args());
        $this->_conditions[self::WHEREGTE][$field] = [self::OPERATORS['gte'] => $value];
        return $this;
    }

    /**
     * Construct where less than or equal query
     *
     * @return $this
     */
    public function whereLessThanOrEq()
    {
        list($field, $value) = $this->extractQueryToFieldAndValue(func_get_args());
        $this->_conditions[self::WHERELTE][$field] = [self::OPERATORS['lte'] => $value];
        return $this;
    }

    /**
     * Construct where less than query
     *
     * @return $this
     */
    public function whereLessThan()
    {
        list($field, $value) = $this->extractQueryToFieldAndValue(func_get_args());
        $this->_conditions[self::WHERELT][$field] = [self::OPERATORS['lt'] => $value];
        return $this;
    }

    /**
     * Extract query
     *
     * @return array
     */
    private function extractQueryToFieldAndValue(array $args)
    {
        list($field, $value) = $args;
        return [$field, $value];
    }

    /**
     * Get constructed wheres
     *
     * @return array
     */
    public function get(): array
    {
        return $this->mergeConditions();
    }

    /**
     * Merge all where conditions
     *
     * @return array
     */
    private function mergeConditions(): array
    {
        $wheres = [];
        foreach($this->_conditions as $values){
            $wheres = array_merge($wheres, $values);
            unset($values);
        }

        return array_filter($wheres);
    }

    public function __call($name, $args)
    {
        $this->_model->setQueryBuilder($this);
        if (method_exists($this->_model, $name)) {
            return $this->_model->{$name}();
        }
    }
}