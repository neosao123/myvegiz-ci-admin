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
		$this->load->library('cashfree');//new version
		$this->load->library('cashfreepayment');//old version  
		$this->load->library('assignorder');
		$this->load->library('testassignorder');
		
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
		$columns = array("foodslider.*");
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
	}

	public function getMenuCategoryList_get()
	{
		$tableName = "menucategory";
		$orderColumns = array("menucategory.id,menucategory.code,menucategory.menuCategoryName,menucategory.isActive,menucategory.priority");
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
		$orderColumns = array("menusubcategory.id,menusubcategory.code,menusubcategory.menuCategoryCode,menucategory.menuCategoryName,menusubcategory.menuSubCategoryName,menusubcategory.isActive");
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
		$extraCondition = "cuisinemaster.isDelete IS NULL";  
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
		$currentDate = date('Y-m-d');
		$cond = "vendor.isActive=1";
		if (isset($getData['cuisineCode']) && $getData['cuisineCode'] != "") {
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
		if (isset($getData['cityCode']) && $getData['cityCode'] != "") {
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

		$geoLocation = "";
		$having = "";
		if (isset($getData['latitude'])) {
			if (isset($getData['longitude'])) {
				if ($getData['latitude'] != "" && $getData['longitude'] != "") {
					$geoLocation = ",ROUND( 6371 * acos( cos( radians(" . $getData['latitude'] . ") ) * cos( radians( vendor.latitude ) ) * cos( radians( vendor.longitude ) - radians(" . $getData['longitude'] . ") ) + sin( radians(" . $getData['latitude'] . ") ) * sin(radians(vendor.latitude)) ) ) AS distance";
					$having = " HAVING distance <= 20";
				}
			}
		}

		$columns = "select vendor.*,entitycategory.entityCategoryName,citymaster.cityName,customaddressmaster.place" . $geoLocation . " from vendor ";
		$orderBy = " order by vendor.isServiceable DESC , vendor.id ASC";
		$join = " INNER JOIN entitycategory ON vendor.entitycategoryCode = entitycategory.`code` LEFT JOIN vendorcuisinelineentries ON vendor.`code` = vendorcuisinelineentries.vendorCode ";
		$join .= " LEFT JOIN citymaster ON vendor.cityCode = citymaster.`code` LEFT JOIN customaddressmaster ON vendor.`addressCode` = customaddressmaster.code ";
		$groupBy = " Group by vendor.code,citymaster.cityName";
		$limit = 10;
		$offset = "";
		if (isset($getData['offset'])) {
			if ($getData['offset'] > 0) $limit_offset = " limit " . $getData['offset'] . ',10';
			else $limit_offset = " limit 10";
		} else {
			$limit_offset = " limit 10";
		}
		if ($cond != "") $whereCondition = " where " . $cond;
		else $whereCondition = "";
		$query = $columns . $join . $whereCondition . $groupBy . $having . $orderBy . $limit_offset;

		$result = $this->db->query($query);
		//echo $this->db->last_query();
		$ar = [];
		if ($result && $result->num_rows() > 0) {

			$data = array();
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
				if($r['isServiceable']==1 && $r['manualIsServiceable']==1){
				    $vendorar['isServiceable'] = "1";
				}else{
					$vendorar['isServiceable'] ="0";
				}
				if ($r['entityImage'] != "") {
					$path = 'uploads/vendor/' . $r['code'] . '/' . $r['entityImage'];
					if (file_exists($path)) $vendorar['entityImage'] = $path;
					else $vendorar['entityImage']  = "uploads/file_not_found.png";
				} else $vendorar['entityImage']  = "uploads/file_not_found.png";

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

				$discount = new stdClass();
				$currentDate = date('Y-m-d');
				$discountData =  $this->db->query("select * from  vendoroffer where discount=(select max(discount) from vendoroffer where vendorCode='" . $r['code'] . "' and (`vendoroffer`.`isDelete` = 0 OR vendoroffer.isDelete IS NULL) and ('" . $currentDate . "' between `startDate`  AND `endDate`) and isAdminApproved=1 and isActive=1) and vendorCode='" . $r['code'] . "'");
				$q=$this->db->last_query();
				if ($discountData->num_rows() > 0) {
					$disc = $discountData->result_array()[0];
					$discountValue = $disc['discount'];
					$discount->offerCode = $disc['code'];
					$discount->vendorCode = $disc['vendorCode'];
					$discount->coupanCode = $disc['coupanCode'];
					$discount->offerType = $disc['offerType'];
					if ($disc['offerType'] === 'flat') {
						$sign = ' ₹';
					} else {
						$sign = ' %';
					}
					$discount->discount = $disc['discount'] . $sign;
					$discount->minimumAmount = $disc['minimumAmount'];
					$discount->perUserLimit = $disc['perUserLimit'];
					$discount->startDate = date('d-m-Y h:i A', strtotime($disc['startDate']));
					$discount->endDate = date('d-m-Y h:i A', strtotime($disc['endDate']));
					$discount->capLimit = $disc['capLimit'];
					$discount->termsAndConditions = $disc['termsAndConditions'];
				}

				$vendorar['discount'] = $discount;
				$dayofweek = strtolower(date('l'));

				$timeData = $this->GlobalModel->selectQuery("vendorhours.code as hourCode,time_format(vendorhours.fromTime,'%h:%i %p') as fromTime,time_format(vendorhours.toTime,'%h:%i %p') as toTime", "vendorhours", array("vendorhours.vendorCode" => $r['code'], "vendorhours.weekDay" => $dayofweek));
				if ($timeData) {
					$vendorar['vendorHours'] = $timeData->result_array();
				} else {
					$vendorar['vendorHours'] = array();
				}
				$data[] = $vendorar;
			}
			$response['vendors'] = $data;
			return $this->response(array("status" => "200", "message" => 'Data Found', "result" => $response), 200);
		} else {
			return $this->response(array("status" => "300", "message" => 'No Data Found'), 200);
		}
	}

	public function getMenuItemList_get()
	{
		$getData = $this->get();
		if (isset($getData['vendorCode']) && $getData['vendorCode'] != "") {
			$tableName1 = "menucategory";
			$orderColumns1 = array("menucategory.*");
			$condition1 = array('menucategory.isActive' => 1);
			$orderBy1 = array('menucategory' . '.priority' => 'ASC');
			$Records = $this->GlobalModel->selectQuery($orderColumns1, $tableName1, $condition1, $orderBy1);
			//echo $this->db->last_query();
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
					$orderColumns2 = array("vendoritemmaster.*,vendor.entityName,vendor.manualIsServiceable,vendor.isServiceable as vendorIsServiceable,vendor.tagCode,tagmaster.tagTitle,tagmaster.tagColor");
					$condition2 = array('vendoritemmaster.isActive' => 1, "vendoritemmaster.vendorCode" => $getData['vendorCode'], "vendoritemmaster.menuCategoryCode" => $catCode, "vendoritemmaster.isAdminApproved" => 1);
					$orderBy2 = array('vendoritemmaster' . '.id' => 'DESC');
					$joinType2 = array("vendor" => "inner", "tagmaster" => "left");
					$join2 = array("vendor" => "vendoritemmaster.vendorCode=vendor.code", "tagmaster" => "vendor.tagCode=tagmaster.code");
					$groupByColumn2 = array();
					$extraCondition2 = " (vendoritemmaster.isDelete=0 OR vendoritemmaster.isDelete IS NULL) and (vendoritemmaster.menuSubCategoryCode is Null or vendoritemmaster.menuSubCategoryCode='')";
					$like2 = array();
					$itemRecords = $this->GlobalModel->selectQuery($orderColumns2, $tableName2, $condition2, $orderBy2, $join2, $joinType2, $like2, "", "", $groupByColumn2, $extraCondition2);
					//echo $this->db->last_query();	
					if ($itemRecords) {
						foreach ($itemRecords->result_array() as $r) {
							$addonArray = array();
							$choiceArray = array();
							$vendorItemCode = $r['code'];
							$CCRecordsAddon = $this->GlobalModel->selectQuery('customizedcategory.*', 'customizedcategory', array('customizedcategory.isEnabled' => 1, 'customizedcategory.vendorItemCode' => $vendorItemCode, 'customizedcategory.categoryType' => 'addon'));
							if ($CCRecordsAddon) {
								foreach ($CCRecordsAddon->result_array() as $ccra) {
									$customizedCategoryCode = $ccra['code'];
									$categoryTitle = $ccra['categoryTitle'];
									$CCRecordsAddonLine = $this->GlobalModel->selectQuery('customizedcategorylineentries.*', 'customizedcategorylineentries', array('customizedcategorylineentries.isEnabled' => 1, 'customizedcategorylineentries.customizedCategoryCode' => $customizedCategoryCode));
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

							$CCRecordsChoice = $this->GlobalModel->selectQuery('customizedcategory.*', 'customizedcategory', array('customizedcategory.isEnabled' => 1, 'customizedcategory.vendorItemCode' => $vendorItemCode, 'customizedcategory.categoryType' => 'choice'));
							if ($CCRecordsChoice) {
								foreach ($CCRecordsChoice->result_array() as $ccrc) {
									$customizedCategoryCode = $ccrc['code'];
									$categoryTitle = $ccrc['categoryTitle'];
									$CCRecordsChoiceLine = $this->GlobalModel->selectQuery('customizedcategorylineentries.*', 'customizedcategorylineentries', array('customizedcategorylineentries.isEnabled' => 1, 'customizedcategorylineentries.customizedCategoryCode' => $customizedCategoryCode));
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
								if (file_exists($filepath))  $path =  'partner/uploads/' . $r['vendorCode'] . '/vendoritem/' . $r['itemPhoto'];
							}
							if($r['vendorIsServiceable']==1&& $r['manualIsServiceable']==1){
								$serviceable="1";
							}else{
								$serviceable="0";
							}
							$mainitemArray[] = array(
								"vendorCode" => $getData['vendorCode'],
								"tagCode" => $r['tagCode'] ?? "",
								"tagTitle" => $r['tagTitle'] ?? "",
								"tagColor" => $r['tagColor'] ?? "",
								"itemCode" => $r['code'],
								"itemName" => $r['itemName'],
								"itemDescription" => $r['itemDescription'],
								"salePrice" => $r['salePrice'],
								"itemPhoto" => $path,
								"vendorName" => $r['entityName'],
								"vendorIsServiceable" => $serviceable,
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
					//echo $this->db->last_query();
					if ($subCateRecords) {
						$subcount	= sizeof($subCateRecords->result());
						foreach ($subCateRecords->result_array() as $subrow) {
							$subCategoryCode = $subrow['code'];
							$subCategoryName = $subrow['menuSubCategoryName'];

							$tableName4 = "vendoritemmaster";
							$orderColumns4 = array("vendoritemmaster.*,vendor.entityName,vendor.manualIsServiceable,vendor.isServiceable as vendorIsServiceable,vendor.tagCode,tagmaster.tagTitle,tagmaster.tagColor");
							$condition4 = array('vendoritemmaster.isActive' => 1, "vendoritemmaster.vendorCode" => $getData['vendorCode'], "vendoritemmaster.menuSubCategoryCode" => $subCategoryCode, "vendoritemmaster.isAdminApproved" => 1);
							$orderBy4 = array('vendoritemmaster' . '.id' => 'DESC');
							$joinType4 = array("vendor" => "inner", "menusubcategory" => "left", "tagmaster" => "left");
							$join4 = array("vendor" => "vendoritemmaster.vendorCode=vendor.code", "menusubcategory" => "vendoritemmaster.menuSubCategoryCode=menusubcategory.code", "tagmaster" => "vendor.tagCode=tagmaster.code");
							$groupByColumn4 = array();
							$extraCondition4 = " (vendoritemmaster.isDelete=0 OR vendoritemmaster.isDelete IS NULL)";
							$like4 = array();
							$Records = $this->GlobalModel->selectQuery($orderColumns4, $tableName4, $condition4, $orderBy4, $join4, $joinType4, $like4, "", "", $groupByColumn4, $extraCondition4);
							//	echo $this->db->last_query();
							if ($Records) {
								$itemArray = array();
								$count = sizeof($Records->result_array());
								foreach ($Records->result_array() as $r) {
									$addonArray = array();
									$choiceArray = array();
									$vendorItemCode = $r['code'];
									$CCRecordsAddon = $this->GlobalModel->selectQuery('customizedcategory.*', 'customizedcategory', array('customizedcategory.isEnabled' => 1, 'customizedcategory.vendorItemCode' => $vendorItemCode, 'customizedcategory.categoryType' => 'addon'));
									if ($CCRecordsAddon) {
										foreach ($CCRecordsAddon->result_array() as $ccra) {
											$customizedCategoryCode = $ccra['code'];
											$categoryTitle = $ccra['categoryTitle'];
											$CCRecordsAddonLine = $this->GlobalModel->selectQuery('customizedcategorylineentries.*', 'customizedcategorylineentries', array('customizedcategorylineentries.isEnabled' => 1, 'customizedcategorylineentries.customizedCategoryCode' => $customizedCategoryCode));
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

									$CCRecordsChoice = $this->GlobalModel->selectQuery('customizedcategory.*', 'customizedcategory', array('customizedcategory.isEnabled' => 1, 'customizedcategory.vendorItemCode' => $vendorItemCode, 'customizedcategory.categoryType' => 'choice'));
									if ($CCRecordsChoice) {
										foreach ($CCRecordsChoice->result_array() as $ccrc) {
											$customizedCategoryCode = $ccrc['code'];
											$categoryTitle = $ccrc['categoryTitle'];
											$CCRecordsChoiceLine = $this->GlobalModel->selectQuery('customizedcategorylineentries.*', 'customizedcategorylineentries', array('customizedcategorylineentries.isEnabled' => 1, 'customizedcategorylineentries.customizedCategoryCode' => $customizedCategoryCode));
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
										if (file_exists($filepath))  $path =  'partner/uploads/' . $r['vendorCode'] . '/vendoritem/' . $r['itemPhoto'];
									}
									if($r['vendorIsServiceable']==1&& $r['manualIsServiceable']==1){
										$serviceable="1";
									}else{
										$serviceable="0";
									}
									$itemArray[] = array(
										"vendorCode" => $getData['vendorCode'],
										"tagCode" => $r['tagCode'] ?? "",
										"tagTitle" => $r['tagTitle'] ?? "",
										"tagColor" => $r['tagColor'] ?? "",
										"itemCode" => $r['code'],
										"itemName" => $r['itemName'],
										"itemDescription" => $r['itemDescription'],
										"salePrice" => $r['salePrice'],
										"itemPhoto" => $path,
										"vendorName" => $r['entityName'],
										"vendorIsServiceable" => $serviceable,
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

	public function getVendorByCode_post()
	{
		$postData = $this->post();

		if (isset($postData["vendorCode"]) && $postData["vendorCode"] != "") {
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
				//$response['vendors'] = $checkData->result_array()[0];
				$dayofweek = strtolower(date('l'));

				$timeData = $this->GlobalModel->selectQuery("vendorhours.code as hourCode,time_format(vendorhours.fromTime,'%h:%i %p') as fromTime,time_format(vendorhours.toTime,'%h:%i %p') as toTime", "vendorhours", array("vendorhours.vendorCode" => $postData["vendorCode"], "vendorhours.weekDay" => $dayofweek));
				if ($timeData) {
					$response['vendorHours'] = $timeData->result_array();
				} else {
					$response['vendorHours'] = array();
				}

				$maxDiscount = $this->db->query("Select vendoroffer.code as offerCode,vendoroffer.vendorCode as vendorCode,vendoroffer.coupanCode,vendoroffer.offerType,ifnull(GREATEST(ifnull(MAX(vendoroffer.discount),0),ifnull(MAX(vendoroffer.flatAmount),0)),'') as discount,vendoroffer.minimumAmount,vendoroffer.perUserLimit,vendoroffer.startDate,vendoroffer.endDate,vendoroffer.capLimit,vendoroffer.termsAndConditions,vendoroffer.isAdminApproved as vAdminapproved,vendoroffer.isActive from vendoroffer where vendorCode= '" . $postData["vendorCode"] . "'");
				if ($maxDiscount) {
					foreach ($maxDiscount->result_array() as $r) {
						$data = $r;
						$sign = '';
						if ($r['offerType'] === 'flat') {
							$sign = ' ₹';
						} else {
							$sign = ' %';
						}
						$data['discount'] = $r['discount'] . $sign;
					}
					$response['discountDetails'] = $data;
				} else {
					$response['discountDetails'] = array();
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
		log_message("error", "place order data=>".json_encode($postData));
		if (
			isset($postData['packagingType'])  && $postData['packagingType'] != "" && isset($postData["vendorCode"]) && $postData["vendorCode"] != "" && isset($postData["clientCode"]) && $postData["clientCode"] != "" && isset($postData["paymentMode"]) && $postData["paymentMode"] != "" && isset($postData["address"]) && $postData["address"] != "" && isset($postData['discount'])  && $postData['discount'] != "" && isset($postData['total'])  && $postData['total'] != "" && isset($postData['subTotal'])  && $postData['subTotal'] != "" && isset($postData['grandTotal'])  && $postData['grandTotal'] != "" &&
			isset($postData['shippingCharges'])  && $postData['shippingCharges'] != "" && isset($postData['packagingCharges'])  && $postData['packagingCharges'] != "" && isset($postData['tax'])  && $postData['tax'] != "" && isset($postData['addressType'])  && $postData['addressType'] != "" && isset($postData['phone'])  && $postData['phone'] != "" && isset($postData['cart'])  && $postData['cart'] != ""
		) {
			$vendorCode = $postData["vendorCode"];
			$clientCode = $postData["clientCode"];
			if (isset($postData['coupanCode'])) {
				$coupanCode = $postData["coupanCode"];
			} else {
				$coupanCode = "";
			}
			$isOnlinePayment = false;
			$paymentStatus = 'PNDG';
			if ($postData['paymentMode'] == 'COD') {
				$orderStatus = "PND";
			} else {
				$isOnlinePayment = true;
				$orderStatus = "PND";
			}
			$currTime = date('H:i:00');
			$dayofweek = strtolower(date('l'));
			$getClientDetails = $this->GlobalModel->selectQuery("clientmaster.name,clientmaster.emailId,clientmaster.mobile,clientmaster.code", "clientmaster", array("clientmaster.code" => $postData['clientCode']));
			if ($getClientDetails != false) {
				$clientData = $getClientDetails->result_array()[0];
				$name = $clientData['name'];
				$email = $clientData['emailId'] ?? mailId;
				$mobile = $clientData['mobile'];
				$clientCode = $clientData['code'];
				if ($name != "" && $mobile != "" && $clientCode != "") {
					$conditionTime = array("vendorhours.vendorCode" => $postData["vendorCode"], "vendorhours.weekDay" => $dayofweek);
					$extrconditionTime = " ('" . $currTime . "' between vendorhours.fromTime and vendorhours.toTime)";
					$timeResult = $this->GlobalModel->selectQuery("vendorhours.vendorCode", "vendorhours", $conditionTime, array(), array(), array(), array(), "", "", array(), $extrconditionTime);
					//echo $this->db->last_query();
					//exit();
					if ($timeResult) {
						$serviceable = 1;
					} else {
						$serviceable = 0;
					}
					if ($serviceable == 1) {
						 //log_message("error", $postData['cart']);
						$cart = json_decode($postData["cart"], true);
						$orderId="ORDERFD".(int)(microtime(true) * 1000);
						if (!empty($cart)) {
							$data = [
								"vendorCode" => $vendorCode,
								"clientCode" => $clientCode,
								"coupanCode" => $coupanCode,
								"addressType" => $postData['addressType'],
								"discount" => $postData['discount'],
								"total"=>$postData['total'],
								"subTotal" => $postData['subTotal'],
								"grandTotal" => $postData['grandTotal'],
								"shippingCharges" => $postData['shippingCharges'],
								"totalPackgingCharges" => $postData['packagingCharges'],
								"paymentStatus" => $paymentStatus,
								"paymentMode" => $postData['paymentMode'],
								"orderStatus" => $orderStatus,
								"address" => $postData['flat'].",".$postData['landmark'].",".$postData['address'],
								'phone' => $postData['phone'],
								"tax" => $postData['tax'],
								"packagingType" => $postData['packagingType'],
								"orderId"=>$orderId,
								//"isActive" => 1,
							];
							if ($postData['paymentMode'] == 'COD'){
								$data["isActive"]=1;
								$data["isDelete"]=0;
							}else{
								$data["isActive"]=0;
								$data["isDelete"]=1;
							}
							if (isset($postData['clientNote']) && $postData['clientNote'] != "") $data['clientNote'] = $postData['clientNote'];
							if (isset($postData['latitude']) && $postData['latitude'] != "") $data['latitude'] = $postData['latitude'];
							if (isset($postData['longitude']) && $postData['longitude'] != "") $data['longitude'] = $postData['longitude'];
							if (isset($postData['flat']) && $postData['flat'] != "") $data['flat'] = $postData['flat'];
							if (isset($postData['landmark']) && $postData['landmark'] != "") $data['landmark'] = $postData['landmark'];

							//$orderCode = 'ORDERFD' . rand(99, 99999);
							$orderCode = 'ORDERFD';
							$insertResult = $this->GlobalModel->addWithoutYear($data, 'vendorordermaster', $orderCode);

							if ($insertResult) {
								$orderMasterArray = array();
								$ordData = array();
								if ($isOnlinePayment) {
									$orderid = $insertResult;
									
									$cashfreeResult=$this->cashfree->create_order($orderId,  $postData['grandTotal'], "INR", $clientCode, $email, $mobile, $name);
									if (array_key_exists("message", $cashfreeResult)) {
										return $this->response(array("status" => "300", "message" => "Payment for this order is not generated. Please try again", "submessage" => $cashfreeResult['message']), 200);
									} else {
										//print_r($cashfreeResult);
										//$orderMasterArray['paymentOrderId'] = $cashfreeResult['order_id'];
										//$orderMasterArray['paymentOrderToken'] = $cashfreeResult['cf_order_id'];
										$orderMasterArray['paymentOrderToken'] = $cashfreeResult['payment_session_id'];
										$orderMasterArray['finalAmount'] = $postData['grandTotal'];
                                        $orderMasterArray['paymentResult'] = $cashfreeResult;
										$ordData['paymentOrderId'] = $cashfreeResult['order_id'];
										$ordData['paymentOrderToken'] = $cashfreeResult['payment_session_id'];
									}
									/*$cashfreeResult = $this->cashfreepayment->payment($orderid,  $postData['grandTotal'], "INR", $clientCode, $email, $mobile, $name);
									if (array_key_exists("message", $cashfreeResult)) {
										return $this->response(array("status" => "300", "message" => "Payment for this order is not generated. Please try again", "submessage" => $cashfreeResult['message']), 200);
									} else {
										//print_r($cashfreeResult);
										$orderMasterArray['paymentOrderId'] = $cashfreeResult['order_id'];
										//$orderMasterArray['paymentOrderToken'] = $cashfreeResult['cf_order_id'];
										$orderMasterArray['paymentOrderToken'] = $cashfreeResult['payment_session_id'];
										$orderMasterArray['finalAmount'] = $postData['grandTotal'];

										$ordData['paymentOrderId'] = $cashfreeResult['order_id'];
										$ordData['paymentOrderToken'] = $cashfreeResult['payment_session_id'];
									}*/
								}
								$orderMasterArray['orderCode'] = $orderId;
								//$orderMasterArray['orderCode'] = $insertResult;
								foreach ($cart as $a) {
									$addons = $a['addons'];
									$addonsCode = $a['addonsCode'];
									$price = $a['price'];
									$quantity = $a['quantity'];
									$itemCode = $a['itemCode'];
									if ($postData['packagingType'] == "PRODUCT") {
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

								if ($coupanCode != "") {
									if ((isset($postData['vendorOfferCode'])) && $postData['vendorOfferCode'] != "") {
										if ((isset($postData['decidedExisitingLimit'])) && $postData['decidedExisitingLimit'] > 1) {
											$this->use_User_Coupon("update", $postData['vendorOfferCode'], $coupanCode, $clientCode, $postData['decidedExisitingLimit'], $vendorCode);
										} else {
											$this->use_User_Coupon("add", $postData['vendorOfferCode'], $coupanCode, $clientCode, $postData['decidedExisitingLimit'], $vendorCode);
										}
									}
								}
								if (!empty($ordData)) {
									$this->GlobalModel->doEdit($ordData, 'vendorordermaster', $insertResult);
								}
								$dataBookLine = array(
									"orderCode" => $insertResult,
									"statusPutCode" => $clientCode,
									"statusLine" => 'PND',
									"reason" => 'Booked by Client',
									"statusTime" => date("Y-m-d H:i:s"),
									"isActive" => 1
								);
								$bookLineResult = $this->GlobalModel->addWithoutYear($dataBookLine, 'bookorderstatuslineentries', 'BOL');

								if (!$isOnlinePayment) {
									
									// call to notify
									$curl = curl_init();

									$url = 'https://notify.myvegiz.com/order/place?orderNo=' . $insertResult . '&orderDate=' . date('Y-m-d H:i:s') . '&orderStatus=PND&orderTotal=' . $postData['grandTotal'] . '&orderItems=' . sizeof($cart) . '&restaurantCode=' . $vendorCode;

									curl_setopt_array($curl, array(
									CURLOPT_URL => $url,
									CURLOPT_RETURNTRANSFER => true,
									CURLOPT_ENCODING => '',
									CURLOPT_MAXREDIRS => 10,
									CURLOPT_TIMEOUT => 0,
									CURLOPT_FOLLOWLOCATION => true,
									CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
									CURLOPT_CUSTOMREQUEST => 'GET',
									));

									$response = curl_exec($curl);

									curl_close($curl);

									// assign delivery boy to order
									$this->assignorder->allocate_delivery_boy_to_order($insertResult);
								}

								return $this->response(array("status" => "200", "message" => "Order Placed Successfully..", "result" => $orderMasterArray), 200);
							} else {
								$this->response(array("status" => "300", "message" => " Opps...! Something went wrong please try again."), 200);
							}
						} else {
							$this->response(array("status" => "300", "message" => " Opps...! empty cart."), 200);
						}
					} else {
						$this->response(array("status" => "300", "message" => "Restaurant has been closed and shall not accept the order! Sorry for your inconvenience."), 200);
					}
				} else {
					$this->response(array("status" => "300", "message" => "Please complete your profile first"), 200);
				}
			} else {
				$this->response(array("status" => "300", "message" => "Invalid Client Code"), 200);
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
			$where['vendorofferCode'] = $vendorOfferCode;

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
		if (isset($postData["clientCode"]) && $postData["clientCode"] != '') {
			$clientCode = $postData["clientCode"];
			$tableName = "vendorordermaster";
			$orderColumns = array("vendorordermaster.deliveryBoyCode,usermaster.name as dBoyName,usermaster.latitude as dBoyLat,usermaster.longitude as dBoyLong,vendorordermaster.vendorCode,vendorordermaster.totalPackgingCharges,vendorordermaster.packagingType,vendorordermaster.addressType,vendorordermaster.code as orderCode,vendorordermaster.tax,vendorordermaster.discount,vendorordermaster.total,vendorordermaster.subTotal,vendorordermaster.shippingCharges as deliveryCharges,vendorordermaster.paymentmode,vendorordermaster.address,vendorordermaster.grandTotal as orderTotalPrice,vendorordermaster.addDate as orderDate, vendororderstatusmaster.statusName as orderStatus, paymentstatusmaster.statusName as paymentStatus,vendor.entityName,vendor.address as pickUpAddress,vendor.latitude as sourceLat,vendor.longitude as sourceLong,vendor.cityCode,vendor.addressCode,citymaster.cityName,customaddressmaster.place,vendorordermaster.latitude as destiLat,vendorordermaster.longitude as destiLong,bookorderstatuslineentries.statusTime");
			$cond = array('vendorordermaster.clientCode' => $clientCode, "vendorordermaster.isActive" => 1);
			$orderBy = array('vendorordermaster' . ".id" => 'DESC');
			$join = array('usermaster' => 'vendorordermaster' . '.deliveryBoyCode=' . 'usermaster' . '.code', 'vendororderstatusmaster' => 'vendorordermaster' . '.orderStatus=' . 'vendororderstatusmaster' . '.statusSName', 'paymentstatusmaster' => 'vendorordermaster' . '.paymentStatus=' . 'paymentstatusmaster' . '.statusSName', "vendor" => "vendorordermaster.vendorCode=vendor.code", "citymaster" => "vendor.cityCode=citymaster.code", "customaddressmaster" => "vendor.addressCode=customaddressmaster.code", "bookorderstatuslineentries" => "vendorordermaster.orderStatus=bookorderstatuslineentries.statusLine");
			$joinType = array('usermaster' => 'left', 'vendororderstatusmaster' => 'inner', 'paymentstatusmaster' => 'inner', "vendor" => "inner", "citymaster" => "left", "customaddressmaster" => "left", "bookorderstatuslineentries" => "left");
			$extracondition = " bookorderstatuslineentries.orderCode = vendorordermaster.code";
			$resultQuery = $this->GlobalModel->selectQuery($orderColumns, $tableName, $cond, $orderBy, $join, $joinType, array(), "", "", array(), $extracondition);
			//array(), array(), array(), array(), "", "", array(), $extraCondition
		//	$resultQuery = $this->GlobalModel->selectQuery($orderColumns, $tableName, $cond, $orderBy, $join, $joinType);
			$imageArray = array();
            // echo $this->db->last_query();
			if ($resultQuery) {
				$clientOrderList = $resultQuery->result_array();
				$totalOrders = sizeof($clientOrderList);

				for ($i = 0; $i < $totalOrders; $i++) {
					$clientOrderList[$i]['statusTime'] = date('d-m-Y h:i A', strtotime($clientOrderList[$i]['statusTime']));
					$addonArray = $resultAddonArray = [];
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
						foreach ($orderProductRes->result_array() as $subItem) {
							$addonArray = $subItem;
							$resultArr = [];
							if ($subItem['addonsCode'] != '' && $subItem['addonsCode'] != NULL) {
								$subItem['addonsCode'] = rtrim($subItem['addonsCode'], ',');
								$savedaddonsCodes = explode(',', $subItem['addonsCode']);
								foreach ($savedaddonsCodes as $addon) {
									$categoryArr = [];
									$joinType1 = array('customizedcategory' => 'inner');
									$condition1 = array('customizedcategorylineentries.code' => $addon);
									$join1 = array('customizedcategory' => "customizedcategory.code=customizedcategorylineentries.customizedCategoryCode");
									$getAddonDetails = $this->GlobalModel->selectQuery("customizedcategory.categoryTitle,customizedcategory.categoryType,customizedcategorylineentries.subCategoryTitle,customizedcategorylineentries.price", "customizedcategorylineentries", $condition1, array(), $join1, $joinType1, array(), array(), '', array(), '');
									if ($getAddonDetails) {
										$categoryArr = $getAddonDetails->result_array()[0];
									}
									$resultArr[] = $categoryArr;
								}
							}
							$addonArray['addonsDetails'] = $resultArr;
							$resultAddonArray[] = $addonArray;
						}
					}

					$clientOrderList[$i]['orderedItems'] = $resultAddonArray;
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

	public function getCouponList_post()
	{
		$postData = $this->post();
		if (isset($postData['vendorCode']) && $postData['vendorCode'] != "") {
			$today = date('Y-m-d');
			$condition = array('vendoroffer.vendorCode' => $postData['vendorCode'], 'vendoroffer.isActive' => 1, "vendoroffer.isAdminApproved" => 1);
			$extraCondition = "( '" . $today . "' between vendoroffer.startDate and vendoroffer.endDate)";
			$Result = $this->GlobalModel->selectQuery('vendoroffer.coupanCode as couponCode,vendoroffer.offerType,vendoroffer.discount,vendoroffer.flatAmount,vendoroffer.minimumAmount,vendoroffer.perUserLimit,vendoroffer.termsAndConditions,vendoroffer.capLimit,vendoroffer.startDate,vendoroffer.endDate', 'vendoroffer', $condition, array(), array(), array(), array(), "", "", array(), $extraCondition);
		
			if ($Result) {
				$recordsCount = sizeof($Result->result_array());
				$data = array();
				foreach ($Result->result_array() as $r) {
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
		if (isset($postData['clientCode']) && $postData['clientCode'] != "" && isset($postData["coupanCode"]) && $postData["coupanCode"] != "" && isset($postData['vendorCode']) &&  $postData['vendorCode'] != "" && isset($postData['cartAmount']) && $postData['cartAmount'] != "") {
			$couponCode = $postData["coupanCode"];
			$vendorCode = $postData["vendorCode"];
			$cartAmount = $postData['cartAmount'];
			$clientCode = $postData['clientCode'];

			$today = date('Y-m-d');
			$limit = 0;

			$condition = array('vendoroffer.coupanCode' => $couponCode, 'vendoroffer.vendorCode' => $vendorCode, 'vendoroffer.isActive' => 1, "vendoroffer.perUserLimit>=" => 1, "vendoroffer.isAdminApproved" => 1);
			$extraCondition = "( '" . $today . "' between vendoroffer.startDate and vendoroffer.endDate)";
			$Result = $this->GlobalModel->selectQuery('vendoroffer.*', 'vendoroffer', $condition,array(), array(), array(), array(), "", "", array(), $extraCondition);
			$query = $this->db->last_query();
			if ($Result) {
				$r = $Result->result_array()[0];
				$vendorOfferCode = $r['code'];
				$minimumAmount = $r['minimumAmount'];
				$offerType = $r['offerType'];
				$couponCode = $r['coupanCode'];
				$sign = '';
				
				if ($r['offerType'] === 'flat') {
					$discount = $r['flatAmount'];
				} else {
				    	$discount = round($postData['cartAmount'] * ($r['discount'] / 100), 2);
				//	$discount = $r['discount'];
				}
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
					//echo $this->db->last_query();
					if ($clientUseCouponResult) {
						$useCoupon = $clientUseCouponResult->result_array()[0];
						$userLimit = $useCoupon['decidedExisitingLimit'];
						echo $userLimit . '#' . $perUserLimit;
						if ($userLimit < $perUserLimit) {
							$nextLimit = $userLimit + 1;
							return $this->response(array("status" => "200", "nextLimit" => $nextLimit, "message" => $couponCode . " and save every time you order", "result" => $data), 200);
						} else {
							return $this->response(array("status" => "300", "message" => "Coupon Already Used"), 200);
						}
					} else {
						//not single use return coupon
						return $this->response(array("status" => "200", "nextLimit" => 1, "message" => $couponCode . " and save every time you order", "result" => $data), 200);
					}
				} else {
					return $this->response(array("status" => "300", "message" => "Amount should be greater than " . $minimumAmount), 200);
				}
			} else {
				return $this->response(array("status" => "300", "message" => "Invalid Coupon Code."), 200);
			}
		} else {
			return $this->response(array("status" => "400", "message" => " * are required field(s)."), 400);
		}
	}

	public function getVendorItemsOnSearch_post()
	{
		$postData =  $this->post();
		if (isset($postData['search']) && $postData['search'] != "" && isset($postData['cityCode']) && $postData['cityCode'] != "") {
			$code = '';
			if (isset($postData['vendorCode'])) {
				if ($postData['vendorCode'] != "") {
					$code = $postData['vendorCode'];
				}
			}

			$table1 = "vendor";
			$cityCode = $postData['cityCode'];
			$orderColumns1 = "vendor.*,entitycategory.entityCategoryName,citymaster.cityName,customaddressmaster.place";
			$condition1['vendor.isActive'] = 1;
			$condition1['vendor.cityCode'] = $cityCode;
			$condition1['vendor.code'] = $code;
			$orderBy1["vendor.isServiceable"] = 'DESC';
			$orderBy1["vendor.id"] = 'ASC';
			$join1 = array(
				"entitycategory" => "vendor.entitycategoryCode=entitycategory.code",
				"vendorcuisinelineentries" => "vendor.`code` = vendorcuisinelineentries.vendorCode",
				"citymaster" => "vendor.cityCode=citymaster.code",
				"customaddressmaster" => "vendor.addressCode=customaddressmaster.code"
			);
			$joinType1 = array('entitycategory' => 'inner', "vendorcuisinelineentries" => "inner", "citymaster" => "left", "customaddressmaster" => "left");
			$like1 = array("vendor.entityName" => $postData['search'] . '~both');
			$limit1 = '10';
			$offset1 = '';
			if (isset($post['offset'])) {
				if ($postData['offset'] != "") $offset1 = $postData['offset'];
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
						else $vendorar['entityImage']  = "uploads/file_not_found.png";
					} else $vendorar['entityImage']  = "uploads/file_not_found.png";

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
					$vendorar['isServiceable'] = $r['isServiceable'];

					$discount = new stdClass();
					$currentDate = date('Y-m-d');
					$discountData =  $this->db->query("select * from  vendoroffer where discount=(select max(discount) from vendoroffer where vendorCode='" . $r['code'] . "' and (`vendoroffer`.`isDelete` = 0 OR vendoroffer.isDelete IS NULL) and ('" . $currentDate . "' between `startDate`  AND `endDate`) and isAdminApproved=1 and isActive=1)  and vendorCode='" . $r['code'] . "'");
					if ($discountData->num_rows() > 0) {
						$disc = $discountData->result_array()[0];
						$discountValue = $disc['discount'];
						$discount->offerCode = $disc['code'];
						$discount->vendorCode = $disc['vendorCode'];
						$discount->coupanCode = $disc['coupanCode'];
						$discount->offerType = $disc['offerType'];
						if ($disc['offerType'] === 'flat') {
							$sign = ' ₹';
						} else {
							$sign = ' %';
						}
						$discount->discount = $disc['discount'] . $sign;
						$discount->minimumAmount = $disc['minimumAmount'];
						$discount->perUserLimit = $disc['perUserLimit'];
						$discount->startDate = date('d-m-Y h:i A', strtotime($disc['startDate']));
						$discount->endDate = date('d-m-Y h:i A', strtotime($disc['endDate']));
						$discount->capLimit = $disc['capLimit'];
						$discount->termsAndConditions = $disc['termsAndConditions'];
					}

					$vendorar['discount'] = $discount;

					$data[] = $vendorar;
				}
				$res['vendorList'] = $data;
			} else {
				$res['vendorList'] = array();
			}

			$table = "vendoritemmaster";
			$orderColumns = "vendoritemmaster.vendorCode,vendoritemmaster.maxOrderQty,vendoritemmaster.itemPackagingPrice,vendor.entityName,vendoritemmaster.code,vendoritemmaster.itemName,vendoritemmaster.itemDescription,vendoritemmaster.cuisineType,vendoritemmaster.salePrice,vendoritemmaster.itemPhoto,vendor.isServiceable as vendorIsServiceable,vendoritemmaster.itemActiveStatus";
			$condition['vendoritemmaster.isActive'] = 1;
			$condition['vendoritemmaster.vendorCode'] = $code;
			$condition['vendoritemmaster.isAdminApproved'] = 1;
			$condition['vendor.isActive'] = 1;
			$condition['vendor.cityCode'] = $cityCode;
			//$condition['vendor.isServiceable'] = 1;
			//$condition['vendoritemmaster.itemActiveStatus'] = 1;
			$orderBy["vendor.isServiceable"] = 'DESC';
			$orderBy["vendoritemmaster.id"] = 'ASC';
			$join = array("vendor" => "vendoritemmaster.vendorCode=vendor.code");
			$joinType = array('vendor' => 'inner');
			$like = array("vendoritemmaster.itemName" => $postData['search'] . '~both');
			$limit = '10';
			$offset = '';
			if (isset($post['offset'])) {
				if ($postData['offset'] != "") $offset = $postData['offset'];
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
					$itemAr['vendorIsServiceable'] = $r['vendorIsServiceable'];
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
							else $vendorar['entityImage']  = "uploads/file_not_found.png";
						} else $vendorar['entityImage']  = "uploads/file_not_found.png";

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

	public function getVendorItemsOnSearchold_post()
	{
		$postData =  $this->post();
		if (isset($postData['search']) && $postData['search'] != "" && isset($postData['cityCode']) && $postData['cityCode'] != "") {
			$table1 = "vendor";
			$cityCode = $postData['cityCode'];
			$orderColumns1 = "vendor.*,entitycategory.entityCategoryName,citymaster.cityName,customaddressmaster.place";
			$condition1['vendor.isActive'] = 1;
			$condition1['vendor.cityCode'] = $cityCode;
			$orderBy1["vendor.isServiceable"] = 'DESC';
			$orderBy1["vendor.id"] = 'ASC';
			$join1 = array(
				"entitycategory" => "vendor.entitycategoryCode=entitycategory.code",
				"vendorcuisinelineentries" => "vendor.`code` = vendorcuisinelineentries.vendorCode",
				"citymaster" => "vendor.cityCode=citymaster.code",
				"customaddressmaster" => "vendor.addressCode=customaddressmaster.code"
			);
			$joinType1 = array('entitycategory' => 'inner', "vendorcuisinelineentries" => "inner", "citymaster" => "left", "customaddressmaster" => "left");
			$like1 = array("vendor.entityName" => $postData['search'] . '~both');
			$limit1 = '10';
			$offset1 = '';
			if (isset($post['offset'])) {
				if ($postData['offset'] != "") $offset1 = $postData['offset'];
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
						else $vendorar['entityImage']  = "uploads/file_not_found.png";
					} else $vendorar['entityImage']  = "uploads/file_not_found.png";

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
					$vendorar['isServiceable'] = $r['isServiceable'];
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
			$like = array("vendoritemmaster.itemName" => $postData['search'] . '~both', "vendor.entityName" => $postData['search'] . '~both');
			$limit = '10';
			$offset = '';
			if (isset($post['offset'])) {
				if ($postData['offset'] != "") $offset = $postData['offset'];
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
					$itemAr['vendorIsServiceable'] = $r['vendorIsServiceable'];
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
							else $vendorar['entityImage']  = "uploads/file_not_found.png";
						} else $vendorar['entityImage']  = "uploads/file_not_found.png";

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

	public function verifyPayment_post()
	{
		$postData = $this->post();
		$random = rand(0, 999);
		$random = date('his') . $random;
		if (isset($postData['clientCode']) && $postData['clientCode'] != "" && isset($postData['paymentOrderId']) && $postData['paymentOrderId'] != "" && isset($postData['paymentOrderToken']) && $postData['paymentOrderToken'] != "" && isset($postData['orderCode']) && $postData['orderCode'] != "") {
			
			$dvCondition['clientCode'] = $postData['clientCode'];
			$clientDevices = $this->GlobalModel->selectQuery("clientdevicedetails.firebaseId", "clientdevicedetails", $dvCondition);
			$DeviceIdsArr = array();
			if ($clientDevices) {
				sleep(1);
				foreach ($clientDevices->result() as $key) {
					$DeviceIdsArr[] = $key->firebaseId;
				}
			}
			//get table record
			$table = "vendorordermaster";
			$clms = "vendorordermaster.code,vendorordermaster.paymentOrderId,vendorordermaster.paymentOrderToken,vendorordermaster.paymentStatus,vendorordermaster.grandTotal,vendorordermaster.vendorCode";
			$cond = array("vendorordermaster.code" => $postData['orderCode']);
			$result = $this->GlobalModel->selectQuery($clms, $table, $cond);
		
			if ($result != false) {
				$payment = $result->result_array()[0];
				if ($payment['paymentOrderId'] == $postData['paymentOrderId']) {
				    
				     if($payment['paymentStatus']=="PID"){
				        $message = "Payment successfull";
				        return $this->response(array("status" => 200, "message" => $message, "paymentId" => $postData['paymentOrderId'], "payment_status" =>"SUCCESS"), 200);
				    }
				    
					//$cashFreeResult = $this->cashfreepayment->getOrderStatus($postData['paymentOrderId']);
                  	$cashFreeResult = $this->cashfree->getOrderStatus($postData['paymentOrderId']);
					
					log_message("error", " payments response => " . trim(stripslashes(json_encode($cashFreeResult))));
                    $data=array('webhookResponse'=>trim(stripslashes(json_encode($cashFreeResult))));
                    if (!empty($cashFreeResult)) {
                    
                        //$payments = $cashFreeResult['payments'];
						//$payments = $payments[0];
						$txtStatus= $cashFreeResult['order_status'];
						if ($txtStatus="PAID") {
						//if ($payments['is_captured'] == true  && $txtStatus="SUCCESS") {
						    $this->assignorder->allocate_delivery_boy_to_order($postData['orderCode']);
						 
					    	$data['orderStatus'] = 'PND';
							$data['paymentStatus'] = "PID";
							$data['isActive'] = 1;
							$message = "Payment Successful";
							$dataNoti = array('title' => 'Payment Successful', 'message' => $postData['orderCode'] . ' Order placed successfully.','order_id'=>$postData['orderCode'] ,'unique_id' => $postData['orderCode'], 'random_id' => $random, 'type' => 'VendorOrder');
						}
						else if($txtStatus="EXPIRED")
						{
						    $data['orderStatus'] = 'PND';
							$data['paymentStatus'] = "EXP";
							$message = "Payment Expired";
							$dataNoti = array('title' => '"Payment Expired', 'message' => $postData['orderCode'] . ' Order expired','order_id'=>$postData['orderCode'], 'unique_id' => $postData['orderCode'], 'random_id' => $random, 'type' => 'VendorOrder');
						}else{
						    $data['orderStatus'] = 'CAN';
							$data['paymentStatus'] = "RJCT";
							$message = "Payment Failed";
							$dataNoti = array('title' => 'Payment Failed', 'message' => $postData['orderCode'] . ' Order Cancelled','order_id'=>$postData['orderCode'], 'unique_id' => $postData['orderCode'], 'random_id' => $random, 'type' => 'VendorOrder');
						}
						$result1 = $this->GlobalModel->doEdit($data, 'vendorordermaster', $postData['orderCode']);
    				
    					//if ($payments['is_captured'] == true  && $txtStatus="SUCCESS") {
						if ($txtStatus="PAID") {

							// call to notify
							$curl = curl_init();

							$url = 'https://notify.myvegiz.com/order/place?orderNo=' . $postData['orderCode'] . '&orderDate=' . date('Y-m-d H:i:s') . '&orderStatus=PND&orderTotal=' . $payment['grandTotal'] . '&orderItems=1&restaurantCode=' . $payment['vendorCode'];

							curl_setopt_array($curl, array(
								CURLOPT_URL => $url,
								CURLOPT_RETURNTRANSFER => true,
								CURLOPT_ENCODING => '',
								CURLOPT_MAXREDIRS => 10,
								CURLOPT_TIMEOUT => 0,
								CURLOPT_FOLLOWLOCATION => true,
								CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
								CURLOPT_CUSTOMREQUEST => 'GET',
							));

							$response = curl_exec($curl);

							curl_close($curl);

							$this->assignorder->allocate_delivery_boy_to_order($postData['orderCode']);
						}
						//$this->sendNotification($DeviceIdsArr, $title, $message, $postData['orderCode']);
    					return $this->response(array("status" => "200", "message" => $message, "paymentId" => $postData['paymentOrderId'], "payment_status" => $txtStatus), 200);
                    }
                    else
                    {
                        $data['orderStatus'] = 'CAN';
    					$data['paymentStatus'] = "FAIL";
    					$result = $this->GlobalModel->doEdit($data, 'vendorordermaster', $postData['orderCode']);
    					$dataNoti = array('title' => 'Payment Failed', 'message' => $postData['orderCode'] . ' Order Cancelled','order_id'=>$postData['orderCode'], 'unique_id' => $postData['orderCode'], 'random_id' => $random, 'type' => 'VendorOrder');
    					$this->sendNotification($DeviceIdsArr, $title, $message, $postData['orderCode']);
    					return $this->response(array("status" => "300",  "message" => "Payment Failed", "paymentId" => $postData['paymentOrderId'], "payment_status" => "FAILED"), 200);
                    }
				
				} else {
					$data['orderStatus'] = 'CAN';
					$data['paymentStatus'] = "FAIL";
					$result = $this->GlobalModel->doEdit($data, 'vendorordermaster', $postData['orderCode']);
					$dataNoti = array('title' => 'Payment Failed', 'message' => $postData['orderCode'] . ' Order Cancelled','order_id'=>$postData['orderCode'], 'unique_id' => $postData['orderCode'], 'random_id' => $random, 'type' => 'VendorOrder');
					$this->sendNotification($DeviceIdsArr, $title, $message, $postData['orderCode']);
					return $this->response(array("status" => "300",  "message" => "Payment Failed", "paymentId" => $postData['paymentOrderId'], "payment_status" => "FAILED"), 200);
				}
			} else {
				return $this->response(array("status" => "300", "message" => "Unable to process payment"), 200);
			}
		}
		return $this->response(array("status" => "400", "message" => "* are required fields"), 200);
	}

	public function vendorPaymentWebhook_post()
	{
		$postData = $this->post();
		if (!empty($postData)) {
			$post = json_encode($postData);
			log_message("error", $post);
			$result = json_decode($post);
			if (array_key_exists("orderId", $result)) {
				$orderId = $result->orderId;
				$referenceId = $result->referenceId;
				$orderAmount = $result->orderAmount;
				$txStatus = $result->txStatus;
				$paymentMode = $result->paymentMode;
				$txMsg = $result->txMsg;
				$txTime = $result->txTime;
				$signature = $result->signature;
				$timeStamp = date("Y-m-d h:i:s");
				$table = "vendorordermaster";
				$clms = "vendorordermaster.paymentOrderId,vendorordermaster.paymentOrderToken";
				$cond = array("vendorordermaster.code" => $postData['orderCode']);
				$result = $this->GlobalModel->selectQuery($clms, $table, $cond);
				if ($result != false) {
					$data = $orderId . $orderAmount . $referenceId . $txStatus . $paymentMode . $txMsg . $txTime;
					$hash_hmac = hash_hmac('sha256', $data, CASHFREE_CLIENT_SECRET, true);
					$computedSignature = base64_encode($hash_hmac);
					if ($signature == $computedSignature) {
					}
				}
			}
		}
	}

	public function getCartAmountDetails_post()
	{
		$postData = $this->post();
		if (isset($postData['clientCode'])  && $postData['clientCode'] != "" && isset($postData["vendorCode"]) && $postData["vendorCode"] != "" && isset($postData["subTotal"]) && $postData["subTotal"] != '' && isset($postData["cartItems"]) && $postData["cartItems"] != '') {
			$cityCode = '';
			$clientCode = $postData['clientCode'];
			//get coupon Details to calculate discount
			$discount = 0;
			$isserviceable = false;
			$resResult = $this->GlobalModel->selectQuery("vendor.isServiceable", "vendor", array("vendor.code" => $postData["vendorCode"]));
			if ($resResult != false) {
				$data = $resResult->result_array()[0];
				$status = $data['isServiceable'];
				if ($status == 1) {
					$isserviceable = true;
				} else {
					$isserviceable = false;
				}
			}
			if (isset($postData['couponCode']) && $postData['couponCode'] != "") {
				$couponData = $this->GlobalModel->selectQuery("vendoroffer.code,vendoroffer.discount as coupondiscount,vendoroffer.offerType,vendoroffer.capLimit,IFNULL(vendoroffer.flatAmount,0) as flatAmount,vendoroffer.minimumAmount", "vendoroffer", array("vendoroffer.coupanCode" => $postData['couponCode']));
				if ($couponData != false) {
					$couponRow = $couponData->result()[0];
					//echo $postData['subTotal'].'#'.$couponRow->minimumAmount;
					if ($postData['subTotal'] >= $couponRow->minimumAmount) {
						switch ($couponRow->offerType) {
							case 'flat':
								$discount = $couponRow->flatAmount;
								break;
							case 'cap':
								$discount = round($postData['subTotal'] * ($couponRow->coupondiscount / 100), 2);
								if ($discount > $couponRow->capLimit) {
									$discount = $couponRow->capLimit;
								}
								break;
						}
					}
				}
			}
			$orderAmount=$subTotal = $taxAmount = $cartPackagingPrice=0;
			if ($discount > $postData['subTotal']) {
				$submessage = 'Invalid Coupon discount';
				$discount = 0;
			} else {
				$submessage = '';
				$subTotal = $postData['subTotal'] - $discount;
				$taxAmount = $cartPackagingPrice = 0;
				$restLatitude = $restLongitude = '';
				$gstData  = $this->GlobalModel->selectQuery("vendor.gstApplicable,vendor.packagingType,vendor.gstPercent,vendor.latitude,vendor.longitude,vendor.cartPackagingPrice", "vendor", array("vendor.code" => $postData['vendorCode']));
				if ($gstData) {
					$tax = $gstData->result()[0]->gstPercent;
					$restLatitude = $gstData->result()[0]->latitude;
					$restLongitude = $gstData->result()[0]->longitude;
					$taxApplicable = $gstData->result()[0]->gstApplicable;
					$packagingType = $gstData->result()[0]->packagingType;
					if ($packagingType == 'CART') {
						$cartPackagingPrice = $gstData->result()[0]->cartPackagingPrice;
					} else {
						if ($postData['cartItems'] != '') {
							$itemCodes = implode("', '", explode('~', $postData['cartItems']));
							$getProductPackingPrice = $this->db->query("select ifnull(sum(itemPackagingPrice),0) as packagingPrice from vendoritemmaster where code in ('" . $itemCodes . "')");
							if ($getProductPackingPrice) {
								$cartPackagingPrice = $getProductPackingPrice->result()[0]->packagingPrice;
							}
						}
					}
					if ($taxApplicable == 'YES') {
						if ($tax > 0) {
							$taxAmount = round(($subTotal * $tax) / 100, 2);
						}
					}
				}
			}
				//echo $taxAmount.'#'.$subTotal.'#'.$cartPackagingPrice;
				$orderAmount = $subTotal + $taxAmount + $cartPackagingPrice;
				
				$shortestdistance = 0;
				$charges = 0;
				
				if ($restLatitude != "" && $restLongitude != "") {
					$result = $this->calculateDistanceDeliveryCharges($restLatitude, $restLongitude, $clientCode, $orderAmount);
				//	print_r($result);
					$charges = $result['charges'];
					$shortestdistance = $result['shortestdistance'];
				}
				
/*
				// get city wise delivery charge
				$client = $this->GlobalModel->selectQuery("clientmaster.cityCode", "clientmaster", array("clientmaster.code" => $clientCode));
				if ($client) $cityCode = $client->result()[0]->cityCode;
				$deliveryCharge = $deliveryChargesPerKm = $minFreeDeliveryKm = $minOrder = 0;
				$deliveryChargeCurrency = 'INR';
				$chargesResult = $this->GlobalModel->selectQuery('citymaster.deliveryCharge,citymaster.minOrder, citymaster.deliveryChargeCurrency,citymaster.deliveryChargesPerKm,citymaster.minFreeDeliveryKm', 'citymaster', array('citymaster.code' => $cityCode));
				if ($chargesResult) {
					$charge = $chargesResult->result()[0];
					$deliveryCharge = $charge->deliveryCharge;
					$minOrder = $charge->minOrder;
					$deliveryChargeCurrency = $charge->deliveryChargeCurrency;
					$deliveryChargesPerKm = $charge->deliveryChargesPerKm;
					$minFreeDeliveryKm  = $charge->minFreeDeliveryKm;
				}


				// shortest distance delivery charges
				$latitude = $longitude = '';
				$addressLatitudeData = $this->GlobalModel->selectQuery("clientprofile.latitude,clientprofile.longitude", "clientprofile", array("clientprofile.isActive" => "1", "clientprofile.isSelected" => "1", "clientprofile.clientCode" => $clientCode));
				if ($addressLatitudeData != false) {
					$data = $addressLatitudeData->result_array()[0];
					$latitude = $data['latitude'];
					$longitude = $data['longitude'];
				}
				$shortestdistance = 0;
				$charges = 0;
				$arrayDist = array();
				if ($postData['subTotal'] > $minOrder || $postData['subTotal'] == 0) {
					$shortestdistance = 0;
					$charges = 0;
				} elseif ($latitude != "" && $longitude != "" && $restLatitude != "" && $restLongitude != "" && $minFreeDeliveryKm != 0 && $deliveryChargesPerKm != 0) {
					$url = "https://maps.googleapis.com/maps/api/directions/json?destination=$restLatitude,$restLongitude&mode=driving&origin=$latitude,$longitude&key=" . PLACE_API_KEY;
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
					curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
					$response = curl_exec($ch);
					curl_close($ch);
					$response_all = json_decode($response, TRUE);
					//print_r($response_all);
					if (!empty($response_all['routes'])) {
						foreach ($response_all['routes'] as $res1 => $val) {
							foreach ($val['legs'] as $keys => $value) {
								$distance = round($value['distance']['value'] / 1000);
								array_push($arrayDist, $distance);
							}
						}
						//print_r($arrayDist);
						$mindistance = min($arrayDist);
						if ($mindistance > $minFreeDeliveryKm) {
							$finalDistance = $mindistance - $minFreeDeliveryKm;
							$shortestdistance = $mindistance;
							$shippingCharges1 = $finalDistance * $deliveryChargesPerKm;
							$charges = $shippingCharges1;
						} else {
							$shortestdistance = $mindistance;
							$charges = $deliveryCharge;
						}
					}
				}
			*/
			
		    $note = "<ol><li><b>&nbsp; Once you place an order, it cannot be cancelled.</b><br></li><li><b>&nbsp; If any product is missing from your order, the payment for that item will be refunded.</b><br></li></ol>";		
			$finalOrderAmount = $orderAmount + $charges;
			$res['submessage'] = $submessage;
			$res['itemTotal'] = $postData['subTotal'];
			$res['discount'] = strval($discount);
			$res["subTotal"] = $subTotal;
			$res["taxAmount"] = strval($taxAmount);
			$res["packaging_charges"] = $cartPackagingPrice;
			$res["shortestDistance"] = $shortestdistance;
			$res["shippingCharges"] = $charges; 
			$res["finalOrderAmount"] = strval(1);  
			//$res["finalOrderAmount"] = strval($finalOrderAmount);
			$res["isserviceable"] = $isserviceable;
			$res["note"]=$note;
			return $this->response(array("status" => "200", "message" => "Data Found", "result" => $res), 200);
		} else {
			return $this->response(array("status" => "400", "message" => "Required fields not found."), 400);
		}
	}
	
	public function calculateDistanceDeliveryCharges($storeLatitude, $storeLongitude, $clientCode, $cartAmount)
	{
	   // echo $cartAmount;
		$shortestdistance = 0;
		$charges = 0;
		$cityCode="";
		$clientLatitude = $clientLongitude = '';
		$addressLatitudeData = $this->GlobalModel->selectQuery("clientprofile.latitude,clientprofile.longitude,clientprofile.cityCode", "clientprofile", array("clientprofile.isActive" => "1", "clientprofile.isSelected" => "1", "clientprofile.clientCode" => $clientCode));
		if ($addressLatitudeData != false) {
			$data = $addressLatitudeData->result_array()[0];
			$clientLatitude = $data['latitude'];
			$clientLongitude = $data['longitude'];
			$cityCode = $data['cityCode'];
		}
		
		$deliveryCharge = $perKmCharges = $minimumKmForFixedCharges =$minimumChargesForFixedKm  = 0;
		$deliveryChargeCurrency = 'INR';
		$chargesResult = $this->GlobalModel->selectQuery('deliverycomissionandcharges.*', 'deliverycomissionandcharges', array('deliverycomissionandcharges.cityCode' => $cityCode ,'deliverycomissionandcharges.forWhichService'=>'customer_food'));
		$fixedDeliveryFlag=0;
		if ($chargesResult) {
			$charge = $chargesResult->result()[0];
			if($charge->isFixedChargesFlag==1){
			    
				if($cartAmount>$charge->minOrderAmountForFixedCharge || $cartAmount == 0){
			        $deliveryCharge=0;
			    }
			    else{
    			    $deliveryCharge=$charge->fixedChargesOrCommission;
			    }
				
			
			    $fixedDeliveryFlag=1; 
			}
			else
			{
			    $minimumKmForFixedCharges  = $charge->minimumKmForFixedCharges;
			    $minimumChargesForFixedKm =$charge->minimumChargesForFixedKm;
			    $perKmCharges = $charge->perKmCharges;
			
			}
		}  
	    
	    if ($clientLatitude != "" && $clientLongitude != "") {
			$arrayDist = array();
			$url = "https://maps.googleapis.com/maps/api/directions/json?destination=$storeLatitude,$storeLongitude&mode=driving&origin=$clientLatitude,$clientLongitude&key=" . PLACE_API_KEY;
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			$response = curl_exec($ch);
			curl_close($ch);
			$response_all = json_decode($response, TRUE);
			if (!empty($response_all['routes'])) {
				foreach ($response_all['routes'] as $res1 => $val) {
					foreach ($val['legs'] as $keys => $value) {
						$distance = round($value['distance']['value'] / 1000);
						array_push($arrayDist, $distance);
					}
				}
				$mindistance = min($arrayDist);
				if($fixedDeliveryFlag==1){
				    $shortestdistance=$mindistance;
				    $charges=$deliveryCharge;
				}
				else
				{
				    if($mindistance > $minimumKmForFixedCharges){
				        $finalDistance = $mindistance - $minimumKmForFixedCharges;
				        $shortestdistance = $mindistance;
				        $shippingCharges1 = $minimumChargesForFixedKm;
				        $shippingCharges1 = $shippingCharges1 + ($finalDistance * $perKmCharges);
				        $charges = $shippingCharges1;
				    }
				    else
				    {
				        $shortestdistance = $mindistance;
				        $charges = $minimumChargesForFixedKm;
				    }
				}
			}
		}
		$result['shortestdistance'] = $shortestdistance . ' Kms';
		$result['charges'] = $charges;
		return $result;
	}
	

	public function sendNotification($DeviceIdsArr, $title, $message, $orderCode)
	{
		$random = rand(0, 999);
		$dataArr = $notification = array();
		$dataArr['device_id'] = $DeviceIdsArr;
		$dataArr['message'] = $message; //Message which you want to send
		$dataArr['title'] = $title;
		$dataArr['order_id'] = $orderCode;
		$dataArr['random_id'] = $random;
		$dataArr['type'] = 'order';
		$notification['device_id'] = $DeviceIdsArr;
		$notification['message'] = $message; //Message which you want to send
		$notification['title'] = $title;
		$notification['order_id'] = $orderCode;
		$notification['random_id'] = $random;
		$notification['type'] = 'order';
		$notify = $this->notificationlibv_3->sendNotification($dataArr, $notification);
	    log_message("error", "Customer Notification result->".$notify);
	}
	
	public function sendTestNoti_get(){
		$this->sendNotification(array("cNhnwAbpRziq6Y8rlBqxyl:APA91bGMoQDHzBx1zvLpbt9NiQWo3SSpn5x5QFK1HfkC6iAG_tN_ggBFbjS_lL0aZjDU9qIhYQ9-ysHRAk6o2d40QIj1UMiZfLeJcawIScquQKQTAQbWU2-RITlk0nxvXpkLLGhhp8XT"),"hello","how r you?",rand(000,999));
	}
	

	public function getSearchListByTab_post()
	{
		$postData = $this->post();
		if (isset($postData['activeTab'])  && $postData['activeTab'] != "" && isset($postData["keyword"]) && $postData["keyword"] != "" && isset($postData['cityCode'])  && $postData['cityCode'] != "") {
			if ($postData['activeTab'] == 'menuitem') {
				$data = $this->getMenuItemListByKeyword($postData["keyword"], $postData['cityCode']);
			} elseif ($postData['activeTab'] == 'vendor') {
				$data = $this->getVendorListByKeyword($postData["keyword"], $postData['cityCode']);
			}
			//print_r($data);
			return $this->response(array("status" => "200", "result" => $data), 200);
		} else {
			return $this->response(array("status" => "400", "message" => "* are required fields."), 400);
		}
	}

	public function getVendorListByKeyword($keyword, $cityCode)
	{
		$data = array();
		$currentDate = date('Y-m-d');
		$table = "vendor";
		$orderColumns = "vendor.*,entitycategory.entityCategoryName,citymaster.cityName,customaddressmaster.place,vendoroffer.code as offerCode,vendoroffer.vendorCode as vendorCode,vendoroffer.coupanCode,vendoroffer.offerType,ifnull(GREATEST(ifnull(MAX(vendoroffer.discount),0),ifnull(MAX(vendoroffer.flatAmount),0)),'') as discount,vendoroffer.minimumAmount,vendoroffer.perUserLimit,vendoroffer.startDate,vendoroffer.endDate,vendoroffer.capLimit,vendoroffer.termsAndConditions,vendoroffer.isAdminApproved as vAdminapproved,vendoroffer.isActive as status";
		$condition = array("vendor.cityCode" => $cityCode, "vendor.isActive" => 1);
		$orderBy = array("vendor.isServiceable" => "DESC", "vendor.id" => "ASC");
		$join = array("entitycategory" => "vendor.entitycategoryCode = entitycategory.`code`", "vendorcuisinelineentries" => "vendor.`code` = vendorcuisinelineentries.vendorCode", "citymaster" => "vendor.cityCode = citymaster.`code`", "customaddressmaster" => "vendor.`addressCode` = customaddressmaster.code", "vendoroffer" => "vendor.`code` = vendoroffer.vendorCode");
		$joinType = array("entitycategory" => "inner", "vendorcuisinelineentries" => "left", "citymaster" => "left", "customaddressmaster" => "left", "vendoroffer" => "left");
		$groupBy = array('vendor.code');
		$like = array('vendor.entityName' => $keyword . '~both');
		$Records = $this->GlobalModel->selectQuery($orderColumns, $table, $condition, $orderBy, $join, $joinType, $like, 10, "", $groupBy, "");
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
				$like1 = array('cuisinemaster.cuisineName' => $keyword . '~both');
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
					else $vendorar['entityImage']  = "uploads/file_not_found.png";
				} else $vendorar['entityImage']  = "uploads/file_not_found.png";
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
				$discount = new stdClass();
				if ($r['discount'] != '' && $r['vAdminapproved'] == 1 && $r['startDate'] <= $currentDate && $r['endDate'] >= $currentDate && $r['status'] == 1) {
					$discount->offerCode = $r['offerCode'];
					$discount->vendorCode = $r['vendorCode'];
					$discount->coupanCode = $r['coupanCode'];
					$discount->offerType = $r['offerType'];
					$sign = '';
					if ($r['offerType'] === 'flat') {
						//$sign=' ₹';
					} else {
						//$sign=' %';
					}
					$discount->discount = $r['discount'] . $sign;
					$discount->minimumAmount = $r['minimumAmount'];
					$discount->perUserLimit = $r['perUserLimit'];
					$discount->startDate = date('d-m-Y h:i A', strtotime($r['startDate']));
					$discount->endDate = date('d-m-Y h:i A', strtotime($r['endDate']));
					$discount->capLimit = $r['capLimit'];
					$discount->termsAndConditions = $r['termsAndConditions'];
				}
				$vendorar['discount'] = $discount;
				$dayofweek = strtolower(date('l'));

				$timeData = $this->GlobalModel->selectQuery("vendorhours.code as hourCode,time_format(vendorhours.fromTime,'%h:%i %p') as fromTime,time_format(vendorhours.toTime,'%h:%i %p') as toTime", "vendorhours", array("vendorhours.vendorCode" => $r['code'], "vendorhours.weekDay" => $dayofweek));
				if ($timeData) {
					$vendorar['vendorHours'] = $timeData->result_array();
				} else {
					$vendorar['vendorHours'] = array();
				}

				$data[] = $vendorar;
			}
		}
		return $data;
	}

	public function getMenuItemListByKeyword($keyword, $cityCode)
	{
		$data = array();
		//echo 1;
		$tableName1 = "menucategory";
		$orderColumns1 = array("menucategory.*");
		$condition1 = array('menucategory.isActive' => 1);
		$orderBy1 = array('menucategory.priority' => 'ASC');
		$like = array();
		$Records = $this->GlobalModel->selectQuery($orderColumns1, $tableName1, $condition1, $orderBy1, array(), array(), $like);
		//echo $this->db->last_query();
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
				$condition2 = array("vendor.cityCode" => $cityCode, 'vendoritemmaster.isActive' => 1, "vendoritemmaster.menuCategoryCode" => $catCode, "vendoritemmaster.isAdminApproved" => 1);
				$orderBy2 = array('vendoritemmaster' . '.id' => 'DESC');
				$joinType2 = array("vendor" => "inner");
				$join2 = array("vendor" => "vendoritemmaster.vendorCode=vendor.code");
				$groupByColumn2 = array();
				$extraCondition2 = " (vendoritemmaster.isDelete=0 OR vendoritemmaster.isDelete IS NULL) or (vendoritemmaster.menuSubCategoryCode is Null or vendoritemmaster.menuSubCategoryCode='')";
				$like2 = array('vendoritemmaster.itemName' => $keyword . '~both');
				$itemRecords = $this->GlobalModel->selectQuery($orderColumns2, $tableName2, $condition2, $orderBy2, $join2, $joinType2, $like2, "", "", $groupByColumn2, $extraCondition2);
				//echo $this->db->last_query();	
				if ($itemRecords) {
					foreach ($itemRecords->result_array() as $r) {
						$vendorItemCode = $r['code'];
						$CCRecordsAddon = $this->GlobalModel->selectQuery('customizedcategory.*', 'customizedcategory', array('customizedcategory.isEnabled' => 1, 'customizedcategory.vendorItemCode' => $vendorItemCode, 'customizedcategory.categoryType' => 'addon'));
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

						$CCRecordsChoice = $this->GlobalModel->selectQuery('customizedcategory.*', 'customizedcategory', array('customizedcategory.isEnabled' => 1, 'customizedcategory.vendorItemCode' => $vendorItemCode, 'customizedcategory.categoryType' => 'choice'));
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
							if (file_exists($filepath))  $path =  'partner/uploads/' . $r['vendorCode'] . '/vendoritem/' . $r['itemPhoto'];
						}
						$mainitemArray[] = array(
							"vendorCode" => $r['vendorCode'],
							"itemCode" => $r['code'],
							"itemName" => $r['itemName'],
							"itemDescription" => $r['itemDescription'],
							"salePrice" => $r['salePrice'],
							"itemPhoto" => $path,
							"vendorName" => $r['entityName'],
							"vendorIsServiceable" => $r['vendorIsServiceable'],
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
				$orderBy3 = array('menusubcategory.id' => 'ASC');
				$like3 = array();
				$subCateRecords = $this->GlobalModel->selectQuery($orderColumns3, $tableName3, $condition3, $orderBy3, array(), array(), $like3);
				if ($subCateRecords) {
					$subcount	= sizeof($subCateRecords->result());
					foreach ($subCateRecords->result_array() as $subrow) {
						$subCategoryCode = $subrow['code'];
						$subCategoryName = $subrow['menuSubCategoryName'];

						$tableName4 = "vendoritemmaster";
						$orderColumns4 = array("vendoritemmaster.*,vendor.entityName,vendor.isServiceable as vendorIsServiceable");
						$condition4 = array("vendor.cityCode" => $cityCode, 'vendoritemmaster.isActive' => 1, "vendoritemmaster.vendorCode" => $getData['vendorCode'], "vendoritemmaster.menuSubCategoryCode" => $subCategoryCode, "vendoritemmaster.isAdminApproved" => 1);
						$orderBy4 = array('vendoritemmaster' . '.id' => 'DESC');
						$joinType4 = array("vendor" => "inner", "menusubcategory" => "inner");
						$join4 = array("vendor" => "vendoritemmaster.vendorCode=vendor.code", "menusubcategory" => "vendoritemmaster.menuSubCategoryCode=menusubcategory.code");
						$groupByColumn4 = array();
						$extraCondition4 = " (vendoritemmaster.isDelete=0 OR vendoritemmaster.isDelete IS NULL)";
						$like4 = array('vendoritemmaster.itemName' => $keyword . '~both');
						$Records = $this->GlobalModel->selectQuery($orderColumns4, $tableName4, $condition4, $orderBy4, $join4, $joinType4, $like4, "", "", $groupByColumn4, $extraCondition4);
						if ($Records) {
							$itemArray = array();
							$count = sizeof($Records->result_array());
							foreach ($Records->result_array() as $r) {

								$vendorItemCode = $r['code'];
								$CCRecordsAddon = $this->GlobalModel->selectQuery('customizedcategory.*', 'customizedcategory', array('customizedcategory.isEnabled' => 1, 'customizedcategory.vendorItemCode' => $vendorItemCode, 'customizedcategory.categoryType' => 'addon'));
								if ($CCRecordsAddon) {
									foreach ($CCRecordsAddon->result_array() as $ccra) {
										$customizedCategoryCode = $ccra['code'];
										$categoryTitle = $ccra['categoryTitle'];
										$CCRecordsAddonLine = $this->GlobalModel->selectQuery('customizedcategorylineentries.*', 'customizedcategorylineentries', array('customizedcategorylineentries.isEnabled' => 1, 'customizedcategorylineentries.customizedCategoryCode' => $customizedCategoryCode));
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

								$CCRecordsChoice = $this->GlobalModel->selectQuery('customizedcategory.*', 'customizedcategory', array('customizedcategory.isEnabled' => 1, 'customizedcategory.vendorItemCode' => $vendorItemCode, 'customizedcategory.categoryType' => 'choice'));
								if ($CCRecordsChoice) {
									foreach ($CCRecordsChoice->result_array() as $ccrc) {
										$customizedCategoryCode = $ccrc['code'];
										$categoryTitle = $ccrc['categoryTitle'];
										$CCRecordsChoiceLine = $this->GlobalModel->selectQuery('customizedcategorylineentries.*', 'customizedcategorylineentries', array('customizedcategorylineentries.isEnabled' => 1, 'customizedcategorylineentries.customizedCategoryCode' => $customizedCategoryCode));
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
									if (file_exists($filepath))  $path =  'partner/uploads/' . $r['vendorCode'] . '/vendoritem/' . $r['itemPhoto'];
								}
								$itemArray[] = array(
									"vendorCode" => $getData['vendorCode'],
									"itemCode" => $r['code'],
									"itemName" => $r['itemName'],
									"itemDescription" => $r['itemDescription'],
									"salePrice" => $r['salePrice'],
									"itemPhoto" => $path,
									"vendorName" => $r['entityName'],
									"vendorIsServiceable" => $r['vendorIsServiceable'],
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
		}
		return $data;
	}

	public function testNearestDB_get()
	{
		$this->testassignorder->allocate_delivery_boy_to_order('ORDER78542_855');
	}

	public function getPopularVendorList_get()
	{
		$getData = $this->get();
		$currentDate = date('Y-m-d');
		$cond = "vendor.isActive=1 and vendor.isPopular=1";
		if (isset($getData['cuisineCode']) && $getData['cuisineCode'] != "") {
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
		if (isset($getData['cityCode']) && $getData['cityCode'] != "") {
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

		$geoLocation = "";
		$having = "";
		if (isset($getData['latitude'])) {
			if (isset($getData['longitude'])) {
				if ($getData['latitude'] != "" && $getData['longitude'] != "") {
					$geoLocation = ",ROUND( 6371 * acos( cos( radians(" . $getData['latitude'] . ") ) * cos( radians( vendor.latitude ) ) * cos( radians( vendor.longitude ) - radians(" . $getData['longitude'] . ") ) + sin( radians(" . $getData['latitude'] . ") ) * sin(radians(vendor.latitude)) ) ) AS distance";
					$having = " HAVING distance <= 20";
				}
			}
		}

		$columns = "select vendor.*,entitycategory.entityCategoryName,citymaster.cityName,customaddressmaster.place" . $geoLocation . ",vendoroffer.code as offerCode,vendoroffer.vendorCode as vendorCode,vendoroffer.coupanCode,vendoroffer.offerType,ifnull(GREATEST(ifnull(MAX(vendoroffer.discount),0),ifnull(MAX(vendoroffer.flatAmount),0)),'') as discount,vendoroffer.minimumAmount,vendoroffer.perUserLimit,vendoroffer.startDate,vendoroffer.endDate,vendoroffer.capLimit,vendoroffer.termsAndConditions,vendoroffer.isAdminApproved as vAdminapproved,vendoroffer.isActive as status from vendor ";
		$orderBy = " order by vendor.isServiceable DESC , vendor.id ASC";
		$join = " INNER JOIN entitycategory ON vendor.entitycategoryCode = entitycategory.`code` LEFT JOIN vendorcuisinelineentries ON vendor.`code` = vendorcuisinelineentries.vendorCode ";
		$join .= " LEFT JOIN citymaster ON vendor.cityCode = citymaster.`code` LEFT JOIN customaddressmaster ON vendor.`addressCode` = customaddressmaster.code ";
		$join .= " LEFT JOIN vendoroffer ON vendor.`code` = vendoroffer.vendorCode ";
		$groupBy = " Group by vendor.code ";
		$limit = 10;
		$offset = "";
		if (isset($getData['offset'])) {
			if ($getData['offset'] > 0) $limit_offset = " limit " . $getData['offset'] . ',10';
			else $limit_offset = " limit 10";
		} else {
			$limit_offset = " limit 10";
		}
		if ($cond != "") $whereCondition = " where " . $cond;
		else $whereCondition = "";
		$query = $columns . $join . $whereCondition . $groupBy . $having . $orderBy . $limit_offset;

		$result = $this->db->query($query);
		//echo $this->db->last_query();
		if ($result && $result->num_rows() > 0) {

			$data = array();
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
					if (file_exists($path)) $vendorar['entityImage'] = base_url() . $path;
					else $vendorar['entityImage']  = "uploads/file_not_found.png";
				} else $vendorar['entityImage']  = "uploads/file_not_found.png";

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
				$discount = new stdClass();
				if ($r['discount'] != '' && $r['vAdminapproved'] == 1 && $r['startDate'] <= $currentDate && $r['endDate'] >= $currentDate && $r['status'] == 1) {
					$discount->offerCode = $r['offerCode'];
					$discount->vendorCode = $r['vendorCode'];
					$discount->coupanCode = $r['coupanCode'];
					$discount->offerType = $r['offerType'];
					$sign = '';
					if ($r['offerType'] === 'flat') {
						$sign = ' ₹';
					} else {
						$sign = ' %';
					}
					$discount->discount = $r['discount'] . $sign;
					$discount->minimumAmount = $r['minimumAmount'];
					$discount->perUserLimit = $r['perUserLimit'];
					$discount->startDate = date('d-m-Y h:i A', strtotime($r['startDate']));
					$discount->endDate = date('d-m-Y h:i A', strtotime($r['endDate']));
					$discount->capLimit = $r['capLimit'];
					$discount->termsAndConditions = $r['termsAndConditions'];
				}
				$vendorar['discount'] = $discount;
				$dayofweek = strtolower(date('l'));

				$timeData = $this->GlobalModel->selectQuery("vendorhours.code as hourCode,time_format(vendorhours.fromTime,'%h:%i %p') as fromTime,time_format(vendorhours.toTime,'%h:%i %p') as toTime", "vendorhours", array("vendorhours.vendorCode" => $r['code'], "vendorhours.weekDay" => $dayofweek));
				if ($timeData) {
					$vendorar['vendorHours'] = $timeData->result_array();
				} else {
					$vendorar['vendorHours'] = array();
				}

				$data[] = $vendorar;
			}

			$response['vendors'] = $data;
			return $this->response(array("status" => "200", "message" => 'Data Found', "result" => $response), 200);
		} else {
			return $this->response(array("status" => "300", "message" => 'No Data Found'), 200);
		}
	}
	
	   
}
