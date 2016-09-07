<?php

use PHPUnit\Framework\TestCase;
use Dyike\JudgeSql\JudgeSql;

class JudgeSqlTest extends TestCase
{
    public function testGetTableToAdd(array $tablesOnLine, array $tablesOffLine)
    {
        $this->assertNotEmpty($tablesOnLine);
        $this->assertNotEmpty($tablesOffLine);
    }
}