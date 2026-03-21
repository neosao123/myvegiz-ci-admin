<?php
require(APPPATH . '/libraries/REST_Controller.php');

use Restserver\Libraries\REST_Controller;

class Vendor extends REST_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->helper('form', 'url', 'html');
		$this->load->library('form_validation');
		$this->load->model('GlobalModel');
		$this->load->model('ApiModel');
		$this->load->library('notificationlibv_3');
		$this->load->library('firestore');
	}

	public function vendorLoginProcess_post()
	{
		$postData = $this->post();
		if (isset($postData['ownerContact']) && $postData['ownerContact'] != "" && isset($postData['password']) &&  $postData['password'] != "") {
			$ownerContact = trim($postData["ownerContact"]);
			$password = trim($postData["password"]);
			$enc_password = md5($this->input->post('password'));
			$table = "vendor";
			$column = array("vendor.*");
			$condition = array('vendor.ownerContact' => $ownerContact, 'vendor.password' => $enc_password);
			$result = $this->GlobalModel->selectQuery($column, $table, $condition);
			if ($result) {
				$orderColumns = "vendor.code,vendor.ownerContact,vendor.entityName,vendor.firstName,vendor.lastName,vendor.middleName,vendor.address,vendor.fssaiNumber,vendor.gstNumber,vendor.latitude,vendor.longitude,vendor.entityImage,vendor.ownerContact,vendor.entityContact,vendor.email,vendor.cityCode,vendor.addressCode,vendor.packagingType,citymaster.cityName,customaddressmaster.place,customaddressmaster.state,customaddressmaster.district,customaddressmaster.taluka,customaddressmaster.pincode";
				$table = "vendor";
				$condition2 = array('vendor.ownerContact' => $ownerContact);
				$join = array(
					"customaddressmaster" => "vendor.addressCode=customaddressmaster.code",
					"citymaster" => "vendor.cityCode=citymaster.code"
				);
				$joinType = array(
					"customaddressmaster" => "left",
					"citymaster" => "inner"
				);
				$resultData = $this->GlobalModel->selectQuery($orderColumns, $table, $condition2, array(), $join, $joinType);
				// $r=$this->db->last_query();
				if ($resultData) {
					$resdata = $resultData->result_array()[0];

					$data['code'] = $resdata['code'];
					$data['entityName'] = $resdata['entityName'];
					$data['firstName'] = $resdata['firstName'];
					$data['lastName'] = $resdata['lastName'];
					$data['middleName'] = $resdata['middleName'];
					$data['address'] = $resdata['address'];
					$data['fssaiNumber'] = $resdata['fssaiNumber'];
					$data['gstNumber'] = $resdata['gstNumber'];
					$data['latitude'] =  $resdata['latitude'];
					$data['longitude'] = $resdata['longitude'];
					$data['ownerContact'] = $resdata['ownerContact'];
					$data['entityContact'] = $resdata['entityContact'];
					$data['email'] = $resdata['email'];
					$data['cityCode'] = $resdata['cityCode'];
					$data['addressCode'] = $resdata['addressCode'];
					$data['packagingType'] = $resdata['packagingType'];
					$data['cityName'] = $resdata['cityName'];
					$data['place'] = $resdata['place'];
					$data['district'] = $resdata['district'];
					$data['taluka'] = $resdata['taluka'];
					$data['pincode'] = $resdata['pincode'];
					$data['state'] = $resdata['state'];
					$image = "noimage";
					if ($resdata['entityImage'] != "") {
						$path = 'uploads/restaurant/' . $resdata['code'] . '/' . $resdata['entityImage'];
						if (file_exists($path)) {
							$data['entityImage'] = base_url($path);
						}
					}
					return $this->response(array("status" => "200", "message" => "Login Successfully...", "result" => $data), 200);
				} else {
					return $this->response(array("status" => "300", "message" => "Invalid Mobile OR Code OR Password."), 200);
				}
			} else {
				return $this->response(array("status" => "300", "message" => "Invalid Mobile OR Code OR Password."), 200);
			}
		} else {
			return $this->response(array("status" => "400", "message" => " * field(s) are required."), 200);
		}
	}

	public function newPasswordUpdate_post()
	{
		$postData = $this->post();
		if (isset($postData['vendorCode']) && $postData['vendorCode'] != "" && isset($postData['oldPassword']) && $postData['oldPassword'] != "" && isset($postData['newPassword']) && $postData['newPassword'] != "") {
			$md5_old = md5($postData['oldPassword']);
			$condition['code'] = $postData['vendorCode'];
			$condition['password'] = $md5_old;
			$table = "vendor";
			$result = $this->GlobalModel->selectQuery("vendor.code", "vendor", $condition);

			if ($result) {
				$data['password'] = md5(trim($postData['newPassword']));
				$data['editID'] = $postData['vendorCode'];
				$data['editIP'] = $_SERVER['REMOTE_ADDR'];
				$result = $this->GlobalModel->doEdit($data, 'vendor', $postData['vendorCode']);
				if ($result == 'true') {
					return $this->response(array("status" => "200", "message" => "New password is updated successfully.."), 200);
				} else {
					return $this->response(array("status" => "300", "message" => "Failed to update the new password"), 200);
				}
			} else {
				return $this->response(array("status" => "300", "message" => "Please provide an correct old password."), 200);
			}
		} else {
			return $this->response(array("status" => "400", "message" => " * field(s) are required."), 200);
		}
	}

	public function getprofileinfoVendor_post()
	{
		$postData = $this->post();
		if (isset($postData["vendorCode"]) && $postData["vendorCode"] != "") {
			$vendorCode = trim($postData["vendorCode"]);
			$table = "vendor";
			$column = array("vendor.*");
			$condition = array('vendor.Code' => $vendorCode);
			$result = $this->GlobalModel->selectQuery($column, $table, $condition);
			if ($result) {
				$orderColumns = "vendor.code,vendor.ownerContact,vendor.entityName,vendor.firstName,vendor.lastName,vendor.middleName,vendor.address,vendor.fssaiNumber,vendor.gstNumber,vendor.latitude,vendor.longitude,vendor.entityImage,vendor.ownerContact,vendor.entityContact,vendor.email,vendor.cityCode,vendor.addressCode,vendor.packagingType,
					             vendor.password,vendor.entitycategoryCode,vendor.bankDetails,vendor.cartPackagingPrice,vendor.gstApplicable,vendor.gstPercent,vendor.firebaseId,vendor.walletAmount,vendor.isServiceable,vendor.manualIsServiceable,citymaster.cityName,customaddressmaster.place,customaddressmaster.state,customaddressmaster.district,customaddressmaster.taluka,customaddressmaster.pincode";
				$table = "vendor";
				$condition2 = array('vendor.Code' => $vendorCode);
				$join = array(
					"customaddressmaster" => "vendor.addressCode=customaddressmaster.code",
					"citymaster" => "vendor.cityCode=citymaster.code"
				);
				$joinType = array(
					"customaddressmaster" => "left",
					"citymaster" => "inner"
				);
				$resultData = $this->GlobalModel->selectQuery($orderColumns, $table, $condition2, array(), $join, $joinType);
				$r = $this->db->last_query();
				// print_r($resultData->result());
				// exit;
				if ($resultData) {
					$resdata = $resultData->result_array()[0];

					$data['code'] = $resdata['code'];
					$data['entityName'] = $resdata['entityName'];
					$data['firstName'] = $resdata['firstName'];
					$data['lastName'] = $resdata['lastName'];
					$data['middleName'] = $resdata['middleName'];
					$data['address'] = $resdata['address'];
					$data['fssaiNumber'] = $resdata['fssaiNumber'];
					$data['gstNumber'] = $resdata['gstNumber'];
					$data['latitude'] =  $resdata['latitude'];
					$data['longitude'] = $resdata['longitude'];
					$data['ownerContact'] = $resdata['ownerContact'];
					$data['entityContact'] = $resdata['entityContact'];
					$data['password'] = $resdata['password'];
					$data['email'] = $resdata['email'];
					$data['cityCode'] = $resdata['cityCode'];
					$data['addressCode'] = $resdata['addressCode'];
					$data['packagingType'] = $resdata['packagingType'];


					$data['entitycategoryCode'] = $resdata['entitycategoryCode'];
					$data['cartPackagingPrice'] = $resdata['cartPackagingPrice'];
					$data['gstApplicable'] = $resdata['gstApplicable'];
					$data['gstPercent'] = $resdata['gstPercent'];
					$data['bankDetails'] = $resdata['bankDetails'];


					$data['firebaseId'] = $resdata['firebaseId'];
					$data['walletAmount'] = $resdata['walletAmount'];
					$data['isServiceable'] = $resdata['isServiceable'];
					$data['manualIsServiceable'] = $resdata['manualIsServiceable'];

					$data['cityName'] = $resdata['cityName'];
					$data['place'] = $resdata['place'];
					$data['district'] = $resdata['district'];
					$data['taluka'] = $resdata['taluka'];
					$data['pincode'] = $resdata['pincode'];
					$data['state'] = $resdata['state'];



					$image = "noimage";
					if ($resdata['entityImage'] != "") {
						$path = 'uploads/restaurant/' . $resdata['code'] . '/' . $resdata['entityImage'];
						if (file_exists($path)) {
							$data['entityImage'] = base_url($path);
						}
					}
					return $this->response(array("status" => "200", "message" => "getprofileinfo Successfully...", "result" => $data), 200);
				} else {
					return $this->response(array("status" => "300", "message" => "Invalid OR Code ." . $r), 200);
				}
			} else {
				return $this->response(array("status" => "300", "message" => "Invalid  Code ." . $r), 200);
			}
		} else {
			return $this->response(array("status" => "400", "message" => " * field(s) are required."), 200);
		}
	}

	public function getMainMenuList_get()
	{
		$tableName = "menucategory";
		$orderColumns = array("menucategory.code,menucategory.menuCategoryName");
		$condition = array('menucategory.isActive' => 1);
		$orderBy = array('menucategory' . '.priority' => 'ASC');
		$joinType = array();
		$join = array();
		$groupByColumn = array();
		$limit = '';
		$offset = '';
		$extraCondition = "(menucategory.isDelete=0 OR menucategory.isDelete IS NULL) AND menucategory.isActive=1 ";
		$like = array();
		$MainMenuListResult = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);

		if ($MainMenuListResult) {
			return $this->response(array("status" => "200", "result" => $MainMenuListResult->result()), 200);
		} else {
			$data = new stdClass();
			$this->response(array("status" => "300", "message" => "Data not found.", "result" => $data), 200);
		}
	}

	public function getMenuItemList_post()
	{
		$getData = $this->post();
		if (isset($getData['vendorCode']) && $getData['vendorCode'] != "") {
			$mainitemArray = array();
			$subCategoryItemArray = array();
			$maincount = 0;
			$tableName = "menucategory";
			$orderColumns = array("menucategory.*");
			$condition = array('menucategory.isActive' => 1);
			$orderBy = array('menucategory' . '.id' => 'ASC');
			$Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy);
			if ($Records) {
				foreach ($Records->result_array() as $ra) {

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
					if ($itemRecords) {
						foreach ($itemRecords->result_array() as $r) {
							$addonArray = array();
							$choiceArray = array();
							$vendorItemCode = $r['code'];
							$CCRecordsAddon = $this->GlobalModel->selectQuery('customizedcategory.*', 'customizedcategory', array('customizedcategory.vendorItemCode' => $vendorItemCode, 'customizedcategory.categoryType' => 'addon'));
							if ($CCRecordsAddon) {
								foreach ($CCRecordsAddon->result_array() as $ccra) {
									$customizedCategoryCode = $ccra['code'];
									$categoryTitle = $ccra['categoryTitle'];
									$CCRecordsAddonLine = $this->GlobalModel->selectQuery('customizedcategorylineentries.*', 'customizedcategorylineentries', array('customizedcategorylineentries.customizedCategoryCode' => $customizedCategoryCode));
									$addonCustomizedCategoryArray = array();
									if ($CCRecordsAddonLine) {
										//$addonCustomizedCategoryArray = array();
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
										//$choiceCustomizedCategoryArray = array();
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
								if (file_exists($filepath)) {
									$imagepath =  '/partner/uploads/' . $r['vendorCode'] . '/vendoritem/' . $r['itemPhoto'];
									$path = base_url($imagepath);
								}
							}
							$mainitemArray[] = array(
								"vendorCode" => $getData['vendorCode'],
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

					$tableName3 = "menusubcategory";
					$orderColumns3 = array("menusubcategory.*");
					$condition3 = array('menusubcategory.isActive' => 1, "menusubcategory.menuCategoryCode" => $catCode);
					$orderBy3 = array('menusubcategory' . '.id' => 'ASC');
					$subCateRecords = $this->GlobalModel->selectQuery($orderColumns3, $tableName3, $condition3, $orderBy3);
					$data = array();
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
									$addonArray = array();
									$choiceArray = array();
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
										if (file_exists($filepath)) {
											$imagepath =  '/partner/uploads/' . $r['vendorCode'] . '/vendoritem/' . $r['itemPhoto'];
											$path = base_url($imagepath);
										}
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
								if (!empty($itemArray)) {
									$subCategoryItemArray[] = array("subCategoryCode" => $subCategoryCode, "subCategoryName" => $subCategoryName, "count" => $count, "itemList" => $itemArray);
								}
							}
						}
					}
				}
				if ($maincount > 0) {
					$data[] = array("menuCategoryCode" => $catCode, "count" => $maincount, "menuCategoryName" => $catName, "itemList" => $mainitemArray, "subCategoryList" => $subCategoryItemArray);
				}
				//$response['menuItemList'] = array( "count" => $maincount, "itemList" => $mainitemArray, "subCategoryList" => $subCategoryItemArray);
				return $this->response(array("status" => "200", "message" => 'Data Found', "result" => $data), 200);
			} else {
				return $this->response(array("status" => "200", "message" => 'No Data Found'), 200);
			}
		} else {
			return $this->response(array("status" => "400", "message" => " * field(s) are required."), 200);
		}
	}

	public function getMenuItemListold_get()
	{
		$getData = $this->get();
		if (isset($getData['vendorCode']) && $getData['vendorCode'] != "" && isset($getData['categoryCode']) && $getData['categoryCode'] != "") {

			$mainitemArray = array();
			$addonArray = array();
			$choiceArray = array();
			$maincount = 0;
			$catCode = $getData['categoryCode'];
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
						if (file_exists($filepath))  $path =  'partner/uploads/' . $r['vendorCode'] . '/vendoritem/' . $r['itemPhoto'];
					}
					$mainitemArray[] = array(
						"vendorCode" => $getData['vendorCode'],
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
			$orderBy3 = array('menusubcategory' . '.id' => 'ASC');
			$subCateRecords = $this->GlobalModel->selectQuery($orderColumns3, $tableName3, $condition3, $orderBy3);
			$data = array();
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

			$data = array("count" => $maincount, "itemList" => $mainitemArray, "subCategoryList" => $subCategoryItemArray);
			$response['menuItemList'] = $data;
			return $this->response(array("status" => "200", "message" => 'Data Found', "result" => $response), 200);
		} else {
			return $this->response(array("status" => "400", "message" => " * field(s) are required."), 200);
		}
	}

	public function changeItemStockStatus_post()
	{
		$postData = $this->post();
		if (isset($postData['itemCode']) && $postData['itemCode'] != "" && isset($postData['flag']) && $postData['flag'] != "" && isset($postData["vendorCode"]) && $postData["vendorCode"] != "") {
			$itemCode = trim($postData["itemCode"]);
			$flag = trim($postData["flag"]);

			$data = array(
				'itemActiveStatus' => $flag,
			);

			$resultData = $this->GlobalModel->doEdit($data, 'vendoritemmaster', $itemCode);
			if ($resultData) {
				return $this->response(array("status" => "200", "message" => 'Service status updated successfully.'), 200);
			} else {
				return $this->response(array("status" => "300", "message" => "Failed to update the status..."), 200);
			}
		} else {
			return $this->response(array("status" => "400", "message" => " * field(s) are required."), 200);
		}
	}

	// Start update profile
	public function updateFirebaseId_post()
	{
		$postData = $this->post();
		if (isset($postData["vendorCode"]) && $postData["vendorCode"] != '' && isset($postData["firebaseId"]) && $postData["firebaseId"] != '') {
			$vendorCode = $postData["vendorCode"];
			$dataDevice['firebaseId'] = $postData['firebaseId'];
			$result = $this->GlobalModel->doEdit($dataDevice, 'vendor', $vendorCode);

			if ($result != 'false') {
				return $this->response(array("status" => "200", "message" => "Firebase Id Update Successfully"), 200);
			} else {
				return $this->response(array("status" => "300", "message" => " Failed to update Firebase Id."), 200);
			}
		} else {
			$this->response(array("status" => "400", "message" => " * are required field(s)."), 200);
		}
	}
	// End update firebaseId

	// fetch all offers by vendor
	public function getOffersByVendor_post()
	{
		$postData = $this->post();
		if (isset($postData["vendorCode"]) && $postData["vendorCode"] != '') {
			$vendorCode = $postData["vendorCode"];
			$tableName = "vendoroffer";
			$orderColumns = array("vendoroffer.*");
			$condition = array('vendoroffer' . '.vendorCode' => $vendorCode);
			$orderBy = array('vendoroffer' . '.id' => 'DESC');
			$joinType = array();
			$join = array();
			$groupByColumn = array();
			$limit = '';
			$offset = '';
			$extraCondition = "(vendoroffer.isDelete=0 OR vendoroffer.isDelete IS NULL) AND vendoroffer.isActive=1";
			$like = array();
			$Offersresult = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
			if ($Offersresult) {
				return $this->response(array("status" => "200", "result" => $Offersresult->result()), 200);
			} else {
				$data = new stdClass();
				$this->response(array("status" => "300", "message" => "Data not found.", "result" => $data), 200);
			}
		} else {
			$this->response(array("status" => "400", "message" => " * are required field(s)."), 200);
		}
	}

	// fetch  offer details by  offerId For Edit....
	public function getOffersByOfferID_post()
	{
		$postData = $this->post();
		if (isset($postData["offerCode"]) && $postData["offerCode"] != '') {
			$offerCode = $postData["offerCode"];
			$tableName = "vendoroffer";
			$orderColumns = array("vendoroffer.*");
			$condition = array('vendoroffer' . '.code' => $offerCode);
			$orderBy = array('vendoroffer' . '.id' => 'DESC');
			$joinType = array();
			$join = array();
			$groupByColumn = array();
			$limit = '';
			$offset = '';
			$extraCondition = "";
			$like = array();
			$Offersresult = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
			if ($Offersresult) {
				return $this->response(array("status" => "200", "result" => $Offersresult->result()), 200);
			} else {
				$data = new stdClass();
				$this->response(array("status" => "300", "message" => "Data not found.", "result" => $data), 200);
			}
		} else {
			$this->response(array("status" => "400", "message" => " * are required field(s)."), 200);
		}
	}

	public function onlineOfflineStatusChange_post()
	{
		$postData = $this->post();
		if (isset($postData['flag']) && $postData['flag'] != "" && isset($postData["vendorCode"]) && $postData["vendorCode"] != "") {
			$flag = trim($postData["flag"]);

			$data = array(
				'isServiceable' => $flag,
			);

			$resultData = $this->GlobalModel->doEdit($data, 'vendor', $postData["vendorCode"]);
			if ($resultData) {
				return $this->response(array("status" => "200", "message" => 'Service status updated successfully.'), 200);
			} else {
				return $this->response(array("status" => "300", "message" => "Failed to update the status..."), 200);
			}
		} else {
			return $this->response(array("status" => "400", "message" => " * field(s) are required."), 200);
		}
	}

	public function getOnlineOfflineStatus_post()
	{
		$postData = $this->post();
		if (isset($postData["vendorCode"]) && $postData["vendorCode"] != "") {
			$vendorCode = trim($postData["vendorCode"]);
			$table = "vendor";
			$column = array("vendor.isServiceable");
			$condition = array('vendor.Code' => $vendorCode);
			$result = $this->GlobalModel->selectQuery($column, $table, $condition);
			if ($result) {
				return $this->response(array("status" => "200", "message" => "getprofileinfo Successfully...", "result" => $result->result_array()[0]), 200);
			} else {
				return $this->response(array("status" => "300", "message" => "Invalid  Vendor Code"), 200);
			}
		} else {
			return $this->response(array("status" => "400", "message" => " * field(s) are required."), 200);
		}
	}

	public function getDashboardCounts_post()
	{
		$postData = $this->post();
		if (isset($postData['restaurantCode']) && $postData['restaurantCode'] != "") {

			$resCode = $postData['restaurantCode'];
			$penaltyCommissionAmount = $orginalCommissionAmount = $custAddonCat = $custChoiceCat = $totalOrders = $pendingOrders_del = $placedOrders_del = $deliveredOrders_del = $rejectedOrders_del = $cancelledOrder_del = 0;
			$vendoroffer = $vendoritems = $dlbResetPasswordCounts = 0;

			$extraCondition1 = "vendorordermaster.orderStatus IN ('RFP','PUP','PRE','RCH','DEL','RJT') ";
			$dataTotalOrders = $this->GlobalModel->selectQuery('COUNT(id) as totalOrders', 'vendorordermaster', array('vendorordermaster.vendorCode' => $resCode, 'vendorordermaster.isActive' => 1), array(), array(), array(), array(), '', '', array(), $extraCondition1);
			if ($dataTotalOrders) {
				$totalOrders = $dataTotalOrders->result_array()[0]['totalOrders'];
			}

// 			$condition['vendorCode'] = $resCode;
// 			$condition['orderStatus'] = 'PND';
// 			$condition['deliveryBoyCode!='] = '';
			$condition=array("vendorCode"=>$resCode,"orderStatus"=>'PLC');
			$extraCondition=" AND deliveryBoyCode!=''";
			$result = $this->GlobalModel->selectQuery("count(*) as Count", "vendorordermaster", $condition);//,array(), array(), array(), array(), "", "", array(), $extraCondition
			if ($result) $pendingOrders_del = $result->result_array()[0]['Count'];


   
			/*$condition1['vendorCode'] = $resCode;
			$condition1['orderStatus'] = 'PLC';
			$result = $this->GlobalModel->selectQuery("count(*) as Count","vendorordermaster",$condition1);
			if($result) $placedOrders_del = $result->result_array()[0]['Count'];*/

			//$data['placeOrder'] = 0;
			$extraCondition = "vendorordermaster.orderStatus IN ('RFP','PUP','PRE','RCH') ";
			$dataPlaceOrder = $this->GlobalModel->selectQuery('COUNT(id) as countPlaced', 'vendorordermaster', array('vendorordermaster.vendorCode' => $resCode), array(), array(), array(), array(), '', '', array(), $extraCondition);
			if ($dataPlaceOrder) {
				$placedOrders_del = $dataPlaceOrder->result_array()[0]['countPlaced'];
			}

			$condition1['vendorCode'] = $resCode;
			$condition1['orderStatus'] = 'DEL';
			$result1 = $this->GlobalModel->selectQuery("count(*) as Count", "vendorordermaster", $condition1);
			if ($result1) $deliveredOrders_del = $result1->result_array()[0]['Count'];

			$condition3['vendorCode'] = $resCode;
			$condition3['orderStatus'] = 'RJT';
			$result2 = $this->GlobalModel->selectQuery("count(*) as Count", "vendorordermaster", $condition3);
			if ($result2) $rejectedOrders_del = $result2->result_array()[0]['Count'];

			$condition4['vendorCode'] = $resCode;
			$condition4['orderStatus'] = 'CAN';
			$result3 = $this->GlobalModel->selectQuery("count(*) as Count", "vendorordermaster", $condition4);
			if ($result3) $cancelledOrders_del = $result3->result_array()[0]['Count'];


			$condition5['vendorCode'] = $resCode;
			$condition5['orderStatus'] = 'PRE';
			$result4 = $this->GlobalModel->selectQuery("count(*) as Count", "vendorordermaster", $condition5);
			if ($result4) $preparing_order = $result4->result_array()[0]['Count'];

			$condition6['vendorCode'] = $resCode;
			$condition6['orderStatus'] = 'RFP';
			$result5 = $this->GlobalModel->selectQuery("count(*) as Count", "vendorordermaster", $condition6);
			if ($result5) $readyforpickup_order = $result5->result_array()[0]['Count'];

			$dataVendorOffer = $this->GlobalModel->selectQuery('COUNT(id) as countOffer', 'vendoroffer', array('vendoroffer.vendorCode' => $resCode));
			if ($dataVendorOffer) $vendoroffer = $dataVendorOffer->result_array()[0]['countOffer'];

			$dataVendorItems = $this->GlobalModel->selectQuery('COUNT(id) as countItems', 'vendoritemmaster', array('vendoritemmaster.vendorCode' => $resCode, 'vendoritemmaster.isAdminApproved' => 1));
			if ($dataVendorItems) $vendoritems = $dataVendorItems->result_array()[0]['countItems'];


			$orderBy = array();
			$joinType = array('customizedcategory' => 'inner');
			$join = array('customizedcategory' => 'customizedcategory.vendorItemCode=vendoritemmaster.code');
			$datacustChoiceCat = $this->GlobalModel->selectQuery('COUNT(vendoritemmaster.id) as countCustChoiceCat', 'vendoritemmaster', array('vendoritemmaster.vendorCode' => $resCode, 'customizedcategory.categoryType' => 'choice'), $orderBy, $join, $joinType, array(), '', '', array(), '');
			if ($datacustChoiceCat) {
				$custChoiceCat = $datacustChoiceCat->result_array()[0]['countCustChoiceCat'];
			}

			$orderBy = array();
			$joinType = array('customizedcategory' => 'inner');
			$join = array('customizedcategory' => 'customizedcategory.vendorItemCode=vendoritemmaster.code');
			$datacustAddonCat = $this->GlobalModel->selectQuery('COUNT(vendoritemmaster.id) as countCustAddonCat', 'vendoritemmaster', array('vendoritemmaster.vendorCode' => $resCode, 'customizedcategory.categoryType' => 'addon'), $orderBy, $join, $joinType, array(), '', '', array(), '');
			if ($datacustAddonCat) {
				$custAddonCat = $datacustAddonCat->result_array()[0]['countCustAddonCat'];
			}


			$cond = array('vendorordercommission.commissionType' => 'regular', 'vendorordercommission.deliveryBoyCode' => $resCode);
			$extraCon = "vendorordercommission.commissionType='' OR vendorordercommission.commissionType IS NULL";
			$orginalComm = $this->GlobalModel->selectQuery('ifnull(SUM(vendorordercommission.grandTotal),0) as orginalComm', 'vendorordercommission', $cond, array(), array(), array(), array(), '', '', array(), $extraCon);
			if ($orginalComm) {
				$orginalCommissionAmount = $orginalComm->result_array()[0]['orginalComm'];
			}

			$cond1 = array('vendorordercommission.commissionType' => 'penalty', 'vendorordercommission.deliveryBoyCode' => $resCode);
			$penaltyComm = $this->GlobalModel->selectQuery('ifnull(SUM(vendorordercommission.grandTotal),0) as penalty', 'vendorordercommission', $cond1, array(), array(), array(), array(), '', '', array(), '');
			if ($penaltyComm) {
				$penaltyCommissionAmount = $penaltyComm->result_array()[0]['penalty'];
			}

			$totalEarning = $orginalCommissionAmount - $penaltyCommissionAmount;

			$activePlaces = $this->GlobalModel->getCountOfPerticularValue('customaddressmaster', 'isService', '1');
			$recentInward = $this->GlobalModel->getCountWthField('inwardentries', 'code');
			$resetPwd = $this->GlobalModel->getTableRecordCount('resetpassword');
			$todayssaleAmt = $this->GlobalModel->getCountWithAmount('ordermaster', 'totalPrice', 'deliveredTime');
			$todayspurchaseAmt = $this->GlobalModel->getCountWithAmount('inwardentries', 'total', 'inwardDate');
			//$customer = $this->GlobalModel->getCountOfPerticularValue('clientmaster','isDelete',0);
			//$orders = $this->GlobalModel->selectData('ordermaster');

			$orderColumns = array("count(resetpassword.id) pCount,usermaster.code,usermaster.role");
			$cond = array("resetpassword.isActive" => 1, "usermaster.role" => "DLB");
			$orderBy = array('resetpassword' . ".id" => 'ASC');
			$join = array("usermaster" => "usermaster.code = resetpassword.userCode");
			$joinType = array("usermaster" => "inner");
			$like = array();
			$limit = "";
			$offset = "";
			$groupByColumn = array();
			$extraCondition = "";

			$p_result = $this->GlobalModel->selectQuery($orderColumns, 'resetpassword', $cond, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
			if ($p_result) $dlbResetPasswordCounts = $p_result->result_array()[0]["pCount"];
			$res['orderCounts'] = array(
				'totalOrders' => $totalOrders,
				'pendingOrders' => $pendingOrders_del,
				'placedOrders' => $placedOrders_del,
				'deliveredOrders' => $deliveredOrders_del,
				'cancelledOrders' => $cancelledOrders_del,
				'rejectedOrders' => $rejectedOrders_del,
				'readyforpickupOrders' => $readyforpickup_order,
				'preparingOrders' => $preparing_order,
				'vendoroffer' => $vendoroffer,
				'custChoiceCat' => $custChoiceCat,
				'custAddonCat' => $custAddonCat,
				'vendoritems' => $vendoritems,
				'activePlaces' => $activePlaces,
				'recentInward' => $recentInward,
				'resetPwd' => $resetPwd,
				'totalEarning' => $totalEarning,
				'todaysSaleAmount' => $todayssaleAmt,
				'todaysPurchaseAmount' => $todayspurchaseAmt,
				//'customerCount' => $customer,
				//'ordersCount' => $orders,
				'dlbResetPasswordCount' => $dlbResetPasswordCounts,
			);
			return $this->response(array("status" => "200", "message" => "Success", "result" => $res), 200);
		} else {
			return $this->response(array("status" => "400", "message" => " * field(s) are required."), 200);
		}
	}

	public function getOrderDetails_post()
	{
		$postData = $this->post();
		$dataDeliveryBoy = array();
		$clientOrderList = array();
		$totalOrders = 0;
		if (isset($postData['restaurantCode']) && $postData['restaurantCode'] != "" && isset($postData['orderCode']) && $postData['orderCode'] != "") {
			$tableName = "vendorordermaster";
			$orderColumns = array("clientmaster.name as customerName,clientmaster.mobile,vendorordermaster.deliveryBoyCode,usermaster.userEmail,usermaster.mobile as dBoyContactNumber,usermaster.name as dBoyName,usermaster.latitude as dBoyLatitude,usermaster.longitude as dBoyLongitude,vendorordermaster.clientCode,vendorordermaster.code as orderCode,vendorordermaster.shippingCharges as deliveryCharges,vendorordermaster.paymentmode,vendorordermaster.address,vendorordermaster.phone,vendorordermaster.grandTotal as orderTotalPrice,vendorordermaster.subTotal,vendorordermaster.tax,vendorordermaster.discount,vendorordermaster.totalPackgingCharges,vendorordermaster.addDate as orderDate, orderstatusmaster.statusName as orderStatus, paymentstatusmaster.statusName as paymentStatus");
			$cond = array('vendorordermaster.code' => $postData['orderCode'], 'vendorordermaster.vendorCode' => $postData['restaurantCode']);
			$orderBy = array('vendorordermaster.id' => 'DESC');
			$join = array('usermaster' => 'usermaster.code=vendorordermaster.deliveryBoyCode', 'clientmaster' => 'vendorordermaster.clientCode=clientmaster.code', 'orderstatusmaster' => 'vendorordermaster.orderStatus=orderstatusmaster.statusSName', 'paymentstatusmaster' => 'vendorordermaster.paymentStatus=paymentstatusmaster.statusSName');
			$joinType = array('usermaster' => 'left', 'clientmaster' => 'inner', 'orderstatusmaster' => 'inner', 'paymentstatusmaster' => 'inner');
			$resultQuery = $this->GlobalModel->selectQuery($orderColumns, $tableName, $cond, $orderBy, $join, $joinType);
			if ($resultQuery) {
				$clientOrderList = $resultQuery->result_array();
				$totalOrders = sizeof($clientOrderList);
				$linetableName = "vendororderlineentries";
				$lineorderColumns = array("vendororderlineentries.orderCode,vendororderlineentries.vendorItemCode,vendororderlineentries.vendorItemCode,vendororderlineentries.addons,vendororderlineentries.addonsCode,vendororderlineentries.addonsCode,vendororderlineentries.quantity,vendororderlineentries.priceWithQuantity,vendoritemmaster.itemName,vendoritemmaster.itemPhoto");
				$linecond = array("vendororderlineentries.orderCode" => $postData['orderCode']);
				$lineorderBy = array('vendororderlineentries.id' => 'ASC');
				$linejoin = array('vendoritemmaster' => 'vendororderlineentries.vendorItemCode=vendoritemmaster.code');
				$linejoinType = array('vendoritemmaster' => 'inner');
				$orderProductRes = $this->GlobalModel->selectQuery($lineorderColumns, $linetableName, $linecond, $lineorderBy, $linejoin, $linejoinType);
				//echo $this->db->last_query();
				if ($orderProductRes) {
					$orderProductList = $orderProductRes->result_array();
					for ($j = 0; $j < sizeof($orderProductList); $j++) {
						/*$restaurantCode = $orderProductList[$j]["restaurantCode"];
						$itemPhoto = $orderProductList[$j]["itemPhoto"];
						$imageArray = array();
						array_push($imageArray, base_url('uploads/restaurantitem/' . $restaurantCode . '/restaurantitem/' . $itemPhoto));
						$orderProductList[$j]['images'] = $imageArray;
							unset($imageArray);*/
					}
					$clientOrderList[0]['orderedProduct'] = $orderProductList;
					$dFormat = DateTime::createFromFormat('Y-m-d H:i:s', $clientOrderList[0]['orderDate']);
					$oDt = $dFormat->format('d-m-Y H:i:s');
					$clientOrderList[0]['orderDate'] = $oDt;
				}
				return $this->response(array("status" => "200", "totalOrders" => $totalOrders, "result" => $clientOrderList), 200);
			}
			return $this->response(array("status" => "300", "message" => "Data not found"));
		} else {
			return $this->response(array("status" => "400", "message" => "* Fields are required..."), 200);
		}
	}

	public function getOrderDetailsOld_post()
	{
		$postData = $this->post();
		$dataDeliveryBoy = array();
		$clientOrderList = array();
		if (isset($postData['restaurantCode']) && $postData['restaurantCode'] != "" && isset($postData['orderCode']) && $postData['orderCode'] != "") {
			$tableName = "vendorordermaster";
			$orderColumns = array("clientmaster.name as customerName,clientmaster.mobile,vendorordermaster.deliveryBoyCode,vendorordermaster.clientCode,vendorordermaster.code as orderCode,vendorordermaster.shippingCharges as deliveryCharges,vendorordermaster.paymentmode,vendorordermaster.address,vendorordermaster.phone,vendorordermaster.grandTotal as orderTotalPrice,vendorordermaster.subTotal,vendorordermaster.tax,vendorordermaster.discount,vendorordermaster.totalPackgingCharges,vendorordermaster.addDate as orderDate, orderstatusmaster.statusName as orderStatus, paymentstatusmaster.statusName as paymentStatus");
			$cond = array('vendorordermaster.code' => $postData['orderCode'], 'vendorordermaster.vendorCode' => $postData['restaurantCode']);
			$orderBy = array('vendorordermaster.id' => 'DESC');
			$join = array('clientmaster' => 'vendorordermaster.clientCode=clientmaster.code', 'orderstatusmaster' => 'vendorordermaster.orderStatus=orderstatusmaster.statusSName', 'paymentstatusmaster' => 'vendorordermaster.paymentStatus=paymentstatusmaster.statusSName');
			$joinType = array('clientmaster' => 'inner', 'orderstatusmaster' => 'inner', 'paymentstatusmaster' => 'inner');
			$resultQuery = $this->GlobalModel->selectQuery($orderColumns, $tableName, $cond, $orderBy, $join, $joinType);
			if ($resultQuery) {
				$clientOrderList = $resultQuery->result_array();
				$deliveryBoyCode = $clientOrderList[0]['deliveryBoyCode'];
				$totalOrders = sizeof($clientOrderList);
				$restaurantCode = $postData["restaurantCode"];
				$tableName = "usermaster";
				//$orderColumns = array("usermaster.code as userCode,employeemaster.firstName,employeemaster.lastName,employeemaster.contact1");
				$orderColumns = array("usermaster.userEmail,usermaster.mobile as dBoyContactNumber,usermaster.code as userCode,usermaster.name as dBoyName,usermaster.latitude as dBoyLatitude,usermaster.longitude as dBoyLongitude");
				$condition = array('usermaster.code' => $deliveryBoyCode);
				$orderBy = array('usermaster.id' => 'DESC');
				$joinType = array();
				$join = array();
				//$joinType = array('employeemaster' => 'inner');
				//$join = array('employeemaster' => 'usermaster.empCode=employeemaster.code');
				$restRes = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType);
				if ($restRes) {
					$result = $restRes->result_array()[0];
					$name = $result['dBoyName'];
					$userCode = $result['userCode'];
					$dBoyEmail = $result['userEmail'];
					$dboyContactNumber = $result['dBoyContactNumber'];
					$dboyLatitude = $result['dBoyLatitude'];
					$dboyLongitude = $result['dBoyLongitude'];

					//$lastName = $result['lastName'];
					//$mobile = $result['contact1'];
					//$dataRest = array('code' => $userCode, 'deliveryBoyCode' => $deliveryBoyCode, 'firstName' => $firstName, 'lastName' => $lastName, 'deliveryBoyMobile' => $mobile);
					$dataRest = array('code' => $userCode, 'deliveryBoyCode' => $deliveryBoyCode, 'name' => $name, 'email' => $dBoyEmail, 'deliveryBoyMobile' => $dboyContactNumber, 'dBoyLatitude' => $dboyLatitude, 'dBoyLongitude' => $dboyLongitude);
					array_push($dataDeliveryBoy, $dataRest);
				}
				$linetableName = "vendororderlineentries";
				$lineorderColumns = array("vendororderlineentries.orderCode,vendororderlineentries.vendorItemCode,vendororderlineentries.vendorItemCode,vendororderlineentries.addons,vendororderlineentries.addonsCode,vendororderlineentries.addonsCode,vendororderlineentries.quantity,vendororderlineentries.priceWithQuantity,vendoritemmaster.itemName,vendoritemmaster.itemPhoto");
				$linecond = array("vendororderlineentries.orderCode" => $postData['orderCode']);
				$lineorderBy = array('vendororderlineentries.id' => 'ASC');
				$linejoin = array('vendoritemmaster' => 'vendororderlineentries.vendorItemCode=vendoritemmaster.code');
				$linejoinType = array('vendoritemmaster' => 'inner');
				$orderProductRes = $this->GlobalModel->selectQuery($lineorderColumns, $linetableName, $linecond, $lineorderBy, $linejoin, $linejoinType);
				//echo $this->db->last_query();
				if ($orderProductRes) {
					$orderProductList = $orderProductRes->result_array();
					for ($j = 0; $j < sizeof($orderProductList); $j++) {
						/*$restaurantCode = $orderProductList[$j]["restaurantCode"];
						$itemPhoto = $orderProductList[$j]["itemPhoto"];
						$imageArray = array();
						array_push($imageArray, base_url('uploads/restaurantitem/' . $restaurantCode . '/restaurantitem/' . $itemPhoto));
						$orderProductList[$j]['images'] = $imageArray;
							unset($imageArray);*/
					}
					$clientOrderList[0]['orderedProduct'] = $orderProductList;
					$dFormat = DateTime::createFromFormat('Y-m-d H:i:s', $clientOrderList[0]['orderDate']);
					$oDt = $dFormat->format('d-m-Y H:i:s');
					$clientOrderList[0]['orderDate'] = $oDt;
				}
			}
			$finalResult['orderDetails'] = $clientOrderList;
			$finalResult['deliveryBoyDetails'] = $dataDeliveryBoy;
			return $this->response(array("status" => "200", "result" => $finalResult), 200);
		} else {
			return $this->response(array("status" => "400", "message" => "* Fields are required..."), 200);
		}
	}

	public function orderStatusUpdate_post()
	{
		$today = date('Y-m-d H:i:s');
		$postData = $this->post();
		if (isset($postData['vendorCode']) && $postData['vendorCode'] != "" && isset($postData['orderCode']) && $postData['orderCode'] != "" && isset($postData['orderStatus']) && $postData['orderStatus'] != "") {
			$checkOrder = $this->db->query("select id from vendorordermaster where code='" . $postData['orderCode'] . "' and orderStatus not in ('CAN','DEL','RJT','PND')");
			if ($checkOrder->num_rows() > 0) {
				$orderCode = $postData['orderCode'];
				$timeStamp = date("Y-m-d h:i:s");
				$data = array('editID' => $timeStamp, 'editDate' => $timeStamp);
				switch ($postData['orderStatus']) {
					case 'RJT':
						$data['orderStatus'] = 'RJT';
						$data['editDate'] = $timeStamp;
						$reason = 'Order is rejected';
						$message = "Order No - " . $orderCode . " is rejected. Currently we are not accepting the order";
						$title = "Order Rejected";
						break;
					case 'PRE':
						$data['orderStatus'] = 'PRE';
						$data['editDate'] = $timeStamp;
						$reason = "Accepted Order and Preparing it";
						$message = "Order No - " . $orderCode . " is set for preparing.";
						$title = "Order Accepted";
						break;
					case 'RFP':
						$data['orderStatus'] = 'RFP';
						$data['editDate'] = $timeStamp;
						$reason = "Order is prepared and ready for pickup now";
						$message = "Order No-" . $orderCode . " is ready for pickup now";
						$title = "Order Picked up";
						break;
				}
				$result = $this->GlobalModel->doEdit($data, 'vendorordermaster', $orderCode);
				if ($result != 'false') {

					$this->firestore->update_order_status($orderCode, $postData['orderStatus'], 'food');

					$order_status = $this->GlobalModel->selectQuery("vendororderstatusmaster.*", "vendororderstatusmaster", array("vendororderstatusmaster.statusSName" => $postData['orderStatus']));
					if ($order_status && count($order_status->result_array()) > 0) {
						$order_status_record = $order_status->result()[0];
						$statusTitle = $title;
						#replace $ template in title 
						$statusDescription = $message;
						$statusDescription = str_replace("$", $orderCode, $statusDescription);
						$dataBookLine = array(
							"orderCode" => $orderCode,
							"statusPutCode" => $postData['vendorCode'],
							"statusLine" => $postData['orderStatus'],
							"reason" => $reason,
							"statusTime" => $timeStamp,
							"statusTitle" => $statusTitle,
							"statusDescription" => $statusDescription,
							"isActive" => 1
						);
						$bookLineResult = $this->GlobalModel->addWithoutYear($dataBookLine, 'bookorderstatuslineentries', 'BOL');
					}
					//notification			
					$orderData = $this->GlobalModel->selectQuery("vendorordermaster.*", 'vendorordermaster', array("vendorordermaster.code" => $orderCode));
					if ($orderData) {
						$orderData = $orderData->result_array()[0];
						//set client code and delivery boy
						$clientCode = $orderData['clientCode'];
						$deliveryBoyCode = $orderData['deliveryBoyCode'];
						//set notification to customer
						$clientdata = $this->GlobalModel->selectQuery("clientdevicedetails.firebaseId", "clientdevicedetails", array("clientdevicedetails.clientCode" => $clientCode));
						if ($clientdata) {
							$DeviceIdsArr = array();
							foreach ($clientdata->result_array() as $c) {
								$DeviceIdsArr[] = $c['firebaseId'];
							}
							$this->sendFirebaseNotification($DeviceIdsArr, $title, $message, $orderCode);
						}
						//send notification to delivery boy
						$userData = $this->GlobalModel->selectQuery("usermaster.firebase_id", "usermaster", array("usermaster.code" => $clientCode));
						if ($userData) {
							$DeviceIdsArr = array();
							foreach ($clientdata->result_array() as $c) {
								$DeviceIdsArr[] = $c['firebase_id'];
							}
							$this->sendFirebaseNotification($DeviceIdsArr, $title, $message, $orderCode, "forDB");
						}
					}
					return $this->response(array("status" => "200", "message" => "Order Status updated successfully"), 200);
				} else {
					return $this->response(array("status" => "300", "message" => "Failed to update order status"), 200);
				}
			} else {
				return $this->response(array("status" => "300", "message" => "Invalid Order Code"), 200);
			}
		} else {
			return $this->response(array("status" => "400", "message" => "* fields are required."), 200);
		}
	}

	// fetch all Main Categories
	public function getMainCategoryList_get()
	{
		$tableName = "maincategorymaster";
		$orderColumns = array("maincategorymaster.mainCategoryName,maincategorymaster.categoryPhoto,maincategorymaster.code");
		$condition = array();
		$orderBy = array('maincategorymaster' . '.id' => 'ASC');
		$joinType = array();
		$join = array();
		$groupByColumn = array();
		$limit = '';
		$offset = '';
		$extraCondition = "(maincategorymaster.isDelete=0 OR maincategorymaster.isDelete IS NULL) AND maincategorymaster.isActive=1 ";
		$like = array();
		$MainCategoryresult = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition)->result();

		if ($MainCategoryresult) {
			return $this->response(array("status" => "200", "result" => $MainCategoryresult), 200);
		} else {
			$data = new stdClass();
			$this->response(array("status" => "300", "message" => "Data not found.", "result" => $data), 200);
		}
	}

	public function getVendorCommissionList_post()
	{
		$postData = $this->post();
		if (isset($postData['vendorCode']) && $postData['vendorCode'] != "") {
			$orderColumns = array("vendorordercommission.deliveryBoyCode,vendor.firstName,vendor.entityName, vendor.lastName,vendorordercommission.orderCode,vendorordercommission.comissionPercentage,vendorordercommission.subTotal,vendorordercommission.comissionAmount,vendorordercommission.vendorAmount,vendorordercommission.grandTotal");
			$condition = array('vendorordercommission.deliveryBoyCode' => $postData['vendorCode']);
			$startDate = date("Y-m-d") . " 00:00:00";
			$endDate = date("Y-m-d") .  " 23:59:59";
			$orderBy = array('vendorordercommission.id' => 'DESC');
			$joinType = array('vendor' => 'inner');
			$join = array('vendor' => 'vendorordercommission.deliveryBoyCode=vendor.code');
			$extraCondition = '';
			$extraCondition = "vendorordercommission.addDate between '" . $startDate . "' And '" . $endDate . "'";
			$like = array();
			$Records = $this->GlobalModel->selectQuery($orderColumns, 'vendorordercommission', $condition, $orderBy, $join, $joinType, array(), '', '', array(), $extraCondition);
			$totalAmount = 0;
			if ($Records) {
				foreach ($Records->result() as $row) {
					$totalAmount += $row->vendorAmount;
					$p[] = $row;
					$res = $p;
				}
				return $this->response(array("status" => "200", "Total" => $totalAmount, "result" => $res), 200);
			} else {
				return $this->response(array("status" => "300", "message" => "No data found"), 200);
			}
		} else {
			return $this->response(array("status" => "400", "message" => " * fields are required."), 200);
		}
	}

	// Vendor Coupon Offer List
	public function getVendorOfferList_post()
	{
		$postData = $this->post();
		if (isset($postData['vendorCode']) && $postData['vendorCode'] != "") {
			$orderColumns = array('vendoroffer.id,vendoroffer.code,vendoroffer.coupanCode,vendoroffer.offerType,vendoroffer.discount,vendoroffer.minimumAmount,vendoroffer.perUserlimit,DATE_FORMAT(vendoroffer.startDate, "%d %b %Y") as startDate,DATE_FORMAT(vendoroffer.endDate, "%d %b %Y") as endDate,vendoroffer.capLimit,vendoroffer.termsAndConditions,ifnull(vendoroffer.flatAmount,0) as flatAmount,vendoroffer.isAdminApproved');
			$condition = array('vendoroffer.isActive' => 1, 'vendoroffer.vendorCode' => $postData['vendorCode']);
			$orderBy = array();
			$joinType = array();
			$join = array();
			$groupByColumn = array();
			$extraCondition = "'" . date('Y-m-d') . "' between vendoroffer.startDate and vendoroffer.endDate";
			$like = array();
			$limit = '';
			$offset = '';
			$resultData = $this->GlobalModel->selectQuery($orderColumns, "vendoroffer", $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
			if ($resultData != false) {
				$res = $resultData->result_array();
				return $this->response(array("status" => "200", "result" => $res), 200);
			} else {
				return $this->response(array("status" => "300", "message" => "No data found"), 200);
			}
		} else {
			return $this->response(array("status" => "400", "message" => " * fields are required."), 200);
		}
	}

	public function addVendorCouponOffer_post()
	{
		$postData = $this->post();
		if (isset($postData['vendorCode']) && $postData['vendorCode'] != "" && isset($postData['couponCode']) && $postData['couponCode'] != "" && isset($postData['offerType']) && $postData['offerType'] != "" &&  isset($postData['minimumAmount']) && $postData['minimumAmount'] != "" && isset($postData['perUserLimit']) && $postData['perUserLimit'] != ""  &&  isset($postData['startDate']) && $postData['startDate'] != "" &&  isset($postData['endDate']) && $postData['endDate'] != "") {
			$capLimit = $discount = $flatAmount = $termsAndConditions = '';
			$invalid = 0;
			$ip = $_SERVER['REMOTE_ADDR'];
			if (isset($postData['termsAndConditions']) && $postData['termsAndConditions'] != "") {
				$termsAndConditions = $postData['termsAndConditions'];
			}
			if (strtolower($postData['offerType']) == 'cap') {
				if (isset($postData['discount']) && $postData['discount'] != "" && isset($postData['capLimit']) && $postData['capLimit'] != "") {
					$discount = $postData['discount'];
					$capLimit = $postData['capLimit'];
				} else {
					$invalid = 1;
				}
			} elseif (strtolower($postData['offerType']) == 'flat') {
				if (isset($postData['flatAmount']) && $postData['flatAmount'] != "") {
					$flatAmount = $postData['flatAmount'];
				} else {
					$invalid = 1;
				}
			} else {
				$invalid = 1;
				return $this->response(array("status" => "300", "message" => "Invalid Offer Type"), 200);
			}
			if ($invalid == 0) {
				$data = array(
					'vendorCode' => $postData['vendorCode'],
					'coupanCode' => $postData['couponCode'],
					'offerType' => strtolower($postData['offerType']),
					'minimumAmount' => trim($postData["minimumAmount"]),
					'capLimit' => trim($capLimit),
					'perUserLimit' => trim($postData["perUserLimit"]),
					'startDate' => trim($postData["startDate"]),
					'endDate' => trim($postData["endDate"]),
					'termsAndConditions' => trim($termsAndConditions),
					'discount' => trim($discount),
					'addID' => $postData['vendorCode'],
					'addIP' => $ip,
					'isActive' => 1,
					'flatAmount' => trim($flatAmount),
					'isAdminApproved' => 0
				);
				$code = $this->GlobalModel->addWithoutYear($data, 'vendoroffer', 'VOFF');
				if ($code != false) {
					return $this->response(array("status" => "300", "message" => "Offer Coupon added successfully", "result" => $code), 200);
				} else {
					return $this->response(array("status" => "300", "message" => "Failed to add offer coupon"), 200);
				}
			} else {
				return $this->response(array("status" => "300", "message" => "Some parameters are missing"), 200);
			}
		} else {
			return $this->response(array("status" => "400", "message" => " * fields are required."), 200);
		}
	}

	public function updateVendorCouponOffer_post()
	{
		$postData = $this->post();
		if (isset($postData['offerCode']) && $postData['offerCode'] != "" && isset($postData['vendorCode']) && $postData['vendorCode'] != "" && isset($postData['couponCode']) && $postData['couponCode'] != "" && isset($postData['offerType']) && $postData['offerType'] != "" &&  isset($postData['minimumAmount']) && $postData['minimumAmount'] != "" && isset($postData['perUserLimit']) && $postData['perUserLimit'] != ""  &&  isset($postData['startDate']) && $postData['startDate'] != "" &&  isset($postData['endDate']) && $postData['endDate'] != "") {
			$capLimit = $discount = $flatAmount = $termsAndConditions = '';
			$invalid = 0;
			$ip = $_SERVER['REMOTE_ADDR'];
			if (isset($postData['termsAndConditions']) && $postData['termsAndConditions'] != "") {
				$termsAndConditions = $postData['termsAndConditions'];
			}
			if (strtolower($postData['offerType']) == 'cap') {
				if (isset($postData['discount']) && $postData['discount'] != "" && isset($postData['capLimit']) && $postData['capLimit'] != "") {
					$discount = $postData['discount'];
					$capLimit = $postData['capLimit'];
				} else {
					$invalid = 1;
				}
			} elseif (strtolower($postData['offerType']) == 'flat') {
				if (isset($postData['flatAmount']) && $postData['flatAmount'] != "") {
					$flatAmount = $postData['flatAmount'];
				} else {
					$invalid = 1;
				}
			} else {
				$invalid = 1;
				return $this->response(array("status" => "300", "message" => "Invalid Offer Type"), 200);
			}
			if ($invalid == 0) {
				$data = array(
					'vendorCode' => $postData['vendorCode'],
					'coupanCode' => $postData['couponCode'],
					'offerType' => strtolower($postData['offerType']),
					'minimumAmount' => trim($postData["minimumAmount"]),
					'capLimit' => trim($capLimit),
					'perUserLimit' => trim($postData["perUserLimit"]),
					'startDate' => trim($postData["startDate"]),
					'endDate' => trim($postData["endDate"]),
					'termsAndConditions' => trim($termsAndConditions),
					'discount' => trim($discount),
					'editID' => $postData['vendorCode'],
					'editIP' => $ip,
					'isActive' => 1,
					'flatAmount' => trim($flatAmount),
					'isAdminApproved' => 0
				);
				$code = $this->GlobalModel->doEdit($data, 'vendoroffer', trim($postData['offerCode']));
				if ($code != false) {
					return $this->response(array("status" => "300", "message" => "Offer Coupon updated successfully", "result" => trim($postData['offerCode'])), 200);
				} else {
					return $this->response(array("status" => "300", "message" => "Failed to update offer coupon"), 200);
				}
			} else {
				return $this->response(array("status" => "300", "message" => "Some parameters are missing"), 200);
			}
		} else {
			return $this->response(array("status" => "400", "message" => " * fields are required."), 200);
		}
	}

	public function deleteVendorCouponOffer_post()
	{
		$postData = $this->post();
		if (isset($postData['offerCode']) && $postData['offerCode'] != "") {
			$resultData = $this->GlobalModel->delete($postData['offerCode'], 'vendoroffer');
			if ($resultData == 'true') {
				return $this->response(array("status" => "300", "message" => "Offer Coupon Deleted successfully"), 200);
			} else {
				return $this->response(array("status" => "300", "message" => "Failed to delete offer coupon"), 200);
			}
		} else {
			return $this->response(array("status" => "400", "message" => " * fields are required."), 200);
		}
	}

	public function getVendorOfferByCode_post()
	{
		$postData = $this->post();
		if (isset($postData['vendorCode']) && $postData['vendorCode'] != "" && isset($postData['offerCode']) && $postData['offerCode'] != "") {
			$orderColumns = array('vendoroffer.id,vendoroffer.code,vendoroffer.coupanCode,vendoroffer.offerType,vendoroffer.discount,vendoroffer.minimumAmount,vendoroffer.perUserlimit,DATE_FORMAT(vendoroffer.startDate, "%d %b %Y") as startDate,DATE_FORMAT(vendoroffer.endDate, "%d %b %Y") as endDate,vendoroffer.capLimit,vendoroffer.termsAndConditions,ifnull(vendoroffer.flatAmount,0) as flatAmount,vendoroffer.isAdminApproved');
			$condition = array('vendoroffer.isActive' => 1, 'vendoroffer.code' => $postData['offerCode'], 'vendoroffer.vendorCode' => $postData['vendorCode']);
			$orderBy = array();
			$joinType = array();
			$join = array();
			$groupByColumn = array();
			$extraCondition = "'" . date('Y-m-d') . "' between vendoroffer.startDate and vendoroffer.endDate";
			$like = array();
			$limit = '';
			$offset = '';
			$resultData = $this->GlobalModel->selectQuery($orderColumns, "vendoroffer", $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
			if ($resultData != false) {
				$res = $resultData->result_array();
				return $this->response(array("status" => "200", "result" => $res), 200);
			} else {
				return $this->response(array("status" => "300", "message" => "No data found"), 200);
			}
		} else {
			return $this->response(array("status" => "400", "message" => " * fields are required."), 200);
		}
	}

	public function rejectOrderByVendor_post()
	{
		$postData = $this->post();
		if (isset($postData['vendorCode']) && $postData['vendorCode'] != "" && isset($postData['orderCode']) && $postData['orderCode'] != "") {
			$checkOrder = $this->db->query("select id from vendorordermaster where code='" . $postData['orderCode'] . "'");
			if ($checkOrder->num_rows() > 0) {
				$orderCode = $postData['orderCode'];
				$timeStamp = date("Y-m-d h:i:s");
				$data = array('orderStatus' => 'RJT', 'paymentStatus' => 'RJCT', 'editDate' => $timeStamp);
				$result = $this->GlobalModel->doEdit($data, 'vendorordermaster', $orderCode);
				if ($result == 'true') {
					$bookLineResult = 'true';
					$order_status = $this->GlobalModel->selectQuery("vendororderstatusmaster.*", "vendororderstatusmaster", array("vendororderstatusmaster.statusSName" => "RJT"));
					if ($order_status && count($order_status->result_array()) > 0) {
						$order_status_record = $order_status->result()[0];
						$statusTitle = $order_status_record->messageTitle;
						#replace $ template in title 
						$statusDescription = $order_status_record->messageDescription;
						$statusDescription = str_replace("$", $orderCode, $statusDescription);
						$dataBookLine = array(
							"orderCode" => $orderCode,
							"statusPutCode" => $postData['vendorCode'],
							"statusLine" => 'RJT',
							"reason" => 'Order Rejected By Vendor',
							"statusTime" => $timeStamp,
							"statusTitle" => $statusTitle,
							"statusDescription" => $statusDescription,
							"isActive" => 1
						);
						$bookLineResult = $this->GlobalModel->addWithoutYear($dataBookLine, 'bookorderstatuslineentries', 'BOL');
					}
					$checkOrderNew = $this->GlobalModel->selectQuery("vendorordermaster.*", "vendorordermaster", array("vendorordermaster.code" => $postData['orderCode']));
					if ($checkOrderNew->num_rows() > 0) {
						$orderData = $checkOrderNew->result_array()[0];
						$orderStatus = $orderData['orderStatus'];
						$clientCode = $orderData['clientCode'];
						$deliveryBoyCode = $orderData['deliveryBoyCode'];
						$vendCode = $orderData['vendorCode'];
						$grandTotal = $orderData['grandTotal'];
						$subTotal = $orderData['subTotal'];
						if ($orderStatus != 'PND') {
							$DBFlag = 0;
							$restoFlag = 0;
							if ($orderStatus == 'PLC') {
								$DBFlag = 1;
							} else {
								$restoFlag = 1;
								$DBFlag = 1;
							}
							if ($DBFlag == 1) {
								$dbCode = $deliveryBoyCode;
								$orderColumns = array("usermaster.firebase_id");
								$cond = array('usermaster' . '.isActive' => 1, "usermaster.code" => $dbCode);
								$resultDBoy = $this->GlobalModel->selectQuery($orderColumns, 'usermaster', $cond);
								if ($resultDBoy) {
									//remove delivery boy current active order
									$dataUpCnt['orderCount'] = 0;
									$dataUpCnt['orderCode'] = null;
									$dataUpCnt['orderType'] = null;
									$delbActiveOrder = $this->GlobalModel->doEditWithField($dataUpCnt, 'deliveryBoyActiveOrder', 'deliveryBoyCode', $dbCode);
									$dBoyRelease['deliveryBoyCode'] = null;
									$resultRelase = $this->GlobalModel->doEdit($dBoyRelease, 'vendorordermaster', $orderCode);
								}
							}
							if ($restoFlag == 1) {
								//	echo 1;
								//add 10 ruppes comission to delivery
								$dataUpCnt['commissionAmount'] = 10;
								$dataUpCnt['orderCode'] = $orderCode;
								$dataUpCnt['orderType'] = 'food';
								$dataupCnt['deliveryBoyCode'] = $deliveryBoyCode;
								$dataupCnt['addID'] = $postData['vendorCode'];
								$dataupCnt['isActive'] = 1;
								$dataupCnt['deliveryBoyCode'] = $deliveryBoyCode;
								$delbActiveOrder = $this->GlobalModel->addNew($dataUpCnt, 'deliveryboyearncommission', 'DBEC');

								//vendor penulty
								$settingData = $this->GlobalModel->selectQuery("settings.settingValue", "settings", array("settings.code" => "SET_10"));
								if ($settingData) {
									$vendorPenulty = $settingData->result_array()[0]['settingValue'];
									if ($vendorPenulty != 0 && $vendorPenulty != '' && $vendorPenulty != NULL) {
										$commAmount = round(($subTotal * $vendorPenulty) / 100, 2);
										$vcData['comissionPercentage'] = $vendorPenulty;
										$vcData['comissionAmount'] = $commAmount;
										$vcData['subTotal'] = $subTotal;
										$vcData['vendorAmount'] = $subTotal - $commAmount;
										$vcData['grandTotal'] = $grandTotal;
										$vcData['commissionType'] = 'penalty';
										$vcData['deliveryBoyCode'] = $postData['vendorCode'];
										$vcData['orderCode'] = $orderCode;
										$vcData['isActive'] = 1;
										$delboyCommission = $this->GlobalModel->addNew($vcData, 'vendorordercommission', 'VNDC');
									}
								}
							}
						}
						//set notification to customer
						$clientdata = $this->GlobalModel->selectQuery("clientdevicedetails.firebaseId", "clientdevicedetails", array("clientdevicedetails.clientCode" => $clientCode));
						if ($clientdata) {
							$DeviceIdsArr = array();
							foreach ($clientdata->result_array() as $c) {
								$DeviceIdsArr[] = $c['firebaseId'];
							}
							$message = 'Apologies! The order you placed for No-' . $orderCode . ' is rejected. Please try later';
							$title = 'Order Rejected';
							$this->sendFirebaseNotification($DeviceIdsArr, $title, $message, $orderCode);
						}
						//send notification to delivery boy
						$userData = $this->GlobalModel->selectQuery("usermaster.firebase_id", "usermaster", array("usermaster.code" => $clientCode));
						if ($userData) {
							$DeviceIdsArr = array();
							foreach ($clientdata->result_array() as $c) {
								$DeviceIdsArr[] = $c['firebase_id'];
							}
							$message = 'Order No-' . $orderCode . ' is being rejected. ';
							$title = 'Order Rejected';
							$this->sendFirebaseNotification($DeviceIdsArr, $title, $message, $orderCode, 'forDB');
						}
					}
					return $this->response(array("status" => "200", "message" => "Order Rejected successfully"), 200);
				} else {
					return $this->response(array("status" => "300", "message" => " Failed to reject order"), 200);
				}
			} else {
				return $this->response(array("status" => "300", "message" => " Invalid Order Code"), 200);
			}
		} else {
			return $this->response(array("status" => "400", "message" => " * fields are required."), 200);
		}
	}

	public function getAllOrderListByVendor_post()
	{
		$postData = $this->post();
		if (isset($postData["vendorCode"]) && $postData["vendorCode"] != '') {
			$vendorCode = $postData["vendorCode"];
			$tableName = "vendorordermaster";
			$orderColumns = array("clientmaster.name as customerName,clientmaster.emailId as customerEmail,vendorordermaster.address as customerAddress,clientmaster.mobile as customerMobile,vendorordermaster.deliveryBoyCode,usermaster.name as dBoyName,usermaster.latitude as dBoyLat,usermaster.longitude as dBoyLong,usermaster.mobile as contactNumber,vendorordermaster.vendorCode,vendorordermaster.totalPackgingCharges,vendorordermaster.packagingType,vendorordermaster.addressType,vendorordermaster.code as orderCode,vendorordermaster.tax,vendorordermaster.discount,vendorordermaster.subTotal as actualAmount,vendorordermaster.grandTotal as totalAmount,vendorordermaster.shippingCharges as deliveryCharges,vendorordermaster.paymentmode,vendorordermaster.address,vendorordermaster.grandTotal as orderTotalPrice,DATE_FORMAT(vendorordermaster.addDate, '%Y-%m-%d') as orderDate, vendororderstatusmaster.statusSName as orderStatus,vendororderstatusmaster.statusName as orderStatusName, paymentstatusmaster.statusName as paymentStatus,vendor.entityName,vendor.address as pickUpAddress,vendor.latitude as sourceLat,vendor.longitude as sourceLong,vendor.cityCode,vendor.addressCode,citymaster.cityName,customaddressmaster.place,vendorordermaster.latitude as destiLat,vendorordermaster.longitude as destiLong,ifnull(vendorordermaster.preparingMinutes,0) as  preparationTime"); //,bookorderstatuslineentries.statusTime");
			$cond = array('vendorordermaster.vendorCode' => $vendorCode, "vendorordermaster.isActive" => 1);
			$orderBy = array('vendorordermaster' . ".id" => 'DESC');
			$join = array('usermaster' => 'vendorordermaster' . '.deliveryBoyCode=' . 'usermaster' . '.code', 'vendororderstatusmaster' => 'vendorordermaster' . '.orderStatus=' . 'vendororderstatusmaster' . '.statusSName', 'paymentstatusmaster' => 'vendorordermaster' . '.paymentStatus=' . 'paymentstatusmaster' . '.statusSName', "vendor" => "vendorordermaster.vendorCode=vendor.code", "citymaster" => "vendor.cityCode=citymaster.code", "customaddressmaster" => "vendor.addressCode=customaddressmaster.code", 'clientmaster' => 'clientmaster' . '.code=' . 'vendorordermaster' . '.clientCode'); //,"bookorderstatuslineentries" => "vendorordermaster.orderStatus=bookorderstatuslineentries.statusLine");
			$joinType = array('usermaster' => 'left', 'vendororderstatusmaster' => 'inner', 'paymentstatusmaster' => 'inner', "vendor" => "inner", "citymaster" => "left", "customaddressmaster" => "left", "clientmaster" => "inner", "bookorderstatuslineentries" => "left");
			$extraCondition = "vendorordermaster.orderStatus IN ('PLC','PRE','RFP','PUP')";
			$resultQuery = $this->GlobalModel->selectQuery($orderColumns, $tableName, $cond, $orderBy, $join, $joinType, array(),  "", "", array(), $extraCondition);
			$r = $this->db->last_query();
			$imageArray = array();
			if ($resultQuery) {
				$clientOrderList = $resultQuery->result_array();
				$totalOrders = sizeof($clientOrderList);
				for ($i = 0; $i < $totalOrders; $i++) {
					$addonArray = $resultAddonArray = [];
					$srno = 0;
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
									$getAddonDetails = $this->GlobalModel->selectQuery("customizedcategory.categoryTitle,customizedcategory.categoryType,customizedcategorylineentries.subCategoryTitle,customizedcategorylineentries.price", "customizedcategorylineentries", $condition1, array(), $join1, $joinType1, array(), "", "", array(), '');
									if ($getAddonDetails) {
										$categoryArr = $getAddonDetails->result_array()[0];
									}
									$resultArr[] = $categoryArr;
								}
							}
							$addonArray['addonsDetails'] = $resultArr;
							$srno++;
							$addonArray['noofitems'] = $srno;
							$resultAddonArray[] = $addonArray;
						}
					}
					$clientOrderList[$i]['noofitems'] = $srno;
					$clientOrderList[$i]['orderedItems'] = $resultAddonArray;
				}
				$finalResult['orders'] = $clientOrderList;
				// "r"=> $r
				return $this->response(array("status" => "200",  "message" => "Orders found", "result" => $finalResult), 200);
			} else {
				return $this->response(array("status" => "300", "message" => "Data not found."), 200);
			}
		} else {
			return $this->response(array("status" => "400", "message" => " * are required field(s)."), 400);
		}
	}

	public function getOrderListByVendor_post()
	{
		$postData = $this->post();
		if (isset($postData["vendorCode"]) && $postData["vendorCode"] != '') {
			$vendorCode = $postData["vendorCode"];
			$tableName = "vendorordermaster";
			$orderColumns = array("clientmaster.name as customerName,clientmaster.emailId as customerEmail,vendorordermaster.address as customerAddress,clientmaster.mobile as customerMobile,vendorordermaster.deliveryBoyCode,usermaster.name as dBoyName,usermaster.latitude as dBoyLat,usermaster.longitude as dBoyLong,usermaster.mobile as contactNumber,vendorordermaster.vendorCode,vendorordermaster.totalPackgingCharges,vendorordermaster.packagingType,vendorordermaster.addressType,vendorordermaster.code as orderCode,vendorordermaster.tax,vendorordermaster.discount,vendorordermaster.subTotal as actualAmount,vendorordermaster.grandTotal as totalAmount,vendorordermaster.shippingCharges as deliveryCharges,vendorordermaster.paymentmode,vendorordermaster.address,vendorordermaster.grandTotal as orderTotalPrice,vendorordermaster.addDate as orderDate, vendororderstatusmaster.statusSName as orderStatus,vendororderstatusmaster.statusName as orderStatusName, paymentstatusmaster.statusName as paymentStatus,vendor.entityName,vendor.address as pickUpAddress,vendor.latitude as sourceLat,vendor.longitude as sourceLong,vendor.cityCode,vendor.addressCode,citymaster.cityName,customaddressmaster.place,vendorordermaster.latitude as destiLat,vendorordermaster.longitude as destiLong"); //,bookorderstatuslineentries.statusTime");
			$cond = array('vendorordermaster.vendorCode' => $vendorCode, "vendorordermaster.isActive" => 1);
			$orderBy = array('vendorordermaster' . ".id" => 'DESC');
			$join = array('usermaster' => 'vendorordermaster' . '.deliveryBoyCode=' . 'usermaster' . '.code', 'vendororderstatusmaster' => 'vendorordermaster' . '.orderStatus=' . 'vendororderstatusmaster' . '.statusSName', 'paymentstatusmaster' => 'vendorordermaster' . '.paymentStatus=' . 'paymentstatusmaster' . '.statusSName', "vendor" => "vendorordermaster.vendorCode=vendor.code", "citymaster" => "vendor.cityCode=citymaster.code", "customaddressmaster" => "vendor.addressCode=customaddressmaster.code", 'clientmaster' => 'clientmaster' . '.code=' . 'vendorordermaster' . '.clientCode'); //,"bookorderstatuslineentries" => "vendorordermaster.orderStatus=bookorderstatuslineentries.statusLine");
			$joinType = array('usermaster' => 'left', 'vendororderstatusmaster' => 'inner', 'paymentstatusmaster' => 'inner', "vendor" => "inner", "citymaster" => "left", "customaddressmaster" => "left", "clientmaster" => "inner", "bookorderstatuslineentries" => "left");
			//$extracondition=" bookorderstatuslineentries.orderCode = vendorordermaster.code";
			$extracondition = "";
			if (isset($postData["status"]) && $postData["status"] != "") {
				if ($postData["status"] == "All") {
					$extracondition .= " vendorordermaster.orderStatus NOT IN ('PND')";
				} else {
					$status = $postData["status"]; //= explode(",",$postData["status"]);
					$splittedStatus = explode(",", $status);
					$finalStatus = "'" . implode("','", $splittedStatus) . "'";
					$extracondition .= " vendorordermaster.orderStatus IN (" . $finalStatus . ")";
				}
			} else {
				$extracondition .= " vendorordermaster.orderStatus NOT IN ('PND')";
			}

			$resultQuery = $this->GlobalModel->selectQuery($orderColumns, $tableName, $cond, $orderBy, $join, $joinType, array(), "", "", array(), $extracondition);
			$imageArray = array();
			$query = $this->db->last_query();
			if ($resultQuery) {
				$clientOrderList = $resultQuery->result_array();
				$totalOrders = sizeof($clientOrderList);
				for ($i = 0; $i < $totalOrders; $i++) {
					//$clientOrderList[$i]['statusTime']=date('d-m-Y h:i A',strtotime($clientOrderList[$i]['statusTime']));
					$addonArray = $resultAddonArray = [];
					$srno = 0;
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
							$srno++;
							$addonArray['noofitems'] = $srno;
							$resultAddonArray[] = $addonArray;
						}
					}
					$clientOrderList[$i]['noofitems'] = $srno;
					$clientOrderList[$i]['orderedItems'] = $resultAddonArray;
				}
				$finalResult['orders'] = $clientOrderList;
				return $this->response(array("status" => "200", "query" => $query, "message" => "Orders found", "result" => $finalResult), 200);
			} else {
				return $this->response(array("status" => "300", "query" => $query, "message" => "Data not found."), 200);
			}
		} else {
			return $this->response(array("status" => "400", "message" => " * are required field(s)."), 400);
		}
	}

	public function sendFirebaseNotification($DeviceIdsArr, $title, $message, $orderId, $forDB = "normal")
	{
		$random = rand(0, 999);
		$random = date('his') . $random;
		$dataArr = array();
		$dataArr['device_id'] = $DeviceIdsArr;
		$dataArr['message'] = $message; //Message which you want to send
		$dataArr['title'] = $title;
		$dataArr['order_id'] = $orderId;
		$dataArr['random_id'] = $random;
		$dataArr['type'] = 'order';
		$notification['device_id'] = $DeviceIdsArr;
		$notification['message'] = $message; //Message which you want to send
		$notification['title'] = $title;
		$notification['order_id'] = $orderId;
		$notification['random_id'] = $random;
		$notification['type'] = 'order';
		if ($forDB == "forDB") {
			$notify = $this->notificationlibv_3->sendDeliveryNotification($dataArr, $notification, "ringing");
		} else {
			$notify = $this->notificationlibv_3->pushNotification($dataArr, $notification);
		}
		return $notify;
	}

	public function confirmOrderStatusUpdate_post()
	{
		$postData = $this->post();
		if (isset($postData['vendorCode']) && $postData['vendorCode'] != "" && isset($postData['orderCode']) && $postData['orderCode'] != "" && isset($postData['orderStatus']) && $postData['orderStatus'] != "") {
			$checkOrder = $this->db->query("select id from vendorordermaster where code='" . $postData['orderCode'] . "' and orderStatus not in ('CAN','DEL','RJT','PND')");
			if ($checkOrder->num_rows() > 0) {
				$orderCode = $postData['orderCode'];
				$timeStamp = date("Y-m-d h:i:s");
				$data = array('editID' => $timeStamp, 'editDate' => $timeStamp);
				switch ($postData['orderStatus']) {
					case 'PRE':
						$data['orderStatus'] = 'PRE';
						$reason = "Accepted Order and Preparing it";
						$message = "Order No-" . $orderCode . " is set for preparing.";
						$title = "Order Accepted";
						break;
					case 'RFP':
						$data['orderStatus'] = 'RFP';
						$reason = "Order is prepared and ready for pickup now";
						$message = "Order No-" . $orderCode . " is ready for pickup now";
						$title = "Order Picked up";
						break;
					case 'REL':
						$data['orderStatus'] = 'REL';
						$reason = 'Order is released now';
						$message = "Order No-" . $orderCode . " is released now";
						$title = "Order Released";
						break;
					case 'RCH':
						$data['orderStatus'] = 'RCH';
						$reason = 'Order is reached';
						$message = "Order No-" . $orderCode . " is reached";
						$title = "Order Reached";
						break;
				}
				$result = $this->GlobalModel->doEdit($data, 'vendorordermaster', $orderCode);
				if ($result != 'false') {
					$order_status = $this->GlobalModel->selectQuery("vendororderstatusmaster.*", "vendororderstatusmaster", array("vendororderstatusmaster.statusSName" => $postData['orderStatus']));
					if ($order_status && count($order_status->result_array()) > 0) {
						$order_status_record = $order_status->result()[0];
						$statusTitle = $order_status_record->messageTitle;
						#replace $ template in title 
						$statusDescription = $order_status_record->messageDescription;
						$statusDescription = str_replace("$", $orderCode, $statusDescription);
						$dataBookLine = array(
							"orderCode" => $orderCode,
							"statusPutCode" => $postData['vendorCode'],
							"statusLine" => $postData['orderStatus'],
							"reason" => $reason,
							"statusTime" => $timeStamp,
							"statusTitle" => $statusTitle,
							"statusDescription" => $statusDescription,
							"isActive" => 1
						);
						$bookLineResult = $this->GlobalModel->addWithoutYear($dataBookLine, 'bookorderstatuslineentries', 'BOL');
					}
					$type = 0;
					$url = $port = '';
					//notification			
					$orderData = $this->GlobalModel->selectQuery("vendorordermaster.*", 'vendorordermaster', array("vendorordermaster.code" => $orderCode));
					if ($orderData) {
						$orderData = $orderData->result_array()[0];
						if ($postData['orderStatus'] == 'PRE') {
						/*	$checkActiveConnectedPort = $this->GlobalModel->selectQuery('activeports.port,activeports.id', 'activeports', array('activeports.status' => 1, "activeports.isConnected" => '0', "activeports.isJunk" => '0'), array('activeports.id' => 'ASC'), array(), array(), array(), '1');
							if ($checkActiveConnectedPort) {
								if ($checkActiveConnectedPort->num_rows() > 0) {
									$port = $checkActiveConnectedPort->result_array()[0]['port'];
									$url = "https://myvegiz.com";
									$id = $checkActiveConnectedPort->result_array()[0]['id'];
									$updateOrder['trackingPort'] = $port;
									$update = $this->GlobalModel->doEdit($updateOrder, 'vendorordermaster', $orderCode);
									$updatePort['isConnected'] = 1;
									$this->db->query("update activeports set isConnected=1 where id='" . $id . "'");
									//$message1 = "\nTracking Details : \nURL : ".$url."\nPORT : ".$port;
								}
							}*/
						}
						//set client code and delivery boy
						$clientCode = $orderData['clientCode'];
						$deliveryBoyCode = $orderData['deliveryBoyCode'];
						//set notification to customer
						$clientdata = $this->GlobalModel->selectQuery("clientdevicedetails.firebaseId", "clientdevicedetails", array("clientdevicedetails.clientCode" => $clientCode));
						if ($clientdata) {
							$DeviceIdsArr = array();
							foreach ($clientdata->result_array() as $c) {
								$DeviceIdsArr[] = $c['firebaseId'];
							}
							$this->sendFirebaseNotification($DeviceIdsArr, $title, $message, $orderCode);
						}
						//send notification to delivery boy
						$userData = $this->GlobalModel->selectQuery("usermaster.firebase_id", "usermaster", array("usermaster.code" => $clientCode));
						if ($userData) {
							$DeviceIdsArr = array();
							foreach ($clientdata->result_array() as $c) {
								$DeviceIdsArr[] = $c['firebase_id'];
							}
							$this->sendFirebaseNotification($DeviceIdsArr, $title, $message, $orderCode, "forDB");
						}
					}
					return $this->response(array("status" => "200", "message" => "Order Status updated successfully"), 200);
				} else {
					return $this->response(array("status" => "300", "message" => "Failed to update order status"), 200);
				}
			} else {
				return $this->response(array("status" => "300", "message" => " Invalid Order Code"), 200);
			}
		} else {
			return $this->response(array("status" => "400", "message" => " * fields are required."), 200);
		}
	}

	public function getPenultyDetails_post()
	{
		$postData = $this->post();
		if (isset($postData['vendorCode']) && $postData['vendorCode'] != "") {
			$todayDate = date('Y-m-d');
			$previousDate = date('Y-m-d', strtotime(' - 7 days'));
			$extraCondition = " vendorordercommission.addDate BETWEEN '" . $previousDate . "' AND '" . $todayDate . " 23:59:59.999'";
			$payableAmt = $this->GlobalModel->selectQuery('ifnull(sum(vendorordercommission.vendorAmount),0) as payableAmt', 'vendorordercommission', array("vendorordercommission.isActive" => 1, "vendorordercommission.isPaid" => 1, "vendorordercommission.deliveryBoyCode" => $postData['vendorCode']), array(), array(), array(), array(), "", "", array(), $extraCondition)->result()[0]->payableAmt;
			$penaltyAmt = $this->GlobalModel->selectQuery('ifnull(sum(vendorordercommission.vendorAmount),0) as penaltyAmt', 'vendorordercommission', array("vendorordercommission.isActive" => 1, "vendorordercommission.commissionType" => 'penalty', "vendorordercommission.deliveryBoyCode" => $postData['vendorCode']), array(), array(), array(), array(), "", "", array(), $extraCondition)->result()[0]->penaltyAmt;
			$grandPayable = $payableAmt - $penaltyAmt;
			$orderColumns = "vendorordercommission.addDate,vendorordercommission.orderCode,vendorordercommission.subTotal,vendorordercommission.comissionPercentage,vendorordercommission.comissionAmount,vendorordercommission.vendorAmount,vendorordercommission.commissionType,vendorordercommission.isPaid as status";
			$condition = array("vendorordercommission.isActive" => 1, 'vendorordercommission.deliveryBoyCode' => $postData['vendorCode']);
			$Records = $this->GlobalModel->selectQuery($orderColumns, 'vendorordercommission', $condition, array('vendorordercommission.id' => 'DESC'), array(), array(), array(), '', '', array(), $extraCondition);
			//echo $this->db->last_query();
			if ($Records) {
				$totalRecords = count($Records->result_array());
				foreach ($Records->result_array() as $Rec) {
					$arr = $Rec;
					if ($Rec['status'] == 1) {
						$arr['status'] = 'Paid';
					} else {
						$arr['status'] = 'Unpaid';
					}
					$resultArr[] = $arr;
				}
				return $this->response(array("status" => "200", "totalRecords" => $totalRecords, "payableAmt" => $payableAmt, "penaltyAmt" => $penaltyAmt, "grandPayable" => $grandPayable, "result" => $resultArr), 200);
			} else {
				return $this->response(array("status" => "300", "message" => " No data found"), 200);
			}
		} else {
			return $this->response(array("status" => "400", "message" => " * fields are required."), 200);
		}
	}

	public function getBillingList_post()
	{
		$postData = $this->post();
		if (isset($postData['vendorCode']) && $postData['vendorCode'] != "") {
			$vendorCode = $postData['vendorCode'];
			$selectedDate = "";
			if (isset($postData['selectedDate']) && $postData['selectedDate'] != "") {
				$selectedDate = $postData['selectedDate'];
			}
			$settingData = $this->GlobalModel->selectQuery("settings.settingValue", "settings", array("settings.code" => "SET_13"));
			if ($settingData) {
				$commission = $settingData->result_array()[0]['settingValue'];
			}
			$tableName = "vendorordermaster";
			$orderColumns = array("vendorordermaster.code,vendorordermaster.grandTotal,vendorordermaster.discount,vendorordermaster.shippingCharges,vendorordermaster.tax,vendorordermaster.totalPackgingCharges,round((((vendorordermaster.grandTotal-vendorordermaster.shippingCharges)*" . $commission . ")/100),2) as adminComission,round(((vendorordermaster.grandTotal-vendorordermaster.shippingCharges)-(((vendorordermaster.grandTotal-vendorordermaster.shippingCharges)*" . $commission . ")/100)),2) as payableAmount,vendorordermaster.addDate");
			$cond = array('vendorordermaster.vendorCode' => $vendorCode, "vendorordermaster.isActive" => 1, "vendorordermaster.orderStatus" => "DEL", "(Date(vendorordermaster.addDate))" => $selectedDate);
			$orderBy = array();
			$join = array();
			$joinType = array();
			$extracondition = "";
			$resultQuery = $this->GlobalModel->selectQuery($orderColumns, $tableName, $cond, $orderBy, $join, $joinType, array(), array(), "", "", $extracondition);
			$total = 0;
			//echo $this->db->last_query();  
			if ($resultQuery) {
				foreach ($resultQuery->result_array() as $res) {
					//echo $res['payableAmount'];
					//echo "s";
					$total += $res['payableAmount'];
				}
				return $this->response(array("status" => "200", "message" => "Billing Details", "totalPayable" => round(number_format($total, 2, ".", "")), "result" => $resultQuery->result_array()), 200);
			} else {
				return $this->response(array("status" => "300", "message" => " No data found"), 200);
			}
		} else {
			return $this->response(array("status" => "400", "message" => " * fields are required."), 200);
		}
	}

	public function testNotification_post()
	{
		$postData = $this->post();
		//  return $this->response(array("status" => "200", "message" => $postData['firebase_id']), 200);
		if (isset($postData['firebase_id']) && $postData['firebase_id'] != "") {
			$DeviceIdsArr = array();
			$DeviceIdsArr[] = $postData['firebase_id'];
			$message = 'Test ringing notification ';
			$title = 'Check Notification';
			$orderCode = "ORDER_1";
			$type = trim($postData['type']) != "" ? "forDB" : "";
			$res = $this->sendFirebaseNotification($DeviceIdsArr, $title, $message, $orderCode, $type);
			return $this->response(array("status" => "200", "message" => $res, "data" => $postData), 200);
		}
	}
	
	public function updatePreparingTime_post(){
		$postData = $this->post();
		$timeStamp = date("Y-m-d h:i:s");
		if (isset($postData['orderCode']) && $postData['orderCode'] != "" && isset($postData['preparingTime']) && $postData['preparingTime'] != "" && isset($postData['previousTime']) && $postData['previousTime'] != "") {
			$newTime=($postData['preparingTime']+$postData['previousTime']);
			$data = array('preparingMinutes' => $newTime, 'editDate' => $timeStamp);
		    $result = $this->GlobalModel->doEdit($data, 'vendorordermaster', $postData['orderCode']);
			if($result){
				return $this->response(array("status" => "200", "message" => "Preparation Time is updated."), 200);
			}
		}else {
			return $this->response(array("status" => "400", "message" => " * fields are required."), 200);
		}
	}
}
