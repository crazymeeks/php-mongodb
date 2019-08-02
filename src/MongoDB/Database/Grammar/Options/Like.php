<?php

/*
 * This file is part of the Nucleus package.
 *
 * (c) Jefferson Claud <jefferson.claud@nuworks.ph>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleus\Databases\Grammar\Options;

class Like
{


    /**
     * Case insensitivity to match upper and lower cases.
     * 
     * @var const
     */
    const INSENSITIVE = 'i';

    /**
     * Multiline match
     * 
     * @var const
     */
    const MULTILINE = 'm';

    /**
     * Indicates that the like query
     * should match character in any position
     * of the string.
     * 
     * Equivalent to SQL's like '%char%'
     * 
     * @var const
     */
    const COMPARE_ANY = 'any';

    /**
     * Indicates that the like query
     * should match strings at the beginning
     * 
     * Equivalent to SQL's like 'jan%'
     * 
     * @var const
     */
    const COMPARE_START = 'compare_start';

    /**
     * Indicates that the like query
     * should match strings at the end
     * 
     * Equivalent to SQL's like '%fferson'
     * 
     * @var const
     */
    const COMPARE_END = 'compare_end';
}