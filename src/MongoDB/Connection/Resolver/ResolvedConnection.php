<?php

namespace Crazymeeks\MongoDB\Connection\Resolver;

class ResolvedConnection
{


    public function setDefaultDatabase()
    {
        return $this;
    }

    public function connect()
    {
        return null;
    }
}