<?php

namespace Nucleus\Databases\Grammar;

use Nucleus\Databases\Support\ConnectedClientTrait;
use Nucleus\Databases\Grammar\Options\QueryOptions;
use Nucleus\Exceptions\Database\Grammar\QueryGrammarException;

class QueryGrammar
{

    use ConnectedClientTrait, QueryOptions;

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
     * Constructor
     *
     * @param string $collection Collection name where the query will run
     * 
     */
    public function __construct(string $collection)
    {
        $this->collection = $collection;

        $this->splStackWhere = new \SplStack();
        $this->splQueryOptions = new \SplStack();
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
        $args = array_filter($args);

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
     * @return void
     */
    private function redefineArgs(&$args)
    {
        if (count($args) === 2) {
            $args[] = null;
        }

        if (count($args) === 1) {
            $args[] = null;
            $args[] = null;
        }
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
        
        $args = array_filter($args);
        if (count($args) <= 0) {
            throw new \InvalidArgumentException();
        }

        if (!$args[0] instanceof \Closure) {
            if (count($args) < 2) {
                throw new \InvalidArgumentException("Arguments passed to " . __METHOD__ . " must at least 2 or instance of \Closure.");
            }

            $args[1] = ['$ne' => $args[1]];

        }
        
        return $this->pushQueryToTheirStack($args);
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

    

    public function print_where()
    {
        echo "<pre>";
        print_r($this->queryStack);exit;
    }
}