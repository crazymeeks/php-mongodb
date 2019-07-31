<?php

namespace Nucleus\MongoDB\Database;


/*
 * This file is part of the Nucleus package.
 *
 * (c) Jefferson Claud <jefferson.claud@nuworks.ph>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Setup
{

    private static $configurations = [];

    /**
     * Required configuration
     *
     * @var array
     */
    private static $requiredConfigs = [
        'host',
        'database',
    ];

    /**
     * Create database configuration
     *
     * @param array{host:string|array,database:string,port:string|null,options:array{username:string,password:string}|[]} $configurations
     * 
     * @return void
     */
    public static function createConfiguration(array $configurations)
    {

        $flipped = array_map(function($value, $key){
            return $key;
        }, $configurations, array_keys($configurations));
        
        list($host, $database) = self::$requiredConfigs;
        
        if (!in_array($host, $flipped)) {
            throw new \Exception(sprintf("The {%s} key is required when setting database configuration.", $host));
        }

        if (!in_array($database, $flipped)) {
            throw new \Exception(sprintf("The {%s} key is required when setting database configuration.", $database));
        }
        
        self::$configurations = $configurations;
    }

    /**
     * Get database configuration
     *
     * @return array
     */
    public static function getConfiguration()
    {
        return self::$configurations;
    }
}