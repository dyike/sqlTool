<?php

use Dyike\Sql\Sql;
use Dyike\JudgeSql\JudgeSql;

class SqlTest PHPUnit_Framework_TestCase
{
    public function test()
    {
        $tableOffLine = new Sql('192.168.200.252', 'patient', 'php_biz', 'drink_coffee', '3307');
        $tableOnLine = new Sql('192.168.33.10', 'patient', 'root', '123456yf', '3306');
        echo "<pre>";
        $tOnline = $tableOnLine->getTables();
        $tOffLine = $tableOffLine->getTables();
        print_r($tOffLine);
    }

}