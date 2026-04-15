<?php
defined('BASEPATH') or exit('No direct script access allowed');
date_default_timezone_set('Asia/Kolkata');
class GlobalModel extends CI_Model
{

    public function _construct()
    {
        parent::_construct();
        date_default_timezone_set('Asia/Kolkata');
    }
    //// only insert function
    public function onlyinsert($transaction, $tblname)
    {
        $this->db->insert($tblname, $transaction);
        $currentId = $this->db->insert_id();
        if ($currentId > 0) {
            $res = $currentId;
        }
        else {
            $res = 'false';
        }
        return $res;
    }
    //BASIC CRUD operations queries for all views Starts
    public function addWithoutCode($transaction, $tblname)
    {
        $this->db->insert($tblname, $transaction);
        $nowdate = date('Y-m-d H:i:s');
        $currentId = $this->db->insert_id();
        $this->db->query("UPDATE `" . $tblname . "` SET  `addDate` = '" . $nowdate . "' WHERE `id` = '" . $currentId . "'");

        if ($this->db->affected_rows() > 0) {
            $res = 'true';
        }
        else {
            $res = 'false';
        }
        return $res;
    }

    public function addWithoutYear($transaction, $tblname, $initial)
    {
        $this->db->insert($tblname, $transaction);
        //echo $this->db->last_query();
        $nowdate = date('Y-m-d H:i:s');
        //Get Last inseted Id from Table
        // $currentId=$this->db->select('id')->order_by('id','desc')->limit(1)->get($tblname)->row('id');
        $currentId = $this->db->insert_id();

        if (in_array($tblname, ["ordermaster", "vendorordermaster"])) {
            $len = strlen($currentId);
            if ($len == 1) {
                $leadingZeros = "000";
            }
            else if ($len == 2) {
                $leadingZeros = "00";
            }
            else if ($len == 3) {
                $leadingZeros = "0";
            }
            else {
                $leadingZeros = "";
            }
            $hashCode = $initial . "_" . $leadingZeros . $currentId;
        }
        else {
            //Update Code with update query
            $hashCode = $initial . "_" . $currentId;
        }

        $this->db->query("UPDATE `" . $tblname . "` SET `code` = '" . $hashCode . "', `addDate` = '" . $nowdate . "' WHERE `id` = '" . $currentId . "'");

        if ($this->db->affected_rows() > 0) {

            $res = $hashCode;
        }
        else {
            $res = 'false';
        }
        return $res;
    }

    public function addNew($transaction, $tblname, $initial)
    {

        $this->db->insert($tblname, $transaction);
        $nowdate = date('Y-m-d H:i:s');
        //Get Last inseted Id from Table
        // $currentId=$this->db->select('id')->order_by('id','desc')->limit(1)->get($tblname)->row('id');
        $currentId = $this->db->insert_id();

        //Update Code with update query
        $hashCode = $initial . date("y") . "_" . $currentId;

        $this->db->query("UPDATE `" . $tblname . "` SET `code` = '" . $hashCode . "', `addDate` = '" . $nowdate . "' WHERE `id` = '" . $currentId . "'");

        if ($this->db->affected_rows() > 0) {

            $res = $hashCode;
        }
        else {
            $res = 'false';
        }
        return $res;
    }

    public function addNewWithImage($transaction, $tblname, $initial)
    {
        $this->db->insert($tblname, $transaction);
        $nowdate = date('Y-m-d h:i:s');
        //Get Last inseted Id from Table
        $currentId = $this->db->insert_id();

        $res = array();
        //Update currency Code with update query
        $hashCode = $initial . date("y") . "_" . $currentId;

        $photopath = 'Rocktech_' . $hashCode . '.jpg';

        $this->db->query("UPDATE `" . $tblname . "` SET `code` = '" . $hashCode . "', `image` = '" . $photopath . "' ,`addDate` = '" . $nowdate . "' WHERE `id` = '" . $currentId . "'");

        if ($this->db->affected_rows() > 0) {
            $res['code'] = $hashCode;
            $res['photo'] = $photopath;
            $res['status'] = 'true';
        }
        else {
            $res = 'false';
        }
        return $res;
    }

    public function addSubTable($transaction, $tblname)
    {
        $nowdate = date('Y-m-d h:i:s');
        $this->db->insert($tblname, $transaction);
        $currentId = $this->db->insert_id();
        $this->db->query("UPDATE `" . $tblname . "` SET `addDate` = '" . $nowdate . "' WHERE `id` = '" . $currentId . "'");
        if ($this->db->affected_rows() > 0) {
            $res = 'true';
        }
        else {
            $res = 'false';
        }
        return $res;
    }
    // function addTestimonialWithImage($transaction, $tblname , $initial, $preName) {
    //     $this->db->insert($tblname, $transaction);

    //     //Get Last inseted Id from Table
    //     // $currentId=$this->db->select('id')->order_by('id','desc')->limit(1)->get($tblname)->row('id');
    //     $currentId = $this->db->insert_id();

    //     //Update Code with update query
    //     $hashCode = $initial.date("y")."_".$currentId;
    //     $imgPath = $preName.$hashCode.".jpg";
    //     $this->db->query("UPDATE `".$tblname."` SET `code` = '".$hashCode."', `addDate` = NOW(), `certificate` = '".$imgPath."' WHERE `id` = '".$currentId."'");

    //     if ($this->db->affected_rows() > 0) {

    //         $res = $imgPath;
    //     } else {
    //         $res = 'false';
    //     }
    //     return $res;
    // }
    public function activityAdd($log, $logtable, $logInitial)
    {
        // $totalRecords = $this->db->count_all($logtable);

        // if($totalRecords >=5000){
        // $this->load->dbutil();
        // $this->load->helper('file');
        // $delimiter = ",";
        // $newline = "\r\n";
        // $query = "SELECT * FROM `".$logtable."`";
        // $result = $this->db->query($query);
        // $data = $this->dbutil->csv_from_result($result, $delimiter, $newline);
        // $dbname='Activity-Log-Backup-'.date('Y-m-d').'.csv';
        // $save= 'activitylog/'.$dbname;
        // write_file($save,$data);
        //$this->db->empty_table($logtable);

        // }
        // else{
        $nowdate = date('Y-m-d h:i:s');
        $this->db->insert($logtable, $log);
        $currentActId = $this->db->insert_id();
        //Update Code with update query
        $hashActCode = $logInitial . date("y") . "_" . $currentActId;
        $this->db->query("UPDATE `" . $logtable . "` SET `code` = '" . $hashActCode . "', `date` = '" . $nowdate . "'    WHERE `id` = '" . $currentActId . "'");
        if ($this->db->affected_rows() > 0) {
            $res = 'true';
        }
        else {
            $res = 'false';
        }
        return $res;
    //}
    }

    //select data by creating view
    public function selectQuery($sel, $table, $cond = array(), $orderBy = array(), $join = array(), $joinType = array(), $like = array(), $limit = '', $offset = '', $groupByColumn = '', $extraCondition = "")
    {
        $this->db->select($sel, false);
        $this->db->from($table);
        foreach ($cond as $k => $v) {
            if ($v != "") {
                $this->db->where($k, $v);
            }
        }

        foreach ($orderBy as $key => $val) {
            $this->db->order_by($key, $val);
        }
        $lc = 0;
        foreach ($like as $k => $v) {
            $val = explode("~", $v);
            if ($val[0] != "") {
                if ($lc == 0) {
                    $this->db->like($k, $val[0], $val[1]);
                    $lc++;
                }
                else {
                    $this->db->or_like($k, $val[0], $val[1]);
                }
            }
        }
        foreach ($join as $key => $val) {
            if (!empty($joinType) && $joinType[$key] != "") {
                $this->db->join($key, $val, $joinType[$key]);
            }
            else {
                $this->db->join($key, $val);
            }
        }
        if ($extraCondition != "") {
            $this->db->where($extraCondition);
        }
        if ($limit != '') {
            $this->db->limit($limit, $offset);
        }

        $this->db->group_by($groupByColumn);
        $query = $this->db->get();
        //echo $this->db->last_query(); die;
        if (is_bool($query)) {
            return false;
        }
        else {
            if ($query->num_rows() > 0) {
                return $query;
            }
            else {
                return false;
            }
        }
    }

    public function selectData($tblname)
    {
        $query = $this->db->query("SELECT * FROM `" . $tblname . "` WHERE `isDelete` IS NULL OR `isDelete`='0'");
        return $query;
    }
    public function selectActiveData($tblname)
    {
        $query = $this->db->query("SELECT * FROM `" . $tblname . "` WHERE `isActive` = '1'");
        return $query;
    }

    public function selectDataExcludeDelete($tblname)
    {
        $query = $this->db->query("SELECT * FROM `" . $tblname . "`");
        return $query;
    }

    public function selectUnusedData($resultTable, $field1, $fromTable, $field2)
    {
        $query = $this->db->query("SELECT * FROM `" . $resultTable . "` WHERE `" . $field1 . "` NOT IN (SELECT `" . $field2 . "` FROM `" . $fromTable . "` WHERE isDelete IS NULL OR isDelete != '1')");
        return $query;
    }

    //Take recent data from table
    public function recentData($tblname)
    {
        $query = $this->db->query("SELECT * FROM `" . $tblname . "` WHERE ORDER BY code desc limit 10");
        return $query;
    }
    public function selectDataUser($tblname)
    {
        $query = $this->db->query("SELECT * FROM `" . $tblname . "` WHERE `isActive`='1' ");
        return $query;
    }
    //Get Data of Entity in Perticular entity category
    public function selectEntityInPerticularCategory($category)
    {
        $query = $this->db->query("SELECT * FROM `entities` WHERE  `entityCategory` = '" . $category . "' AND `isActive`='1' ");
        return $query;
    }

    //Get Data of Entity in Perticular entity category
    public function selectOfficeInPerticularCategory($category)
    {
        $query = $this->db->query("SELECT ofc1.* from `officemaster` ofc1 INNER JOIN `entities` ent ON ofc1.`entityCode` = ent.`code` WHERE ent.`entityCategory` = '" . $category . "' AND ofc1.`isActive`='1'");
        return $query;
    }

    //Get Data of Item in Perticular Item category
    public function selectItemInPerticularCategory($category)
    {
        $query = $this->db->query("SELECT * FROM `itemmaster` WHERE  `itemCategory` = '" . $category . "' AND `isActive`='1'");
        return $query;
    }

    //Get Data of Entity in Perticular entity category
    // function selectOfficeInPerticularCategory($category) {
    // $query = $this->db->query("SELECT ofc1.* , ofc2.`address`, ofc2.`pinCode`, ofc2.`place` , ofc2.`taluka`, ofc2.`district`, ofc2.`state`, ofc2.`country`, ofc3.`contactNo`, ofc3.`email` from `officemaster` ofc1 INNER JOIN `entities` ent ON ofc1.`entityCode` = ent.`code` INNER JOIN `ofcaddressmaster` ofc2 ON ofc1.`code`= ofc2.`ofcCode` INNER JOIN `ofccontactmaster` ofc3 ON ofc1.`code`= ofc3.`ofcCode` WHERE ent.`entityCategory` = '".$category."' AND ofc1.`isActive`='1'");
    // return $query;
    // }
    //Get Data of Office in Perticular entity category
    // function selectEntityInPerticularCategory($category) {
    // $query = $this->db->query("SELECT * FROM `entities` WHERE  `entityCategory` = '".$category."' AND `isActive`='1' ");
    // return $query;
    // }

    //*************Common Method for Get Data from third table based on other two table join *********************
    /*1. First Table name which contains our value
     2. First Table column which is use for compare with other table
     3. First Table column which contains our actual value
     4. Second Table which contains resultant value's code
     5. Second Table column which is use for compare with other table
     6. Alias of Second Table column
     6. Second Table column which contains our actual value's code
     7. Third Table which contains resultant value
     8. Third Table column which is use for compare with other table
     9. Alias of Third Table column
     10.Third Table column which contains our actual value
     11.Our Input, based on we are searching result from other tables.
     */
    public function selectCombineResult($tblname1, $tblname1FieldForCompare, $tblname1FieldForResult, $tblname2, $tblname2Field1, $tblname2FieldAlias1, $tblname2Field2, $tblname3, $tblname3Field1, $tblname3FieldAlias1, $tblname3Field2, $value)
    {
        $query = $this->db->query("SELECT `" . $tblname1 . "` . * ,  `" . $tblname2 . "`.`" . $tblname2Field1 . "` as $tblname2FieldAlias1 ,  `" . $tblname2 . "`.`" . $tblname2Field2 . "` ,  `" . $tblname3 . "`.`" . $tblname3Field1 . "` as $tblname3FieldAlias1 ,  `" . $tblname3 . "`.`" . $tblname3Field2 . "`
		FROM  `" . $tblname1 . "`
		INNER JOIN  `" . $tblname2 . "` ON  `" . $tblname1 . "`.`" . $tblname1FieldForCompare . "` =  `" . $tblname2 . "`.`" . $tblname2Field1 . "`
		RIGHT JOIN  `" . $tblname3 . "` ON  `" . $tblname3 . "`.`" . $tblname3Field1 . "` =  `" . $tblname2 . "`.`" . $tblname2Field2 . "`
		WHERE  `" . $tblname1 . "`.`" . $tblname1FieldForResult . "` =  '" . $value . "'");
        return $query;
    }

    //*************Common Method Get Data of Another table's perticular Field *********************
    public function selectPerticularFieldFromAnotherTable($tblname1, $tblname2, $fieldname1, $fieldname2)
    {
        $query = $this->db->query("SELECT `" . $tblname1 . "`.*, `" . $tblname2 . "`.`" . $fieldname2 . "` FROM `" . $tblname1 . "` INNER JOIN `" . $tblname2 . "` ON `" . $tblname1 . "`.`" . $fieldname1 . "` = `" . $tblname2 . "`.`code` WHERE `" . $tblname1 . "`.`isDelete` IS NULL OR `" . $tblname1 . "`.`isDelete`= 0 ");
        return $query;
    }
    //*************Common Method Get Data of Another table's perticular Field($fieldname3) with where condition *********************
    public function selectPerticularFieldFromAnotherTable1($tblname1, $tblname2, $fieldname1, $fieldname2, $fieldname3, $code)
    {
        $query = $this->db->query("SELECT `" . $tblname1 . "`.*, `" . $tblname2 . "`.`" . $fieldname3 . "` FROM `" . $tblname1 . "` INNER JOIN `" . $tblname2 . "` ON `" . $tblname1 . "`.`" . $fieldname1 . "` = `" . $tblname2 . "`.`" . $fieldname2 . "` WHERE `" . $tblname1 . "`.`" . $fieldname1 . "` = '" . $code . "' AND (`" . $tblname1 . "`.`isDelete` IS NULL OR `" . $tblname1 . "`.`isDelete`= 0 )");
        return $query;
    }

    //*************Common Method Get Data of Another 2 table's perticular Fields *********************
    //  pass last conditoin parameter when you have extra condition on table eg... ("t.code='".$empCode."' AND")
    public function selectDataFromAnotherTwoTables($tableName, $joinTableName1, $joinTableName2, $tableColumnName1, $joinTable1ColumnName, $tableColumnName2, $joinTable2ColumnName, $valueColumnName1, $valueColumnName2, $condition = '')
    {
        $this->db->select('t.*');
        $this->db->select('t1.' . $valueColumnName1 . ' as joinvalue1');
        $this->db->select('t2.' . $valueColumnName2 . ' as joinvalue2');
        $this->db->from($tableName . ' as t');
        $this->db->join($joinTableName1 . ' as t1', 't.' . $tableColumnName1 . '= t1.' . $joinTable1ColumnName);
        $this->db->join($joinTableName2 . ' as t2', 't.' . $tableColumnName2 . '= t2.' . $joinTable2ColumnName);
        $this->db->where($condition . 't.isDelete IS NULL OR t.isDelete=0');
        $query = $this->db->get();

        return $query;
    }

    //*************Common Method Get Data of Another table's perticular Code *********************

    public function getAnotherTableDataFromCode($tblname, $codeField, $code)
    {
        $query = $this->db->query("SELECT * FROM `" . $tblname . "` WHERE `" . $codeField . "` = '" . $code . "' AND `isActive`='1' ");
        return $query;
    }

    //*************Get Data of Another table's perticular Code *********************
    public function selectDataById($id, $tblname)
    {
        $query = $this->db->query("SELECT * FROM `" . $tblname . "` WHERE `code` = '" . $id . "'");

        return $query;
    }

    //*************Get Data of Another table's perticular Field *********************
    public function selectDataByField($field, $value, $tblname)
    {
        $query = $this->db->query("SELECT * FROM `" . $tblname . "` WHERE `" . $field . "` = '" . $value . "' AND (isDelete IS NULL OR isDelete=0)");

        return $query;
    }
    public function selectDataByFieldWithoutisDelete($field, $value, $tblname) //nitin

    {
        $query = $this->db->query("SELECT * FROM `" . $tblname . "` WHERE `" . $field . "` = '" . $value . "' ");

        return $query;
    }
    public function selectDataByFieldWithOrder($field, $value, $tblname)
    {
        $query = $this->db->query("SELECT * FROM `" . $tblname . "` WHERE `" . $field . "` = '" . $value . "' AND isDelete IS NULL OR isDelete=0 ORDER BY `id`");

        return $query;
    }

    public function selectActiveDataByField($field, $value, $tblname)
    {
        $query = $this->db->query("SELECT * FROM `" . $tblname . "` WHERE `" . $field . "` = '" . $value . "' AND `isActive`='1' ");

        return $query;
    }

    public function selectActiveDataByMultipleFields($conditionColumns, $conditionValues, $tblname, $cond = "", $select = "")
    {

        $fromCondtions = "";
        for ($e = 0; $e < sizeof($conditionColumns); $e++) {
            $fromCondtions .= " " . $conditionColumns[$e] . "  = '" . $conditionValues[$e] . "' AND";
        }
        $fromCondtions = substr($fromCondtions, 0, -3);
        if ($select == 1) {
            $select = "";
        }
        else {
            $select = 'SELECT * FROM';
        }

        $query = $this->db->query("" . $select . " " . $tblname . " WHERE " . $fromCondtions . $cond);

        return $query;
    }

    //*************Get Data or if not then empty signal of Another table's perticular Code and field *********************
    public function selectDataByIdWithEmpty($id, $tblname)
    {
        $query = $this->db->query("SELECT * FROM `" . $tblname . "` WHERE `code` = '" . $id . "'");
        if ($this->db->affected_rows() > 0) {
            $res = $query;
        }
        else {
            $res = null;
        }
        return $res;
    }

    public function selectDataByFieldWithEmpty($field, $value, $tblname)
    {
        $query = $this->db->query("SELECT * FROM `" . $tblname . "` WHERE `" . $field . "` = '" . $value . "'");
        if ($this->db->affected_rows() > 0) {
            $res = $query;
        }
        else {
            $res = null;
        }
        return $res;
    }

    public function selectDataByPND($field, $value, $tblname, $client)
    {
        $query = $this->db->query("SELECT * FROM `" . $tblname . "` WHERE `" . $field . "` = '" . $value . "' AND orderStatus='PND' AND clientCode='" . $client . "'");
        if ($this->db->affected_rows() > 0) {
            $res = $query;
        }
        else {
            $res = false;
        }
        return $res;
    }

    //*************Get Data of line table's distinct Fields by perticular field *********************
    public function selectDistinctDataByField($field, $value, $requiredColumns, $groupByValue, $tblname)
    {
        $columns = "";
        $limit = sizeof($requiredColumns);
        for ($t = 0; $t < sizeof($requiredColumns); $t++) {
            if ($t == ($limit - 1)) {
                $columns .= "`" . $requiredColumns[$t] . "`";
            }
            else {
                $columns .= "`" . $requiredColumns[$t] . "`,";
            }
        }
        $query = $this->db->query("SELECT DISTINCT " . $columns . " FROM `" . $tblname . "` WHERE `" . $field . "` = '" . $value . "' GROUP BY `" . $groupByValue . "`");
        return $query;
    }
    //*************Get distinct Data of perticular field  by tblname n cloumn name *******************//
    public function selectDistinctData($requiredColumn, $tblname)
    {
        $query = $this->db->query("SELECT DISTINCT `" . $requiredColumn . "` FROM `" . $tblname . "` ");
        return $query;
    }

    // function selectGallery($tblname) {
    //     $query = $this->db->query("SELECT * FROM `".$tblname."` WHERE `isActive`='1'" );
    //     return $query;
    // }
    public function edit($tblname, $id)
    {
        $query = $this->db->query("SELECT * FROM `" . $tblname . "` WHERE `code`='" . $id . "'");
        // $querym =  $this->db->query("Select SUM(projectNo) FROM projects");

        return $query;
    // return $querym;
    }
    public function doEdit($data, $tblname, $code)
    {
        $nowdate = date('Y-m-d H:i:s');
        $editDate = array('editDate' => $nowdate);
        $data = array_merge($data, $editDate);
        $this->db->where('code', $code);
        $this->db->update($tblname, $data);

        if ($this->db->affected_rows() > 0) {
            $res = 'true';
        }
        else {
            $res = 'false';
        }
        return $res;
    }
    //Update with perticualar field
    public function doEditWithField($data, $tblname, $field, $code)
    {
        $nowdate = date('Y-m-d H:i:s');
        $editDate = array('editDate' => $nowdate);
        $data = array_merge($data, $editDate);
        $this->db->where($field, $code);
        $this->db->update($tblname, $data);
        if ($this->db->affected_rows() > 0) {
            $res = 'true';
        }
        else {
            $res = 'false';
        }
        return $res;
    }

    public function delete($id, $tblname)
    {
        $nowdate = date('Y-m-d h:i:s');
        $this->db->query("UPDATE `" . $tblname . "` SET `isDelete` = '1', `isActive` = '0', `deleteDate` = '" . $nowdate . "'  WHERE `code` = '" . $id . "'");
        if ($this->db->affected_rows() > 0) {
            $res = 'true';
        }
        else {
            $res = 'false';
        }
        return $res;
    }

    public function deleteWithField($field, $id, $tblname)
    {
        $nowdate = date('Y-m-d h:i:s');
        $this->db->query("UPDATE `" . $tblname . "` SET `isDelete` = '1', `isActive` = '0', `deleteDate` = '" . $nowdate . "'  WHERE `" . $field . "` = '" . $id . "'");
        if ($this->db->affected_rows() > 0) {
            $res = 'true';
        }
        else {
            $res = 'false';
        }
        return $res;
    }

    public function deleteWithoutActive($id, $tblname)
    {
        $nowdate = date('Y-m-d h:i:s');
        $this->db->query("UPDATE `" . $tblname . "` SET `isDelete` = '1', `deleteDate` = '" . $nowdate . "'  WHERE `code` = '" . $id . "'");
        if ($this->db->affected_rows() > 0) {
            $res = 'true';
        }
        else {
            $res = 'false';
        }
        return $res;
    }
    //Delete record permanently using Code
    public function deleteForever($id, $tblname)
    {
        $res = $this->db->query("DELETE FROM `" . $tblname . "` WHERE `code` = '" . $id . "'");
        if ($this->db->affected_rows() > 0) {
            $res = 'true';
        }
        else {
            $res = 'false';
        }
        return $res;
    }

    //Delete record permanently using field
    public function deleteForeverFromField($field, $value, $tblname)
    {
        $res = $this->db->query("DELETE FROM `" . $tblname . "` WHERE `" . $field . "` = '" . $value . "'");
        if ($this->db->affected_rows() > 0) {
            $res = 'true';
        }
        else {
            $res = 'false';
        }
        return $res;
    }

    //Delete record permanently using Condition
    public function deleteForeverFromCondition($fieldCondition, $tblname)
    {
        $res = $this->db->query("DELETE FROM `" . $tblname . "` WHERE " . $fieldCondition);
        if ($this->db->affected_rows() > 0) {
            $res = 'true';
        }
        else {
            $res = 'false';
        }
        return $res;
    }

    // function deleteAllImgesUnderProject($nm, $tblname) {
    //     $res = $this->db->query("DELETE FROM `".$tblname."` WHERE `galleryTag` = '".$nm."'");
    //     if ($this->db->affected_rows() > 0) {
    //      $res = 'true';
    //     } else {
    //         $res = 'false';
    //     }
    //     return $res;
    // }
    //BASIC CRUD operation queries for all views Ends

    public function getAddressInfoFromPin($tblname, $pin)
    {
        $query = $this->db->query("SELECT * FROM `" . $tblname . "` WHERE  `pincode`= '" . $pin . "' ");
        return $query;
    }
    public function similarResultFind($tblname, $field, $value)
    {
        $query = $this->db->query("SELECT `" . $field . "` FROM `" . $tblname . "` WHERE `" . $field . "` LIKE '" . $value . "%' LIMIT 10");
        return $query;
    }

    public function similarResultAddress($tblname, $field, $value)
    {
        $query = $this->db->query("SELECT `" . $field . "`,code FROM `" . $tblname . "` WHERE `" . $field . "` LIKE '" . $value . "%' LIMIT 10");
        return $query;
    }

    public function similarResultFindWithDistinct($tblname, $field, $value)
    {
        $query = $this->db->query("SELECT DISTINCT `" . $field . "` FROM `" . $tblname . "` WHERE `" . $field . "` LIKE '" . $value . "%' LIMIT 10");
        return $query;
    }

    //for check duplicate record
    // public function checkDuplicateRecord($field, $value, $tblname)
    // {

    // $query = $this->db->query("SELECT * FROM `" . $tblname . "` WHERE `" . $field . "`='" . $value . "' AND (`isDelete` IS NULL OR `isDelete`='0')");

    // $count_row = $query->num_rows();

    // if ($count_row > 0) {
    // $stmt = true;
    // } else {
    // $stmt = false;
    // }
    // return $stmt;
    // }

    public function checkDuplicateRecord($field, $value, $tblname)
    {

        $query = $this->db->query("SELECT * FROM `" . $tblname . "` WHERE `" . $field . "`='" . $value . "' AND (`isDelete` IS NULL OR `isDelete`='0')");
        if ($query) {
            $count_row = $query->num_rows();
        }
        else {
            $count_row = 0;
        }

        if ($count_row > 0) {
            $stmt = true;
        }
        else {
            $stmt = false;
        }
        return $stmt;
    }

    //For get All Data related to field
    public function getAllDataFromField($tblname, $field, $value)
    {
        $query = $this->db->query("SELECT * FROM `" . $tblname . "` WHERE  `" . $field . "`= '" . $value . "' ");
        return $query;
    }

    ////////////////////// get item code list from vendor code//////////////////////

    // function getItemsFromVendor($tblname,$vendor)
    // {
    // $query = $this->db->query("SELECT DISTINCT `code` FROM `".$tblname."` WHERE vendorCode ='".$vendor."' " );
    // return $query->result();
    // }

    //////////////////////For RFQ ////////////////////////
    ////////////////////// get itemCode list from vendor //////////////////////

    public function getItemsFromVendor($vendor)
    {
        $query = $this->db->query("SELECT DISTINCT `itemCode` FROM `itemvendormaster` WHERE vendorCode ='" . $vendor . "' ");
        //$query = $this->db->query("SELECT DISTINCT `itemvendormaster`.`code` ,  `itemmaster`.`name` FROM `itemvendormaster` INNER JOIN `itemmaster` ON `itemvendormaster`.`code` = `itemmaster`.`code` WHERE `itemvendormaster`.vendorCode ='".$vendor."' " );
        return $query;
    // if ($this->db->affected_rows() > 0) {
    // $res = $query;
    // } else {
    // $res = 'null';
    // }
    // return $res;
    }

    ////////////////////// get Line Item list from PR  //////////////////////

    public function getLineItems($item, $from, $to)
    {
        //$query = $this->db->query("SELECT pr.code,pr.itemCode,pr.itemName,SUM(pr.itemQuantity) as quantity,pr.itemUom,pr.deliveryDate,pr.prDate, SUM(pr.subtotal) as subtotalsum, im.itemPrice FROM `prlineentries` pr INNER JOIN `itemmaster` im ON pr.itemCode = im.code WHERE pr.itemCode ='".$item."' AND pr.`prDate` BETWEEN '".$from."' AND '".$to."' GROUP BY pr.itemName" );
        $query = $this->db->query("SELECT pr.code,pr.itemCode,pr.itemName,pr.itemQuantity,pr.itemUom,pr.deliveryDate,pr.prDate,pr.subtotal, im.itemPrice FROM `prlineentries` pr INNER JOIN `itemmaster` im ON pr.itemCode = im.code INNER JOIN `prentries` pre ON pr.prCode = pre.code WHERE pre.releaseStatus = '2' AND pr.itemCode ='" . $item . "' AND pr.`prDate` BETWEEN '" . $from . "' AND '" . $to . "' ");
        //return $query;
        if ($this->db->affected_rows() > 0) {
            $res = $query;
        }
        else {
            $res = 'null';
        }
        return $res;
    }
    public function getLineItemsCombine($item, $from, $to)
    {
        //$query = $this->db->query("SELECT pr.code,pr.itemCode,pr.itemName,SUM(pr.itemQuantity) as quantity,pr.itemUom,pr.deliveryDate,pr.prDate, SUM(pr.subtotal) as subtotalsum, im.itemPrice FROM `prlineentries` pr INNER JOIN `itemmaster` im ON pr.itemCode = im.code WHERE pr.itemCode ='".$item."' AND pr.`prDate` BETWEEN '".$from."' AND '".$to."' GROUP BY pr.itemName" );
        $query = $this->db->query("SELECT pr.code,pr.itemCode,pr.itemName,SUM(pr.itemQuantity) as quantity,pr.itemUom,pr.deliveryDate,pr.prDate,SUM(pr.subtotal) as subtotalsum, im.itemPrice FROM `prlineentries` pr INNER JOIN `itemmaster` im ON pr.itemCode = im.code INNER JOIN `prentries` pre ON pr.prCode = pre.code WHERE pre.releaseStatus = '2' AND pr.itemCode ='" . $item . "' AND pr.`prDate` BETWEEN '" . $from . "' AND '" . $to . "' GROUP BY pr.itemName");
        //return $query;
        if ($this->db->affected_rows() > 0) {
            $res = $query;
        }
        else {
            $res = 'null';
        }
        return $res;
    }
    //**************************** Get Available stock from DB    **********************************//

    public function getAvailableStock($item, $storage)
    {
        $query = $this->db->query("SELECT stock FROM `stockinfo` WHERE itemCode ='" . $item . "' AND storageSection='" . $storage . "'");

        if ($this->db->affected_rows() > 0) {
            $res = $query;
        }
        else {
            $res = 'null';
        }
        return $res;
    }

    //**************************** Add stock in DB    **********************************//

    public function addStock($productCode, $stock)
    {
        //$activity = substr($activityCode,0,3);

        $currentStock = $this->db->query("SELECT `stock` FROM `stockinfo` WHERE productCode ='" . $productCode . "' ");
        foreach ($currentStock->result() as $current) {
            $currentStock = $current->stock;
        }
        $currentStock = floatval($currentStock);

        if ($this->db->affected_rows() > 0) {
            $newStock = $currentStock + $stock;
            $updateQuery = $this->db->query("UPDATE `stockinfo` SET `stock` = '" . $newStock . "' WHERE `productCode` ='" . $productCode . "' ");
            $updateQueryResult = $this->db->affected_rows();
            if ($updateQueryResult > 0) {
                $result = 'true';
            }
            else {
                $result = 'false';
            }
            return $result;
        }
        else {
            $insertQuery = $this->db->query("insert into `stockinfo` (productCode,stock) values ('" . $productCode . "','" . $stock . "')");
            //print_r($insertQuery);
            //$insertQueryResult = $this->db->affected_rows();
            if ($insertQuery == 1) {
                $result = $insertQuery;
            }
            else {
                $result = 'false';
            }
            return $result;
        }
    }

    public function stockChange($productCode, $stock, $action)
    {
        $currentStock = $this->db->query("SELECT `stock` FROM `stockinfo` WHERE productCode ='" . $productCode . "' ");
        foreach ($currentStock->result() as $current) {
            $currentStock = $current->stock;
        }
        $currentStock = floatval($currentStock);

        if ($this->db->affected_rows() > 0) {

            if ($action == 'add') {
                $newStock = $currentStock + $stock;
                $updateQuery = $this->db->query("UPDATE `stockinfo` SET `stock` = '" . $newStock . "' WHERE `productCode` ='" . $productCode . "' ");
                if ($this->db->affected_rows() > 0) {
                    $result = 'true';
                }
                else {
                    $result = 'false';
                }
            }
            else {

                $newStock = $currentStock - $stock;
                $updateQuery = $this->db->query("UPDATE `stockinfo` SET `stock` = '" . $newStock . "' WHERE `productCode` ='" . $productCode . "' ");

                if ($this->db->affected_rows() > 0) {
                    $result = 'true';
                }
                else {
                    $result = 'false';
                }
            }
        }
        else {
        }
    }

    //**************************** Subtract stock in DB    **********************************//

    public function consumeStock($activityCode, $itemCode, $stock, $storage, $itemUom, $itemPrice)
    {
        $activity = substr($activityCode, 0, 3);

        $currentStock = $this->db->query("SELECT `stock` FROM `stockinfo` WHERE itemCode ='" . $itemCode . "' AND storageSection='" . $storage . "'");

        foreach ($currentStock->result() as $current) {
            $currentStock = $current->stock;
        }
        $currentStock = floatval($currentStock);
        if ($this->db->affected_rows() > 0) {
            $newStock = $currentStock - $stock;
            $updateQuery = $this->db->query("UPDATE `stockinfo` SET `stock` = '" . $newStock . "' WHERE `itemCode` ='" . $itemCode . "'  AND storageSection='" . $storage . "'");

            if ($this->db->affected_rows() > 0) {

                $result = 'true';

                $currentLineStock = $this->db->query("SELECT `itemQuantity` FROM `stocklinesinfo` WHERE itemCode ='" . $itemCode . "' AND activityCode = '" . $activityCode . "' AND storageSection='" . $storage . "'");

                foreach ($currentLineStock->result() as $currentLine) {
                    $currentLineStock = $currentLine->itemQuantity;
                }
                $currentLineStock = floatval($currentLineStock);
                $newLineStock = $currentLineStock - $stock;
                $this->db->query("UPDATE `stocklinesinfo` SET `itemQuantity` = '" . $newLineStock . "' WHERE itemCode ='" . $itemCode . "' AND activityCode = '" . $activityCode . "' AND storageSection='" . $storage . "'");
            // if($activity == "INW")
            // {
            // $this->db->query("insert into `stocklinesinfo` (activityCode,itemCode,itemQuantity,itemUom,itemPrice,storageSection) values ('".$activityCode."','".$itemCode."','".$stock."','".$itemUom."','".$itemPrice."','".$storage."')");
            // }
            }
            else {
                $result = 'false';
            }
            return $result;
        }
        else {
            $res = null;
        }
        return $res;
    }

    public function checkExistAndInsertRecords($data, $tableName)
    {
        $query = $this->db->get_where($tableName, $data);
        if ($query->num_rows() > 0) {
            return false;
        }
        else {
            //$result=$this->addWithoutCode($data,$tableName);
            return true; //$result;
        }
    }

    public function getLastRecordInTable($tableName)
    {
        $query = $this->db->query("SELECT * FROM " . $tableName . " ORDER BY id DESC LIMIT 1");
        $result = $query->result();
        return $result;
    }

    public function getTableRecordCount($tableName)
    {
        return $this->db->count_all($tableName);
    }

    //#################Storage section wise stock ######################//
    public function stockFromStorage($tableName, $itemCode, $storage)
    {
        $query = $this->db->query("SELECT * FROM " . $tableName . " where itemCode='" . $itemCode . "' and storageSection = '" . $storage . "'");
        return $query;
    }

    //################ Get total holidays in between Contract dates ######################//

    public function getHolidays($from, $to)
    {
        $query = $this->db->query("SELECT count(`holidayDate`) as holidays FROM `holidaymaster` WHERE `holidayDate` BETWEEN '" . $from . "' AND '" . $to . "'");
        return $query;
    }

    //################ Get holidays by month year and sitecode ######################//
    public function getHolidaysbyMonthYearSite($month, $year, $siteCode)
    {
        $query = $this->db->query("SELECT hm.*, `hsm`.`siteCode`, `hsm`.`holidayCode` FROM `holidaysitemaster` hsm INNER JOIN holidaymaster hm ON hm.code=hsm.holidayCode WHERE `siteCode`='" . $siteCode . "' AND MONTH(hm.`holidayDate`) =" . $month . " AND YEAR(hm.`holidayDate`) =" . $year . "");
        return $query;
    }

    //################# Dependant result of another table #####################//
    /*
     1.Table name, where we have data for search.
     2.Common field name of both, mentioned in first table
     3.Value from which we want result.
     4.Resultant Table name.
     */
    public function dependantResult($tblname1, $commonField, $value, $tblname2)
    {
        $query = $this->db->query("SELECT `" . $tblname2 . "`.* FROM `" . $tblname1 . "` INNER JOIN `" . $tblname2 . "` ON `" . $tblname1 . "`.`" . $commonField . "` = `" . $tblname2 . "`.`code` WHERE `" . $tblname1 . "`.`code` = '" . $value . "' AND `" . $tblname1 . "`.`isActive` = '1'");
        return $query;
    }

    //############# Attendance #######################//

    public function getDates()
    {
        $distinctDate = $this->db->select('distinct(date)')->get_where('attendancedtransaction', array('flag' => 'N'));
        return $distinctDate;
    }

    public function getDailyAttData()
    {
        $result = $this->db->get_where('attendancedailytransaction', array('flag' => 'N'));
        return $result;
    }

    public function dailyTransaction($date)
    {
        $distinctCode = $this->db->select('distinct(machinecode)')->get_where('attendancedtransaction', array('flag' => 'N', 'date' => $date));
        foreach ($distinctCode->result() as $rowCode) {
            $minmaxData = $this->db->get_where('attendancedtransaction', array('machinecode' => $rowCode->machinecode, 'date' => $date));
            $inoutData = array();
            $count = 1;

            $employeeCode = $this->db->get_where('employeemaster', array('empToken' => $rowCode->machinecode));
            if ($this->db->affected_rows() > 0) {
                //print_r($minmaxData->result());
                foreach ($minmaxData->result() as $minmaxCode) {
                    $arr = [];
                    if ($count % 2 == 0) {
                        $arr['outTime'] = $minmaxCode->time;
                    }
                    else {
                        $arr['inTime'] = $minmaxCode->time;
                    }
                    $count++;
                    array_push($inoutData, $arr);
                }

                $data = array(
                    'empCode' => $employeeCode->result()[0]->code,
                    'machineCode' => $rowCode->machinecode,
                    'contractCode' => $employeeCode->result()[0]->contractCode,
                    'attendanceDate' => $date,
                    'inoutCount' => $count - 1,
                    'inoutJson' => json_encode($inoutData),
                );
                if ($this->db->insert('attendancedailytransaction', $data)) {
                    //echo 'insert_true';
                    $condition = "machinecode ='" . $rowCode->machinecode . "' AND date='" . $date . "'";
                    $this->db->where($condition);
                    if ($this->db->update('attendancedtransaction', array('flag' => 'Y'))) {
                    //return 'update_true';
                    }
                    else {
                    //return 'update_false';
                    }
                }
                else {
                //return 'insert_false';
                }
            }
        }
    }

    //////////////////////For Leave Application ////////////////////////
    ////////////////////// get HolidayCode list from siteCode //////////////////////

    public function getHolidaysFromSite($site)
    {
        $query = $this->db->query("SELECT DISTINCT `holidayCode` FROM `holidaysitemaster` WHERE siteCode ='" . $site . "' ");
        return $query;
    }

    ////////////////////// get Line Item list from PR  //////////////////////

    public function getHolidayCount($holiday, $from, $to)
    {
        $query = $this->db->query("SELECT count(`code`) as count FROM `holidaymaster` WHERE code ='" . $holiday . "' AND `holidayDate` BETWEEN '" . $from . "' AND '" . $to . "' AND `isActive` = '1'");
        //return $query;
        if ($this->db->affected_rows() > 0) {
            $res = $query->result()[0]->count;
        }
        else {
            $res = 'null';
        }
        return $res;
    }

    /////////////////// GET sitewise week offs ////////////////////////////////
    public function getSiteWiseWeekOffs($year)
    {
        $query = $this->db->query("SELECT DISTINCT si.`siteName`, ho.`day` ,ho.`code` ,ho.`year`, hs.`siteCode` FROM holidaysitemaster hs INNER JOIN sitemaster si ON hs.siteCode = si.code INNER JOIN holidaymaster ho ON hs.holidayCode = ho.code WHERE ho.type = 'WO' AND ho.year = '" . $year . "' GROUP BY hs.siteCode");
        //return $query;
        if ($this->db->affected_rows() > 0) {
            $res = $query;
        }
        else {
            $res = 'null';
        }
        return $res;
    }

    /////////////////////For Getting Stock Info////////////////////////////////////
    public function getStockInfoByCondition($itemCode, $siteCode, $storageCode, $storageSectionCode)
    {

        $condition1 = "";
        $condition2 = "";
        $andFlag = false;

        if ($itemCode != '') {
            if ($condition2 != '') {
                $andFlag = true;
            }

            if ($andFlag) {
                $condition2 .= " AND ";
            }

            $condition2 .= "im.code='" . $itemCode . "'";
        }

        if ($storageSectionCode != '') {
            if ($condition2 != '') {
                $andFlag = true;
            }

            if ($andFlag) {
                $condition2 .= " AND ";
            }

            $condition2 .= "ssm.code='" . $storageSectionCode . "'";
        }
        if ($storageCode != '') {
            if ($condition2 != '') {
                $andFlag = true;
            }

            if ($andFlag) {
                $condition2 .= " AND ";
            }

            $condition2 .= "sm.code='" . $storageCode . "'";
        }

        if ($siteCode != '') {
            if ($condition2 != '') {
                $andFlag = true;
            }

            if ($andFlag) {
                $condition2 .= " AND ";
            }

            $condition2 .= "sim.code='" . $siteCode . "'";
        }

        if ($condition2 != '') {
            $condition1 = "  WHERE ";
            $condition1 .= $condition2;
        }

        //$query ="SELECT  si.`itemCode`,si.`storageSection`,si.`stock`,sli.`activityCode`,sli.`itemQuantity`,sli.`itemUom`,sli.`itemPrice`, ssm.`storageCode`,sm.`storageName`,sm.`siteCode`,sim.`siteName`,sim.`contractCode`,cm.`contractName`,cm.`mineName`, im.`name` as itemName,ssm.storageSectionName  FROM `stockinfo` si INNER JOIN  `stocklinesinfo` sli ON si.`itemCode`=sli.`itemCode` and si.`storageSection` = sli.`storageSection` INNER JOIN `itemmaster` im ON im.code=si.`itemCode` INNER JOIN `storagesectionmaster` ssm ON ssm.`code`=si.`storageSection`  INNER JOIN `storagemaster` sm ON sm.code= ssm.`storageCode` INNER JOIN  `sitemaster` sim ON sim.code= sm.`siteCode` INNER JOIN `contractmaster` cm ON cm.code=sim.`contractCode` GROUP BY ssm.code".$condition1;
        $query = "SELECT si.`itemCode`,si.`storageSection`,si.`stock`, ssm.`storageCode`,sm.`storageName`,sm.`siteCode`,sim.`siteName`,sim.`contractCode`,cm.`contractName`,cm.`mineName`, im.`name` as itemName,ssm.storageSectionName FROM `stockinfo` si INNER JOIN`itemmaster` im ON im.code=si.`itemCode` INNER JOIN `storagesectionmaster` ssm ON ssm.`code`=si.`storageSection` INNER JOIN `storagemaster` sm ON sm.code= ssm.`storageCode` INNER JOIN `sitemaster` sim ON sim.code= sm.`siteCode` INNER JOIN `contractmaster` cm ON cm.code=sim.`contractCode`" . $condition1;
        //print_r($query);
        // DISTINCT
        return $query;
    }

    public function make_datatables($itemCode, $siteCode, $storageCode, $storageSectionCode)
    {
        $query = $this->getStockInfoByCondition($itemCode, $siteCode, $storageCode, $storageSectionCode);

        // if($_GET["length"] != -1 && $_GET["length"] != '')
        // {
        // $query.=" LIMIT ".$_GET['start'].",".$_GET['length'];
        // }
        $result = $this->db->query($query);

        return $result;
    }

    public function get_count($itemCode, $siteCode, $storageCode, $storageSectionCode)
    {
        //$myQuery=$this->getStockInfoByCondition($itemCode='',$storageSectionCode='',$siteCode='');
        $myQuery = $this->getStockInfoByCondition($itemCode, $siteCode, $storageCode, $storageSectionCode);
        $query2 = $this->db->query($myQuery);
        return $query2->num_rows();
    }
    //FOR USER AUTHENTICATION

    // Read data using username and password
    public function login($data)
    {
        $condition = "`username` ='" . $data['username'] . "' AND `password` = '" . $data['password'] . "' AND `isActive` = '1'";
        $this->db->select('*');
        $this->db->from('usermaster');
        $this->db->where($condition);
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return true;
        }
        else {
            return false;
        }
    }

    // Read data from database to show data in admin page
    public function read_user_information($username)
    {
        $condition = "username =" . "'" . $username . "'";
        $this->db->select('*');
        $this->db->from('usermaster');
        $this->db->where($condition);
        $this->db->limit(1);
        $query = $this->db->get();

        if ($query->num_rows() == 1) {
            return $query->result();
        }
        else {
            return false;
        }
    }

    //for checking existing password
    public function checkExistingPass($user, $pass, $tblname)
    {

        $query = $this->db->query("SELECT `password` FROM `" . $tblname . "` WHERE `username`='" . $user . "'");

        foreach ($query->result() as $row) {
            $query = $row->password;
        }

        if ($query == $pass) {
            $stmt = 'true';
        }
        else {
            $stmt = 'false';
        }
        return $stmt;
    }

    //Change User Profile using username
    public function changeUser($data, $tblname, $user)
    {

        $this->db->where('username', $user);
        $this->db->update($tblname, $data);
        if ($this->db->affected_rows() > 0) {
            echo '<Script type="javascript"> alert(Transaction Successfull) </script>';
        }
        else {
            echo '<Script type="javascript"> alert("<h4>Transaction Unsuccessfull</h4>") </script>';
        }
    }

    //getdata by where condition with limited records 10
    public function getdatabycolumlike_limit($columnName, $value, $tablename, $limit)
    {
        $this->db->like($columnName, $value);

        $this->db->limit($limit);

        $query = $this->db->get($tablename);
        if ($query->num_rows() > 0) {
            return $query->result();
        }
        else {
            return false;
        }
    }

    public function getCountOfPerticularValue($tblname, $field, $value)
    {
        $query = $this->db->query("SELECT count(`" . $field . "`) as count FROM `" . $tblname . "` WHERE `" . $field . "` = '" . $value . "' AND `isActive` = '1'");
        //return $query;
        if ($this->db->affected_rows() > 0) {
            $res = $query->result()[0]->count;
        }
        else {
            $res = 'null';
        }
        return $res;
    }

    public function getCountOfValueWithDate($tblname, $field, $value)
    {
        $currentDate = date("Y-m-d");
        $query = $this->db->query("SELECT count(`" . $field . "`) as count FROM `" . $tblname . "` WHERE `" . $field . "` = '" . $value . "' AND `isActive` = '1' AND `addDate` BETWEEN '" . $currentDate . "' AND '" . $currentDate . " 23:59:59.999' "); //01:00:01
        //return $query;
        if ($this->db->affected_rows() > 0) {
            $res = $query->result()[0]->count;
        }
        else {
            $res = 'null';
        }
        return $res;
    }

    public function getCountOfAdmAction($tblname, $field, $value, $condition)
    {
        $currentDate = date("Y-m-d");

        $query = $this->db->query("SELECT count(`" . $field . "`) as count FROM `" . $tblname . "` WHERE `" . $field . "` = '" . $value . "' AND `isActive` = '1' AND `" . $condition . "` BETWEEN '" . $currentDate . "' AND '" . $currentDate . " 23:59:59.999' "); //AND `editID` = '".$userCode."'
        //return $query;
        if ($this->db->affected_rows() > 0) {
            $res = $query->result()[0]->count;
        }
        else {
            $res = 'null';
        }
        return $res;
    }

    public function getCountWthField($tblname, $field)
    {
        $currentDate = date("Y-m-d");
        $query = $this->db->query("SELECT count(`" . $field . "`) as count FROM `" . $tblname . "` WHERE `inwardDate` BETWEEN '" . $currentDate . "' AND '" . $currentDate . " 23:59:59.999' "); //01:00:01
        //return $query;
        if ($this->db->affected_rows() > 0) {
            $res = $query->result()[0]->count;
        }
        else {
            $res = 'null';
        }
        return $res;
    }

    public function getCountWithAmount($tblname, $field, $condition)
    {
        $currentDate = date("Y-m-d");
        $query = $this->db->query("SELECT sum(`" . $field . "`) as total FROM `" . $tblname . "` WHERE `" . $condition . "` BETWEEN '" . $currentDate . "' AND '" . $currentDate . " 23:59:59.999' "); //01:00:01
        //return $query;
        if ($this->db->affected_rows() > 0) {
            $res = $query->result()[0];
        }
        else {
            $res = 'null';
        }
        return $res;
    }

    public function checkDuplicateRecordNotEqualtoCode($field, $value, $tblname, $code)
    {

        $query = $this->db->query("SELECT * FROM `" . $tblname . "` WHERE " . $field . "='" . $value . "' AND code!= '" . $code . "' and (`isDelete` IS NULL OR `isDelete`='0')");
        //echo $this->db->last_query();

        $count_row = $query->num_rows();

        if ($count_row > 0) {
            $stmt = true;
        }
        else {
            $stmt = false;
        }
        return $stmt;
    }

    //Update with perticualar field
    public function doEditwitharray($data, $tblname, $array)
    {
        $this->db->where($array);
        $this->db->update($tblname, $data);
        if ($this->db->affected_rows() > 0) {
            $res = 'true';
        }
        else {
            $res = 'false';
        }
        return $res;
    }

    //for check duplicate record with array
    public function getRecordsWithArray($sel, $table, $condition = array())
    {
        $this->db->select($sel, false);
        $this->db->from($table);
        foreach ($condition as $k => $v) {
            if ($v != "") {
                $this->db->where($k, $v);
            }
        }
        $this->db->where("(`isDelete` IS NULL OR `isDelete`='0')");
        $query = $this->db->get();
        $count_row = $query->num_rows();
        // return  $query->result_array();
        if ($count_row > 0) {
            return $query;
        }
        else {
            return false;
        }
    }

    function randomCode($n)
    {
        $characters = '0123456789';
        $randomString = '';

        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }

        return $randomString;
    }
    function generateOTPMaster()
    {
        $otp = $this->randomCode(6);
        // $result = $this->db->query("select * from registerOTP where otp='".$otp."'")->num_rows();
        $result = $this->db->query("select * from vendor where code='" . $otp . "'")->num_rows();
        if ($result > 0) {
            $otp = $this->randomCode(6);
            // $this->db->query("insert into `registerOTP`(`otp`) values('".$otp."')");
            return $otp;
        }
        else {
            // $this->db->query("insert into `registerOTP`(`otp`) values('".$otp."')");
            return $otp;
        }
    }

    public function checkDuplicateRecordNew($condition, $tablename)
    {
        $this->db->where($condition);
        $query = $this->db->where('(isDelete = 0 OR isDelete IS NULL)')->get($tablename);
        if ($query->num_rows() > 0) {
            return true;
        }
        else {
            return false;
        }
    }

    public function checkDuplicateRecordInUpdate($field, $value, $code, $tblname)
    {
        $query = $this->db->query("SELECT * FROM `" . $tblname . "` WHERE " . $field . "='" . $value . "' AND code != '" . $code . "' AND (`isDelete` IS NULL OR `isDelete`='0')");
        $count_row = $query->num_rows();
        if ($count_row > 0) {
            $stmt = true;
        }
        else {
            $stmt = false;
        }
        return $stmt;
    }



    public function hasDeliveryboyReleasedOrder($deliveryBoy, $orderCode)
    {
        $records = $this->db->query("select * from deliveryboystatuslines where orderCode='" . $orderCode . "' and deliveryBoyCode='" . $deliveryBoy . "' and orderStatus='REL'");
        log_message("error", $this->db->last_query());
        if ($records->num_rows() > 0) {
            log_message('error', "Query returned rows: " . $records->num_rows());
            return true;
        }
        else {
            log_message('error', "Query returned rows: " . $records->num_rows());
            return false;
        }
    }

    public function selectUserAccessActiveDataSequence($tblname)
    {
        $query = $this->db->query("SELECT * FROM `" . $tblname . "` WHERE `isActive` = '1' ORDER BY sequence ASC");
        return $query;
    }

    public function selectUserAccessActiveDataByFieldSequence($field, $value, $tblname)
    {
        $query = $this->db->query("SELECT * FROM `" . $tblname . "` WHERE `" . $field . "` = '" . $value . "' AND `isActive`='1' ORDER BY sequence ASC");

        return $query;
    }

    //Modules and sub modules
    function getAllModules()
    {
        $query = $this->db->query("SELECT * FROM `u_modulemaster` WHERE `isActive`=1 AND `type`!=2  ORDER BY `sequence` ASC");
        $jsonResult = array();
        $temp_array = array();
        if ($query->num_rows() > 0) {
            $jsonResult["status"] = true;
            foreach ($query->result() as $row) {
                $tarray = array();
                $temp2_array = array();
                $modulecode = $row->code;
                $tarray["id"] = $row->id;
                $tarray["code"] = $row->code;
                $tarray["moduleName"] = $row->moduleName;
                $tarray["moduleIcon"] = $row->moduleIcon;
                $tarray["displayUrl"] = $row->displayUrl;
                $tarray["routeUrl"] = $row->routeUrl;
                $tarray["sequence"] = $row->sequence;
                $tarray["type"] = $row->type;


                $query2 = $this->db->query("SELECT * FROM `u_submodulemaster` WHERE `moduleCode`='" . $modulecode . "' AND isActive=1 ORDER BY `sequence` ASC");
                if ($query2->num_rows() > 0) {
                    $tarray["subStatus"] = true;
                    foreach ($query2->result() as $row2) {
                        $temp3_array = array();
                        $subtarray = array();
                        $subtarray["id"] = $row2->id;
                        $subtarray['isArray'] = false;
                        $subtarray["code"] = $row2->code;
                        $subtarray["subModuleName"] = $row2->subModuleName;
                        $subtarray["subModuleIcon"] = $row2->subModuleIcon;
                        $subtarray["displayUrl"] = $row2->displayUrl;
                        $subtarray["routeUrl"] = $row2->routeUrl;
                        $subtarray["sequence"] = $row2->sequence;
                        array_push($temp2_array, $subtarray);
                    }

                    $tarray["subModules"] = $temp2_array;
                }
                else {
                    $tarray["subStatus"] = false;
                }
                array_push($temp_array, $tarray);
            } //end query1 for

        } //end query 1 num rows if
        else {
            $jsonResult["status"] = false;
        } //end query 1 num rows else

        $jsonResult["ModulesData"] = $temp_array;
        return json_encode($jsonResult);
    }

    function directQuery(string $query_string)
    {
        $query_result = $this->db->query($query_string);
        if ($query_result->num_rows() > 0) {
            return $query_result->result_array();
        }
        return array();
    }

    public function set_main_variant($productCode)
    {
        $this->db->where("productCode", $productCode)->update("productratelineentries", ["isMainVariant" => 0]);

        $this->db->select("id");
        $this->db->from("productratelineentries");
        $this->db->where("productCode", $productCode);
        $this->db->where("isActive", 1);
        $this->db->group_by("cityCode,productCode");
        $this->db->order_by("id", "ASC");
        $result = $this->db->get();
        if ($result && $result->num_rows() > 0) {
            $result = $result->result();
            for ($i = 0; $i < count($result); $i++) {
                $this->db->where("id", $result[$i]->id)->update("productratelineentries", ["isMainVariant" => 1]);
            }
        }
        return true;
    }

    public function check_vege_food_order($orderCode)
    {
        $cnt = $this->db->where('code', $orderCode)->from('ordermaster')->count_all_results();
        if ($cnt > 0) {
            return "vege";
        }

        $cnt = $this->db->where('code', $orderCode)->from('vendorordermaster')->count_all_results();
        if ($cnt > 0) {
            return "food";
        }
    }
}
