<?php

namespace Nucleus\Databases\Contracts\Resolver;

use Nucleus\Databases\Contracts\ConnectionInterface;

interface ConnectionResolverInterface
{
 
    
    function constructConnectionString(): string;
    function resolve(): ConnectionInterface;
}