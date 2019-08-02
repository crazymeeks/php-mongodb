<?php

/*
 * This file is part of the Nucleus package.
 *
 * (c) Jefferson Claud <jefferson.claud@nuworks.ph>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleus\MongoDB\Database;

use MongoDB\BSON\ObjectId;
use Nucleus\Databases\Grammar\QueryGrammar;
use Nucleus\MongoDB\Exceptions\ModelCollectionNotSetException;

abstract class Model
{

    /**
     * Model collection name
     *
     * @var string
     */
    protected $collection;

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
     * Handles dynamic calls
     * 
     * @param string $method
     * @param array $arguments
     * 
     * @return \Nucleus\Databases\Grammar\QueryGrammar
     */
    public function __call($method, $arguments)
    {
        
        $grammar = new QueryGrammar($this->getModelCollection());
        
        
        if (!method_exists($grammar, $method)) {
            $grammar->{$method}($arguments);
        }

        $args = $grammar->redefineArgs($arguments);

        list($field, $operator, $value) = $args;

        return $grammar->{$method}($field, $operator, $value);
        
    }

    /**
     * Handles dynamic static calls
     *
     * @param string $name
     * @param array $arguments
     * 
     * @return \Nucleus\Databases\Grammar\QueryGrammar
     */
    public static function __callStatic($method, $arguments)
    {
        $me = new static();
        
        $grammar = new QueryGrammar($me->getModelCollection());
        
        
        if (!method_exists($grammar, $method)) {
            $grammar->{$method}($arguments);
        }

        $args = $grammar->redefineArgs($arguments);

        list($field, $operator, $value) = $args;

        return $grammar->{$method}($field, $operator, $value);
    }

}