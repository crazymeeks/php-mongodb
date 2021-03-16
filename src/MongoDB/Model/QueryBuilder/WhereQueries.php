<?php

namespace Crazymeeks\MongoDB\Model\QueryBuilder;

use MongoDB\BSON\Regex as MongoRegex;

trait WhereQueries
{
    
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
     * Construct an or where query
     *
     * @return $this
     */
    public function orWhere()
    {
        list($field, $value) = $this->extractQueryToFieldAndValue(func_get_args());
        return $this->addOrWhereStack([$field => $value]);
    }

    /**
     * Construct an or where not query
     *
     * @return $this
     */
    public function orWhereNotEq()
    {
        list($field, $value) = $this->extractQueryToFieldAndValue(func_get_args());
        return $this->addOrWhereStack([$field => [self::OPERATORS['ne'] => $value]]);
    }

    /**
     * Construct an or where in query
     *
     * @return $this
     */
    public function orWhereIn()
    {
        list($field, $value) = $this->extractQueryToFieldAndValue(func_get_args());
        if (!is_array($value)) {
            $value = [$value];
        }
        return $this->addOrWhereStack([$field => [self::OPERATORS['in'] => $value]]);   
    }

    /**
     * Construct an or where not in query
     *
     * @return $this
     */
    public function orWhereNotIn()
    {
        list($field, $value) = $this->extractQueryToFieldAndValue(func_get_args());
        if (!is_array($value)) {
            $value = [$value];
        }
        return $this->addOrWhereStack([$field => [self::OPERATORS['nin'] => $value]]);   
    }


    /**
     * Add to or where stack
     * @param array $values
     * 
     * @return $this
     */
    private function addOrWhereStack(array $values)
    {

        $current_ors = [];
        if (count($this->_orWhere) > 0) {
            $current_ors = $this->_orWhere['$or'];
            $current_ors = array_merge($current_ors, $values);
        } else {
            $current_ors = $values;
        }
        
        $this->_orWhere['$or'] = array_filter($current_ors);

        return $this;
    }
}