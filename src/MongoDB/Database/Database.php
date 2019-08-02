<?php

/*
 * This file is part of the Nucleus package.
 *
 * (c) Jefferson Claud <jefferson.claud@nuworks.ph>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleus\Databases;

use Nucleus\Databases\Contracts\Resolver\ConnectionResolverInterface;

class Database
{


    private static $instance = null;


    private function __construct()
    {

    }

    private function __clone()
    {

    }

    private function __wakeup()
    {

    }


    /**
     * Initialize a connection to the database
     *
     * @param \Nucleus\Databases\Contracts\Resolver\ConnectionResolverInterface $connectionResolver
     * 
     * @return \Nucleus\Databases\Contracts\ConnectionInterface
     */
    public static function connect(ConnectionResolverInterface $connectionResolver)
    {
        
        if (self::$instance === null) {
            self::$instance = $connectionResolver->resolve();
        }

        return self::$instance;
    }

}