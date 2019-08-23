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
     * The first collection defined in join
     *
     * @var string
     */
    protected $join_source_collection = null;

    /**
     * Construct join query and push it to the stack
     * 
     * @param string $source_collection Collection where foreign key of $join_collection exist
     * @param string $join_collection The collection to join
     *
     * @return $this
     */
    public function join(string $source_collection, string $join_collection)
    {
        list($source_collection, $localField) = $this->extractCollectionAndKey($source_collection);
        
        list($join_collection, $foreignField) = $this->extractCollectionAndKey($join_collection);

        $source_collection = $this->setJoinSourceCollection($source_collection);

        if ($source_collection) {
            $localField = "$source_collection.$localField";
            
        }

        $lookup = [
            '$lookup' => [
                'from' => $join_collection,
                'foreignField' => $foreignField,
                'localField'   => "$localField",
                'as'           => $join_collection
            ],
        ];
        

        $this->splJoinStack[] = $lookup;
        $this->splJoinStack[] = ['$unwind' => "$$join_collection"];
         
        return $this;

    }
    
    /**
     * Set join source collection name
     *
     * @param string $source_collection
     * 
     * @return mixed
     */
    private function setJoinSourceCollection(string $source_collection)
    {
        
        if (!$this->join_source_collection) {
            $this->join_source_collection = $source_collection;
            return null;
        }
        
        return $source_collection;
    }

    /**
     * Get the main collection name set for join query
     * then use this in aggregate() later
     *
     * @return string
     */
    private function getJoinSourceCollection()
    {
        return $this->join_source_collection;
    }

    /**
     * Get constructed join stack.
     * Always return the latest
     *
     * @return array
     */
    private function getJoinStack()
    {
        return $this->splJoinStack;
    }

    public function print_join_stack()
    {
        
        $client = $this->getConnectedClient();
        $result = $client->{$this->getJoinSourceCollection()}->aggregate($this->splJoinStack)->toArray();

        // echo "<pre>";
        // print_r($result);exit;
        echo "<pre>";
        print_r(json_encode($this->getJoinStack()));exit;
    }
}