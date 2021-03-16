<?php

namespace Crazymeeks\MongoDB\Model\QueryBuilder;

trait LimitOption
{

    /**
     * Add limit to query
     *
     * @return $this
     */
    public function limit()
    {
        $args = func_get_args();
        list($limit) = $args;

        $options = $this->_options;
        
        if ($limit) {
            $options[self::LIMIT] = (int) $limit;
            $this->_options = $options;
        }

        return $this;
    }
}