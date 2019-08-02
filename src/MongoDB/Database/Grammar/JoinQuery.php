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

trait JoinQuery
{

    /**
     * Construct leftJoin query and push it to the stack
     * 
     * @param string $joined_collection Collection to be joined
     * @param string $col_one Collection name where $joined_collection will be join. Format 'mycol._id'
     * @param string $col_two Collection name where $col_one will be compared. Format 'mycol2.my_col_id'
     *
     * @return $this
     */
    public function leftJoin(string $joined_collection, string $col_one, string $col_two)
    {

    }

}