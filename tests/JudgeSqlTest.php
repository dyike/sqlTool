<?php

use PHPUnit\Framework\TestCase;
use Dyike\JudgeSql;

class JudgeSqlTest extends TestCase
{
    public function testGetTableToAdd()
    {
        $tablesOnLine = ['test', 'ityike'];
        $tablesOffLine = ['test', 'file'];
        $diff = array_diff($tablesOffLine, $tablesOnLine);
        $this->assertNotEmpty($diff);
        // $this->assertNotEmpty($tablesOffLine);
    }

    public function testToAddFieldSql()
    {

    }
}