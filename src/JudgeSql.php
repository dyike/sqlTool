<?php

namespace Dyike\JudgeSql;

class JudgeSql
{
    // private $tablesOnline;
    // private $tablesOffLine;


    /**
     * 判断线上需要增加的表
     * @param  [array] $tableOnline  线上的表
     * @param  [array] $tableOffLine 线下的表
     * @return [array] 计算两个表的差集
     */
    public function getTableToAdd($tablesOnline, $tablesOffLine)
    {
        $res = array_diff($tableOffLine, $tableOnline);
        return $res;
    }

}