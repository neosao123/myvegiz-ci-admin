<?php
 
require(APPPATH.'/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;
 
class Api extends REST_Controller {
    
    public function __construct()
    {
        parent::__construct();
        //$this->load->model('book_model');		
		$this->load->helper('form','url','html');
   		$this->load->library('form_validation');
        $this->load->model('GlobalModel');
		$this->load->model('ApiModel');
		$this->load->library('notificationlibv_3');
    }
	public function getEntityCategoryList_get()
	{
		$tableName="entitycategory";
		$orderColumns = array("entitycategory.*");
		$condition=array('entitycategory.isActive'=>1);
		$orderBy = array('entitycategory' . '.id' => 'DESC');
		$joinType=array();
		$join = array();
		$groupByColumn=array();
		$limit=$this->input->GET("length");
		$offset=$this->input->GET("start");
		$extraCondition=" (entitycategory.isDelete=0 OR entitycategory.isDelete IS NULL)";
		$like = array();
		$Records = $this->GlobalModel->selectQuery($orderColumns,$tableName,$condition,$orderBy,$join,$joinType,$like,$limit,$offset,$groupByColumn,$extraCondition);
		if($Records)
		{
		    $data = array();
		    foreach($Records->result_array() as $r)
		    {
		        $data[] = array("code"=>$r['code'],"entityCategoryName"=>$r["entityCategoryName"]);
		    }
	    	$response['entitycategory']=$data;			
			return $this->response(array("status" => "200","message"=>'Data Found',"result"=>$response), 200);
		}
		else
		{
		   	return $this->response(array("status" => "300","message"=>'No Data Found'), 200);
		} 
	}
    public function getFoodSliderImages_get()
	{
		$postData = $this->post();
		//if($postData['cityCode']!=""){
			$columns = array("foodslider.*");
			//$cond=array('productslider' . ".isActive" => 1,'productslider.productCode'=>$postData['cityCode']);
			$cond=array('foodslider' . ".isActive" => 1);
			$orderBy = array('foodslider' . ".id" => 'ASC');
			$Records = $this->GlobalModel->selectQuery($columns,'foodslider',$cond,$orderBy);
			if($Records){
    			$data=array();		
    		    foreach($Records->result_array() as $r)
    		    {
    		        $path="";      
    		        if(file_exists('uploads/foodslider/'.$r['sliderPhoto'])){
    					$path=base_url().'uploads/foodslider/'.$r['sliderPhoto']; 	
    				}
    				$data[] = array("code"=>$r['code'],"sliderPhoto"=>$path,"caption"=>$r['caption']);
    		    } 			
    			$response['mainCategoryList']=$data;			
    			return $this->response(array("status" => "200","message"=>'Data Found',"result"=>$response), 200);
			}
			else{
				$data['sliderImages'] = array();
				return $this->response(array("status" => "300", "message" => "Data not found.",'result'=>$data), 200);
			}
		// }
			// else 
			//{ 
			// $data['sliderImages'] = array();
			// return $this->response(array("status" => "200", "message" => "Data not found.",'result'=>$data), 200);
		// }
	}  
	public function getMenuCategoryList_get()
	{
		$tableName="menucategory";
		$orderColumns = array("menucategory.*");
		$condition=array('menucategory.isActive'=>1);
		$orderBy = array('menucategory' . '.id' => 'DESC');
		$joinType=array();
		$join = array();
		$groupByColumn=array();
		$limit=$this->input->GET("length");
		$offset=$this->input->GET("start");
		$extraCondition=" menucategory.isDelete=0 OR menucategory.isDelete IS NULL";
		$like = array();
		$Records = $this->GlobalModel->selectQuery($orderColumns,$tableName,$condition,$orderBy,$join,$joinType,$like,$limit,$offset,$groupByColumn,$extraCondition);
		if($Records)
		{
		    $data = array();
		    foreach($Records->result_array() as $r)
		    {
		        $data[] = $r;
		    }
	    	$response['menuCategory']=$data;			
			return $this->response(array("status" => "200","message"=>'Data Found',"result"=>$response), 200);
		}
		else
		{
		   	return $this->response(array("status" => "300","message"=>'No Data Found'), 200);
		} 
	}
	public function getMenuSubCategoryList_get()
	{
		$tableName="menusubcategory";
		$orderColumns = array("menusubcategory.*,menucategory.menuCategoryName");
		$condition=array('menusubcategory.isActive'=>1);
		$orderBy = array('menusubcategory' . '.id' => 'DESC');
		$joinType=array('menucategory'=>'inner');
		$join = array('menucategory'=>'menucategory.code=menusubcategory.menuCategoryCode');
		$groupByColumn=array();
		$limit=$this->input->GET("length");
		$offset=$this->input->GET("start");
		$extraCondition=" (menusubcategory.isDelete=0 OR menusubcategory.isDelete IS NULL)";
		$like = array();
		$Records = $this->GlobalModel->selectQuery($orderColumns,$tableName,$condition,$orderBy,$join,$joinType,$like,$limit,$offset,$groupByColumn,$extraCondition);
		if($Records)
		{
		    $data = array();
		    foreach($Records->result_array() as $r)
		    {
		        $data[] = $r;
		    }
	    	$response['menuSubCategory']=$data;			
			return $this->response(array("status" => "200","message"=>'Data Found',"result"=>$response), 200);
		}
		else
		{
		   	return $this->response(array("status" => "300","message"=>'No Data Found'), 200);
		} 
	}
	public function getCuisinesList_get()
	{
		$tableName="cuisinemaster";
		$orderColumns = array("cuisinemaster.*");
		$condition=array('cuisinemaster.isActive'=>1);
		$orderBy = array('cuisinemaster' . '.id' => 'DESC');
		$joinType=array();
		$join = array();
		$groupByColumn=array();
		$limit=$this->input->GET("length");
		$offset=$this->input->GET("start");
		$extraCondition=" cuisinemaster.isDelete=0 OR cuisinemaster.isDelete IS NULL";
		$like = array();
		$Records = $this->GlobalModel->selectQuery($orderColumns,$tableName,$condition,$orderBy,$join,$joinType,$like,$limit,$offset,$groupByColumn,$extraCondition);
		if($Records)
		{
		    $data = array();
		    foreach($Records->result_array() as $r)
		    {
		       	$path ="";
				if($r['cuisinePhoto']!="")
				{
					$path = base_url('uploads/cuisinemaster/'.$r['cuisinePhoto']);
				}
				else
				{
					$path = "";
				}
				$data[] = array("code"=>$r['code'],"cuisineName"=>$r['cuisineName'],"cuisinePhoto"=>$path);
		    }
	    	$response['cuisinesList']=$data;			
			return $this->response(array("status" => "200","message"=>'Data Found',"result"=>$response), 200);
		}
		else
		{
		   	return $this->response(array("status" => "300","message"=>'No Data Found'), 200);
		} 
	}
	public function getVendorList_get()
	{
		$getData = $this->get();
		$cond = "vendor.isActive=1";
		
		// if((isset($getData['latitude'])) && (isset($getData['latitude'])))
		// {
		
		// }
		
		if(isset($getData['cuisineCode']) && $getData['cuisineCode']!="")	
		{
			$split_cuisine = explode(',',$getData['cuisineCode']);
			if(!empty($split_cuisine))
			{
				$values  = "";
				foreach($split_cuisine as $cui)
				{
					$values != "" && $values .= ",";
					$values .= "'".$cui."'";
				}
				if($cond!="") $cond .= " and vendorcuisinelineentries.cuisineCode in (". $values .") "; 
				else $cond .= " vendorcuisinelineentries.cuisineCode in (". $values .") "; 
			}
		}
		
		if(isset($getData['entitycategoryCode']))	
		{
			if($getData['entitycategoryCode']!="") 
			{
				if($cond!="") $cond .= " and vendor.entitycategoryCode='".$getData['entitycategoryCode']."' "; 
				else $cond .= " vendor.entitycategoryCode='".$getData['entitycategoryCode']."' "; 
			} 
		}
	    if(isset($getData['entityName']))	
		{
			if($getData['entityName']!="") 
			{
				if($cond!="") $cond .= " and vendor.entityName like '%".$getData['entityName']."%' "; 
				else $cond .= " vendor.entityName like '%".$getData['entityName']."%' "; 
			}
		}		
		$columns = "select vendor.*,entitycategory.entityCategoryName,citymaster.cityName,customaddressmaster.place from vendor ";
		$orderBy = " order by vendor.id ASC ";
		$join = " INNER JOIN entitycategory ON vendor.entitycategoryCode = entitycategory.`code` LEFT JOIN vendorcuisinelineentries ON vendor.`code` = vendorcuisinelineentries.vendorCode ";
		$join .= " LEFT JOIN citymaster ON vendor.cityCode = citymaster.`code` LEFT JOIN customaddressmaster ON vendor.`addressCode` = customaddressmaster.code ";
		$groupBy = " Group by vendor.code ";  
		$limit = 10;
		$offset = "";
		if(isset($getData['offset']))	
		{
			if($getData['offset']>0) $limit_offset = " limit 10,".$getData['offset']; 
			else $limit_offset = " limit 10";
		}
		else
		{
			$limit_offset = " limit 10";
		}  
		if($cond!="") $whereCondition = " where " .$cond;
		else $whereCondition ="";
		$query = $columns . $join . $whereCondition . $groupBy . $orderBy .$limit_offset;
		$result = $this->db->query($query);
		//echo $this->db->last_query();
		if($result->num_rows()>0)
		{
		    $data = array();
		    foreach($result->result_array() as $r)
		    {
				/* get cuisines served by vendor*/
				$cuisinesList = "";				
				$table1 = "cuisinemaster";
				$orderColumns1 = "GROUP_CONCAT(cuisinemaster.cuisineName) as cuisines";
				$condition1 = array("vendorcuisinelineentries.vendorCode"=>$r['code'],"cuisinemaster.isActive"=>1);
				$orderBy1 = array();
				$join1 =array("vendorcuisinelineentries"=>"cuisinemaster.`code` = vendorcuisinelineentries.cuisineCode");
				$joinType1 = array("vendorcuisinelineentries"=>"inner");
				$cuisineRecords = $this->GlobalModel->selectQuery($orderColumns1,$table1,$condition1,$orderBy1,$join1,$joinType1);
				if($cuisineRecords) 
				{
					$cuisinesList = $cuisineRecords->result_array()[0]['cuisines'];
				}
				
				$vendorar['code'] =$r['code'] ;
				$vendorar['entityName'] =$r['entityName'];				
				$vendorar['firstName'] = $r['firstName'];
				$vendorar['middleName'] = $r['middleName'];
				$vendorar['lastName'] = $r['lastName'];
				if($r['entityImage']!="")
				{
					$path = 'uploads/vendor/'.$r['code'].'/'.$r['entityImage'];
					if(file_exists($path)) $vendorar['entityImage'] = $path;
					else $vendorar['entityImage']  ="noimage";
				}
				else $vendorar['entityImage']  = "noimage";
				 
				$vendorar['address'] = $r['address'];
				$vendorar['ownerContact'] = $r['ownerContact'] ;
				$vendorar['entityContact'] =$r['entityContact'] ;
				$vendorar['email'] =$r['email'] ;
				$vendorar['entityCategoryName'] =$r['entityCategoryName'];
				$vendorar['fssaiNumber'] =$r['fssaiNumber'];	 
				$vendorar['cityName'] =$r['cityName'];	 
				$vendorar['aresName'] =$r['place'];	 
				$vendorar['cuisinesList'] =$cuisinesList;		
				 
		        $data[] = $vendorar;
		    }
	    	$response['vendors']=$data;			
			return $this->response(array("status" => "200","message"=>'Data Found',"result"=>$response), 200);
		}
		else
		{
		   	return $this->response(array("status" => "300","message"=>'No Data Found'), 200);
		} 
	}
	public function getMenuItemList_get()
	{
		$getData = $this->get();
		if($getData['vendorCode']!="")
		{	
			$tableName1="menucategory";
			$orderColumns1 = array("menucategory.*");
			$condition1=array('menucategory.isActive'=>1);
			$orderBy1 = array('menucategory' . '.id' => 'DESC');
			$Records = $this->GlobalModel->selectQuery($orderColumns1,$tableName1,$condition1,$orderBy1);
			if($Records)
			{
				$data=array();
				
				foreach($Records->result_array() as $ra)
				{
					$mainitemArray = array(); 
					
					$maincount = 0;
					
					$catCode = $ra['code'];
					$catName = $ra['menuCategoryName']; 
					
					$tableName2="vendoritemmaster";
					$orderColumns2 = array("vendoritemmaster.*,vendor.entityName");
					$condition2=array('vendoritemmaster.isActive'=>1,"vendoritemmaster.vendorCode"=>$getData['vendorCode'],"vendoritemmaster.menuCategoryCode"=>$catCode);
					$orderBy2 = array('vendoritemmaster' . '.id' => 'DESC');
					$joinType2 =array("vendor"=>"inner");
					$join2 = array("vendor"=>"vendoritemmaster.vendorCode=vendor.code");
					$groupByColumn2=array(); 
					$extraCondition2=" (vendoritemmaster.isDelete=0 OR vendoritemmaster.isDelete IS NULL) and (vendoritemmaster.menuSubCategoryCode is Null or vendoritemmaster.menuSubCategoryCode='')";
					$like2 = array();
					$itemRecords = $this->GlobalModel->selectQuery($orderColumns2,$tableName2,$condition2,$orderBy2,$join2,$joinType2,$like2,"","",$groupByColumn2,$extraCondition2);
					//echo $this->db->last_query();	
					if($itemRecords)
					{
						foreach($itemRecords->result_array() as $r)
						{
							$path = "nophoto";
							if($r['itemPhoto']!="")
							{
								$path = 'partner/uploads/'.$r['vendorCode'].'/vendoritem/'.$r['itemPhoto'];
							}
							$mainitemArray[] = array(
								"itemName"=>$r['itemName'],
								"itemDescription"=>$r['itemDescription'],
								"salePrice"=>$r['salePrice'],
								"itemPhoto"=>$path,
								"vendorName"=>$r['entityName'],
								"cuisineType"=>$r['cuisineType'],
								"isActive"=>$r['isActive'],
							); 
							$maincount++;
						} 
					}  
					
					$subCategoryItemArray = array();
					
					$tableName3="menusubcategory";
					$orderColumns3 = array("menusubcategory.*");
					$condition3=array('menusubcategory.isActive'=>1,"menusubcategory.menuCategoryCode"=>$catCode);
					$orderBy3 = array('menusubcategory' . '.id' => 'DESC');
					$subCateRecords = $this->GlobalModel->selectQuery($orderColumns3,$tableName3,$condition3,$orderBy3);
					if($subCateRecords)
					{
						$subcount	= sizeof($subCateRecords->result());
						foreach($subCateRecords->result_array() as $subrow)
						{
							$subCategoryCode = $subrow['code'];
							$subCategoryName = $subrow['menuSubCategoryName'];
							
							$tableName4="vendoritemmaster";
							$orderColumns4 = array("vendoritemmaster.*,vendor.entityName");
							$condition4=array('vendoritemmaster.isActive'=>1,"vendoritemmaster.vendorCode"=>$getData['vendorCode'],"vendoritemmaster.menuSubCategoryCode"=>$subCategoryCode);//"vendoritemmaster.menuCategoryCode"=>$catCode,
							$orderBy4 = array('vendoritemmaster' . '.id' => 'DESC');
							$joinType4=array("vendor"=>"inner","menusubcategory"=>"inner");
							$join4 = array("vendor"=>"vendoritemmaster.vendorCode=vendor.code","menusubcategory"=>"vendoritemmaster.menuSubCategoryCode=menusubcategory.code");
							$groupByColumn4=array();							 
							$extraCondition4=" (vendoritemmaster.isDelete=0 OR vendoritemmaster.isDelete IS NULL)";
							$like4 = array();
							$Records = $this->GlobalModel->selectQuery($orderColumns4,$tableName4,$condition4,$orderBy4,$join4,$joinType4,$like4,"","",$groupByColumn4,$extraCondition4);
							if($Records)
							{
								$itemArray=array();
								$count = sizeof($Records->result_array());
								foreach($Records->result_array() as $r)
								{
									$path = "nophoto";
									if($r['itemPhoto']!="")
									{
										$path = 'partner/uploads/'.$r['vendorCode'].'/vendoritem/'.$r['itemPhoto'];
									}
									$itemArray[] = array(
										"itemName"=>$r['itemName'],
										"itemDescription"=>$r['itemDescription'],
										"salePrice"=>$r['salePrice'],
										"itemPhoto"=>$path,
										"vendorName"=>$r['entityName'],
										"cuisineType"=>$r['cuisineType'],
										"isActive"=>$r['isActive'],
									); 
									$maincount++;
								} 
								
								$subCategoryItemArray[] = array("subCategoryCode"=>$subCategoryCode,"subCategoryName"=>$subCategoryName,"count"=>$count,"itemList"=>$itemArray);
							} 					
						}
					} 
					if($maincount>0)
					{
						$data[] = array("menuCategoryCode"=>$catCode,"count"=>$maincount,"menuCategoryName"=>$catName,"itemList"=>$mainitemArray,"subCategoryList"=>$subCategoryItemArray);
					}
				}
				$response['menuItemList']=$data;		 
				return $this->response(array("status" => "200","message"=>'Data Found',"result"=>$response), 200);
			}
			else
			{
				return $this->response(array("status" => "300","message"=>'No Data Found'), 200);
			}
			
		}
	}
	
}