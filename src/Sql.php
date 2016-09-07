<?php

namespace Dyike\Sql;

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
     * 获取表的字段
     * @param  [string] $tableName 表名
     * @return [array]  字段名
     */
    public function getFields($tableName)
    {
        $fields = $this->pdo->query("show full fields from $tableName")->fetchAll(\PDO::FETCH_ASSOC);
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
        $tableSql = array_column($res, 'Create Table');
        return $tableSql;
    }

}

// $tableOffLine = new Sql('192.168.200.252', 'patient', 'php_biz', 'drink_coffee', '3307');
// $tableOnLine = new Sql('192.168.33.10', 'patient', 'root', '123456yf', '3306');
// echo "<pre>";
// $tOnline = $tableOnLine->getTables();
// $tOffLine = $tableOffLine->getTables();


// $fields = $tableOffLine->getFields('Blood');
// print_r($fields);

// $sql = $tableOffLine->getCreateTableSql("Blood");
// print_r($sql);

// //$tableToAdd = $tableOffLine->getTableToAdd($tOnline, $tOffLine);

// $table = new JudgeSql();
// $tableToAdd->getTableToAdd($tOnline, $tOffLine);
// print_r($tableToAdd);








