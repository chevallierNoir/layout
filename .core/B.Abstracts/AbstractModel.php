<?php
/**
 * Created by PhpStorm.
 * User: Alejandro Suarez
 * Date: 07/06/14
 * Time: 10:32
 */

//TODO: Chargement automatique des classes
abstract  class AbstractModel {
    static $className = '';
    private $data = null;
    function __construct($entry = array()){
        $this->data = $entry;
    }
    function getAttribute( $attribute){
        return $this->data[$attribute];
    }
    function setAttribute($attribute, $value){
        $this->data[$attribute] = $value;
    }
    function get($className){
        //TODO: $where
        $models = array();
        $query=null;
        $db = Application::getDataBase();
        $cls = $this->getClassName();
        if(!is_null($className)){
            $id = "id" . $cls;
            $query = $db->select($className,"*",[$id=>$this->getAttribute('id')]);
        }
        if(static::notifyDBException($db)==true){
            return $models;
        }
        foreach ( $query as $result) {
            $model = new $className($result);
            array_push($models,$model);
        }
        return $models;
    }
    static function getStructure(){
        $db = Application::getDataBase();
        $cls = static::getClassName();
        $data = array();
        foreach ($db->query("DESCRIBE ".$cls) as $field){
            $data[ $field[ 'Field' ] ] = "";
        }
        if(static::notifyDBException($db)){
            return $data;
        }
        return $data;
    }
    static function  readOperation($entries = null){
        $db = Application::getDataBase();
        $cls = static::getClassName();
        $models = array();
        if(is_null($entries)){
            $query = $db->select( $cls,"*");
        }else{
            $query = $db->select( $cls,"*",$entries);
        }
        if(static::notifyDBException($db)){
            return $models;
        }
        foreach ( $query as $result) {
            $model = new $cls($result);
            array_push($models,$model);
        }
        return $models;
    }
    static function saveOperation($entries=array()){
        $toInsert = array();
        $toUpdate = array();
        $saved = array();
        $models=array();
        foreach($entries as $entry){
            if(isset($entry['_new'])){
               if($entry['_new'] == true)
                unset($entry['_new']);
                array_push($toInsert,$entry);
            }else{
                unset($entry['_new']);
                array_push($toUpdate,$entry);
            }
        }
        $db = Application::getDataBase();
        $cls = static::getClassName();
        if(!empty($toInsert)){
            $saved = $db->insert($cls,$toInsert);
        }
        if(static::notifyDBException($db)){
            return $models;
        }
        foreach ( $saved as $result) {
            $model = new $cls($result);
            array_push($models,$model);
        }
        $models=array_merge($models,static::updateOperation($toUpdate));
        return $models;

    }
    static function getClassName()
    {
        return static::$className;
    }
     static function updateOperation($entries){
         $updated = array();
         $models = array();
         $db = Application::getDataBase();
         $cls = static::getClassName();
         foreach($entries as $entry){
             $idEntry = $entry['id'];
             unset($entry['id']);
             array_push($updated, $db->update($cls,$entry,["id" => $idEntry ]));

         }
         if(static::notifyDBException($db)){
             return $models;
         }
         foreach ( $updated as $result) {
             $model = new $cls($result);
             array_push($models,$model);
         }
         return $models;
     }
     static function deleteOperation($entries){
         $db = Application::getDataBase();
         $cls = static::getClassName();
         foreach($entries as $entry){
             $idEntry = $entry['id'];
             unset($entry['id']);
             $db->delete($cls,["id" => $idEntry ]);
         }
         return static::notifyDBException($db);
     }

    /**
     * @param medoo $db
     * @return bool
     */
    static function notifyDBException($db){
        if(is_null($db->error()[2])){
            return false;
        }
        echo $db->last_query();
        echo ' Something happened! '.$db->error()[2];
        return true;
    }
}
