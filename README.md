
SqlTool
======

基于PHP编写的查看线上线下数据库更新修改的状态，同时生成相应的sql语句。

## 安装

使用 Composer 安装:

```
composer require "dyike/sqlTool:~1.0"
```

## 使用

### 查看线上线下数据库的表，新增表、新增字段、修改字段的sql

```php

use Dyike\Sql\Sql;
use Dyike\JudgeSql\JudgeSql;

$tableOffLine = new Sql('hostOnLine', 'dbName', 'dbUser', 'dbPassword', 'dbPort');
$tableOnLine = new Sql('hostOFFLine', 'dbName', 'dbUser', 'dbPassword', 'dbPort');

//线上的数据表
$tOnLine = $tableOnLine->getTables();
//线下的数据表
$tOffLine = $tableOffLine->getTables();
//新增的数据
$getTablesToAdd = $judgeSql->getTableToAdd($tOnLine, $tOffLine);
foreach ($getTablesToAdd as $value) {
    //获取新增表的创建SQL
    $sql = $tableOffLine->getCreateTableSql($value);
    print_r($sql[0]);
    echo "<br>";
}


foreach ($tOnLine as $value) {
    //获取线下表的字段
    $fieldsOffLine = $tableOffLine->getFields($value);
    //获取线上表的字段
    $fieldsOnLine = $tableOnLine->getFields($value);
    //新增字段的SQL
    $addFieldSql = $judgeSql->toAddFieldSql($fieldsOffLine, $fieldsOnLine, $value);
    //修改更新字段的SQL
    $updateFieldSql = $judgeSql->toUpdateFields($fieldsOffLine, $fieldsOnLine, $value);
}

```

