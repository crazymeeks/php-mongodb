<?php

/*
 * This file is part of the Nucleus package.
 *
 * (c) Jefferson Claud <jefferson.claud@nuworks.ph>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleus\Databases\Grammar;

use Nucleus\Databases\Grammar\JoinQuery;
use Nucleus\Databases\Grammar\Options\Like;
use Nucleus\Databases\Support\ConnectedClientTrait;
use Nucleus\Databases\Grammar\Options\QueryOptions;
use Nucleus\Exceptions\Database\Grammar\QueryGrammarException;

class QueryGrammar
{

    use ConnectedClientTrait, QueryOptions, JoinQuery;

    /**
     * Stack name for whereNotEqual query
     * 
     * @var const
     */
    const STACK_WHERENOT = 'whereNotEqual';

    /**
     * Stack name for where query
     * 
     * @var const
     */
    const STACK_WHERE = 'where';

    /**
     * Insert transaction
     * 
     * @var const
     */
    const INSERT_ONE = 'insertOne';

    /**
     * Insert many transaction
     * 
     * @var const
     */
    const INSERT_MANY = 'insertMany';

    /**
     * Update transaction
     * 
     * @var const
     */
    const UPDATE_ONE = 'updateOne';

    /**
     * Update many transaction
     * 
     * @var const
     */
    const UPDATE_MANY = 'updateMany';

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


    const ORDERBY = [
        'asc' => 1,
        'desc' => -1,
    ];

    /**
     * Constructed query stack
     *
     * @var array
     */
    private $queryStack = [
        self::STACK_WHERE => [],
        self::STACK_WHERENOT => [],
    ];

    /**
     * Collection name
     *
     * @var string
     */
    private $collection;

    /**
     * Query grammar stack
     *
     * @var \SplStack
     */
    private $splStackWhere;


    /**
     * The second parameter for find() method where projection, sort, limit
     * will be pushed
     *
     * @var \SplStack
     */
    private $splQueryOptions;

    /**
     * The join query stack
     *
     * @var array
     */
    private $joinStack = [];

    /**
     * Constructor
     *
     * @param string $collection Collection name where the query will run
     * 
     */
    public function __construct(string $collection = null)
    {
        $this->collection = $collection;

        $this->splStackWhere = new \SplStack();
        $this->splQueryOptions = new \SplStack();
        $this->splJoinStack = [];
        
    }

    /**
     * Set collection
     *
     * @param string $collection
     * 
     * @return $this
     */
    public function setCollection(string $collection)
    {
        $this->collection = $collection;

        return $this;
    }

    /**
     * Construct where query and push it to stack
     * 
     * @param mixed ...$args
     *
     * @return $this
     */
    public function where(...$args)
    {
        
        return $this->pushQueryToTheirStack($args);
    }

    /**
     * Push a query to their intended stack.
     * 
     * When develop called a where() method, we will push it to 'where' stack,
     * and when whereNotEqual() method is called, we will push it to 'whereNot' stack
     *
     * @param array $args
     * @param string $stackType The type of stack
     * 
     * @return void
     */
    private function pushQueryToTheirStack(array $args)
    {
        $this->redefineArgs($args);

        // check if $field is instanceof \Closure
        if ($args[0] instanceof \Closure) {
            
            $args[0]($this);

            return $this;
        }
        
        list($field, $operator, $value) = $args;
        
        $this->pushWhereStack($field, $operator, $value);

        return $this;
    }

    /**
     * Redefine the ...$args param
     *
     * @param &$args
     * 
     * @return array
     */
    public function redefineArgs(&$args)
    {
        $args = array_filter($args);

        if (count($args) === 2) {
            $args[] = null;
        }

        if (count($args) === 1) {
            $args[] = null;
            $args[] = null;
        }

        return $args;
    }

    /**
     * Construct whereNot query and push it to stack
     * 
     * @param mixed ...$args
     *
     * @return $this
     */
    public function whereNotEqual(...$args)
    {   

        $this->requireArgs($args);

        if (!$args[0] instanceof \Closure) {
            if (count($args) < 2) {
                throw new \InvalidArgumentException("Arguments passed to " . __METHOD__ . " must at least 2 or instance of \Closure.");
            }

            $args[1] = ['$ne' => $args[1]];

        }
        
        return $this->pushQueryToTheirStack($args);
    }

    /**
     * Construct whereLike query and push it to stack
     * 
     * @param array $args
     *             array(
     *                 'fielname', 'search_value', 'like_symbol', 'flag'
     *             )
     *
     * @return $this
     */
    public function whereLike(...$args)
    {
        $this->requireArgs($args);

        if (!$args[0] instanceof \Closure) {
            if (count($args) < 2) {
                throw new \InvalidArgumentException("Arguments passed to " . __METHOD__ . " must at least 2 or instance of \Closure.");
            }
            // Set like compare type to any
            if (count($args) === 2) {
                $args[] = Like::COMPARE_ANY;
                $args[] = Like::INSENSITIVE;
            }

            // Add flag
            if (count($args) === 3) {
                $args[] = Like::INSENSITIVE;
            }

            list($field, $search_value, $like_symbol, $flag) = $args;
            
        }

        if ($like_symbol === Like::COMPARE_ANY) {
            $search_value = new \MongoDB\BSON\Regex("$search_value", $flag);
        } elseif ($like_symbol === Like::COMPARE_START) {
            $search_value = new \MongoDB\BSON\Regex("^$search_value", $flag);
        } else {
            $search_value = new \MongoDB\BSON\Regex("$search_value$", $flag);
        }

        $args = [
            $field,
            $search_value
        ];
        
        
        return $this->pushQueryToTheirStack($args);

    }

    /**
     * Make sure arguments are passed in 
     * where(), whereNot(), whereLike() query
     *
     * @param array &$args
     * 
     * @return void
     */
    private function requireArgs(array &$args)
    {
        $args = array_filter($args);

        if (count($args) <= 0) {
            throw new \InvalidArgumentException();
        }
    }

    /**
     * Validate if orderBy() second parameter is either 'asc' or 'desc'
     *
     * @param string $orderby
     * 
     * @return void
     */
    private function shouldBeValidOrderByValue($orderby)
    {
        if (!array_key_exists($orderby, self::ORDERBY)) {
            throw QueryGrammarException::unsupportedOrderBy($orderby);
        }
    }

    /**
     * Get all constructed where, whereNot queries
     *
     * @return array
     */
    private function getWhereQueries(): array
    {
        return $this->splStackWhere->isEmpty() ? [] : $this->splStackWhere->top();
    }

    /**
     * Get all query options from stack
     *
     * @return array
     */
    private function getQueryOptions(): array
    {
        return $this->splQueryOptions->isEmpty() ? [] : $this->splQueryOptions->top();
    }
    

    /**
     * Push where query to $splStackWhere
     *
     * @param mixed $field Can be collection's field name instanceof \Closure
     * @param string $operator
     * @param string $value
     * 
     * @return void
     */
    private function pushWhereStack($field, $operator, $value = null)
    {

        if ($this->splStackWhere->isEmpty()) {
            $this->splStackWhere->push([
                $field => $operator
            ]);

            return;
        }

        $popped = $this->splStackWhere->pop();
        $merge = array_merge($popped, [$field => $operator]);
        $this->splStackWhere->push($merge);
        
    }


    /**
     * Get documents
     *
     * @return \Illuminate\Support\Collection
     */
    public function get()
    {
        $client = $this->getConnectedClient();
        $collection = \Illuminate\Support\Collection::make($client->{$this->collection}->find($this->getWhereQueries(), $this->getQueryOptions())->toArray());

        return $collection;

    }

    /**
     * Get the last record from document
     *
     * @return \MongoDB\Model\BSONDocument
     */
    public function last()
    {
        return $this->get()->last();
    }


    /**
     * Get the first record from document
     *
     * @return \MongoDB\Model\BSONDocument
     */
    public function first()
    {
        return $this->get()->first();
    }

    /**
     * Insert data to database
     *
     * @param array $data
     * 
     * @return \Illuminate\Support\Collection
     */
    public function insert(array $data)
    {
        return $this->executeWrite($data, self::INSERT_ONE);
    }

    /**
     * Execute write query
     *
     * @param array $data
     * @param string $operation
     * 
     * @return mixed
     */
    private function executeWrite(array $data, string $operation)
    {
        $client = $this->getConnectedClient();
        return $client->{$this->collection}->{$operation}($data, $this->getWriteConcerns());
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
     * Alias of insert()
     * 
     * @param array $data
     *
     * @return mixed
     */
    public function insertOne(array $data)
    {
        return $this->insert($data);
    }

    /**
     * Execute write many query
     *
     * @return void
     */
    public function insertBulk(array $data)
    {
        return $this->executeWrite($data, self::INSERT_MANY);
    }

    /**
     * Alias of insertBulk
     *
     * @param array $data
     * 
     * @return void
     */
    public function insertMany(array $data)
    {
        return $this->insertBulk($data);
    }
    
    /**
     * Execute update query
     *
     * @param array $data key-value pair
     * @param string $operation
     * 
     * @return bool
     */
    public function update(array $data, string $operation = self::UPDATE_ONE)
    {

        if (count($data) <= 0) {
            throw QueryGrammarException::updateQueryInvalidArgument();
        }

        $args = [
            '$set' => $data
        ];
        
        return $this->executeUpdate($this->getWhereQueries(), $args, $operation);
    }

    /**
     * Execute update many query
     *
     * @param array $data
     * 
     * @return bool
     */
    public function updateMany(array $data)
    {
        return $this->update($data, self::UPDATE_MANY);
    }

    /**
     * Execute update query
     *
     * @param array $where
     * @param array $data
     * 
     * @return bool
     */
    private function executeUpdate(array $where, array $data, string $operation): bool
    {
        $client = $this->getConnectedClient();
        $affected = $client->{$this->collection}->{$operation}($where, $data);

        return $affected->getModifiedCount() > 0;

    }


    /**
     * Extract collection and key delimited by dot(.)
     *
     * @param string $collection_dot_key
     * 
     * @return array
     */
    public function extractCollectionAndKey(string $collection_dot_key)
    {
        $exploded = explode('.', $collection_dot_key);

        if (count($exploded) === 2) {
            return $exploded;
        }

        // if developer didn't define
        // a key name from collection(i.e users not users._id or users.*)
        // we assume the key as _id automatically
        return [
            $exploded[0],
            '_id'
        ];
    }


    /**
     * Handles dynamic calls
     *
     * @param string $method
     * @param array $arguments
     * 
     * @return void
     */
    public function __call($method, $arguments)
    {
        if (!method_exists($this, $method)) {
            throw new \BadMethodCallException(sprintf("Method %s() does not exist.", $method));
        }
    }
}