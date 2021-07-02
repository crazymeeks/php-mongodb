<?php

namespace Crazymeeks\MongoDB\Connection\Resolver\Exceptions;

class ConnectionException extends \Exception
{


    public static function connectionFailed(string $server)
    {
        return new static(sprintf("Could not connect to mongodb server at %s", $server));
    }

    public static function connectNotInitialized()
    {
        return new static("Error: Connection to mongodb has not been initialized.");
    }
}