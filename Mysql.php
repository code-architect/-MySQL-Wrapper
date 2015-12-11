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
     * (1)
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
    public function get($tableName, $numRows = NULL){
        $this->_query = "SELECT * FROM $tableName";
        $stmt = $this->_buildQuery($numRows);
        $stmt->execute();

        $results = $this->bindResults($stmt);
        return $results;
    }

    /**
     * Insert into the database(6)
     */
    function insert($tableName, $insertData){
        $this->_query = "INSERT into $tableName";
        $stmt = $this->_buildQuery(NULL, $insertData);
        $stmt->execute();

        if($stmt->affected_rows){
            return true;
        }
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
     * Where function for explicit search (5)
     */
    function where($whereProp, $whereValue)
    {
        $this->_where[$whereProp] = $whereValue;
    }



    /**
     * Build the query dynamically (4)
     */
    protected function _buildQuery($numRows = NULL, $tableData = false)
    {
        $hasTableData = null;

        if(gettype($tableData) === 'array'){
            $hasTableData = true;
        }

        // We need to ask if the user did call the where method
        if( !empty($this->_where)) {
            $keys = array_keys($this->_where);
            $where_prop = $keys[0];
            $where_value = $this->_where[$where_prop];

            // If data was passed, filter through and create the sql query accordingly.
            if ($hasTableData) {
                $i = 1;
                foreach ($tableData as $prop => $value) {
                    echo $prop ." -> ". $value. '<br>';
                }

            } else {
                // no table data was passed. It's a select statement.
                $this->_paramTypeList = $this->_determineType($where_value);
                $this->_query .= " WHERE " . $where_prop . " = ?";
            }
        }

        //determine if it's insert query (7)
        if($hasTableData){
            $pos = strpos($this->_query, 'INSERT');
        }

        if($pos == false){
            // is insert statement
            $keys = array_keys($tableData);
            $values = array_values($tableData);
            $num = count($keys);

            // wrap the values in quotes
            foreach($values as $key => $val){
                $values[$key] = "'{$val}'";
                $this->_paramTypeList .= $this->_determineType($val);
            }
            $this->_query .= '('. implode($keys, ',') .')';
            $this->_query .= ' VALUES(';

            while($num !== 0){
                ($num !== 1) ? $this->_query .= '?, ' : $this->_query .= '?)';
                $num--;
            }

        }

        // If the number of rows are given by the user
        if(isset($numRows)){
            $this->_query .= " LIMIT ". (int)$numRows;
        }

        $stmt = $this->prepareQuery();

        // Bind the parameters
        if($hasTableData){
            $args = [];
            $args[] = $this->_paramTypeList;
            foreach($tableData as $prop => $val){
                $args[] = &$tableData[$prop];
            }
            call_user_func_array( array($stmt, 'bind_param'),$args );
        }
        else if($this->_where){
            $stmt->bind_param($this->_paramTypeList, $where_value);
        }
        return $stmt;
    }


    /**
     * Determine given value (4.1)
     */
    protected function _determineType($item){
        switch( gettype($item) ){
            case 'string' :
                $param_type = 's';
                break;

            case 'integer' :
                $param_type = 'i';
                break;

            case 'blob' :
                $param_type = 'b';
                break;

            case 'double' :
                $param_type = 'd';
                break;
        }

        return $param_type;
    }



    /**
     * Prepare the query (2)
     */
    protected function prepareQuery()
    {
        if(!$stmt = $this->_mysql->prepare($this->_query)){
            trigger_error('Problem preparing query', E_USER_ERROR);
        }
        return $stmt;
    }



    /**
     * Binding the results (3)
     */
    protected function bindResults($stmt)
    {
        $parameters = [];
        $results = [];

        $meta = $stmt->result_metadata();
        while($field = $meta->fetch_field()){
            $parameters[] = &$row[$field->name];
        }

        // In mysqli you have to do something like $stmt->bind_results();
        // but using call_user_func_array you can't only do bind_results
        // so the way you do it is pass an array then the object in the first one and method in another like here
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