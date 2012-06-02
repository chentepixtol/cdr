<?php


namespace Altic\CdrBundle\Mongo;


class Cdr
{
    protected $db;


    protected $collection;


    public function __construct(Db $db){
        $this->db = $db;
        $this->collection = $db->getDB()->selectCollection("cdr");
    }

    public function getCollection(){
        return $this->collection;
    }

}