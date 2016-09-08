<?php

namespace Dyike\Sqltool;

class JudgeSql
{
    /**
     * 判断线上需要增加的表
     * @param  [array] $tableOnline  线上的表
     * @param  [array] $tableOffLine 线下的表
     * @return [array] 计算两个表的差集
     */
    public function getTableToAdd($tablesOnLine, $tablesOffLine)
    {
        $res = array_diff($tablesOffLine, $tablesOnLine);
        return $res;
    }


    /**
     * 新增字段的sql
     * 判断依据：取线上的已有的数据表去对比线下的数据表的字段是否相同
     * @param  [type] $tableOnline  线上的数据表
     * @param  [type] $tableOffLine 线下的数据表
     * @return [type]
     */
    public function toAddFieldSql($tableOffLineFields, $tableOnLineFields, $tableName)
    {
        $offLineFields = $this->tableFieldsTrans($tableOffLineFields);
        $onLineFields = $this->tableFieldsTrans($tableOnLineFields);
        $fieldsDiff = array_diff($offLineFields, $onLineFields);

        if (!empty($fieldsDiff)) {
            $keysArr = array_keys($fieldsDiff);
            $valueArr = array_values($fieldsDiff);
            $fieldsStr = implode($valueArr, ",");

            $fieldsAdd = '';
            foreach ($keysArr as $val) {
                if ($tableOffLineFields[$val]['Null'] == "YES") {
                    if ($tableOffLineFields[$val]['Default'] === NULL) {
                        $isNull = 'DEFAULT NULL';
                    } elseif ($tableOffLineFields[$val]['Default'] === '') {
                        $isNull = 'DEFAULT'."''";
                    } else {
                        $isNull = 'DEFAULT'."'".$tableOffLineFields[$val]['Default']."'";
                    }
                } elseif ($tableOffLineFields[$val]['Null'] == 'NO') {
                    $isNull = 'NOT NULL';
                }

                $fieldsAdd .= " ADD ".$tableOffLineFields[$val]['Field'].' '.$tableOffLineFields[$val]['Type'] .' '.$isNull." COMMENT"."'".$tableOffLineFields[$val]['Comment']."',";
            }

            $fieldsAdd = rtrim($fieldsAdd, ",");
            $sql = "ALTER TABLE ".$tableName .$fieldsAdd.";";
            $type = "Add Fields";
            $fieldsAddSql = ['Table' => $tableName, 'Type' => $type, 'Fields' => $fieldsStr, 'SQL' => $sql];
            return $fieldsAddSql;
        }

    }

    /**
     * 获取修改字段的sql
     * @param  [type] $tableOffLineFields [线下的数据表字段]
     * @param  [type] $tableOnLineFields  [线上的数据表字段]
     * @param  [type] $tableName          [比较的数据表]
     * @return [type]
     */
    public function toUpdateFields($tableOffLineFields, $tableOnLineFields, $tableName)
    {
        $updateFieldSql = '';

        foreach ($tableOffLineFields as $k => $v) {
            if ($v['Null'] == 'YES') {
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
            $countOnLine = count($tableOnLineFields);
            $countOffLine = count($tableOffLineFields);

            if ($countOnLine == $countOffLine) {
                if ($v['Field'] == $tableOnLineFields[$k]['Field']) {
                    if ($v['Type'] == $tableOnLineFields[$k]['Type']) {
                        if ($v['Null'] === $tableOnLineFields[$k]['Null']) {
                            if ($v['Default'] === $tableOnLineFields[$k]['Default']) {
                                if ($v['Comment'] != $tableOnLineFields[$k]['Comment']) {
                                    $updateFieldSql .= 'ALTER TABLE '.$tableName. ' CHANGE '. $v['Field'] .' '. $v['Type'].' '. $isNull. ' COMMENT '."'".$v['Comment'] ."'".";<br/>";
                                }
                            } else {
                                $updateFieldSql .= 'ALTER TABLE '.$tableName. ' CHANGE '. $v['Field'] .' '. $v['Type'].' '. $isNull. ' COMMENT '."'".$v['Comment'] ."'".";<br/>";
                            }
                        } else {
                            $updateFieldSql .= 'ALTER TABLE '.$tableName. ' CHANGE '. $v['Field'] .' '. $v['Type'].' '. $isNull. ' COMMENT '."'".$v['Comment'] ."'".";<br/>";
                        }
                    } else {
                        $updateFieldSql .= 'ALTER TABLE '.$tableName. ' CHANGE '. $v['Field'] .' '. $v['Type'].' '. $isNull. ' COMMENT '."'".$v['Comment'] ."'".";<br/>";
                    }
                } else {
                    $updateFieldSql .= 'ALTER TABLE '.$tableName. ' CHANGE '. $v['Field'] .' '. $v['Type'].' '. $isNull. ' COMMENT '."'".$v['Comment'] ."'".";<br/>";
                }
            }

        }
        $type = "Update Fields";
        $FieldsUpdateSql = ['Table' => $tableName, 'Type' => $type, 'SQL' => $updateFieldSql];
        return $FieldsUpdateSql;

    }

    /**
     * 只获取字段的转换
     * @param  [type] $tableFields [字段名]
     * @return [array]
     */
    public function tableFieldsTrans($tableFields)
    {
        $fields = [];
        foreach ($tableFields as $value) {
            array_push($fields, $value['Field']);
        }
        return $fields;
    }

}