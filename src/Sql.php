<?php

namespace Dyike\Sqltool;

class Sql
{
    private $dbHost;
    private $dbName;
    private $dbUser;
    private $dbPass;
    private $pdo;

    public function __construct($dbHost, $dbName, $dbUser, $dbPass, $dbPort)
    {
        $this->dbHost = $dbHost;
        $this->dbName = $dbName;
        $this->dbUser = $dbUser;
        $this->dbPass = $dbPass;
        $this->dbPort = $dbPort;
        $pdo = new \PDO("mysql:host=$this->dbHost; dbname=$this->dbName; port=$this->dbPort", $this->dbUser, $this->dbPass);
        $this->pdo = $pdo;
    }

    /**
     * 获取所有的表明
     * @return array
     */
    public function getTables()
    {
        $res = $this->pdo->query('SHOW TABLES')->fetchAll(\PDO::FETCH_ASSOC);
        $column = 'Tables_in_' . $this->dbName;
        $tables = array_column($res, $column);
        return $tables;
    }

    /**
     * 获取表的字段的全部信息
     * @param  [string] $tableName 表名
     * @return [array]  字段名
     */
    public function getFields($tableName)
    {
        $fields = $this->pdo->query("show full fields from $tableName")->fetchAll(\PDO::FETCH_ASSOC);
        return $fields;
    }

    /**
     * 只获取表的字段名
     * @param  [string] $tableName
     * @return [array]
     */
    public function getTableFields($tableName)
    {
        $fields = $this->pdo->prepare("DESCRIBE $tableName");
        $fields->execute();
        $fields = $fields->fetchAll(\PDO::FETCH_COLUMN);
        return $fields;
    }

    /**
     * 获取创建数据表的sql
     * @param  [string] $tableName 表名
     * @return [array]  创建数据表的sql
     */
    public function getCreateTableSql($tableName)
    {
        $res = $this->pdo->query("show create table $tableName")->fetchAll(\PDO::FETCH_ASSOC);
        $sql = array_column($res, 'Create Table');
        $tableSql = ['Table' => $tableName, 'Type' => 'Create Table', 'SQL' => $sql[0]];
        return $tableSql;
    }


}