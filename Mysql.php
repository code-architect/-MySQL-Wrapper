<?php

class MysqlDB{

    protected $_mysql;
    protected $_where = [];
    protected $_query;
    protected $_paramTypeList;

    public function __construct($host, $username, $password, $db){
        $this->_mysql = new mysqli($host, $username, $password, $db) or die('There is a problem with the connection');
    }


    /**
     *
     */
    function query($query)
    {
        $this->_query = filter_var($query, FILTER_SANITIZE_STRING);
        $stmt = $this->prepareQuery();
        $stmt->execute();
        $results = $this->bindResults($stmt);
        return $results;
    }

    /**
     *
     */
    function get($tableName, $numRows = NULL){

    }

    /**
     *
     */
    function insert($tableName, $insertData){

    }

    /**
     *
     */
    function update($tableName, $tableData){

    }

    /**
     *
     */
    function delete($tableName){

    }

    /**
     *
     */
    function where($whereProp, $whereValue){

    }


    /**
     * Prepare the query
     */
    protected function prepareQuery()
    {
        if(!$stmt = $this->_mysql->prepare($this->_query)){
            trigger_error('Problem preparing query', E_USER_ERROR);
        }
        return $stmt;
    }


    /**
     * Binding the results
     */
    protected function bindResults($stmt)
    {
        $parameters = [];
        $results = [];

        $meta = $stmt->result_metadata();
        while($field = $meta->fetch_field()){
            $parameters[] = &$row[$field->name];
        }

        call_user_func_array( array($stmt, 'bind_result'), $parameters );

        while($stmt->fetch()){
            $x = [];

            foreach($row as $key => $val){
                $x[$key] = $val;

            }
            $results[] = $x;
        }
        return $results;
    }


    /**
     *
     */
    function __destruct(){
        $this->_mysql->close();
    }


}