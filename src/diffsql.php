<?php

class Sql {

    private $db;
    private $host;
    private $dbName;
    private $dbUser;
    private $dbPass;

    //查询操作
    public function searchTables($db, $host, $dbName, $dbUser, $dbPass, $port)
    {
        echo "<br/>";
        $dsn = "$db:host=$host;dbname=$dbName;port=$port";
        $pdo = new PDO($dsn, $dbUser, $dbPass);
        echo "$host" . "数据库连接成功<br/>";
        $query = "show tables";
        $res = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
        $pdo = null;
        $table = array_column($res, 'Tables_in_patient');
        return $table;
    }

    public function searchFields($table, $db, $host, $dbName, $dbUser, $dbPass, $port)
    {
        echo "<br/>";
        $dsn = "$db:host=$host;dbname=$dbName;port=$port";
        $pdo = new PDO($dsn, $dbUser, $dbPass);
        $res = $pdo->query("show full fields from $table")->fetchAll(PDO::FETCH_ASSOC);
        $pdo = null;
        return $res;
    }

    /**
     * 获取新增表的创建sql
     * @param  [type] $table  [description]
     * @param  [type] $db     [description]
     * @param  [type] $host   [description]
     * @param  [type] $dbName [description]
     * @param  [type] $dbUser [description]
     * @param  [type] $dbPass [description]
     * @param  [type] $port   [description]
     * @return [type]         [description]
     */
    public function getTablesSql($table, $db, $host, $dbName, $dbUser, $dbPass, $port)
    {
        foreach ($table as $value) {
            echo "<br/>";
            $dsn = "$db:host=$host;dbname=$dbName;port=$port";
            $pdo = new PDO($dsn, $dbUser, $dbPass);
            $res = $pdo->query("show create table $value")->fetchAll(PDO::FETCH_ASSOC);
            $pdo = null;
            print_r($res[0]['Table']."表"."<br/>");
            echo "<br/>";
            print_r($res[0]['Create Table']."<br/>");
            echo "<br/>";
            echo "#=====================================";
            echo "<br/>";
        }
    }



    /**
     * 获取数据表的字段名称
     * @param  [type] $tableName [description]
     * @param  [type] $db        [description]
     * @param  [type] $host      [description]
     * @param  [type] $dbName    [description]
     * @param  [type] $dbUser    [description]
     * @param  [type] $dbPass    [description]
     * @param  [type] $port      [description]
     * @return [type]            [description]
     */
    public function getTableFields($tableName, $db, $host, $dbName, $dbUser, $dbPass, $port)
    {
        $dsn = "$db:host=$host;dbname=$dbName;port=$port";
        $sql = new PDO($dsn, $dbUser, $dbPass);
        // $q = $sql->prepare("SHOW FULL COLUMNS FROM $tableName");
        $q = $sql->prepare("DESCRIBE $tableName");
        $q->execute();
        $table_fields = $q->fetchAll(PDO::FETCH_COLUMN);
        return $table_fields;
    }
    /**
     * 查看缺失的数据表
     * @param  [type] $table1  线下的表名
     * @param  [type] $table2  线上的表名
     * @return [type]         需要新增的表
     */
    public function getTableLost($table1, $table2)
    {
        $res = array_diff($table1, $table2);
        echo "<br/>";
        echo "新增的表有如下：<br/>";
        return $res;
    }


    //提供详细字段参数，生成sql语句
    public function generateTableSql($tableLost)
    {
        foreach ($tableLost as $v) {
            $field = $this->searchFields($v, 'mysql', '192.168.200.252', 'patient', 'php_biz', 'drink_coffee', '3307');
            //print_r($field);
            $num = count($field);
            echo "#=============================<br/>";
            echo "$v 表字段个数为：$num 个" . "<br/>";
            echo "<br/>";
            $sqlHead = "CREATE TABLE `$v` (" . "<br/>";
            $sqlBody = '';
            for ($i = 0; $i < $num; $i++) {
                $sql = '';
                if ($field[$i]['Null'] == 'NO') {
                    $sql = 'NOT NULL';
                } elseif ($field[$i]['Null'] == 'YES') {
                    if ($field[$i]['Default'] === NULL) {
                        $sql = 'DEFAULT NULL';
                    } elseif ($field[$i]['Default'] === '') {
                        $sql = 'DEFAULT'."''";
                    } else {
                        $sql = 'DEFAULT'."'".$field[$i]['Default']."'";
                    }
                }
                $sqlBody .= "`" . $field[$i]['Field'] . "`" . ' ' . $field[$i]['Type'] . ' ' . $sql .' ' .'COMMENT' . "'" . $field[$i]['Comment'] . "'" . ',' . "<br/>";
            }
            $sqlBody .= $this->getTableKeyInfo($v, 'mysql', '192.168.33.10', 'patient', 'username', 'password', 'port');
            $sqlBody = rtrim($sqlBody, ",<br/>");
            $sqlFoot =  "<br/>".") "."ENGINE=InnoDB DEFAULT CHARSET=utf8;";
            $sql = $sqlHead . $sqlBody . $sqlFoot;
            echo $sql;
            echo "<br/>";
        }
    }


    /**
     * 获取表的primary key 和 索引
     * @param  [type] $tableName [description]
     * @param  [type] $db        [description]
     * @param  [type] $host      [description]
     * @param  [type] $dbName    [description]
     * @param  [type] $dbUser    [description]
     * @param  [type] $dbPass    [description]
     * @param  [type] $port      [description]
     * @return [type]            [description]
     */
    public function getTableKeyInfo($tableName, $db, $host, $dbName, $dbUser, $dbPass, $port)
    {
        $fields = $this->searchFields($tableName, 'mysql', '192.168.33.10', 'patient', 'username', 'password', 'port');
        $sqlKey = '';
        foreach ($fields as $key => $value) {
            if ($value['Key'] == 'PRI') {
                $sqlKey .= "PRIMARY KEY (`".$fields[$key]['Field']."`),"."<br/>";
            } elseif ($value['Key'] == 'MUL') {
                $sqlKey .= "KEY `idx_".$fields[$key]['Field']."` (`".$fields[$key]['Field']."`),"."<br/>";
            }
        }
        $sqlKey = rtrim($sqlKey, ",<br/>");
        return $sqlKey;
    }

    /**
     * 根据线上的表，去对比线下的表的字段
     * @param  [type] $table1 线下
     * @param  [type] $table2 线上
     * @return [type]         [description]
     */
    public function addFields($table1, $table2)
    {
        //print_r($table2);exit;
        foreach ($table2 as $v) {
            $fieldsOffLine = $this->getTableFields($v, 'mysql', '192.168.33.10', 'patient', 'username', 'password', 'port');
            $fieldsOnline = $this->getTableFields($v, 'mysql', '192.168.33.10', 'patient', 'username', 'password', 'port');
            // print_r($fieldsOnline);
            // print_r($fieldsOffLine);
            $diffFields = array_diff($fieldsOffLine, $fieldsOnline);
            if (!empty($diffFields)) {
                $keysArr = array_keys($diffFields);
                $valueArr = array_values($diffFields);
                $fieldsStr = implode($valueArr, ",");
                echo "<font size='6' color='red'>".$v ."表新增了字段:"."</font>".$fieldsStr."<br/>";

                // print_r($keysArr);
                $diff = $this->searchFields($v, 'mysql', '192.168.33.10', 'patient', 'username', 'password', 'port');
                //var_dump($diff);
                $sqlAdd = '';
                foreach ($keysArr as $val) {
                    if ($diff[$val]['Null'] == "YES") {
                        if ($diff[$val]['Default'] === NULL) {
                            $isNull = 'DEFAULT NULL';
                        } elseif ($diff[$val]['Default'] === '') {
                            $isNull = 'DEFAULT'."''";
                        } else {
                            $isNull = 'DEFAULT'."'".$diff[$val]['Default']."'";
                        }
                    } elseif ($diff[$val]['Null'] == 'NO') {
                        $isNull = 'NOT NULL';
                    }

                    $sqlAdd .= " ADD ".$diff[$val]['Field'].' '.$diff[$val]['Type'] .' '.$isNull." COMMENT"."'".$diff[$val]['Comment']."',";
                }

                $sqlAdd = rtrim($sqlAdd, ",");

                $sql = "ALTER TABLE ".$v .$sqlAdd.";";
                echo $sql;
                echo "<br/>";
                echo "#===========================================";
                echo "<br/>";
            }
        }
    }

    //根据线上的表去对比线下的表更新的信息
    public function changeFields($table2)
    {
        unset($table2[array_search('patients_0801', $table2)]);
        unset($table2[array_search('drugs_0510', $table2)]);

        foreach ($table2 as $value) {
            //print_r($value);
            $fieldsOffLine = $this->searchFields($value, 'mysql', '192.168.33.10', 'patient', 'username', 'password', 'port');
            $fieldsOnline = $this->searchFields($value, 'mysql', '192.168.33.10', 'patient', 'username', 'password', 'port');
            $countOnLine = count($fieldsOnline);
            $countOffLine = count($fieldsOffLine);
            if ($countOnLine == $countOffLine) {
                $changeSql = '';

                foreach ($fieldsOffLine as $k => $v) {

                    if ($v['Null'] == "YES") {
                        if ($v['Default'] === NULL) {
                            $isNull = 'DEFAULT NULL';
                        } elseif ($v['Default'] === '') {
                            $isNull = 'DEFAULT'."''";
                        } else {
                            $isNull = 'DEFAULT'."'".$v['Default']."'";
                        }
                    } elseif ($v['Null'] == 'NO') {
                        $isNull = 'NOT NULL';
                    }
                    switch (($v['Field'] == $fieldsOnline[$k]['Field'])? 1 : 0) {
                        case '1':
                        switch (($v['Type'] == $fieldsOnline[$k]['Type'])? 1 : 0) {
                            case '1':
                            switch (($v['Null'] === $fieldsOnline[$k]['Null'])? 1 : 0) {
                                case '1':
                                switch (($v['Default'] === $fieldsOnline[$k]['Default'])? 1 : 0) {
                                    case '1':
                                    switch (($v['Comment'] == $fieldsOnline[$k]['Comment'])? 1 : 0) {
                                        case '1':
                                            break;
                                        case '0':
                                            $changeSql .= 'ALTER TABLE '.$value. ' CHANGE '. $v['Field'] .' '. $v['Type'].' '. $isNull. ' COMMENT '."'".$v['Comment'] ."'".";<br/>";
                                            break;
                                    }
                                        break;
                                    case '0':
                                        $changeSql .= 'ALTER TABLE '.$value. ' CHANGE '. $v['Field'] .' '. $v['Type'].' '. $isNull. ' COMMENT '."'".$v['Comment'] ."'".";<br/>";
                                        break;
                                }
                                    break;
                                case '0':
                                    $changeSql .= 'ALTER TABLE '.$value. ' CHANGE '. $v['Field'] .' '. $v['Type'].' '. $isNull. ' COMMENT '."'".$v['Comment'] ."'".";<br/>";
                                    break;
                            }
                                break;
                            case '0':
                                $changeSql .= 'ALTER TABLE '.$value. ' CHANGE '. $v['Field'] .' '. $v['Type'].' '. $isNull. ' COMMENT '."'".$v['Comment'] ."'".";<br/>";
                                break;
                        }
                            break;
                        case '0':
                            $changeSql .= 'ALTER TABLE '.$value. ' CHANGE '. $v['Field'] .' '. $v['Type'].' '. $isNull. ' COMMENT '."'".$v['Comment'] ."'".";<br/>";
                            break;
                    }
                }

            } else {
                echo "<font size='5' color='red'>".$value."表中新增了字段"."请调用addFields()"."</font>";
                echo "<br/>";
                echo "#=======================================";
                echo "<br/>";
            }
            echo $value."表中修改的字段：";
            echo "<br/>";
            echo $changeSql;
            echo "<br/>";
            echo "#=======================================";

        }

    }

}

$sql = new Sql();
$table1 = $sql->searchTables('mysql',  'mysql', '192.168.33.10', 'patient', 'username', 'password', 'port');
$table2 = $sql->searchTables('mysql',  'mysql', '192.168.33.10', 'patient', 'username', 'password', 'port');


//先比较缺少的的那些表
$tableLost = $sql->getTableLost($table1, $table2);
echo "<pre>";
//print_r($tableLost);

echo "<br/>";
echo "#=========================================================";
echo "<br/>";


//新增数据表sql语句
$tableSql = $sql->getTablesSql($tableLost, 'mysql', '192.168.33.10', 'patient', 'username', 'password', 'port');


//表中新增的字段
$addFields = $sql->addFields($table1, $table2);

//表中修改的字段

$changeFields = $sql->changeFields($table2);


