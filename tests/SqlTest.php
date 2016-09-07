<?php

use PHPUnit\Framework\TestCase;
use Dyike\Sql\Sql;
//use Dyike\JudgeSql\JudgeSql;

class SqlTest extends TestCase
{
    public function testGetTables()
    {
        $getTables = new Sql('192.168.33.10', 'patient', 'root', '123456yf', '3306');
    }


}
?>