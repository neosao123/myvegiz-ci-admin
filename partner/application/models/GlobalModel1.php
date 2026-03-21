<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class GlobalModel1 extends CI_Model{
 
 function _construct() {
        parent::_construct();
    }

	  function makeQueryCondition($tables,$requiredColumns,$conditions,$extraConditionColumnNames,$extraConditions,$extraDateConditionColumnNames,$extraDateConditions,$likeFlag)
	  {
		  $query="SELECT ";
		  $colQry="";
		  
		  for($t=0;$t<sizeof($requiredColumns);$t++)
		  {
			  for($c=0;$c<sizeof($requiredColumns[$t]);$c++)
			  {
				  $colQry.="t".$t.".".$requiredColumns[$t][$c]." as ".$requiredColumns[$t][$c]."_".$t.$c;
				 
					  $colQry.=" ,";
				  
			  }
		  }
		  $colQry=rtrim($colQry,",");
		  $fromCondition=" From ";
		  $i=0;
		  
		  if(sizeof($conditions))
		  {
		  for($t=-1;$t<sizeof($tables)-1;$t++)
		  {
			if($t==-1)
			{
				$fromCondition.= $tables[0].' as t0'; 
			}
			else
			{
				$fromCondition.= " INNER JOIN ".$tables[$i]." as t".$i;
				$cond="";
				$d=1;
				for($c=0;$c<1;$c++)
				{
					$cond=" ON t0.".$conditions[$t][$c]." = "." t".$i.".".$conditions[$t][$c+1];
				}
				$fromCondition.=$cond;
			}
			$i++;
		  }
		  }
		  else
		  {
		  	$fromCondition.= $tables[0].' as t0'; 
		  }
		   $fromCondition.=" WHERE ";
		  
		   $fromExtraCondition="";//$fromCondition;
		  for($tbl=0;$tbl<sizeof($extraConditionColumnNames);$tbl++)
		  {
			  for($e=0;$e<sizeof($extraConditions[$tbl]);$e++)
			  {
			  	// $fromExtraCondition.= "t".$t.".".$extraConditionColumnNames[$t][$e]." like '%".$extraConditions[$t][$e]."%' AND ";
				if($extraConditions[$tbl][$e]!='')
				{
					if($likeFlag)
					{
					  $fromExtraCondition.= "t".$tbl.".".$extraConditionColumnNames[$tbl][$e]." like '%".$extraConditions[$tbl][$e]."%' AND ";
					}
					else
					{
					  $fromExtraCondition.= "t".$tbl.".".$extraConditionColumnNames[$tbl][$e]." = '".$extraConditions[$tbl][$e]."' AND ";
					}
				}
			  }
		  }

		  for($dt=0;$dt<sizeof($extraDateConditionColumnNames);$dt++)
		  {
		  	for($e=0;$e<sizeof($extraDateConditions[$dt]);$e++)
			{
				if($extraDateConditions[$dt][$e]!='' && $extraDateConditions[$dt][$e]!='~')
				{
				$seDate=explode("~",$extraDateConditions[$dt][$e]);
		  		$fromExtraCondition .= "t".$dt.".".$extraDateConditionColumnNames[$dt][$e]." between '". $seDate[0]."' AND '".$seDate[1]."' AND ";
		  		}
		  	}
		  }
		
		  $fromCondition.=$fromExtraCondition;
		  $fromCondition.=" (t0.isDelete IS NULL OR t0.isDelete=0) ORDER BY t0.id DESC ";
		  //echo $query.$colQry.$fromCondition;
		  return $query.$colQry.$fromCondition;
	  }
	  
	 
	 function make_datatables($tables,$requiredColumns,$conditions,$extraConditionColumnNames,$extraConditions,$extraDateConditionColumnNames=array(),$extraDateConditions=array(),$likeFlag=true){  
           $query=$this->makeQueryCondition($tables,$requiredColumns,$conditions,$extraConditionColumnNames,$extraConditions,$extraDateConditionColumnNames,$extraDateConditions,$likeFlag);  
            
			// if($_GET["length"] != -1 && $_GET["length"] != '')  
   //         {  
   //             $query.=" LIMIT ".$_GET['start'].",".$_GET['length'];  
   //         }  
			$result=$this->db->query($query);
        return  $result;
      }
	  
	  function make_datatablesWithoutLimit($tables,$requiredColumns,$conditions,$extraConditionColumnNames,$extraConditions,$extraDateConditionColumnNames=array(),$extraDateConditions=array(),$likeFlag=true){  
           $query=$this->makeQueryCondition($tables,$requiredColumns,$conditions,$extraConditionColumnNames,$extraConditions,$extraDateConditionColumnNames,$extraDateConditions,$likeFlag);
			$result=$this->db->query($query);
        return  $result;
      }
	  
      function get_all_data($tables,$requiredColumns,$conditions,$extraConditionColumnNames,$extraConditions,$extraDateConditionColumnNames=array(),$extraDateConditions=array(),$likeFlag=true)  
      {  
		$myQuery=$this->makeQueryCondition($tables,$requiredColumns,$conditions,$extraConditionColumnNames,$extraConditions,$extraDateConditionColumnNames,$extraDateConditions,$likeFlag);
           $query2=$this->db->query($myQuery);
           return $query2->num_rows();
      }
	 
	  //For Calendar 
	  function make_calendar($tables,$requiredColumns,$conditions,$extraConditionColumnNames,$extraConditions,$extraDateConditionColumnNames=array(),$extraDateConditions=array(),$likeFlag=true){  
           $query=$this->makeQueryCondition($tables,$requiredColumns,$conditions,$extraConditionColumnNames,$extraConditions,$extraDateConditionColumnNames,$extraDateConditions,$likeFlag);   
		   //$query.= " GROUP BY t0.".$requiredColumns[0][0];
		   $query = substr( $query, 0, -20 );
		   $query.= " GROUP BY t0.siteCode";
		   $result=$this->db->query($query);
        return  $result;
      }
	  
	  //For reportCategoryWiseName 
	  function make_reportCategoryWiseName($tables,$requiredColumns,$conditions,$extraConditionColumnNames,$extraConditions,$extraDateConditionColumnNames=array(),$extraDateConditions=array(),$likeFlag=true){  
           $query=$this->makeQueryCondition($tables,$requiredColumns,$conditions,$extraConditionColumnNames,$extraConditions,$extraDateConditionColumnNames,$extraDateConditions,$likeFlag);   
		   //$query.= " GROUP BY t0.".$requiredColumns[0][0];
		   $query = substr( $query, 0, -20 );
		   $query.= " GROUP BY t0.reportCategory";
		   $result=$this->db->query($query);
        return  $result;
      }
	  
	  //For Inward Filteration 
	  function make_inwardTable($tables,$requiredColumns,$conditions,$extraConditionColumnNames,$extraConditions,$codeField,$extraDateConditionColumnNames=array(),$extraDateConditions=array(),$likeFlag=true){  
           $query=$this->makeQueryCondition($tables,$requiredColumns,$conditions,$extraConditionColumnNames,$extraConditions,$extraDateConditionColumnNames,$extraDateConditions,$likeFlag);   
		   
		   $query = substr( $query, 0, -20 );
		   $query.= " GROUP BY t3.".$codeField;
		   $result=$this->db->query($query);
        return  $result;
      }
	  
	 
}
?>