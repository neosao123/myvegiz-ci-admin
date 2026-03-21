<?php
require(APPPATH . '/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;
class VendorApi extends REST_Controller
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
		//$this->load->library('sendsms');
	}
	 
	public function vendorLoginProcess_post()
	{
		$postData = $this->post();
		$ownerContact = trim($postData["ownerContact"]);
		$password = trim($postData["password"]);

		if ($ownerContact != "" && $password != "") {
			
				$enc_password = md5($this->input->post('password'));
				$table= "vendor";
				$column=array("vendor.*");
				$condition = array('vendor.ownerContact' => $ownerContact,'vendor.password' => $enc_password);
				$result = $this->GlobalModel->selectQuery($column,$table,$condition);
				if ($result) {
				 	$orderColumns = "vendor.code,vendor.ownerContact,vendor.entityName,vendor.firstName,vendor.lastName,vendor.middleName,vendor.address,vendor.fssaiNumber,vendor.gstNumber,vendor.latitude,vendor.longitude,vendor.entityImage,vendor.ownerContact,vendor.entityContact,vendor.email,vendor.cityCode,vendor.addressCode,vendor.packagingType,citymaster.cityName,customaddressmaster.place,customaddressmaster.state,customaddressmaster.district,customaddressmaster.taluka,customaddressmaster.pincode";
					$table= "vendor";
					$condition2 = array('vendor.ownerContact' => $ownerContact);
					$join = array(
						"customaddressmaster"=>"vendor.addressCode=customaddressmaster.code",
						"citymaster"=>"vendor.cityCode=citymaster.code"
					);
					$joinType =array(
						"customaddressmaster"=>"left",
						"citymaster"=>"inner"
					);
					$resultData = $this->GlobalModel->selectQuery($orderColumns,$table,$condition2,array(),$join,$joinType);
					// $r=$this->db->last_query();
					if ($resultData) {
						$resdata = $resultData->result_array()[0];
						  
						$data['code'] =$resdata['code'];
						$data['entityName'] =$resdata['entityName'];
						$data['firstName'] = $resdata['firstName'];
						$data['lastName'] = $resdata['lastName'];
						$data['middleName'] = $resdata['middleName'];
						$data['address'] = $resdata['address'];
						$data['fssaiNumber']= $resdata['fssaiNumber'];
						$data['gstNumber']= $resdata['gstNumber'];
						$data['latitude']=  $resdata['latitude'];
						$data['longitude']= $resdata['longitude'];						
						$data['ownerContact']= $resdata['ownerContact'];
						$data['entityContact']= $resdata['entityContact']; 
						$data['email']= $resdata['email'];
						$data['cityCode']= $resdata['cityCode'];
						$data['addressCode']= $resdata['addressCode'];
						$data['packagingType']= $resdata['packagingType'];
						$data['cityName']= $resdata['cityName'];
						$data['place']= $resdata['place']; 
						$data['district']= $resdata['district']; 
						$data['taluka']= $resdata['taluka']; 
						$data['pincode']= $resdata['pincode']; 
						$data['state']= $resdata['state']; 
						$image ="noimage";
						if($resdata['entityImage']!=""){
							$path = 'uploads/restaurant/'.$resdata['code'].'/'.$resdata['entityImage'];
							if(file_exists($path)){
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
	
		if($postData['vendorCode']!="" && $postData['oldPassword']!="" && $postData['newPassword']){
			$md5_old = md5($postData['oldPassword']);
			$condition['code'] = $postData['vendorCode'];
			$condition['password'] = $md5_old;
			$table="vendor";
			$result = $this->GlobalModel->selectQuery("vendor.code","vendor",$condition);
			
			if($result){
				$data['password'] = md5(trim($postData['newPassword']));
				$data['editID'] = $postData['vendorCode'];
				$data['editIP'] = $_SERVER['REMOTE_ADDR'];
				$result = $this->GlobalModel->doEdit($data,'vendor',$postData['vendorCode']);
				if($result=='true'){
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
		$vendorCode = trim($postData["vendorCode"]);

		if ($vendorCode != "") {
			
				$table= "vendor";
				$column=array("vendor.*");
				$condition = array('vendor.Code' => $vendorCode);
				$result = $this->GlobalModel->selectQuery($column,$table,$condition);
				if ($result) {
				 	$orderColumns = "vendor.code,vendor.ownerContact,vendor.entityName,vendor.firstName,vendor.lastName,vendor.middleName,vendor.address,vendor.fssaiNumber,vendor.gstNumber,vendor.latitude,vendor.longitude,vendor.entityImage,vendor.ownerContact,vendor.entityContact,vendor.email,vendor.cityCode,vendor.addressCode,vendor.packagingType,
					             vendor.password,vendor.entitycategoryCode,vendor.bankDetails,vendor.cartPackagingPrice,vendor.gstApplicable,vendor.gstPercent,vendor.firebaseId,vendor.walletAmount,vendor.isServiceable,vendor.manualIsServiceable,citymaster.cityName,customaddressmaster.place,customaddressmaster.state,customaddressmaster.district,customaddressmaster.taluka,customaddressmaster.pincode";
					$table= "vendor";
					$condition2 = array('vendor.Code' => $vendorCode);
					$join = array(
						"customaddressmaster"=>"vendor.addressCode=customaddressmaster.code",
						"citymaster"=>"vendor.cityCode=citymaster.code"
					);
					$joinType =array(
						"customaddressmaster"=>"left",
						"citymaster"=>"inner"
					);
					$resultData = $this->GlobalModel->selectQuery($orderColumns,$table,$condition2,array(),$join,$joinType);
					 $r=$this->db->last_query();
					// print_r($resultData->result());
		            // exit;
					if ($resultData) {
						$resdata = $resultData->result_array()[0];
						  
						$data['code'] =$resdata['code'];
						$data['entityName'] =$resdata['entityName'];
						$data['firstName'] = $resdata['firstName'];
						$data['lastName'] = $resdata['lastName'];
						$data['middleName'] = $resdata['middleName'];
						$data['address'] = $resdata['address'];
						$data['fssaiNumber']= $resdata['fssaiNumber'];
						$data['gstNumber']= $resdata['gstNumber'];
						$data['latitude']=  $resdata['latitude'];
						$data['longitude']= $resdata['longitude'];						
						$data['ownerContact']= $resdata['ownerContact'];
						$data['entityContact']= $resdata['entityContact']; 
						$data['password']= $resdata['password'];
						$data['email']= $resdata['email'];
						$data['cityCode']= $resdata['cityCode'];
						$data['addressCode']= $resdata['addressCode'];
						$data['packagingType']= $resdata['packagingType'];
					    
						
						$data['entitycategoryCode']= $resdata['entitycategoryCode'];
						$data['cartPackagingPrice']= $resdata['cartPackagingPrice'];
						$data['gstApplicable']= $resdata['gstApplicable'];
						$data['gstPercent']= $resdata['gstPercent'];
						$data['bankDetails']= $resdata['bankDetails'];
						
						
						$data['firebaseId']= $resdata['firebaseId'];
						$data['walletAmount']= $resdata['walletAmount'];
						$data['isServiceable']= $resdata['isServiceable'];
						$data['manualIsServiceable']= $resdata['manualIsServiceable']; 
						
						$data['cityName']= $resdata['cityName'];
						$data['place']= $resdata['place']; 
						$data['district']= $resdata['district']; 
						$data['taluka']= $resdata['taluka']; 
						$data['pincode']= $resdata['pincode']; 
						$data['state']= $resdata['state']; 
						
	
						
						$image ="noimage";
						if($resdata['entityImage']!=""){
							$path = 'uploads/restaurant/'.$resdata['code'].'/'.$resdata['entityImage'];
							if(file_exists($path)){
								$data['entityImage'] = base_url($path);
							}
						}  
						return $this->response(array("status" => "200", "message" => "getprofileinfo Successfully...", "result" => $data), 200);
					} else {
						return $this->response(array("status" => "300", "message" => "Invalid OR Code .".$r), 200);
					}
				} else {
					return $this->response(array("status" => "300", "message" => "Invalid  Code .".$r), 200);
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
        $joinType = array(				 
					);        
		$join = array(					
					);
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
            $this->response(array("status" => "300", "message" => "Data not found.","result"=>$data), 200);
        }
    }

	
	
	public function getMenuItemList_get()
	{
		$getData = $this->get();
		if ($getData['vendorCode'] != "" && $getData['categoryCode']) {
			
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
			
					$data = array( "count" => $maincount, "itemList" => $mainitemArray, "subCategoryList" => $subCategoryItemArray);
				
				
				$response['menuItemList'] = $data;
				return $this->response(array("status" => "200", "message" => 'Data Found', "result" => $response), 200);
			} else {
				return $this->response(array("status" => "300", "message" => 'No Data Found'), 200);
			}
	}
	
	
	public function changeItemStockStatus_post()
	{
		$postData = $this->post();
		if(isset($postData['itemCode']) && $postData['itemCode']!="" && isset($postData['flag']) && $postData['flag']!="" && isset($postData["vendorCode"]) && $postData["vendorCode"]!=""){
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
		if ($postData["vendorCode"] != ''&& $postData["firebaseId"] != ''){
			$vendorCode = $postData["vendorCode"];
			$dataDevice['firebaseId'] = $postData['firebaseId'];
			$result = $this->GlobalModel->doEdit($dataDevice,'vendor',$vendorCode);
			
			if($result != 'false')
			{ 
				return $this->response(array("status" => "200", "message" => "Firebase Id Update Successfully"), 200);
			}
			else
			{
				return $this->response(array("status" => "300", "message" => " Failed to update Firebase Id."), 200);
			}
		}
		else
		{
			$this->response(array("status" => "300", "message" => " * are required field(s)."), 200);
		}
	}
	// End update firebaseId
	
	
	// fetch all offers by vendor
    public function getOffersByVendor_post()
    {	
		$postData = $this->post();		
		if ($postData["vendorCode"] != ''){
			$vendorCode=$postData["vendorCode"];
			$tableName = "vendoroffer";
			$orderColumns = array("vendoroffer.*");
			$condition = array(
						'vendoroffer' . '.vendorCode' => $vendorCode
						);
			$orderBy = array('vendoroffer' . '.id' => 'DESC');
			 $joinType = array(
						 
						);        
			$join = array(
						 
						);
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
				$this->response(array("status" => "300", "message" => "Data not found.","result"=>$data), 200);
			}
		}
		else
		{
			$this->response(array("status" => "300", "message" => " * are required field(s)."), 200);
		}
    }
	
	// fetch  offer details by  offerId For Edit....
    public function getOffersByOfferID_post()
    {	
		$postData = $this->post();		
		if ($postData["offerCode"] != ''){
			$offerCode=$postData["offerCode"];
			$tableName = "vendoroffer";
			$orderColumns = array("vendoroffer.*");
			$condition = array(
						'vendoroffer' . '.code' => $offerCode
						);
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
				$this->response(array("status" => "300", "message" => "Data not found.","result"=>$data), 200);
			}
		}
		else
		{
			$this->response(array("status" => "300", "message" => " * are required field(s)."), 200);
		}
    }
	
	
	public function onlineOfflineStatusChange_post()
	{
		$postData = $this->post();
		if(isset($postData['flag']) && $postData['flag']!="" && isset($postData["vendorCode"]) && $postData["vendorCode"]!=""){
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
		if ($postData["vendorCode"] != "") {
			$vendorCode = trim($postData["vendorCode"]);
			$table= "vendor";
			$column=array("vendor.isServiceable");
			$condition = array('vendor.Code' => $vendorCode);
			$result = $this->GlobalModel->selectQuery($column,$table,$condition);
			if ($result) {
					return $this->response(array("status" => "200", "message" => "getprofileinfo Successfully...", "result" => $result->result_array()[0]), 200);
			} else {
				return $this->response(array("status" => "300", "message" => "Invalid  Code .".$r), 200);
			}
		} else {
			return $this->response(array("status" => "400", "message" => " * field(s) are required."), 200);
		}
	}
	
	
	
	
	
	
	
	
	
	
	public function getDashboardCounts_post()
	{
		$postData = $this->post();
		if($postData['restaurantCode']!=""){
			
			$resCode = $postData['restaurantCode'];
			
			$pendingOrders_del = $placedOrders_del = $deliveredOrders_del =0;
			$pendingOrders_tbl = $acceptedOrders_tbl = $rejectedOrders_tbl =0;
			
			$condition['restaurantCode'] = $resCode;
			$condition['orderStatus'] = 'PND';
			$result = $this->GlobalModel->selectQuery("count(*) as Count","ordermaster",$condition);
			if($result) $pendingOrders_del = $result->result_array()[0]['Count'];
			
			$condition1['restaurantCode'] = $resCode;
			$condition1['orderStatus'] = 'PLC';
			$result = $this->GlobalModel->selectQuery("count(*) as Count","ordermaster",$condition1);
			if($result) $placedOrders_del = $result->result_array()[0]['Count'];
			
			$condition2['restaurantCode'] = $resCode;
			$condition2['orderStatus'] = 'DEL';
			$result = $this->GlobalModel->selectQuery("count(*) as Count","ordermaster",$condition2);
			if($result) $deliveredOrders_del = $result->result_array()[0]['Count'];
			
			$res['deliveryOrders'] = array(
				'pendingOrders' =>$pendingOrders_del,
				'placedOrders' =>$placedOrders_del,
				'deliveredOrders' =>$deliveredOrders_del
			);
			
			$condition4['restaurantCode'] = $resCode;
			$condition4['actionStatus'] = 'PNDG';
			$result = $this->GlobalModel->selectQuery("count(*) as Count","booktable",$condition4);
			if($result) $pendingOrders_tbl = $result->result_array()[0]['Count'];
			
			$condition5['restaurantCode'] = $resCode;
			$condition5['actionStatus'] = 'ACPT';
			$result = $this->GlobalModel->selectQuery("count(*) as Count","booktable",$condition5);
			if($result) $acceptedOrders_tbl = $result->result_array()[0]['Count'];
			
			$condition6['restaurantCode'] = $resCode;
			$condition6['actionStatus'] = 'RJCT';
			$result = $this->GlobalModel->selectQuery("count(*) as Count","booktable",$condition6);
			if($result) $rejectedOrders_tbl = $result->result_array()[0]['Count'];
			
			
			$res['tableOrders'] = array(
				'pendingOrders' =>$pendingOrders_tbl,
				'acceptedOrders' =>$acceptedOrders_tbl,
				'rejectedOrders' =>$rejectedOrders_tbl
			);
			return $this->response(array("status" => "200", "message" => "Success","result"=>$res), 200);
		} else {
			return $this->response(array("status" => "400", "message" => " * field(s) are required."), 200);
		}
	}
	

	public function getOrderDetails_post()
	{
		$postData = $this->post();
		if($postData['restaurantCode']!="" && $postData['orderCode']!="") { 
			$tableName = "ordermaster";
			$orderColumns = array("customeraddressmaster.name as customerName,customeraddressmaster.mobile,ordermaster.editID as deliveryBoyCode,ordermaster.customerCode,ordermaster.code as orderCode,ordermaster.shippingCharges as deliveryCharges,ordermaster.paymentmode,ordermaster.address,ordermaster.phone,ordermaster.totalPrice as orderTotalPrice,ordermaster.addDate as orderDate, orderstatusmaster.statusName as orderStatus, paymentstatusmaster.statusName as paymentStatus,ordermaster.cityCode");
			$cond = array('ordermaster' . ".code" => $postData['orderCode']);
			$orderBy = array('ordermaster' . ".id" => 'DESC');
			$join = array('customeraddressmaster'=>'ordermaster.customerCode=customeraddressmaster.code','orderstatusmaster' => 'ordermaster.orderStatus=orderstatusmaster.statusSName', 'paymentstatusmaster' => 'ordermaster.paymentStatus=paymentstatusmaster.statusSName');
			$joinType = array('customeraddressmaster'=>'inner','orderstatusmaster' => 'inner', 'paymentstatusmaster' => 'inner');
			$resultQuery = $this->GlobalModel->selectQuery($orderColumns, $tableName, $cond, $orderBy, $join, $joinType);
			if ($resultQuery) {
				$clientOrderList = $resultQuery->result_array();				
				$totalOrders = sizeof($clientOrderList);				
				$restaurantCode = $postData["restaurantCode"]; 
				$usle = $this->GlobalModel->selectQuery('userrestaurantlineentries.*', 'userrestaurantlineentries', array('userrestaurantlineentries.restaurantCode' => $restaurantCode));
				$dataDeliveryBoy = array();
				if ($usle) { 
					foreach ($usle->result_array() as $r) {
						$userCode = $r['userCode'];
						$firstName = "";
						$lastName = "";
						$mobile = "";
						$restRes = $this->GlobalModel->selectQuery('usermaster.firstName,usermaster.lastName,usermaster.mobile', 'usermaster', array('usermaster.code' => $userCode,'usermaster.role!='=>'ADM'));
						if ($restRes) {
							$firstName = $restRes->result_array()[0]['firstName'];
							$lastName = $restRes->result_array()[0]['lastName'];
							$mobile = $restRes->result_array()[0]['mobile'];
						}
						$dataRest = array('code' => $r['code'], 'deliveryBoyCode' => $userCode, 'firstName' => $firstName, 'lastName' => $lastName, 'deliveryBoyMobile' => $mobile);
						array_push($dataDeliveryBoy, $dataRest);
					}
				}   
				for ($i = 0; $i < sizeof($clientOrderList); $i++) {
					
					$linetableName = "orderlineentries";
					$lineorderColumns = array("orderlineentries.restaurantItemCode,orderlineentries.restaurantCode,orderlineentries.weight,orderlineentries.productUom,orderlineentries.salePrice ,orderlineentries.quantity,orderlineentries.totalPrice as productTotalPrice,restaurantitem.itemName,restaurantitem.itemPhoto");
					$linecond = array("orderlineentries.orderCode" => $clientOrderList[$i]['orderCode']);
					$lineorderBy = array('orderlineentries' . ".id" => 'ASC');
					$linejoin = array('restaurantitem' => 'orderlineentries.restaurantItemCode=restaurantitem.code');
					$linejoinType = array('restaurantitem' => 'inner');
					$orderProductRes = $this->GlobalModel->selectQuery($lineorderColumns, $linetableName, $linecond, $lineorderBy, $linejoin, $linejoinType);

					if ($orderProductRes) {
						$orderProductList = $orderProductRes->result_array();
						for ($j = 0; $j < sizeof($orderProductList); $j++) { 
							$restaurantCode = $orderProductList[$j]["restaurantCode"];
							$itemPhoto = $orderProductList[$j]["itemPhoto"];
							$imageArray = array();
							array_push($imageArray, base_url('uploads/restaurantitem/' . $restaurantCode . '/restaurantitem/' . $itemPhoto));
							$orderProductList[$j]['images'] = $imageArray;
							unset($imageArray);
						}
						$clientOrderList[$i]['orderedProduct'] = $orderProductList;
						$dFormat = DateTime::createFromFormat('Y-m-d H:i:s', $clientOrderList[$i]['orderDate']);
						$oDt = $dFormat->format('d-m-Y H:i:s');
						$clientOrderList[$i]['orderDate'] = $oDt;
					}
				}
				$finalResult['orderDetails'] = $clientOrderList;
				$finalResult['deliveryBoyList'] = $dataDeliveryBoy;
				return $this->response(array("status" => "200", "totalOrders" => $totalOrders, "result" => $finalResult), 200);
			} else {
				return $this->response(array("status" => "300", "message"=>"No data found..."), 200);
			}
		}
		else{
			return $this->response(array("status" => "400","message"=>"* Fields are required..."), 200);
		}
	}
	 
	public function orderStatusUpdate_post()
	{
		$today = date('Y-m-d H:i:s');
		$postData = $this->post();
		if ($postData["orderCode"] != '' &&  $postData['restaurantCode']!="" && $postData['orderStatus']!="") {
			$data['orderStatus'] = $postData['orderStatus'];
			$data['editIP'] = $_SERVER['REMOTE_ADDR'];
			$data['editDate'] = $today;
			switch($postData['orderStatus']){
				case 'CAN':					
					break;
				case 'DEL':					
					break;
				case 'PLC':				 
					if((isset($postData['deliveryBoyCode'])) && $postData['deliveryBoyCode']!=""){
						$data['editID'] = $postData['deliveryBoyCode'];
						$data['shippedTime'] = $today;
					}  else {
						return $this->response(array("status" => "400", "message" => " Please select an delivery boy, before changing the order status to picked up."), 200);
					}
					break;
				case 'PND':					
					break;	
				case 'PRE':					
					break;
				case 'PUP':
					if((isset($postData['deliveryBoyCode'])) && $postData['deliveryBoyCode']!=""){
						$data['editID'] = $postData['deliveryBoyCode'];
						$data['shippedTime'] = $today;
					}  else {
						return $this->response(array("status" => "400", "message" => " Please select an delivery boy, before changing the order status to picked up."), 200);
					}
					break;
				case 'RJT': 
					$data['rejectedTime'] = $today;
					break;	
				case 'SHP': 
					$data['shippedTime'] = $today;
					break;	
			}
			$result = $this->GlobalModel->doEdit($data,'ordermaster',$postData['orderCode']);
			if($result) {
				
				$orderData = $this->GlobalModel->selectQuery("ordermaster.editID,ordermaster.customerCode","ordermaster",array("ordermaster.code"=>$postData["orderCode"]));
				if($orderData){
				
					$customerCode = $orderData->result_array()[0]['customerCode'];
					
					$tableName = "customeraddressmaster";
					$orderColumns = array("customeraddressmaster.firebaseId");
					$condition = array("customeraddressmaster.code " => $customerCode);
					$custRecords = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition);

					if ($custRecords->result()[0]->firebaseId != null) {
						$title = "Order Update...";
						switch($postData['orderStatus']){
							case 'CAN':		
								$message = "Your order has been cancelled!";
								break;
							case 'DEL':		
								$message = "Your order has been delivered successfully....";
								break;
							case 'PLC':				 
								$message = "Your order has been placed successfully...."; 
								break;
							case 'PND':	
								$message = "Your order is been pending...";
								break;	
							case 'PRE':					
								$message = "Your order is being preparing...";
								break;
							case 'PUP':
								$message = "Your order has been picked up by the delivery person....";
								break;
							case 'RJT': 
								$message = "Your order has been rejected...."; 
								break;	
							case 'SHP': 
								$message = "Your order has been shipped successfully....";
								break;	
						}
						$random = rand(0, 999);
						$DeviceIdsArr[] = $custRecords->result()[0]->firebaseId;
						$dataArr = array();
						$dataArr['device_id'] = $DeviceIdsArr;
						$dataArr['message'] = $message; //Message which you want to send
						$dataArr['title'] = $title;
						$dataArr['order_id'] = $code;
						$dataArr['random_id'] = $random;
						$dataArr['type'] = 'order';
						$notification['device_id'] = $DeviceIdsArr;
						$notification['message'] = $message; //Message which you want to send
						$notification['title'] = $title;
						$notification['order_id'] = $code;
						$notification['random_id'] = $random;
						$notification['type'] = 'order';
						$notify = $this->notificationlibv_3->pushNotification($dataArr, $notification);
					}
				} 
				return $this->response(array("status" => "200", "message" => "Order Status updated successfully"), 200);
			} else {
				return $this->response(array("status" => "300", "message" => "Failed to update status"), 200);
			}
		} else {
			return $this->response(array("status" => "400", "message" => " * fields are required."), 200);
		}
	}
	

	// public function changeOrderStatus_post()
	// {
	// 	$postData = $this->post();
	// 	$timeStamp = date("Y-m-d h:i:s"); 
	// 	if ($postData['orderCode'] != "" && $postData['orderStatus'] != "") {
	// 		$data = array('orderStatus' => $postData['orderStatus'], 'editDate' => $timeStamp);
	// 		$result = $this->GlobalModel->doEdit($data, 'ordermaster', $postData['orderCode']);
	// 		if ($result != 'false') {
	// 			return $this->response(array("status" => "200", "message" => "Status Chnaged Successfully."), 200);
	// 		} else {
	// 			return $this->response(array("status" => "300", "message" => "Oops.. Somenthing Went Wrong."), 200);
	// 		}
	// 	} else {
	// 		return $this->response(array("status" => "400", "message" => " * are required field(s)."), 400);
	// 	}
	// }
	
	
	// fetch all Main Categories
    public function getMainCategoryList_get()
    {	
		$tableName = "maincategorymaster";
		$orderColumns = array("maincategorymaster.mainCategoryName,maincategorymaster.categoryPhoto,maincategorymaster.code");
		$condition = array();
		$orderBy = array('maincategorymaster' . '.id' => 'ASC');
        $joinType = array(				 
					);        
		$join = array(					
					);
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
            $this->response(array("status" => "300", "message" => "Data not found.","result"=>$data), 200);
        }
    }
	//02 Aug 2021
	// fetch all Main menu Categories
   

	
	
	// Update offer details by  offerId For Edit....
    /* public function updateOffersByOfferID_get($offerCode)
    {	
		$tableName = "vendoroffer";
		$orderColumns = array("vendoroffer.*");
		$condition = array(
					 
					'vendoroffer' . '.code' => $offerCode
					);
		$orderBy = array('vendoroffer' . '.id' => 'DESC');
         $joinType = array(
					 
					);        
		$join = array(
					 
					);
		$groupByColumn = array();
		$limit = '';
		$offset = '';
		$extraCondition = "";
		$like = array();
		$Offersresult = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition)->result();
                 
        if ($Offersresult) {
            return $this->response(array("status" => "200", "result" => $Offersresult), 200);
        } else {
            $data = new stdClass();
            $this->response(array("status" => "300", "message" => "Data not found.","result"=>$data), 200);
        }
    } */
	// fetch all AddOnServices by vendor
    /* public function getAddOnServicesByVendor_get($vendorCode)
    {	
		$tableName = "vendoroffer";
		$orderColumns = array("vendoroffer.*");
		$condition = array(
					 
					'vendoroffer' . '.vendorCode' => $vendorCode
					);
		$orderBy = array('vendoroffer' . '.id' => 'DESC');
         $joinType = array(
					 
					);        
		$join = array(
					 
					);
		$groupByColumn = array();
		$limit = '';
		$offset = '';
		$extraCondition = "(vendoroffer.isDelete=0 OR vendoroffer.isDelete IS NULL) AND vendoroffer.isActive=1 ";
		$like = array();
		$Offersresult = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition)->result();
                 
        if ($Offersresult) {
            return $this->response(array("status" => "200", "result" => $Offersresult), 200);
        } else {
            $data = new stdClass();
            $this->response(array("status" => "300", "message" => "Data not found.","result"=>$data), 200);
        }
    } */
	// fetch  AddOn Service by  Add On ServicID For Edit....
    /* public function getAddOnServiceByAddOnServicID_get($offerCode)
    {	
		$tableName = "vendoroffer";
		$orderColumns = array("vendoroffer.*");
		$condition = array(
					 
					'vendoroffer' . '.code' => $offerCode
					);
		$orderBy = array('vendoroffer' . '.id' => 'DESC');
         $joinType = array(
					 
					);        
		$join = array(
					 
					);
		$groupByColumn = array();
		$limit = '';
		$offset = '';
		$extraCondition = "";
		$like = array();
		$Offersresult = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition)->result();
                 
        if ($Offersresult) {
            return $this->response(array("status" => "200", "result" => $Offersresult), 200);
        } else {
            $data = new stdClass();
            $this->response(array("status" => "300", "message" => "Data not found.","result"=>$data), 200);
        }
    } */
}