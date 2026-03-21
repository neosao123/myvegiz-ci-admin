<?php

require(APPPATH . '/libraries/REST_Controller.php');

use Restserver\Libraries\REST_Controller;

class Api extends REST_Controller
{

	public function __construct()
	{
		parent::__construct();
		//$this->load->model('book_model');		
		$this->load->helper('form', 'url', 'html');
		$this->load->library('form_validation');
		$this->load->model('GlobalModel');
		$this->load->model('ApiModel');
		$this->load->library('notificationlibv_3');
	}
	
	public function getEntityCategoryList_get()
	{
		$tableName = "entitycategory";
		$orderColumns = array("entitycategory.*");
		$condition = array('entitycategory.isActive' => 1);
		$orderBy = array('entitycategory' . '.id' => 'DESC');
		$joinType = array();
		$join = array();
		$groupByColumn = array();
		$limit = $this->input->GET("length");
		$offset = $this->input->GET("start");
		$extraCondition = " (entitycategory.isDelete=0 OR entitycategory.isDelete IS NULL)";
		$like = array();
		$Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
		if ($Records) {
			$data = array();
			foreach ($Records->result_array() as $r) {
				$data[] = array("code" => $r['code'], "entityCategoryName" => $r["entityCategoryName"]);
			}
			$response['entitycategory'] = $data;
			return $this->response(array("status" => "200", "message" => 'Data Found', "result" => $response), 200);
		} else {
			return $this->response(array("status" => "300", "message" => 'No Data Found'), 200);
		}
	}
	
	public function getFoodSliderImages_get()
	{
		$postData = $this->post();
		//if($postData['cityCode']!=""){
		$columns = array("foodslider.*");
		//$cond=array('productslider' . ".isActive" => 1,'productslider.productCode'=>$postData['cityCode']);
		$cond = array('foodslider' . ".isActive" => 1);
		$orderBy = array('foodslider' . ".id" => 'ASC');
		$Records = $this->GlobalModel->selectQuery($columns, 'foodslider', $cond, $orderBy);
		if ($Records) {
			$data = array();
			foreach ($Records->result_array() as $r) {
				$path = "";
				if (file_exists('uploads/foodslider/' . $r['sliderPhoto'])) {
					$path = base_url() . 'uploads/foodslider/' . $r['sliderPhoto'];
				}
				$data[] = array("code" => $r['code'], "sliderPhoto" => $path, "caption" => $r['caption']);
			}
			$response['mainCategoryList'] = $data;
			return $this->response(array("status" => "200", "message" => 'Data Found', "result" => $response), 200);
		} else {
			$data['sliderImages'] = array();
			return $this->response(array("status" => "300", "message" => "Data not found.", 'result' => $data), 200);
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
		$tableName = "menucategory";
		$orderColumns = array("menucategory.*");
		$condition = array('menucategory.isActive' => 1);
		$orderBy = array('menucategory' . '.id' => 'ASC');
		$joinType = array();
		$join = array();
		$groupByColumn = array();
		$limit = $this->input->GET("length");
		$offset = $this->input->GET("start");
		$extraCondition = " menucategory.isDelete=0 OR menucategory.isDelete IS NULL";
		$like = array();
		$Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
		if ($Records) {
			$data = array();
			foreach ($Records->result_array() as $r) {
				$data[] = $r;
			}
			$response['menuCategory'] = $data;
			return $this->response(array("status" => "200", "message" => 'Data Found', "result" => $response), 200);
		} else {
			return $this->response(array("status" => "300", "message" => 'No Data Found'), 200);
		}
	}
	
	public function getMenuSubCategoryList_get()
	{
		$tableName = "menusubcategory";
		$orderColumns = array("menusubcategory.*,menucategory.menuCategoryName");
		$condition = array('menusubcategory.isActive' => 1);
		$orderBy = array('menusubcategory' . '.id' => 'ASC');
		$joinType = array('menucategory' => 'inner');
		$join = array('menucategory' => 'menucategory.code=menusubcategory.menuCategoryCode');
		$groupByColumn = array();
		$limit = $this->input->GET("length");
		$offset = $this->input->GET("start");
		$extraCondition = " (menusubcategory.isDelete=0 OR menusubcategory.isDelete IS NULL)";
		$like = array();
		$Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
		if ($Records) {
			$data = array();
			foreach ($Records->result_array() as $r) {
				$data[] = $r;
			}
			$response['menuSubCategory'] = $data;
			return $this->response(array("status" => "200", "message" => 'Data Found', "result" => $response), 200);
		} else {
			return $this->response(array("status" => "300", "message" => 'No Data Found'), 200);
		}
	}
	
	public function getCuisinesList_get()
	{
		$tableName = "cuisinemaster";
		$orderColumns = array("cuisinemaster.*");
		$condition = array('cuisinemaster.isActive' => 1);
		$orderBy = array('cuisinemaster' . '.id' => 'ASC');
		$joinType = array();
		$join = array();
		$groupByColumn = array();
		$limit = $this->input->GET("length");
		$offset = $this->input->GET("start");
		$extraCondition = " cuisinemaster.isDelete=0 OR cuisinemaster.isDelete IS NULL";
		$like = array();
		$Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
		if ($Records) {
			$data = array();
			foreach ($Records->result_array() as $r) {
				$path = "";
				if ($r['cuisinePhoto'] != "") {
					$path = base_url('uploads/cuisinemaster/' . $r['cuisinePhoto']);
				} else {
					$path = "";
				}
				$data[] = array("code" => $r['code'], "cuisineName" => $r['cuisineName'], "cuisinePhoto" => $path);
			}
			$response['cuisinesList'] = $data;
			return $this->response(array("status" => "200", "message" => 'Data Found', "result" => $response), 200);
		} else {
			return $this->response(array("status" => "300", "message" => 'No Data Found'), 200);
		}
	}
	
	public function getVendorList_get()
	{
		$getData = $this->get();
		$cond = "vendor.isActive=1";

		if (isset($getData['cuisineCode']) && $getData['cuisineCode'] != ""  ) {
			$split_cuisine = explode(',', $getData['cuisineCode']);
			if (!empty($split_cuisine)) {
				$values  = "";
				foreach ($split_cuisine as $cui) {
					$values != "" && $values .= ",";
					$values .= "'" . $cui . "'";
				}
				if ($cond != "") $cond .= " and vendorcuisinelineentries.cuisineCode in (" . $values . ") ";
				else $cond .= " vendorcuisinelineentries.cuisineCode in (" . $values . ") ";
			}
		}
		
		if (isset($getData['cityCode']) && $getData['cityCode'] != "" ) {
			$split_cityCode = explode(',', $getData['cityCode']);
			if (!empty($split_cityCode)) {
				$values  = "";
				foreach ($split_cityCode as $cui) {
					$values != "" && $values .= ",";
					$values .= "'" . $cui . "'";
				}
				if ($cond != "") $cond .= " and vendor.cityCode in (" . $values . ") ";
				else $cond .= " vendor.cityCode in (" . $values . ") ";
			}
		}

		if (isset($getData['entitycategoryCode'])) {
			if ($getData['entitycategoryCode'] != "") {
				if ($cond != "") $cond .= " and vendor.entitycategoryCode='" . $getData['entitycategoryCode'] . "' ";
				else $cond .= " vendor.entitycategoryCode='" . $getData['entitycategoryCode'] . "' ";
			}
		}
		
		if (isset($getData['entityName'])) {
			if ($getData['entityName'] != "") {
				if ($cond != "") $cond .= " and vendor.entityName like '%" . $getData['entityName'] . "%' ";
				else $cond .= " vendor.entityName like '%" . $getData['entityName'] . "%' ";
			}
		}
		
		$geoLocation ="";
		$having ="";
		if(isset($getData['latitude'])) {
			if(isset($getData['longitude'])){
				if ($getData['latitude'] != "" && $getData['longitude']!="") {
					$geoLocation =",( 3959 * acos( cos( radians(".$getData['latitude'].") ) * cos( radians( vendor.latitude ) ) * cos( radians( vendor.longitude ) - radians(".$getData['longitude'].") ) + sin( radians(".$getData['latitude'].") ) * sin(radians(vendor.latitude)) ) ) AS distance";
					$having = " HAVING distance <= 15";
				}	
			}	
		}
		
		$columns = "select vendor.*,entitycategory.entityCategoryName,citymaster.cityName,customaddressmaster.place". $geoLocation ." from vendor ";
		$orderBy = " order by vendor.isServiceable DESC , vendor.id ASC";
		$join = " INNER JOIN entitycategory ON vendor.entitycategoryCode = entitycategory.`code` LEFT JOIN vendorcuisinelineentries ON vendor.`code` = vendorcuisinelineentries.vendorCode ";
		$join .= " LEFT JOIN citymaster ON vendor.cityCode = citymaster.`code` LEFT JOIN customaddressmaster ON vendor.`addressCode` = customaddressmaster.code ";
		$groupBy = " Group by vendor.code ";
		$limit = 10;
		$offset = "";
		if (isset($getData['offset'])) {
			if ($getData['offset'] > 0) $limit_offset = " limit " . $getData['offset'].',10';
			else $limit_offset = " limit 10";
		} else {
			$limit_offset = " limit 10";
		}
		if ($cond != "") $whereCondition = " where " . $cond;
		else $whereCondition = "";
		$query = $columns . $join . $whereCondition . $groupBy . $having. $orderBy . $limit_offset;
		
		$result = $this->db->query($query);
		//$query =  $this->db->last_query();
		if ($result->num_rows() > 0) {
		 
			$data =array();
			foreach ($result->result_array() as $r) {  
				
				/* get cuisines served by vendor*/ 
				$cuisinesList = "";
				$table1 = "cuisinemaster";
				$orderColumns1 = "GROUP_CONCAT(cuisinemaster.cuisineName) as cuisines";
				$condition1 = array("vendorcuisinelineentries.vendorCode" => $r['code'], "cuisinemaster.isActive" => 1);
				$orderBy1 = array();
				$join1 = array("vendorcuisinelineentries" => "cuisinemaster.`code` = vendorcuisinelineentries.cuisineCode");
				$joinType1 = array("vendorcuisinelineentries" => "inner");
				$cuisineRecords = $this->GlobalModel->selectQuery($orderColumns1, $table1, $condition1, $orderBy1, $join1, $joinType1);
				if ($cuisineRecords) {
					$cuisinesList = $cuisineRecords->result_array()[0]['cuisines'];
				}

				$vendorar['code'] = $r['code'];
				$vendorar['entityName'] = $r['entityName'];
				$vendorar['firstName'] = $r['firstName'];
				$vendorar['middleName'] = $r['middleName'];
				$vendorar['lastName'] = $r['lastName'];
				$vendorar['latitude'] = $r['latitude'];
				$vendorar['longitude'] = $r['longitude'];
				$vendorar['isServiceable'] = $r['isServiceable'];
				if ($r['entityImage'] != "") {
					$path = 'uploads/vendor/' . $r['code'] . '/' . $r['entityImage'];
					if (file_exists($path)) $vendorar['entityImage'] = $path;
					else $vendorar['entityImage']  = "noimage";
				} else $vendorar['entityImage']  = "noimage";

				$vendorar['address'] = $r['address'];
				$vendorar['packagingType'] = $r['packagingType'];
				$vendorar['cartPackagingPrice'] = $r['cartPackagingPrice'];
				$vendorar['gstApplicable'] = $r['gstApplicable'];
				$vendorar['gstPercent'] = $r['gstPercent'];
				$vendorar['ownerContact'] = $r['ownerContact'];
				$vendorar['entityContact'] = $r['entityContact'];
				$vendorar['email'] = $r['email'];
				$vendorar['entityCategoryName'] = $r['entityCategoryName'];
				$vendorar['fssaiNumber'] = $r['fssaiNumber'];
				$vendorar['cityName'] = $r['cityName'];
				$vendorar['aresName'] = $r['place'];
				$vendorar['cuisinesList'] = $cuisinesList;
				
				$dayofweek= strtolower(date('l'));
				
				$timeData = $this->GlobalModel->selectQuery("vendorhours.code as hourCode,time_format(vendorhours.fromTime,'%h:%i %p') as fromTime,time_format(vendorhours.toTime,'%h:%i %p') as toTime","vendorhours",array("vendorhours.vendorCode"=>$r['code'],"vendorhours.weekDay"=>$dayofweek));
				if($timeData) {
				    $vendorar['vendorHours'] = $timeData->result_array();
				} else {
				    $vendorar['vendorHours'] = array();
				}
				
			 	$data[] = $vendorar;
			 
			}
		 
			$response['vendors'] = $data;
			return $this->response(array( "status" => "200", "message" => 'Data Found', "result" => $response), 200);
		} else {
			return $this->response(array("status" => "300", "message" => 'No Data Found'), 200);
		}
	}
	
	public function getMenuItemList_get()
	{
		$getData = $this->get();
		if ($getData['vendorCode'] != "") {
			$tableName1 = "menucategory";
			$orderColumns1 = array("menucategory.*");
			$condition1 = array('menucategory.isActive' => 1);
			$orderBy1 = array('menucategory' . '.priority' => 'ASC');
			$Records = $this->GlobalModel->selectQuery($orderColumns1, $tableName1, $condition1, $orderBy1);
			if ($Records) {
				$data = array();
				foreach ($Records->result_array() as $ra) {
					$mainitemArray = array();
					$addonArray = array();
					$choiceArray = array();
					$maincount = 0;
					$catCode = $ra['code'];
					$catName = $ra['menuCategoryName'];
					$tableName2 = "vendoritemmaster";
					$orderColumns2 = array("vendoritemmaster.*,vendor.entityName,vendor.isServiceable as vendorIsServiceable");
					$condition2 = array('vendoritemmaster.isActive' => 1, "vendoritemmaster.vendorCode" => $getData['vendorCode'], "vendoritemmaster.menuCategoryCode" => $catCode, "vendoritemmaster.isAdminApproved" => 1);
					$orderBy2 = array('vendoritemmaster' . '.id' => 'DESC');
					$joinType2 = array("vendor" => "inner");
					$join2 = array("vendor" => "vendoritemmaster.vendorCode=vendor.code");
					$groupByColumn2 = array();
					$extraCondition2 = " (vendoritemmaster.isDelete=0 OR vendoritemmaster.isDelete IS NULL) and (vendoritemmaster.menuSubCategoryCode is Null or vendoritemmaster.menuSubCategoryCode='')";
					$like2 = array();
					$itemRecords = $this->GlobalModel->selectQuery($orderColumns2, $tableName2, $condition2, $orderBy2, $join2, $joinType2, $like2, "", "", $groupByColumn2, $extraCondition2);
					//echo $this->db->last_query();	
					if ($itemRecords) {
						foreach ($itemRecords->result_array() as $r) {
							$vendorItemCode = $r['code'];
							$CCRecordsAddon = $this->GlobalModel->selectQuery('customizedcategory.*', 'customizedcategory', array('customizedcategory.vendorItemCode' => $vendorItemCode, 'customizedcategory.categoryType' => 'addon'));
							if ($CCRecordsAddon) {
								foreach ($CCRecordsAddon->result_array() as $ccra) {
									$customizedCategoryCode = $ccra['code'];
									$categoryTitle = $ccra['categoryTitle'];
									$CCRecordsAddonLine = $this->GlobalModel->selectQuery('customizedcategorylineentries.*', 'customizedcategorylineentries', array('customizedcategorylineentries.customizedCategoryCode' => $customizedCategoryCode));
									$addonCustomizedCategoryArray = array();
									if ($CCRecordsAddonLine) {
										$addonCustomizedCategoryArray = array();
										foreach ($CCRecordsAddonLine->result_array() as $ccraL) {
											$subCategoryTitle = $ccraL['subCategoryTitle'];
											$price = $ccraL['price'];
											$addonCustomizedCategoryArray[] = array(
												"lineCode" => $ccraL['code'],
												"subCategoryTitle" => $subCategoryTitle,
												"price" => $price,
											);
										}
									}
									$addonArray[] = ['addonTitle' => $categoryTitle, 'addonCode' => $customizedCategoryCode, 'addonList' => $addonCustomizedCategoryArray];
								}
							}

							$CCRecordsChoice = $this->GlobalModel->selectQuery('customizedcategory.*', 'customizedcategory', array('customizedcategory.vendorItemCode' => $vendorItemCode, 'customizedcategory.categoryType' => 'choice'));
							if ($CCRecordsChoice) {
								foreach ($CCRecordsChoice->result_array() as $ccrc) {
									$customizedCategoryCode = $ccrc['code'];
									$categoryTitle = $ccrc['categoryTitle'];
									$CCRecordsChoiceLine = $this->GlobalModel->selectQuery('customizedcategorylineentries.*', 'customizedcategorylineentries', array('customizedcategorylineentries.customizedCategoryCode' => $customizedCategoryCode));
									$choiceCustomizedCategoryArray = array();
									if ($CCRecordsChoiceLine) {
										$choiceCustomizedCategoryArray = array();
										foreach ($CCRecordsChoiceLine->result_array() as $ccrcL) {
											$subCategoryTitle = $ccrcL['subCategoryTitle'];
											$price = $ccrcL['price'];
											$choiceCustomizedCategoryArray[] = array(
												"lineCode" => $ccrcL['code'],
												"subCategoryTitle" => $subCategoryTitle,
												"price" => $price,
											);
										}
									}
									$choiceArray[] = ['choiceTitle' => $categoryTitle, 'choiceCode' => $customizedCategoryCode, 'choiceList' => $choiceCustomizedCategoryArray];
								}
							}

							$path = "nophoto";
							if ($r['itemPhoto'] != "") {
								$filepath = 'partner/uploads/' . $r['vendorCode'] . '/vendoritem/' . $r['itemPhoto'];
								if(file_exists($filepath))  $path =  'partner/uploads/' . $r['vendorCode'] . '/vendoritem/' . $r['itemPhoto'];
							}
							$mainitemArray[] = array(
								"vendorCode" => $getData['vendorCode'],
								"itemCode" => $r['code'],
								"itemName" => $r['itemName'],
								"itemDescription" => $r['itemDescription'],
								"salePrice" => $r['salePrice'],
								"itemPhoto" => $path,
								"vendorName" => $r['entityName'],
								"vendorIsServiceable"=>$r['vendorIsServiceable'],
								"isServiceable" => $r['itemActiveStatus'],
								"cuisineType" => $r['cuisineType'],
								"isActive" => $r['isActive'],
								"maxOrderQty" => $r['maxOrderQty'],
								"itemPackagingPrice" => $r['itemPackagingPrice'],
								"addons" => $addonArray,
								"choice" => $choiceArray,
							);
							$maincount++;
						}
					}

					$subCategoryItemArray = array();

					$tableName3 = "menusubcategory";
					$orderColumns3 = array("menusubcategory.*");
					$condition3 = array('menusubcategory.isActive' => 1, "menusubcategory.menuCategoryCode" => $catCode);
					$orderBy3 = array('menusubcategory' . '.id' => 'ASC');
					$subCateRecords = $this->GlobalModel->selectQuery($orderColumns3, $tableName3, $condition3, $orderBy3);
					if ($subCateRecords) {
						$subcount	= sizeof($subCateRecords->result());
						foreach ($subCateRecords->result_array() as $subrow) {
							$subCategoryCode = $subrow['code'];
							$subCategoryName = $subrow['menuSubCategoryName'];

							$tableName4 = "vendoritemmaster";
							$orderColumns4 = array("vendoritemmaster.*,vendor.entityName,vendor.isServiceable as vendorIsServiceable");
							$condition4 = array('vendoritemmaster.isActive' => 1, "vendoritemmaster.vendorCode" => $getData['vendorCode'], "vendoritemmaster.menuSubCategoryCode" => $subCategoryCode, "vendoritemmaster.isAdminApproved" => 1);
							$orderBy4 = array('vendoritemmaster' . '.id' => 'DESC');
							$joinType4 = array("vendor" => "inner", "menusubcategory" => "inner");
							$join4 = array("vendor" => "vendoritemmaster.vendorCode=vendor.code", "menusubcategory" => "vendoritemmaster.menuSubCategoryCode=menusubcategory.code");
							$groupByColumn4 = array();
							$extraCondition4 = " (vendoritemmaster.isDelete=0 OR vendoritemmaster.isDelete IS NULL)";
							$like4 = array();
							$Records = $this->GlobalModel->selectQuery($orderColumns4, $tableName4, $condition4, $orderBy4, $join4, $joinType4, $like4, "", "", $groupByColumn4, $extraCondition4);
							if ($Records) {
								$itemArray = array();
								$count = sizeof($Records->result_array());
								foreach ($Records->result_array() as $r) {

									$vendorItemCode = $r['code'];
									$CCRecordsAddon = $this->GlobalModel->selectQuery('customizedcategory.*', 'customizedcategory', array('customizedcategory.vendorItemCode' => $vendorItemCode, 'customizedcategory.categoryType' => 'addon'));
									if ($CCRecordsAddon) {
										foreach ($CCRecordsAddon->result_array() as $ccra) {
											$customizedCategoryCode = $ccra['code'];
											$categoryTitle = $ccra['categoryTitle'];
											$CCRecordsAddonLine = $this->GlobalModel->selectQuery('customizedcategorylineentries.*', 'customizedcategorylineentries', array('customizedcategorylineentries.customizedCategoryCode' => $customizedCategoryCode));
											$addonCustomizedCategoryArray = array();
											if ($CCRecordsAddonLine) {
												foreach ($CCRecordsAddonLine->result_array() as $ccraL) {
													$subCategoryTitle = $ccraL['subCategoryTitle'];
													$price = $ccraL['price'];
													$addonCustomizedCategoryArray[] = array(
														"lineCode" => $ccraL['code'],
														"subCategoryTitle" => $subCategoryTitle,
														"price" => $price,
													);
												}
											}
											$addonArray[] = ['addonTitle' => $categoryTitle, 'addonCode' => $customizedCategoryCode, 'addonList' => $addonCustomizedCategoryArray];
										}
									}

									$CCRecordsChoice = $this->GlobalModel->selectQuery('customizedcategory.*', 'customizedcategory', array('customizedcategory.vendorItemCode' => $vendorItemCode, 'customizedcategory.categoryType' => 'choice'));
									if ($CCRecordsChoice) {
										foreach ($CCRecordsChoice->result_array() as $ccrc) {
											$customizedCategoryCode = $ccrc['code'];
											$categoryTitle = $ccrc['categoryTitle'];
											$CCRecordsChoiceLine = $this->GlobalModel->selectQuery('customizedcategorylineentries.*', 'customizedcategorylineentries', array('customizedcategorylineentries.customizedCategoryCode' => $customizedCategoryCode));
											$choiceCustomizedCategoryArray = array();
											if ($CCRecordsChoiceLine) {
												foreach ($CCRecordsChoiceLine->result_array() as $ccrcL) {
													$subCategoryTitle = $ccrcL['subCategoryTitle'];
													$price = $ccrcL['price'];
													$choiceCustomizedCategoryArray[] = array(
														"lineCode" => $ccrcL['code'],
														"subCategoryTitle" => $subCategoryTitle,
														"price" => $price,
													);
												}
											}
											$choiceArray[] = ['choiceTitle' => $categoryTitle, 'choiceCode' => $customizedCategoryCode, 'choiceList' => $choiceCustomizedCategoryArray];
										}
									} 
									$path = "nophoto";
									if ($r['itemPhoto'] != "") {
										$filepath = 'partner/uploads/' . $r['vendorCode'] . '/vendoritem/' . $r['itemPhoto'];
										if(file_exists($filepath))  $path =  'partner/uploads/' . $r['vendorCode'] . '/vendoritem/' . $r['itemPhoto'];
									}
									$itemArray[] = array(
										"vendorCode" => $getData['vendorCode'],
										"itemCode" => $r['code'],
										"itemName" => $r['itemName'],
										"itemDescription" => $r['itemDescription'],
										"salePrice" => $r['salePrice'],
										"itemPhoto" => $path,
										"vendorName" => $r['entityName'],
										"vendorIsServiceable"=>$r['vendorIsServiceable'],
										"cuisineType" => $r['cuisineType'],
										"isActive" => $r['isActive'],
										"isServiceable" => $r['itemActiveStatus'],
										"maxOrderQty" => $r['maxOrderQty'],
										"itemPackagingPrice" => $r['itemPackagingPrice'],
										"addons" => $addonArray,
										"choice" => $choiceArray,
									);
									$maincount++;
								}

								$subCategoryItemArray[] = array("subCategoryCode" => $subCategoryCode, "subCategoryName" => $subCategoryName, "count" => $count, "itemList" => $itemArray);
							}
						}
					}
					if ($maincount > 0) {
						$data[] = array("menuCategoryCode" => $catCode, "count" => $maincount, "menuCategoryName" => $catName, "itemList" => $mainitemArray, "subCategoryList" => $subCategoryItemArray);
					}
				}
				$response['menuItemList'] = $data;
				return $this->response(array("status" => "200", "message" => 'Data Found', "result" => $response), 200);
			} else {
				return $this->response(array("status" => "300", "message" => 'No Data Found'), 200);
			}
		}
	}
	
	public function addToCart_post()
	{
		$postData = $this->post();
		if ($postData["vendorCode"] && $postData["clientCode"] != '' && $postData["vendorItemCode"] != '' && $postData["customizedCategoryLineCode"] != '' && $postData["quantity"] != '') {
			$vendorCode = $postData["vendorCode"];
			$clientCode = $postData["clientCode"];
			$vendorItemCode = $postData["vendorItemCode"];
			$customizedCategoryLineCode = $postData["customizedCategoryLineCode"];
			$quantity = $postData["quantity"];

			$checkData = $this->GlobalModel->selectQuery('addonclientcarts.*', 'addonclientcarts', array('addonclientcarts.vendorCode' => $vendorCode, 'addonclientcarts.clientCode' => $clientCode, 'addonclientcarts.vendorItemCode' => $vendorItemCode));
			if ($checkData) {
				$cartCode = $checkData->result_array()[0]['code'];
				$checkDataLine = $this->GlobalModel->selectQuery('addonclientcartslineentries.*', 'addonclientcartslineentries', array('addonclientcartslineentries.cartCode' => $cartCode, 'addonclientcartslineentries.customizedCategoryLineCode' => $customizedCategoryLineCode));
				if ($checkDataLine) {
					$cartLineCode = $checkDataLine->result_array()[0]['code'];
					$updateQuantityData = array('quantity' => $quantity);
					$resultUpdate = $this->GlobalModel->doEdit($updateQuantityData, "addonclientcartslineentries", $cartLineCode);
					if ($resultUpdate != 'false') {
					} else {
					}
				} else {
					$dataLine = [
						"cartCode" => $cartCode,
						"customizedCategoryLineCode" => $customizedCategoryLineCode,
						"quantity" => $quantity,
						"isActive" => 1,
					];
					$insertResultLine = $this->GlobalModel->addNew($dataLine, 'addonclientcartslineentries', 'CARLL');
					if ($insertResultLine) {
						return $this->response(array("status" => "200", "message" => "Successfully added to cart."), 200);
					} else {
					}
				}
			} else {
				$data = [
					"clientCode" => $clientCode,
					"vendorCode" => $vendorCode,
					"vendorItemCode" => $vendorItemCode,
					"isActive" => 1,
				];
				$insertResult = $this->GlobalModel->addNew($data, 'addonclientcarts', 'CART');
				if ($insertResult) {
					$dataLine = [
						"cartCode" => $insertResult,
						"customizedCategoryLineCode" => $customizedCategoryLineCode,
						"quantity" => $quantity,
						"isActive" => 1,
					];
					$insertResultLine = $this->GlobalModel->addNew($dataLine, 'addonclientcartslineentries', 'CARLL');
					if ($insertResultLine) {
						return $this->response(array("status" => "200", "message" => "Successfully added to cart."), 200);
					} else {
						return $this->response(array("status" => "300", "message" => "Failed to add item to cart"), 200);
					}
				} else {
					return $this->response(array("status" => "300", "message" => "User not registered. Please register user before adding product to your cart."), 200);
				}
			}
		} else {
			return $this->response(array("status" => "400", "message" => "Required fields not found."), 400);
		}
	}
	
	public function getVendorByCode_post()
	{
		$postData = $this->post();

		if ($postData["vendorCode"] != "") {
			$vendorCode = $postData["vendorCode"];
			$checkData = $this->GlobalModel->selectQuery('vendor.*', 'vendor', array('vendor.code' => $vendorCode));
			if ($checkData) {
				$path = "nophoto";
				$entityImage = $checkData->result_array()[0]['entityImage'];
				$addressCode = $checkData->result_array()[0]['addressCode'];

				$place = "";
				$checkPlace = $this->GlobalModel->selectQuery('customaddressmaster.place', 'customaddressmaster', array('customaddressmaster.code' => $addressCode));
				if ($checkPlace) {
					$place = $checkPlace->result_array()[0]['place'];
				}

				if ($entityImage != "") {
					$path = base_url() . 'uploads/vendor/' . $vendorCode . '/' . $entityImage;
				}
				$response = $checkData->result_array()[0];
				$response['entityImage'] = $path;
				$response['address'] = $place;
				$response['vendors'] = $checkData->result_array()[0];
				$dayofweek= strtolower(date('l'));
				
				$timeData = $this->GlobalModel->selectQuery("vendorhours.code as hourCode,time_format(vendorhours.fromTime,'%h:%i %p') as fromTime,time_format(vendorhours.toTime,'%h:%i %p') as toTime","vendorhours",array("vendorhours.vendorCode"=>$postData["vendorCode"] ,"vendorhours.weekDay"=>$dayofweek));
				if($timeData) {
				    $response['vendorHours'] = $timeData->result_array();
				} else {
				    $response['vendorHours'] = array();
				}

				return $this->response(array("status" => "200", "message" => 'Data Found', "result" => $response), 200);
			} else {
				$this->response(array("status" => "300", "message" => "No Data Found."), 200);
			}
		} else {
			return $this->response(array("status" => "400", "message" => "Required fields not found."), 400);
		}
	}
	
	public function placeOrderVendor_post()
	{
		$postData = $this->post();

		if ($postData['packagingType'] != "" && $postData["vendorCode"] != "" && $postData["clientCode"] != '' && $postData["paymentMode"] != '' && $postData["address"] != '') {
			$vendorCode = $postData["vendorCode"];
			$clientCode = $postData["clientCode"];
			if(isset($postData['coupanCode'])){
				$coupanCode = $postData["coupanCode"];
			} else {
				$coupanCode = "";
			}		
			$discount = $postData["discount"];
			$subTotal = $postData["subTotal"];
			$grandTotal = $postData["grandTotal"];
			$shippingCharges = $postData["shippingCharges"];
			$paymentStatus = 'PNDG';
			$paymentMode = $postData["paymentMode"];
			// $orderStatus = $postData['orderStatus'];
			$address = $postData['address'];
			$deliveryBoyCode = '';
			$tax = $postData["tax"];
			$clientNote = $postData["clientNote"];
			$flat = $postData["flat"];
			$landmark = $postData["landmark"];
			$latitude = $postData["lat"];
			$longitude = $postData["lang"];
			$addressType = $postData['addressType'];
			$phone = $postData['phone'];
			if (isset($postData['totalPackgingCharges'])) {
				$totalPackgingCharges = $postData['totalPackgingCharges'];
			} else {
				$totalPackgingCharges = 0;
			}
			$packagingType = $postData['packagingType'];
			$currTime = date('H:i:00');
			$dayofweek = strtolower(date('l'));
			$conditionTime = array("vendorhours.vendorCode"=>$postData["vendorCode"],"vendorhours.weekDay"=>$dayofweek);
			$extrconditionTime = " ('".$currTime."' between vendorhours.fromTime and vendorhours.toTime)";
			$timeResult = $this->GlobalModel->selectQuery("vendorhours.vendorCode","vendorhours",$conditionTime,array(),array(),array(),array(),"","",array(),$extrconditionTime);
			//$this->db->last_query();
			if($timeResult){
				$serviceable = 1;
			} else {
				$serviceable = 0;
			}
			if($serviceable==1){
				$data = [
					"vendorCode" => $vendorCode,
					"clientCode" => $clientCode,
					"coupanCode" => $coupanCode,
					"addressType" => $addressType,
					"discount" => $discount,
					"subTotal" => $subTotal,
					"grandTotal" => $grandTotal,
					"shippingCharges" => $shippingCharges,
					"paymentStatus" => $paymentStatus,
					"paymentMode" => $paymentMode,
					"orderStatus" => 'PND',
					"clientNote" => $clientNote,
					"address" => $address,
					'phone' => $phone,
					"tax" => $tax,
					"flat" => $flat,
					"landmark" => $landmark,
					"latitude" => $latitude,
					"longitude" => $longitude,
					"totalPackgingCharges" => $totalPackgingCharges,
					"packagingType" => $packagingType,
					"isActive" => 1,
				];
				$orderCode = 'ORDER' . rand(99, 99999);
				$insertResult = $this->GlobalModel->addWithoutYear($data, 'vendorordermaster', $orderCode);
				if ($insertResult) {
					$cart = json_decode($postData["cart"], true);
					if (!empty($cart)) {
						foreach ($cart as $a) {
							$addons = $a['addons'];
							$addonsCode = $a['addonsCode'];
							$price = $a['price'];
							$quantity = $a['quantity'];
							$itemCode = $a['itemCode'];
							if ($packagingType == "PRODUCT") {
								$itemPackagingCharges = 0;
							} else {
								if (isset($a['itemPackagingCharges']) && $a['itemPackagingCharges'] != "") {
									$itemPackagingCharges = $a['itemPackagingCharges'];
								} else {
									$itemPackagingCharges = 0;
								}
							}
							$dataLine = array(
								"orderCode" => $insertResult,
								"vendorItemCode" => $itemCode,
								"addons" => $addons,
								"addonsCode" => $addonsCode,
								"quantity" => $quantity,
								"priceWithQuantity" => $price,
								'itemPackagingCharges' => $itemPackagingCharges,
								"isActive" => 1
							);
							$orderLineResult = $this->GlobalModel->addWithoutYear($dataLine, 'vendororderlineentries', 'ORDL');
						}
						
						if ($coupanCode !="") {
							if ((isset($postData['vendorOfferCode'])) && $postData['vendorOfferCode'] != "") {
								if ((isset($postData['decidedExisitingLimit'])) && $postData['decidedExisitingLimit'] > 1) {
									$this->use_User_Coupon("update", $postData['vendorOfferCode'], $coupanCode, $clientCode, $postData['decidedExisitingLimit'], $vendorCode);
								} else {
									$this->use_User_Coupon("add", $postData['vendorOfferCode'], $coupanCode, $clientCode, $postData['decidedExisitingLimit'], $vendorCode);
								}
							}
						}
						$dataBookLine = array(
							"orderCode" => $insertResult,
							"statusPutCode" => $clientCode,
							"statusLine" => 'PND',
							"reason" => 'Booked by Client',
							"statusTime" => date("Y-m-d h:i:s"),
							"isActive" => 1
						);
						$bookLineResult = $this->GlobalModel->addWithoutYear($dataBookLine, 'bookorderstatuslineentries', 'BOL'); 
						if ($insertResult) {
							return $this->response(array("status" => "200", "message" => "Order Placed Successfully.. Your OrderID is " . $insertResult), 200);
						} else {
							$this->response(array("status" => "400", "message" => " Opps...! Something went wrong please try again."), 200);
						}
					} else {
						$this->response(array("status" => "400", "message" => " Opps...! empty cart."), 200);
					}
				} else {
					$this->response(array("status" => "400", "message" => " Opps...! Something went wrong please try again."), 200);
				}
			} else {
				$this->response(array("status" => "400", "message" => "Restaurant has been closed and shall not accept the order! Sorry for your inconvenience."), 200);
			}
		} else {
			return $this->response(array("status" => "400", "message" => "Required fields not found."), 400);
		}
	}
	
	public function use_User_Coupon($actionType, $vendorOfferCode, $couponCode, $clientCode, $decidedExisitingLimit, $vendorCode)
	{
		if ($actionType == "add") {
			$data['vendorofferCode'] = $vendorOfferCode;
			$data['couponCode'] = $couponCode;
			$data['clientCode'] = $clientCode;
			$data['decidedExisitingLimit'] = $decidedExisitingLimit;
			$data['vendorCode'] = $vendorCode;
			$data['addID'] = $clientCode;
			$data['addIP'] = $_SERVER['REMOTE_ADDR'];
			$data['usedDate'] = date('Y-m-d H:i:s');
			$data['isActive'] = 1;
			$insert = $this->GlobalModel->addWithoutYear($data, 'couponusesdetail', 'CUDS');
			if ($insert != 'false') {
				return true;
			} else {
				return false;
			}
		} else {
			$data['decidedExisitingLimit'] = $decidedExisitingLimit;
			$data['editID'] = $clientCode;
			$data['editIP'] = $_SERVER['REMOTE_ADDR'];
			$data['usedDate'] = date('Y-m-d H:i:s');

			$where['clientCode'] = $clientCode;
			$where['vendorCode'] = $vendorCode;
			$where['couponCode'] = $couponCode;
			$where['vendorofferCode'] = $vendorofferCode;

			$this->db->where($where);
			$this->db->update('couponusesdetail', $data);
			if ($this->db->affected_rows() > 0) {
				return true;
			} else {
				return false;
			}
		}
	}
	
	public function getVendorOrderList_post()
	{
		$postData = $this->post();
		if ($postData["clientCode"] != '') {
			$clientCode = $postData["clientCode"];
			$tableName = "vendorordermaster";
			$orderColumns = array("vendorordermaster.vendorCode,vendorordermaster.totalPackgingCharges,vendorordermaster.packagingType,vendorordermaster.addressType,vendorordermaster.code as orderCode,vendorordermaster.tax,vendorordermaster.discount,vendorordermaster.subTotal,vendorordermaster.shippingCharges as deliveryCharges,vendorordermaster.paymentmode,vendorordermaster.address,vendorordermaster.grandTotal as orderTotalPrice,vendorordermaster.addDate as orderDate, vendororderstatusmaster.statusName as orderStatus, paymentstatusmaster.statusName as paymentStatus,vendor.entityName,vendor.address as pickUpAddress,vendor.cityCode,vendor.addressCode,citymaster.cityName,customaddressmaster.place,bookorderstatuslineentries.statusTime");
			$cond = array('vendorordermaster' . ".clientCode" => $clientCode,"vendorordermaster.isActive"=>1);
			$orderBy = array('vendorordermaster' . ".id" => 'DESC');
			$join = array('vendororderstatusmaster' => 'vendorordermaster' . '.orderStatus=' . 'vendororderstatusmaster' . '.statusSName', 'paymentstatusmaster' => 'vendorordermaster' . '.paymentStatus=' . 'paymentstatusmaster' . '.statusSName', "vendor" => "vendorordermaster.vendorCode=vendor.code", "citymaster" => "vendor.cityCode=citymaster.code", "customaddressmaster" => "vendor.addressCode=customaddressmaster.code","bookorderstatuslineentries" => "vendorordermaster.orderStatus=bookorderstatuslineentries.statusLine");
			$joinType = array('vendororderstatusmaster' => 'inner', 'paymentstatusmaster' => 'inner', "vendor" => "inner", "citymaster" => "left", "customaddressmaster" => "left","bookorderstatuslineentries" => "left");
			$extracondition=" bookorderstatuslineentries.orderCode = vendorordermaster.code";
			$resultQuery = $this->GlobalModel->selectQuery($orderColumns, $tableName, $cond, $orderBy, $join, $joinType, array(), array(), "", "",$extracondition);
			$imageArray = array();
			if ($resultQuery) {
				$clientOrderList = $resultQuery->result_array();
				$totalOrders = sizeof($clientOrderList);
				
				for ($i = 0; $i < $totalOrders; $i++) {
					$linetableName = "vendororderlineentries";
					$lineorderColumns = array("vendororderlineentries.vendorItemCode,vendororderlineentries.quantity,vendororderlineentries.priceWithQuantity as priceWithQuantity,vendororderlineentries.addons,vendororderlineentries.addonsCode,vendoritemmaster.itemName,vendororderlineentries.itemPackagingCharges");
					$linecond = array("vendororderlineentries.orderCode" => $clientOrderList[$i]['orderCode']);
					$lineorderBy = array('vendororderlineentries' . ".id" => 'ASC');
					$linejoin = array('vendoritemmaster' => 'vendororderlineentries.vendorItemCode=vendoritemmaster.code');
					$linejoinType = array('vendoritemmaster' => 'inner');
					$orderProductRes = $this->GlobalModel->selectQuery($lineorderColumns, $linetableName, $linecond, $lineorderBy, $linejoin, $linejoinType);
					$orderProductList = array();
					if ($orderProductRes) {
						$orderProductList = $orderProductRes->result_array();
					}
					
					$clientOrderList[$i]['orderedItems'] = $orderProductList;
				}
				$finalResult['orders'] = $clientOrderList;
				return $this->response(array("status" => "200", "totalOrders" => $totalOrders, "result" => $finalResult), 200);
			} else {
				return $this->response(array("status" => "300", "message" => "Data not found."), 200);
			}
		} else {
			return $this->response(array("status" => "400", "message" => " * are required field(s)."), 400);
		}
	}
	
// 	public function cancleVendorOrder_post()
// 	{
// 		$postData = $this->post();

// 		if ($postData["clientCode"] != '' && $postData["orderCode"] != '') {
// 			$clientCode = $postData["clientCode"];
// 			$orderCode = $postData["orderCode"];
// 			$timeStamp = date("Y-m-d h:i:s");

// 			$Result = $this->GlobalModel->selectQuery('vendorordermaster.*', 'vendorordermaster', array('vendorordermaster.code' => $orderCode));
// 			if ($Result) {
// 				$orderStatus = $Result->result_array()[0]['orderStatus'];
// 				$paymentMode = $Result->result_array()[0]['paymentMode'];

// 				if ($paymentMode == 'COD') {
// 					if ($orderStatus == 'PND') {
// 						$data = array('orderStatus' => 'CAN', 'editID' => $timeStamp, 'editDate' => $timeStamp);
// 						$resultUpdate = $this->GlobalModel->doEdit($data, 'vendorordermaster', $orderCode);
// 						if ($resultUpdate != 'false') {
// 							$dataBookLine = array(
// 								"orderCode" => $orderCode,
// 								"statusPutCode" => $clientCode,
// 								"statusLine" => 'CAN',
// 								"reason" => 'Order Cancelled By Client',
// 								"statusTime" => $timeStamp,
// 								"isActive" => 1
// 							);
// 							$bookLineResult = $this->GlobalModel->addWithoutYear($dataBookLine, 'bookorderstatuslineentries', 'BOL');
// 							return $this->response(array("status" => "200", "message" => "Order Cancelled Successfully"), 200);
// 						} else {
// 							return $this->response(array("status" => "300", "message" => "Unsuccessfull Order Cancel Please Try Again."), 200);
// 						}
// 					} else {
// 						$data = array('orderStatus' => 'CAN', 'editID' => $timeStamp, 'editDate' => $timeStamp);
// 						$resultUpdate = $this->GlobalModel->doEdit($data, 'vendorordermaster', $orderCode);
// 						if ($resultUpdate != 'false') {
// 							$Cdata = array('isCodEnabled' => 1);
// 							$clentDataUp = $this->GlobalModel->doEdit($Cdata, 'clientmaster', $clientCode);

// 							$dataBookLine = array(
// 								"orderCode" => $orderCode,
// 								"statusPutCode" => $clientCode,
// 								"statusLine" => 'CAN',
// 								"reason" => 'Order Cancelled By Client',
// 								"statusTime" => $timeStamp,
// 								"isActive" => 1
// 							);
// 							$bookLineResult = $this->GlobalModel->addWithoutYear($dataBookLine, 'bookorderstatuslineentries', 'BOL');
// 							return $this->response(array("status" => "200", "message" => "Order Cancelled Successfully"), 200);
// 						} else {
// 							return $this->response(array("status" => "300", "message" => "Unsuccessfull Order Cancel Please Try Again."), 200);
// 						}
// 					}
// 				} else {
// 					$data = array('orderStatus' => 'CAN', 'editID' => $timeStamp, 'editDate' => $timeStamp);
// 					$resultUpdate = $this->GlobalModel->doEdit($data, 'vendorordermaster', $orderCode);
// 					if ($resultUpdate != 'false') {
// 						$dataBookLine = array(
// 							"orderCode" => $orderCode,
// 							"statusPutCode" => $clientCode,
// 							"statusLine" => 'CAN',
// 							"reason" => 'Order Cancelled By Client',
// 							"statusTime" => $timeStamp,
// 							"isActive" => 1
// 						);
// 						$bookLineResult = $this->GlobalModel->addWithoutYear($dataBookLine, 'bookorderstatuslineentries', 'BOL');
// 						return $this->response(array("status" => "200", "message" => "Order Cancelled Successfully"), 200);
// 					} else {
// 						return $this->response(array("status" => "300", "message" => "Unsuccessfull Order Cancel Please Try Again."), 200);
// 					}
// 				}
// 			} else {
// 				return $this->response(array("status" => "300", "message" => "Unsuccessfull Order Cancel Please Try Again."), 200);
// 			}
// 		} else {
// 			return $this->response(array("status" => "400", "msg" => " * are required field(s)."), 400);
// 		}
// 	}
	
	public function getCouponList_post(){
		$postData = $this->post();
		if($postData['vendorCode']!=""){
			$today = date('Y-m-d');
			$condition = array('vendoroffer.vendorCode' => $postData['vendorCode'], 'vendoroffer.isActive' => 1, "vendoroffer.isAdminApproved" => 1);
			$extraCondition = "( '" . $today . "' between vendoroffer.startDate and vendoroffer.endDate)";
			$Result = $this->GlobalModel->selectQuery('vendoroffer.coupanCode as couponCode,vendoroffer.offerType,vendoroffer.discount,vendoroffer.minimumAmount,vendoroffer.perUserLimit,vendoroffer.termsAndConditions,vendoroffer.capLimit,vendoroffer.startDate,vendoroffer.endDate', 'vendoroffer', $condition, array(), array(), array(), array(), array(), "", "", $extraCondition);
			if ($Result) {
				$recordsCount = sizeof($Result->result_array());
				$data = array();
				foreach($Result->result_array() as $r)	{
					$data[] = $r;
				}
				$res['offersList'] = $data;
				return $this->response(array("status" => "200", "totalRecords" => $recordsCount, "message" => "Data found", "result" => $res), 200);
			} else {
				return $this->response(array("status" => "300", "msg" => "No Data found"), 200);
			}
		} else {
			return $this->response(array("status" => "400", "msg" => " * are required field(s)."), 400);
		}
	}
	
	public function getCoupanDetails_post()
	{
		$postData = $this->post();
		if ($postData['clientCode'] != "" && $postData["coupanCode"] != "" && $postData['vendorCode'] != "" && $postData['cartAmount'] != "") {
			$couponCode = $postData["coupanCode"];
			$vendorCode = $postData["vendorCode"];
			$cartAmount = $postData['cartAmount'];
			$clientCode = $postData['clientCode'];

			$today = date('Y-m-d');
			$limit = 0;

			$condition = array('vendoroffer.coupanCode' => $couponCode, 'vendoroffer.vendorCode' => $vendorCode, 'vendoroffer.isActive' => 1, "vendoroffer.perUserLimit>=" => 1, "vendoroffer.isAdminApproved" => 1);
			$extraCondition = "( '" . $today . "' between vendoroffer.startDate and vendoroffer.endDate)";
			$Result = $this->GlobalModel->selectQuery('vendoroffer.*', 'vendoroffer', $condition, array(), array(), array(), array(), array(), "", "", $extraCondition);
			$query=$this->db->last_query();
			if ($Result) {
				$r = $Result->result_array()[0];
				$vendorOfferCode = $r['code'];
				$minimumAmount = $r['minimumAmount'];
				$offerType = $r['offerType'];
				$couponCode = $r['coupanCode'];
				$discount = $r['discount'];
				$capLimit = $r['capLimit'];
				$termsAndConditions = $r['termsAndConditions'];
				$perUserLimit = $r['perUserLimit'];

				$ar['vendorOfferCode'] = $vendorOfferCode;
				$ar['minimumAmount'] = $minimumAmount;
				$ar['offerType'] = $offerType;
				$ar['couponCode'] = $couponCode;
				$ar['discount'] = $discount;
				$ar['capLimit'] = $capLimit;
				$ar['termsAndConditions'] = $termsAndConditions;
				$ar['perUserLimit'] = $perUserLimit;

				$data['couponDetails'] = $ar;

				if ($minimumAmount <= $cartAmount) {
					//check used or not 
					$condition1 = array(
						'couponusesdetail.vendorOfferCode' => $vendorOfferCode,
						'couponusesdetail.couponCode' => $couponCode,
						'couponusesdetail.clientCode' => $clientCode,
						"couponusesdetail.vendorCode" => $vendorCode
					);
					$clientUseCouponResult = $this->GlobalModel->selectQuery('couponusesdetail.*', 'couponusesdetail', $condition1);
					//$q=$this->db->last_query();
					if ($clientUseCouponResult) {
						$useCoupon = $clientUseCouponResult->result_array()[0];
						$userLimit = $useCoupon['decidedExisitingLimit'];
						if ($userLimit < $perUserLimit) {
							$nextLimit = $userLimit + 1;
							return $this->response(array("status" => "200", "nextLimit" => $nextLimit, "message" => $couponCode." and save every time you order", "result" => $data), 200);
						} else {
							return $this->response(array("status" => "300", "message" => "Coupon Already Used"), 200);
						}
					} else {
						//not single use return coupon
						return $this->response(array("status" => "200", "nextLimit" => 1, "message" => $couponCode." and save every time you order", "result" => $data), 200);
					}
				} else {
					return $this->response(array("status" => "300", "message" => "Amount should be greater than " . $minimumAmount), 200);
				}
			} else {
				return $this->response(array("status" => "300", "message" => "Invalid Coupon Code."), 200);
			}
		} else {
			return $this->response(array("status" => "400", "msg" => " * are required field(s)."), 400);
		}
	}
	public function getVendorItemsOnSearch_post()
	{
		$postData =  $this->post();
		if ($postData['search'] != "" && $postData['cityCode'] != "") { 
			$table1 = "vendor";
			$cityCode=$postData['cityCode'];
			$orderColumns1 = "vendor.*,entitycategory.entityCategoryName,citymaster.cityName,customaddressmaster.place";
			$condition1['vendor.isActive'] = 1; 
			$condition1['vendor.cityCode'] = $cityCode;
			$orderBy1["vendor.isServiceable"] = 'DESC';
			$orderBy1["vendor.id"] = 'ASC';
			$join1 = array(
				"entitycategory" => "vendor.entitycategoryCode=entitycategory.code",
				"vendorcuisinelineentries"=>"vendor.`code` = vendorcuisinelineentries.vendorCode",
				"citymaster"=>"vendor.cityCode=citymaster.code",
				"customaddressmaster"=>"vendor.addressCode=customaddressmaster.code"
			);
			$joinType1 = array('entitycategory' => 'inner',"vendorcuisinelineentries"=>"inner","citymaster"=>"left","customaddressmaster"=>"left");
			$like1 = array("vendor.entityName" => $postData['search'] . '~both');
			$limit1 = '10';
			$offset1 = '';
			if(isset($post['offset'])){
				if($postData['offset']!="") $offset1 = $postData['offset'];
			}
			$groupBy1 = array("vendor.code");
			$extraCondition1 = "";
			$Records = $this->GlobalModel->selectQuery($orderColumns1, $table1, $condition1, $orderBy1, $join1, $joinType1, $like1, $limit1, $offset1, $groupBy1, $extraCondition1);
			if ($Records) { 
				$data = array();
				foreach ($Records->result_array() as $r) {
					/* get cuisines served by vendor*/
					$cuisinesList = "";
					$table1 = "cuisinemaster";
					$orderColumns1 = "GROUP_CONCAT(cuisinemaster.cuisineName) as cuisines";
					$condition1 = array("vendorcuisinelineentries.vendorCode" => $r['code'], "cuisinemaster.isActive" => 1);
					$orderBy1 = array();
					$join1 = array("vendorcuisinelineentries" => "cuisinemaster.`code` = vendorcuisinelineentries.cuisineCode");
					$joinType1 = array("vendorcuisinelineentries" => "inner");
					$cuisineRecords = $this->GlobalModel->selectQuery($orderColumns1, $table1, $condition1, $orderBy1, $join1, $joinType1);
					if ($cuisineRecords) {
						$cuisinesList = $cuisineRecords->result_array()[0]['cuisines'];
					} 
					$vendorar['code'] = $r['code'];
					$vendorar['entityName'] = $r['entityName'];
					$vendorar['firstName'] = $r['firstName'];
					$vendorar['middleName'] = $r['middleName'];
					$vendorar['lastName'] = $r['lastName'];
					$vendorar['latitude'] = $r['latitude'];
					$vendorar['longitude'] = $r['longitude'];
					if ($r['entityImage'] != "") {
						$path = 'uploads/vendor/' . $r['code'] . '/' . $r['entityImage'];
						if (file_exists($path)) $vendorar['entityImage'] = $path;
						else $vendorar['entityImage']  = "noimage";
					} else $vendorar['entityImage']  = "noimage";
	
					$vendorar['address'] = $r['address'];
					$vendorar['packagingType'] = $r['packagingType'];
					$vendorar['cartPackagingPrice'] = $r['cartPackagingPrice'];
					$vendorar['gstApplicable'] = $r['gstApplicable'];
					$vendorar['gstPercent'] = $r['gstPercent'];
					$vendorar['ownerContact'] = $r['ownerContact'];
					$vendorar['entityContact'] = $r['entityContact'];
					$vendorar['email'] = $r['email'];
					$vendorar['entityCategoryName'] = $r['entityCategoryName'];
					$vendorar['fssaiNumber'] = $r['fssaiNumber'];
					$vendorar['cityName'] = $r['cityName'];
					$vendorar['aresName'] = $r['place'];
					$vendorar['cuisinesList'] = $cuisinesList;
					$vendorar['isServiceable']=$r['isServiceable'];
					$data[] = $vendorar;
				} 						 
				$res['vendorList'] = $data;
			} else {
				$res['vendorList'] = array();
			}
		 
			$table = "vendoritemmaster";
			$orderColumns = "vendoritemmaster.vendorCode,vendoritemmaster.maxOrderQty,vendoritemmaster.itemPackagingPrice,vendor.entityName,vendoritemmaster.code,vendoritemmaster.itemName,vendoritemmaster.itemDescription,vendoritemmaster.cuisineType,vendoritemmaster.salePrice,vendoritemmaster.itemPhoto,vendor.isServiceable as vendorIsServiceable,vendoritemmaster.itemActiveStatus";
			$condition['vendoritemmaster.isActive'] = 1;
			$condition['vendoritemmaster.isAdminApproved'] = 1;
			$condition['vendor.isActive'] = 1;
			$condition['vendor.cityCode'] = $cityCode;
			//$condition['vendor.isServiceable'] = 1;
			//$condition['vendoritemmaster.itemActiveStatus'] = 1;
			$orderBy["vendor.isServiceable"] = 'DESC';
			$orderBy["vendoritemmaster.id"] = 'ASC';
			$join = array("vendor" => "vendoritemmaster.vendorCode=vendor.code");
			$joinType = array('vendor' => 'inner');
			$like = array("vendoritemmaster.itemName" => $postData['search'] . '~both',"vendor.entityName" => $postData['search'] . '~both');
			$limit = '10';
			$offset = '';
			if(isset($post['offset'])){
				if($postData['offset']!="") $offset = $postData['offset'];
			}
			$groupBy = array();
			$extraCondition = "";
			$Records = $this->GlobalModel->selectQuery($orderColumns, $table, $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupBy, $extraCondition);
			if ($Records) {
				$data = array();
				foreach ($Records->result_array() as $r) {
					$vendorItemCode = $r['code'];
					$addonArray = [];
					$choiceArray = [];
					$CCRecordsAddon = $this->GlobalModel->selectQuery('customizedcategory.*', 'customizedcategory', array('customizedcategory.vendorItemCode' => $vendorItemCode, 'customizedcategory.categoryType' => 'addon'));
					if ($CCRecordsAddon) {
						foreach ($CCRecordsAddon->result_array() as $ccra) {
							$customizedCategoryCode = $ccra['code'];
							$categoryTitle = $ccra['categoryTitle'];
							$CCRecordsAddonLine = $this->GlobalModel->selectQuery('customizedcategorylineentries.*', 'customizedcategorylineentries', array('customizedcategorylineentries.customizedCategoryCode' => $customizedCategoryCode));
							$addonCustomizedCategoryArray = array();
							if ($CCRecordsAddonLine) {
								foreach ($CCRecordsAddonLine->result_array() as $ccraL) {
									$subCategoryTitle = $ccraL['subCategoryTitle'];
									$price = $ccraL['price'];
									$addonCustomizedCategoryArray[] = array(
										"lineCode" => $ccraL['code'],
										"subCategoryTitle" => $subCategoryTitle,
										"price" => $price,
									);
								}
							}
							$addonArray[] = ['addonTitle' => $categoryTitle, 'addonCode' => $customizedCategoryCode, 'addonList' => $addonCustomizedCategoryArray];
						}
					}

					$CCRecordsChoice = $this->GlobalModel->selectQuery('customizedcategory.*', 'customizedcategory', array('customizedcategory.vendorItemCode' => $vendorItemCode, 'customizedcategory.categoryType' => 'choice'));
					if ($CCRecordsChoice) {
						foreach ($CCRecordsChoice->result_array() as $ccrc) {
							$customizedCategoryCode = $ccrc['code'];
							$categoryTitle = $ccrc['categoryTitle'];
							$CCRecordsChoiceLine = $this->GlobalModel->selectQuery('customizedcategorylineentries.*', 'customizedcategorylineentries', array('customizedcategorylineentries.customizedCategoryCode' => $customizedCategoryCode));
							$choiceCustomizedCategoryArray = array();
							if ($CCRecordsChoiceLine) {
								foreach ($CCRecordsChoiceLine->result_array() as $ccrcL) {
									$subCategoryTitle = $ccrcL['subCategoryTitle'];
									$price = $ccrcL['price'];
									$choiceCustomizedCategoryArray[] = array(
										"lineCode" => $ccrcL['code'],
										"subCategoryTitle" => $subCategoryTitle,
										"price" => $price,
									);
								}
							}
							$choiceArray[] = ['choiceTitle' => $categoryTitle, 'choiceCode' => $customizedCategoryCode, 'choiceList' => $choiceCustomizedCategoryArray];
						}
					}

					$itemAr['vendorCode'] = $r['vendorCode'];
					$itemAr['vendorName'] = $r['entityName'];
					$itemAr['itemCode'] = $r['code'];
					$itemAr['itemName'] = $r['itemName'];
					$itemAr['itemDescription'] = $r['itemDescription'];
					$itemAr['cuisineType'] = $r['cuisineType'];
					$itemAr['salePrice'] = $r['salePrice'];
					$itemAr['maxOrderQty'] = $r['maxOrderQty'];
					$itemAr['itemPackagingPrice'] = $r['itemPackagingPrice'];
					$itemAr['addons'] = $addonArray;
					$itemAr['choice'] = $choiceArray;
					$itemAr['vendorIsServiceable']=$r['vendorIsServiceable'];
					$itemAr['isServiceable'] = $r['itemActiveStatus'];

					$table1 = 'vendor';
					$orderColumns1 = "vendor.*,entitycategory.entityCategoryName,citymaster.cityName,customaddressmaster.place";
					$condition1 = array("vendor.code" => $r['vendorCode']);
					$orderBy1 = array("vendor.id" => "ASC");
					$join1 = array("entitycategory" => "vendor.entitycategoryCode = entitycategory.`code`", "vendorcuisinelineentries" => "vendor.`code`=vendorcuisinelineentries.`vendorCode`", "citymaster" => "vendor.cityCode = citymaster.`code`", "customaddressmaster" => "vendor.`addressCode` = customaddressmaster.code ");
					$joinType1 = array("entitycategory" => 'inner', "vendorcuisinelineentries" => 'inner', "citymaster" => 'inner', "customaddressmaster" => "inner");
					$vendorResult = $this->GlobalModel->selectQuery($orderColumns1, $table1, $condition1, $orderBy1, $join1, $joinType1);
					if ($vendorResult) {
						$data1 = array();
						$r1 = $vendorResult->result_array()[0];

						/* get cuisines served by vendor*/
						$cuisinesList = "";
						$table11 = "cuisinemaster";
						$orderColumns11 = "GROUP_CONCAT(cuisinemaster.cuisineName) as cuisines";
						$condition11 = array("vendorcuisinelineentries.vendorCode" => $r1['code'], "cuisinemaster.isActive" => 1);
						$orderBy11 = array();
						$join11 = array("vendorcuisinelineentries" => "cuisinemaster.`code` = vendorcuisinelineentries.cuisineCode");
						$joinType11 = array("vendorcuisinelineentries" => "inner");
						$cuisineRecords = $this->GlobalModel->selectQuery($orderColumns11, $table11, $condition11, $orderBy11, $join11, $joinType11);
						if ($cuisineRecords) {
							$cuisinesList = $cuisineRecords->result_array()[0]['cuisines'];
						}

						$vendorar['code'] = $r1['code'];
						$vendorar['entityName'] = $r1['entityName'];
						$vendorar['firstName'] = $r1['firstName'];
						$vendorar['middleName'] = $r1['middleName'];
						$vendorar['lastName'] = $r1['lastName'];
						$vendorar['latitude'] = $r1['latitude'];
						$vendorar['longitude'] = $r1['longitude'];
						if ($r1['entityImage'] != "") {
							$path = 'uploads/vendor/' . $r1['code'] . '/' . $r1['entityImage'];
							if (file_exists($path)) $vendorar['entityImage'] = $path;
							else $vendorar['entityImage']  = "noimage";
						} else $vendorar['entityImage']  = "noimage";

						$vendorar['address'] = $r1['address'];
						$vendorar['ownerContact'] = $r1['ownerContact'];
						$vendorar['entityContact'] = $r1['entityContact'];
						$vendorar['email'] = $r1['email'];
						$vendorar['entityCategoryName'] = $r1['entityCategoryName'];
						$vendorar['fssaiNumber'] = $r1['fssaiNumber'];
						$vendorar['cityName'] = $r1['cityName'];
						$vendorar['aresName'] = $r1['place'];
						$vendorar['cuisinesList'] = $cuisinesList;
						$vendorar['packagingType'] = $r1['packagingType'];
						$vendorar['cartPackagingPrice'] = $r1['cartPackagingPrice'];
						$vendorar['gstApplicable'] = $r1['gstApplicable'];
						$vendorar['gstPercent'] = $r1['gstPercent'];
						$vendorar['isServiceable'] = $r1['isServiceable'];

						$itemAr['vendorsData'] = $vendorar;
					} else {
						$itemAr['vendorsData'] = array();
					}

					if ($r['itemPhoto'] != null || $r['itemPhoto'] != "") {
						$path = 'partner/uploads/' . $r['vendorCode'] . '/vendoritem/' . $r['itemPhoto'];
						if (file_exists($path)) $path = base_url($path);
						else $path = "nophoto";
					} else {
						$path = "nophoto";
					}
					$itemAr['itemPhoto'] = $path;
					$data[] = $itemAr;
				}
				$res['vendoritemmasterList'] = $data;
				return $this->response(array("status" => "200", "msg" => "Data found.", "result" => $res), 200);
			} else {
				return $this->response(array("status" => "300", "msg" => "No data found."), 200);
			}
		} else {
			return $this->response(array("status" => "400", "msg" => " * are required field(s)."), 200);
		}
	}
		
    public function getShippingCharges_post(){
		$postData = $this->post();
		if
		($postData['code']!=""  && $postData['vendorCode']!="" && $postData['itemTotal']!="" && $postData['tax']!="" && $postData['discount']!='' && $postData['packaging_charges']!=""){
			$couponCode ="";
			if(isset($postData['couponCode'])){
				$couponCode = $postData['couponCode'];	
			}
			
			$itemTotal= $postData['itemTotal'];
			$discount= $postData['discount'];
			$tax   = $postData['tax'];
			$packaging_charges = $postData['packaging_charges'];
			
			$subTotal = $itemTotal - $discount; 
			//get latest tax from vendor table
			
			$taxAmount = 0; 
			
            $gstData  = $this->GlobalModel->selectQuery("vendor.gstApplicable,vendor.gstPercent","vendor",array("vendor.code"=>$postData['vendorCode']));
            if($gstData)
            {
                $tax = $gstData->result()[0]->gstPercent;
                $taxApplicable = $gstData->result()[0]->gstApplicable;
                if($taxApplicable== 'YES'){
                    if($tax>0){
    				    $taxAmount = $subTotal * ($tax/100);  
    				    $taxAmount = floor($taxAmount); 
    			    }  
                }
            } 
            
			//orderAmount
			$orderAmount = $subTotal+$taxAmount+$packaging_charges;
			
			$shippingCharges = $this->GlobalModel->selectQuery("vendorconfiguration.shippingCharges,vendorconfiguration.shippingChargesUpto","vendorconfiguration");
			$orderShippingCharges=0;
			if($shippingCharges){
				$charges = $shippingCharges->result_array()[0];
				 
				$shippingCharges = $charges['shippingCharges']!=""?$charges['shippingCharges']:0;
				$shippingChargesUpto = $charges['shippingChargesUpto']!=""?$charges['shippingChargesUpto']:0;
				
				if($shippingCharges>0){
					if($shippingChargesUpto>0){
						if($orderAmount>=$shippingChargesUpto){
							$orderShippingCharges = 0;
						} else {
							$orderShippingCharges = $shippingCharges;
						}
					} else {
						$orderShippingCharges = $shippingCharges;
					}
				} else {
					$orderShippingCharges = 0;	
				}
				
				$finalOrderAmount = $orderAmount+ 	$orderShippingCharges;
				
				$res['itemTotal'] = $itemTotal;
				$res['discount'] = $discount;
				$res["subTotal"] = $subTotal;
				$res["taxAmount"] = $taxAmount;
				$res["packaging_charges"] = $packaging_charges;
				$res["orderAmount"] = $orderAmount;
				$res["shippingCharges"] = $orderShippingCharges;
				$res["finalOrderAmount"] = $finalOrderAmount;
				
				return $this->response(array("status" => "200", "message" => "Data Found","result"=>$res), 200);
			} else {
				$res["shippingCharges"] = $orderShippingCharges;
				return $this->response(array("status" => "200", "message" => "Data Found","result"=>$res), 200);
			}
		} else {
			return $this->response(array("status" => "400", "msg" => " * are required field(s)."), 400);
		}
	}
	
}