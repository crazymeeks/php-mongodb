<?php

namespace Crazymeeks\MongoDB\Model\QueryBuilder;

use MongoDB\BSON\Regex as MongoRegex;
use Crazymeeks\MongoDB\Model\AbstractModel;

class Builder
{

    protected $_where = [];
    protected $_whereNot = [];
    const OPERATORS = [
        'ne' => '$ne',
        'gte' => '$gte',
        'gt' => '$gt',
        'lte' => '$lte',
        'lt' => '$lt',

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
        $args = func_get_args();
        
        list($field, $value) = $args;
        $this->_where[$field] = new MongoRegex(preg_quote($value), 'i');

        return $this;
    }

    /**
     * Construct a where not query
     *
     * @return $this
     */
    public function whereNotEq()
    {
        $args = func_get_args();
        
        list($field, $value) = $args;
        $this->_whereNot[$field] = [self::OPERATORS['ne'] => new MongoRegex(preg_quote($value), 'i')];
        
        return $this;
    }

    /**
     * Get constructed wheres
     *
     * @return array
     */
    public function get(): array
    {
        $wheres = array_merge($this->_where, $this->_whereNot);

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