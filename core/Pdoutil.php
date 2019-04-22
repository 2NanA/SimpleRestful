<?php

namespace Restful\Core;

/**
 *
 */
class Pdoutil
{
    const SELECT_MODE_ALL = '1';

    const SELECT_MODE_ONE = '2';

    protected static $_instance = null;
    protected $dbName = '';
    protected $dsn;
    protected $dbh;
    protected $config;
    
    /**
     *
     */
    private function __construct()
    {
        try {
            $this->config = parse_ini_file("dbconfig.ini");
            $dbHost = $this->config['host'];
            $dbName = $this->config['dbname'];
            $dbUser = $this->config['user'];
            $dbPasswd = $this->config['password'];
            $this->dsn = 'mysql:host='.$dbHost.';dbname='.$dbName;
            $this->dbh = new \PDO($this->dsn, $dbUser, $dbPasswd);
            $this->dbh->exec('SET character_set_connection=utf8, character_set_results=utf8, character_set_client=binary');
        } catch (\PDOException $e) {
            $this->outputError($e->getMessage());
        }
    }
    
    /**
     *
     */
    private function __clone()
    {
    }
    
    /**
     * Singleton instance
     *
     * @return Object
     */
    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    /**
     * select
     *
     * @param String $strSql
     * @param String $queryMode All/One
     * @param Array $params bind params
     * @param String $fetchMode
     * @return Array
     */
    public function select($strSql, $queryMode, $params = null, $fetchMode = null)
    {
        $stm = $this->prepareSql($strSql);
        if ($stm) {
            $stm->setFetchMode($fetchMode ?? \PDO::FETCH_ASSOC);
            if ($queryMode == self::SELECT_MODE_ALL) {
                $stm->execute();
                $result = $stm->fetchAll();
            } else {
                $stm->execute($params);
                $result = $stm->fetch();
            }
        } else {
            $result = null;
        }
        return $result;
    }
    
    /**
     * Update
     *
     * @param String $table
     * @param Array $data
     * @param Int $id primary
     * @return Int
     */
    public function update($table, $data, $id)
    {
        $now = date('Y-m-d H:i:s');
        if (empty($data['LastModifiedDate'])) {
            $data['LastModifiedDate'] = $now;
        }
        $this->checkFields($table, $data);
        $columnholders = '';
        foreach ($data as $key => $value) {
            $columnholders .= $key . ' = :' . $key . ',';
        }
        $columnholders = substr($columnholders, 0, strlen($columnholders)-1);
        $sql = "UPDATE `{$table}` SET {$columnholders} WHERE id = :id";
        $stm = $this->dbh->prepare($sql);
        
        foreach ($data as $key => $value) {
            $stm->bindValue(':' . $key, $value);
        }

        $stm->bindValue(':id', $id);
        $result = $stm->execute();
        $this->getPDOError();
        return $result;
    }
    
    /**
     * Insert 插入
     *
     * @param String $table 表名
     * @param Array $data 字段与值
     * @param Boolean $debug
     * @return Int
     */
    public function insert($table, $data)
    {
        $now = date('Y-m-d H:i:s');
        if (empty($data['CreatedDate'])) {
            $data['CreatedDate'] = $now;
        }
        $this->checkFields($table, $data);
        $columnCount = count(array_keys($data));
        

        /* ? placehoders */
        // $placeholders = str_repeat('?,', $columnCount);
        // $input = array_values($data);
        // $placeholders = substr($placeholders, 0, strlen($placeholders)-1);

        /* : placehoders */
        $placeholders = implode(', :', array_keys($data));
        $placeholders = ':' . $placeholders;

        /* prepare */
        $strSql = "INSERT INTO `$table` (`".implode('`,`', array_keys($data))."`) VALUES (" . $placeholders . ")";
        $stm = $this->dbh->prepare($strSql);

        /* ? placehoders */
        //    for ($i=1; $i <= $columnCount; $i++) {
        //         $stm->bindParam($i, $input[$i-1]);
        //    }

        /* : placehoders */
        foreach ($data as $key => $value) {
            $stm->bindValue(':' . $key, $value);
        }

        $result = $stm->execute();
        $this->getPDOError();
        return $this->getLastInsertId();
    }
    
    /**
     * Replace cover
     *
     * @param String $table
     * @param Array $data
     * @param Boolean $debug
     * @return Int
     */
    public function replace($table, $data)
    {
        $this->checkFields($table, $data);
        $strSql = "REPLACE INTO `$table`(`".implode('`,`', array_keys($data))."`) VALUES ('".implode("','", $data)."')";
        if ($debug === true) {
            $this->debug($strSql);
        }
        $result = $this->dbh->exec($strSql);
        $this->getPDOError();
        return $result;
    }
    
    /**
     * Delete
     *
     * @param String $table
     * @param Int $id
     * @return Int
     */
    public function delete($table, $id = '')
    {
        if (empty($id)) {
            $this->outputError("Please specify a primary key");
        } else {
            $strSql = "DELETE FROM `$table` WHERE id = {$id}";
            $result = $this->dbh->exec($strSql);
            $this->getPDOError();
            return $result;
        }
    }
    
    /**
     * execSql
     *
     * @param String $strSql
     * @param Boolean $debug
     * @return Int
     */
    public function execSql($strSql)
    {
        $result = $this->dbh->exec($strSql);
        $this->getPDOError();
        return $result;
    }
    
    /**
     * get max
     *
     * @param string $table
     * @param string $fieldName
     * @param string $where
     */
    public function getMaxValue($table, $fieldName, $where = '')
    {
        $strSql = "SELECT MAX(".$fieldName.") AS MAX_VALUE FROM $table";
        if ($where != '') {
            $strSql .= " WHERE $where";
        }
        if ($debug === true) {
            $this->debug($strSql);
        }
        $arrTemp = $this->query($strSql, 'Row');
        $maxValue = $arrTemp["MAX_VALUE"];
        if ($maxValue == "" || $maxValue == null) {
            $maxValue = 0;
        }
        return $maxValue;
    }
    
    /**
     * Gets the number of columns specified
     *
     * @param string $table
     * @param string $fieldName
     * @param string $where
     * @return int
     */
    public function getCount($table, $fieldName, $where = '')
    {
        $strSql = "SELECT COUNT($fieldName) AS NUM FROM $table";
        if ($where != '') {
            $strSql .= " WHERE $where";
        }
        if ($debug === true) {
            $this->debug($strSql);
        }
        $arrTemp = $this->query($strSql, 'Row');
        return $arrTemp['NUM'];
    }
    
    /**
     * prepare sql
     *
     * @param string $sql
     * @return int
     */
    public function prepareSql($sql = '')
    {
        return $this->dbh->prepare($sql);
    }

    /**
     * execute prepare sql
     *
     * @param string $presql
     * @return int
     */
    public function execute($presql)
    {
        return $this->dbh->execute($presql);
    }
 
    /**
     * pdo Property settings
     */
    public function setAttribute($p, $d)
    {
        $this->dbh->setAttribute($p, $d);
    }
 
    /**
     * beginTransaction 
     */
    public function beginTransaction()
    {
        $this->dbh->beginTransaction();
    }
    
    /**
     * commit 
     */
    public function commit()
    {
        $this->dbh->commit();
    }
    
    /**
     * rollback 
     */
    public function rollback()
    {
        $this->dbh->rollback();
    }
    
    /**
     *  Returns the last inserted id.
     *  @return string
     */
    public function getLastInsertId()
    {
        return $this->dbh->lastInsertId();
    }

    /**
     * transaction 
     * @param array $arraySql
     * @return Boolean
     */
    public function execTransaction($arraySql)
    {
        $retval = 1;
        $this->beginTransaction();
        foreach ($arraySql as $strSql) {
            if ($this->execSql($strSql) == 0) {
                $retval = 0;
            }
        }
        if ($retval == 0) {
            $this->rollback();
            return false;
        } else {
            $this->commit();
            return true;
        }
    }
 
    /**
     * Check whether the specified field exists in the specified data table
     *
     * @param String $table
     * @param Array $arrayFields
     * @return Void
     */
    private function checkFields($table, $arrayFields)
    {
        $fields = $this->getFields($table);
        foreach ($arrayFields as $key => $value) {
            if (!in_array($key, $fields)) {
                $this->outputError("Unknown column `$key` in field list.");
            }
        }
    }

    private function getFields($table)
    {
        $fields = array();
        $recordset = $this->dbh->query("SHOW COLUMNS FROM $table");
        $this->getPDOError();
        $recordset->setFetchMode(\PDO::FETCH_ASSOC);
        $result = $recordset->fetchAll();
        foreach ($result as $rows) {
            $fields[] = $rows['Field'];
        }
        return $fields;
    }
    
    /**
     * Capture PDO error information
     */
    private function getPDOError()
    {
        if ($this->dbh->errorCode() != '00000') {
            $arrayError = $this->dbh->errorInfo();
            $this->outputError($arrayError[2]);
        }
    }
    
    /**
     * debug
     *
     * @param mixed $debuginfo
     */
    private function debug($debuginfo)
    {
        dump($debuginfo); /* need symphony dumper */
        exit();
    }
    
    /**
     * @param String $strErrMsg
     */
    private function outputError($strErrMsg)
    {
        throw new \Exception('MySQL Error: '.$strErrMsg);
    }
    
    /**
     * destruct
     */
    public function destruct()
    {
        $this->dbh = null;
    }

    /**
     * exec
     */
    public function exec($sql='')
    {
        return $this->dbh->exec($sql);
    }
}
