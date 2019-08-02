<?php

namespace Nucleus\Databases\Grammar\Options;

trait QueryOptions
{


    /**
     * Construct orderBy query and push it to the stack
     * 
     * @param string $fieldname
     * @param string $order
     *
     * @return $this
     */
    public function orderBy($fieldname, $order)
    {
        $this->shouldBeValidOrderByValue($order);

        $orderBy = [
            'sort' => [
                $fieldname => self::ORDERBY[$order]
            ]
        ];
                
        return $this->pushToQueryOptions($orderBy);
        
    }

    /**
     * Construct limit query and push it to the stack
     * 
     * @param int $limit
     *
     * @return $this
     */
    public function limit(int $limit)
    {
        $limit = [
            'limit' => $limit
        ];

        return $this->pushToQueryOptions($limit);
    }

    /**
     * Construct skip query and push it to the stack
     *
     * @param int $skip
     * 
     * @return $this
     */
    public function skip(int $skip)
    {
        if ($skip <= 0) {
            return $this;
        }

        $skip = [
            'skip' => $skip,
        ];

        return $this->pushToQueryOptions($skip);
    }


    /**
     * Push query options to stack
     *
     * @param mixed $options
     * 
     * @return $this
     */
    private function pushToQueryOptions($options)
    {
        if ($this->splQueryOptions->isEmpty()) {
            $this->splQueryOptions->push($options);
        } else {
            $popped = $this->splQueryOptions->top();
            $merge = array_merge($popped, $options);
            $this->splQueryOptions->push($merge);
        }

        return $this;
    }
}