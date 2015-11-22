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
    public function get($tableName, $numRows = NULL){
        $this->_query = "SELECT * FROM $tableName";
        $stmt = $this->_buildQuery($numRows);
        $stmt->execute();

        $results = $this->bindResults($stmt);
        return $results;
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
     * Where function for explicit search
     */
    function where($whereProp, $whereValue)
    {
        $this->_where[$whereProp] = $whereValue;
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
     * Build the query dynamically
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

            // If update data was passed, filter through and create the sql query accordingly.
            if ($hasTableData) {
                foreach ($tableData as $prop => $value) {

                }

            } else {
                // no table data was passed. It's a select statement.
                $this->_paramTypeList = $this->_determineType($where_value);
                $this->_query .= " WHERE " . $where_prop . " = ?";
            }
        }

        // If the number of rows are given by the user
        if(isset($numRows)){
            $this->_query .= " LIMIT ". (int)$numRows;
        }

        $stmt = $this->prepareQuery();

        // Bind the parameters
        if($this->_where){
            $stmt->bind_param($this->_paramTypeList, $where_value);
        }
        return $stmt;
    }


    /**
     * Determine given value
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