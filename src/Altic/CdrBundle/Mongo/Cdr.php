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

    /**
     *
     * @param string $field
     * @throws \Exception
     */
    public function getCountByField($field){
        $cursor = $this->mapReduce("cdr", "function(){
            emit(this.{$field}, {cont: 1});
        }", "function(key, values){
                var obj = {cont: 0}
                for(i in values){
                    obj.cont += values[i].cont;
                }
                return obj;
        }", "groupByField");

        $result = array();
        foreach ($cursor as $doc){
            $result[$doc['_id']] = $doc['value']['cont'] ?: 0;
        }

        return $result;
    }

    /**
     *
     * @param string $field
     * @throws \Exception
     */
    public function getCountByFields($fieldOne, $fieldTwo){
        $cursor = $this->mapReduce("cdr", "function(){
            var obj = {key1: this.{$fieldOne}, key2: this.{$fieldTwo}};
            emit(obj, {cont: 1});
        }", "function(key, values){
            var obj = {cont: 0}
            for(i in values){
                obj.cont += values[i].cont;
            }
            return obj;
        }", "groupByFields");

        $result = array();
        foreach ($cursor as $doc){
            $result[$doc['_id']['key1']][$doc['_id']['key2']] = $doc['value']['cont'];
        }

        return $result;
    }

    /**
     *
     * @param string $collection
     * @param string $mapFunction
     * @param string $reduceFunction
     * @param string $outCollection
     * @return array
     * @throws \Exception
     */
    private function mapReduce($collection, $mapFunction, $reduceFunction, $outCollection){
        $result = $this->db->getDB()->command(array(
            "mapreduce" => $collection,
            "map" => new \MongoCode($mapFunction),
            "reduce" => new \MongoCode($reduceFunction),
            "out" => array("replace" => $outCollection))
        );

        if( isset($result['ok']) && $result['ok'] ){
            return $this->db->getDB()->selectCollection($result['result'])->find();
        }else {
            throw new \Exception(json_encode($result));
        }
    }
}