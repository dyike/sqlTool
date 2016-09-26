<?php

use PHPUnit\Framework\TestCase;
use Dyike\JudgeSql\JudgeSql;

class JudgeSqlTest extends TestCase
{
    public function testGetTableToAdd()
    {
        $tablesOnLine = ['test', 'ityike'];
        $tablesOffLine = ['test', 'file'];
        $diff = array_diff($tablesOffLine, $tablesOnLine);
        print_r($diff);
        $this->assertNotEmpty($diff);
        // $this->assertNotEmpty($tablesOffLine);
    }

    public function testToAddFieldSql()
    {

    }
}