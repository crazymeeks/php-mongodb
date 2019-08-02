<?php

namespace Nucleus\Exceptions\Database\Grammar;

use Nucleus\Exceptions\NucleusException;

class QueryGrammarException extends NucleusException
{

    public function __construct($message = '', $code = 500, \Exception $previous = null)
    {
        $message = $message ?? "Invalid or unsupported query grammar.";

        parent::__construct($message, $code, $previous);
    }

    /**
     * Unsupported order by
     *
     * @param mixed $orderby
     * 
     * @return static
     */
    public static function unsupportedOrderBy($orderby)
    {
        return new static(sprintf("The {%s} order by is unsupported.", $orderby));
    }

    /**
     * Invalid argument passed to the method of \Nucleus\Databases\Grammar\QueryGrammar::update()
     *
     * @return static
     */
    public static function updateQueryInvalidArgument()
    {
        return new static(sprintf("Invalid parameter passed to \Nucleus\Databases\Grammar\QueryGrammar::update()"));
    }
}