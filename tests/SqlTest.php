<?php

use PHPUnit\Framework\TestCase;
use Dyike\Sql\Sql;
//use Dyike\JudgeSql\JudgeSql;

class SqlTest extends TestCase
{
    public function testGetTables()
    {
        $tables = new Sql('192.168.33.10', 'patient', 'root', '123456yf', '3306');
        $res = $tables->getTables();
        $this->assertNotEmpty($res);
        $firstTableName = $res[0];
        return $firstTableName;
    }

    /**
     * @depends testGetTables
     */
    public function testGetFields($tableName)
    {
        $tables = new Sql('192.168.33.10', 'patient', 'root', '123456yf', '3306');
        $this->assertNotEmpty($tables->getFields($tableName));
    }

    /**
     * @depends testGetTables
     */
    public function testGetCreateTableSql($tableName)
    {
        $tables = new Sql('192.168.33.10', 'patient', 'root', '123456yf', '3306');
        $this->assertNotEmpty($tables->getCreateTableSql($tableName));
    }

}
?>