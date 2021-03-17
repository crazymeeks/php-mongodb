<?php

namespace Crazymeeks\MongoDB\Model\QueryBuilder;


use Crazymeeks\MongoDB\Model\AbstractModel;
use Crazymeeks\MongoDB\Model\QueryBuilder\LimitOption;
use Crazymeeks\MongoDB\Model\QueryBuilder\WhereQueries;

class Builder
{


    use LimitOption, WhereQueries;

    const WHERE = 'where';
    const WHERENOT = 'wherenot';
    const WHEREIN = 'wherein';
    const WHERENOTIN = 'wherenotin';
    const WHEREGREATER = 'wheregt';
    const WHEREGTE = 'wheregte';
    const WHERELTE = 'wherelte';
    const WHERELT = 'wherelt';    

    const SORT_ASC = 'asc';
    const SORT = 'sort';
    const LIMIT = 'limit';

    protected $_conditions = [
        self::WHERE => [],
        self::WHERENOT => [],
        self::WHEREIN => [],
        self::WHERENOTIN => [],
        self::WHEREGREATER => [],
        self::WHERELTE => [],
        self::WHERELT => [],

    ];

    protected $_orWhere = [];

    protected $_options = [];

    const OPERATORS = [
        'or' => '$or',
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
     * Construct sort by
     *
     * @return $this
     */
    public function sortBy()
    {
        $args = func_get_args();
        $numericOrdering = false;
        if (count($args) >= 3) {
            list($field, $value, $numericOrdering) = $args;
        } else {
            list($field, $value) = $this->extractQueryToFieldAndValue(func_get_args());
        }
        $options = $this->_options;

        if ($numericOrdering) {
            // mongodb's sort not properly working with number as string("1000").
            // to fix that behavior, we need to used collation
            $options['collation'] = ['locale' => 'en_US', 'numericOrdering' => true];
        }
        $options[self::SORT] = [$field => $value === self::SORT_ASC ? 1 : -1];
        $this->_options = $options;
        return $this;
    }

    /**
     * Get query options
     *
     * @return array
     */
    public function getQueryOptions(): array
    {
        return $this->_options;
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
    public function getQueries(): array
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

        // Merge orWhere
        $orWhere = [];
        foreach($this->_orWhere as $ors){
            foreach($ors as $field => $value){
                $orWhere[] = [
                    $field => $value
                ];
                unset($values);
            }
            unset($q);
        }

        if (count($orWhere) > 0) {
            $this->_orWhere['$or'] = [];
            $this->_orWhere['$or'] = $orWhere;
        }
        // end orWHere

        $wheres = array_merge($wheres, $this->_orWhere);
        return array_filter($wheres);
    }

    /**
     * Dehydrate conditions
     *
     * @return void
     */
    public function dehydrateConditions()
    {
        $this->_conditions = [
            self::WHERE => [],
            self::WHERENOT => [],
            self::WHEREIN => [],
            self::WHERENOTIN => [],
            self::WHEREGREATER => [],
            self::WHERELTE => [],
            self::WHERELT => [],
    
        ];

        $this->_orWhere = [];
        $this->_options = [];
    }

    public function __call($name, $args)
    {
        $this->_model->setQueryBuilder($this);
        if (method_exists($this->_model, $name)) {
            return $this->_model->{$name}(...$args);
        }
    }
}