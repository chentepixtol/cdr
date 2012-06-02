<?php

namespace Altic\CdrBundle\Mongo;

/**
 *
 * @author chente
 *
 */
class Db
{

    protected $connection;

    protected $databaseName;

    protected $db;

    public function __construct(\Mongo $connection, $databaseName){
        $this->connection = $connection;
        $this->databaseName = $databaseName;
        $this->db = $this->connection->selectDB($this->databaseName);
    }

    /**
     * @return \MongoDb
     */
    public function getDB(){
        return $this->db;
    }


}