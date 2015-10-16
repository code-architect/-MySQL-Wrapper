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
    function query($query){

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
     *
     */
    function __destruct(){
        $this->_mysql->close();
    }


}