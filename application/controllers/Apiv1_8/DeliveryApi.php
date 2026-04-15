<?php
require(APPPATH . '/libraries/REST_Controller.php');

use Restserver\Libraries\REST_Controller;

date_default_timezone_set('Asia/Kolkata');
class DeliveryApi extends REST_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('form', 'url', 'html');
		$this->load->library('form_validation');
		$this->load->model('GlobalModel');
		$this->load->model('ApiModel');
		$this->load->library('notificationlibv_3');
		$this->load->library('sendemail');
		$this->load->library('passwordlib');
		$this->load->library('firestore');
	}

	//Get dashboard
	public function dashboard_post()
	{
		$postData = $this->post();
		if (isset($postData["code"]) && $postData["code"] != '') {
			$deliveryType = $this->GlobalModel->selectQuery("ifnull(usermaster.deliveryType,'') as deliveryType", "usermaster", array("usermaster.code" => $postData['code']))->result_array()[0]['deliveryType'];
			$orderColumns = array("ifnull(count(id),0) as pCount");
			$cond = array("ordermaster.isActive" => 1, "ordermaster.orderStatus" => "PND");
			$p_result = $this->GlobalModel->selectQuery($orderColumns, 'ordermaster', $cond);
			if ($p_result) {
				$pendingCount = $p_result->result_array()[0]["pCount"];
			}
			$orderColumns = array("ifnull(count(id),0) as plcCount");
			$cond = array("ordermaster.isActive" => 1, "ordermaster.orderStatus" => "PLC", "ordermaster.editID" => $postData["code"]);
			$plc_result = $this->GlobalModel->selectQuery($orderColumns, 'ordermaster', $cond);
			if ($plc_result) {
				$placeCount = $plc_result->result_array()[0]["plcCount"];
			}
			$orderColumns = array("ifnull(count(id),0) as dCount");
			$cond = array("ordermaster.isActive" => 1, "ordermaster.orderStatus" => "DEL", "ordermaster.editID" => $postData["code"], "date(ordermaster.editDate) >=" => date('Y-m-d'));
			$d_result = $this->GlobalModel->selectQuery($orderColumns, 'ordermaster', $cond);
			if ($d_result) {
				$deliveredCount = $d_result->result_array()[0]["dCount"];
			}
			$orderColumns = array("ifnull(count(id),0) as rCount");
			$cond = array("ordermaster.isActive" => 1, "ordermaster.orderStatus" => "RJT", "ordermaster.editID" => $postData["code"]);
			$r_result = $this->GlobalModel->selectQuery($orderColumns, 'ordermaster', $cond);
			if ($r_result) {
				$rejectCount = $r_result->result_array()[0]["rCount"];
			}
			$foodpendingCount = $foodplacedCount = $fooddeliveredCount = $foodrejectCount = 0;
			if ($deliveryType == 'food') {
				$orderColumns = array("ifnull(count(id),0) as fpCount");
				$cond = array("vendorordermaster.isActive" => 1, "vendorordermaster.orderStatus" => "PND");
				$fp_result = $this->GlobalModel->selectQuery($orderColumns, 'vendorordermaster', $cond);
				if ($fp_result) {
					$foodpendingCount = $fp_result->result_array()[0]["fpCount"];
				}
				$orderColumns = array("ifnull(count(id),0) as fplcCount");
				$cond = array("vendorordermaster.isActive" => 1, "vendorordermaster.orderStatus" => "PLC", "vendorordermaster.editID" => $postData["code"]);
				$fplc_result = $this->GlobalModel->selectQuery($orderColumns, 'vendorordermaster', $cond);
				if ($fplc_result) {
					$foodplacedCount = $fplc_result->result_array()[0]["fplcCount"];
				}
				$orderColumns = array("ifnull(count(id),0) as fdCount");
				$cond = array("vendorordermaster.isActive" => 1, "vendorordermaster.orderStatus" => "DEL", "vendorordermaster.editID" => $postData["code"], "date(ordermaster.editDate) >=" => date('Y-m-d'));
				$fd_result = $this->GlobalModel->selectQuery($orderColumns, 'vendorordermaster', $cond);
				if ($fd_result) {
					$fooddeliveredCount = $fd_result->result_array()[0]["fdCount"];
				}
				$orderColumns = array("ifnull(count(id),0) as frCount");
				$cond = array("vendorordermaster.isActive" => 1, "vendorordermaster.orderStatus" => "RJT", "vendorordermaster.editID" => $postData["code"]);
				$fr_result = $this->GlobalModel->selectQuery($orderColumns, 'vendorordermaster', $cond);
				if ($fr_result) {
					$foodrejectCount = $fr_result->result_array()[0]["frCount"];
				}
			}
			$response['pendingCount'] = $pendingCount + $foodpendingCount;
			$response['placedCount'] = $placeCount + $foodplacedCount;
			$response['deliveredCount'] = $deliveredCount + $fooddeliveredCount;
			$response['rejectCount'] = $rejectCount + $foodrejectCount;

			$result["dashboard"] = $response;
			$this->response(array("status" => "200", "result" => $result), 200);
		}
		else {
			$this->response(array("status" => "400", "message" => " * are Required field(s)."), 400);
		}
	}

	//login process
	public function loginProcess_post()
	{
		$postData = $this->post();
		if ($postData["userName"] != '' && $postData["userPassword"] != '') {
			$loginData = array(
				"password" => md5($postData["userPassword"]),
				"role" => "DLB",
				"isActive" => 1,
			);

			if (is_numeric($postData['userName'])) {
				$loginData["mobile"] = $postData['userName'];
			}
			else {
				$loginData["username"] = $postData['userName'];
			}

			if ($this->ApiModel->login_delivery($loginData)) {

				if (is_numeric($postData['userName'])) {
					$data["mobile"] = $postData['userName'];
				}
				else {
					$data["username"] = $postData['userName'];
				}

				$resultData = $this->ApiModel->read_Delivery_information($data);
				$userCode = $resultData[0]->code;
				$loginStatus = 0;
				$res = $this->GlobalModel->selectQuery('deliveryBoyActiveOrder.loginStatus', 'deliveryBoyActiveOrder', array('deliveryBoyActiveOrder.deliveryBoyCode' => $userCode));
				if ($res) {
					$loginStatus = $res->result_array()[0]['loginStatus'];
					if ($loginStatus == 0) {
						$this->GlobalModel->doEditWithField(array("deliveryBoyActiveOrder.loginStatus" => 1), "deliveryBoyActiveOrder", 'deliveryBoyCode', $userCode);
						$res = $this->GlobalModel->selectQuery('deliveryBoyActiveOrder.loginStatus', 'deliveryBoyActiveOrder', array('deliveryBoyActiveOrder.deliveryBoyCode' => $userCode));
						$loginStatus = $res->result_array()[0]['loginStatus'];
					}
				}
				else {
					$dataDbActive['deliveryBoyCode'] = $userCode;
					$dataDbActive['orderCount'] = 0;
					$dataDbActive['loginStatus'] = 1;
					$dataDbActive['isActive'] = 1;
					$resultDbActive = $this->GlobalModel->addWithoutYear($dataDbActive, 'deliveryBoyActiveOrder', 'DBA');
				}
				$cityName = "";
				$condition = '`code`="' . $resultData[0]->cityCode . '"';
				$this->db->select('cityName');
				$this->db->from('citymaster');
				$this->db->where($condition);
				$this->db->limit(1);
				$getCityData = $this->db->get();
				if ($getCityData->num_rows() > 0) {
					foreach ($getCityData->result_array() as $item) {
						$cityName = $item["cityName"];
					}
				}
				$resultArray = array(
					'code' => $resultData[0]->code,
					'name' => $resultData[0]->name,
					'cityCode' => $resultData[0]->cityCode,
					'cityName' => $cityName,
					'userName' => $resultData[0]->username,
					'role' => $resultData[0]->role,
					'userEmail' => $resultData[0]->userEmail,
					'profilePhoto' => base_url() . 'uploads/profilePhoto/' . $resultData[0]->profilePhoto,
					'isActive' => $resultData[0]->isActive,
					'contactNumber' => $resultData[0]->mobile,
					'deliveryType' => $resultData[0]->deliveryType,
					'loginStatus' => $loginStatus
				);
				$result['userData'] = $resultArray;

				$firestore = [
					"bearing" => "0.0",
					"id" => $resultData[0]->code,
					"latitude" => "",
					"longitude" => "",
				];
				//add deliveryboy to firebase			   
				$this->firestore->add_deliveryboy($firestore, "PATCH");
				return $this->response(array("status" => "200", "message" => "Login Successfully...", "result" => $result), 200);
			}
			else {
				return $this->response(array("status" => "300", "message" => "incorrect username or Password"), 200);
			}
		}
		else {
			return $this->response(array("status" => "400", "message" => " * are Required field(s)."), 400);
		}
	} //end login  Process

	//login process
	public function getProfileDetails_post()
	{
		$postData = $this->post();
		if (isset($postData["code"]) && $postData["code"] != '') {
			$resultData = $this->ApiModel->read_Delivery_information(array("code" => $postData['code']));
			if ($resultData != false) {

				$cityName = "";
				$condition = '`code`="' . $resultData[0]->cityCode . '"';
				$this->db->select('cityName');
				$this->db->from('citymaster');
				$this->db->where($condition);
				$this->db->limit(1);
				$getCityData = $this->db->get();
				if ($getCityData->num_rows() > 0) {
					foreach ($getCityData->result_array() as $item) {
						$cityName = $item["cityName"];
					}
				}

				$userCode = $resultData[0]->code;
				$loginStatus = 0;
				$res = $this->GlobalModel->selectQuery('deliveryBoyActiveOrder.loginStatus', 'deliveryBoyActiveOrder', array('deliveryBoyActiveOrder.deliveryBoyCode' => $userCode));
				if ($res) {
					$loginStatus = $res->result_array()[0]['loginStatus'];
				}
				$resultArray = array(
					'code' => $resultData[0]->code,
					'name' => $resultData[0]->name,
					'cityName' => $cityName,
					'userName' => $resultData[0]->username,
					'role' => $resultData[0]->role,
					'userEmail' => $resultData[0]->userEmail,
					'profilePhoto' => base_url() . 'uploads/profilePhoto/' . $resultData[0]->profilePhoto,
					'isActive' => $resultData[0]->isActive,
					'contactNumber' => $resultData[0]->mobile,
					'loginStatus' => $loginStatus
				);
				$result['userData'] = $resultArray;
				return $this->response(array("status" => "200", "result" => $result), 200);
			}
			else {
				return $this->response(array("status" => "300", "message" => "No data Found"), 200);
			}
		}
		else {
			return $this->response(array("status" => "400", "message" => " * are Required field(s)."), 400);
		}
	} //end login  Process

	//delivery boy accept order api
	public function deliveryAccpetUpdate_post()
	{
		$postData = $this->post();
		if ($postData['code'] != "" && $postData['status'] != "") {
			if ($postData['status'] == 1) {
				$loginStatus = 0;
				$dataupdate['loginStatus'] = 1;
			}
			else {
				$dataupdate['loginStatus'] = 0;
				$loginStatus = false;
				$this->db->select('deliveryBoyActiveOrder.code');
				$this->db->from('deliveryBoyActiveOrder');
				$this->db->where('deliveryBoyActiveOrder.deliveryBoyCode', $postData['code']);
				$this->db->where('deliveryBoyActiveOrder.orderCount', 1);
				$this->db->where('deliveryBoyActiveOrder.orderCode IS NOT NULL');
				$query = $this->db->get();
				$getOrder = $query->row();
				if (!empty($getOrder)) {
					$dataupdate['loginStatus'] = 1;
					$loginStatus = true;
				}
			}
			$userCode = $postData['code'];
			$dataupdate['editID'] = $userCode;
			$dataupdate['editIP'] = $_SERVER['REMOTE_ADDR'];
			$res = $this->GlobalModel->doEditWithField($dataupdate, 'deliveryBoyActiveOrder', 'deliveryBoyCode', $userCode);
			if ($res != 'false') {
				if ($loginStatus == true) {
					return $this->response(array("status" => "200", "message" => "You have an order. You are not allowed to go offline."), 200);
				}
				return $this->response(array("status" => "200", "message" => "Status updated successfully"), 200);
			}
			else {
				return $this->response(array("status" => "300", "message" => " Failed to update status."), 200);
			}
		}
		else {
			return $this->response(array("status" => "400", "message" => " * are Required field(s)."), 400);
		}
	}

	//profile update 
	public function deliveryProfileUpdate_post()
	{
		$postData = $this->post();
		if (isset($postData["code"]) && $postData["code"] != '') {
			$resultData = $this->GlobalModel->selectDataById($postData["code"], 'usermaster')->result_array();
			if (sizeof($resultData) == 1) {
				if (isset($postData["email"]) && $postData["email"] != "") {
					$data['userEmail'] = $postData["email"];
				}
				if (isset($postData["mobile"]) && $postData["mobile"] != "") {
					$data['mobile'] = $postData["mobile"];
				}
				if (isset($postData["name"]) && $postData["name"] != "") {
					$data['name'] = $postData["name"];
				}
				$condition2 = array('userEmail' => $postData["email"], 'code!=' => $postData["code"]);
				$result2 = $this->GlobalModel->checkDuplicateRecordNew($condition2, 'usermaster');

				$condition1 = array('mobile' => $postData["mobile"], 'code!=' => $postData["code"]);
				$result1 = $this->GlobalModel->checkDuplicateRecordNew($condition1, 'usermaster');
				if ($result2 == 1) {
					return $this->response(array("status" => "300", "message" => "Email already exist."), 200);
				}
				else if ($result1 == 1) {
					return $this->response(array("status" => "300", "message" => "Mobile number already exist."), 200);
				}
				else {
					$resultMaster = $this->GlobalModel->doEdit($data, 'usermaster', $postData["code"]);
					return $this->response(array("status" => "200", "message" => "Your profile has been updated successfully."), 200);
				}
			}
			else {
				return $this->response(array("status" => "300", "message" => "Failed to update profile"), 200);
			}
		}
		else {
			$this->response(array("status" => "300", "message" => " * are required field(s)."), 400);
		}
	}

	//profile pic upload
	public function profilePicUpload_post()
	{
		$postData = $this->post();
		if (isset($postData["code"]) && $postData["code"] != '' && isset($_FILES['file']['name']) && $_FILES['file']['name'] != '') {
			$checkData = array("code" => $postData["code"], "isDelete" => 0);
			if ($this->GlobalModel->checkExistAndInsertRecords($checkData, 'usermaster')) {
				return $this->response(array("status" => "400", "message" => "User Not Exist"), 200);
				exit();
			}
			$profilePhoto = "";
			$uploadRootDir = 'uploads/';
			$uploadDir = 'uploads/profilePhoto/';
			if (!empty($_FILES['file']['name'])) {
				$tmpFile = $_FILES['file']['tmp_name'];
				$filename = $uploadDir . '/' . $postData["code"] . '.jpg';
				move_uploaded_file($tmpFile, $filename);
				if (file_exists($filename)) {
					$profilePhoto = $postData["code"] . '.jpg';
				}
			}
			$subData = array(
				'profilePhoto' => $profilePhoto
			);
			$filedoc = $this->GlobalModel->doEdit($subData, 'usermaster', $postData["code"]);
			$this->response(array("status" => "200", "message" => "Profile photo uploaded successfully."), 200);
		}
		else {
			$this->response(array("status" => "300", "message" => " * are required field(s)."), 400);
		}
	}


	//Start update password
	public function updatePassword_post()
	{
		$postData = $this->post();
		if (isset($postData["code"]) && $postData["code"] != '' && isset($postData["oldPassword"]) && $postData["oldPassword"] != '' && isset($postData["newPassword"]) && $postData["newPassword"] != '') {
			$oldPassword = md5($postData["oldPassword"]);
			$resultData = $this->GlobalModel->selectDataById($postData["code"], 'usermaster')->result_array();
			$dbPassword = $resultData[0]['password'];
			if ($dbPassword == $oldPassword) {
				$passData = [
					"password" => md5($postData["newPassword"])
				];
				$passresult = $this->GlobalModel->doEdit($passData, 'usermaster', $postData["code"]);
				if ($passresult != false) {
					return $this->response(array("status" => "200", "message" => "Your password has been updated successfully."), 200);
				}
				else {
					return $this->response(array("status" => "300", "message" => " Failed to update your password."), 200);
				}
			}
			else {
				return $this->response(array("status" => "300", "message" => "You entered wrong current password."), 200);
			}
		}
		else {
			$this->response(array("status" => "400", "message" => " * are required field(s)."), 400);
		}
	} // End update password

	public function getOrdersByStatus_post()
	{
		$postData = $this->post();
		log_message("error", "getOrdersByStatus=>" . json_encode($postData));
		if (isset($postData['code']) && $postData['code'] != '' && isset($postData['orderStatus']) && $postData['orderStatus'] != "") {
			$clientOrderLista = array();
			$orderStatus = $postData['orderStatus'];
			$orderColumns = array("'vegetable' as orderType,ordermaster.code as orderCode, 0 as tax,0 as totalPackagingCharges, ordermaster.discount,0 as subTotal,ordermaster.shippingCharges as deliveryCharges,ordermaster.paymentmode,ordermaster.address,ordermaster.phone,ordermaster.totalPrice as orderTotalPrice,ordermaster.addDate as orderDate, ordermaster.latitude,ordermaster.longitude, orderstatusmaster.statusName as orderStatus, paymentstatusmaster.statusName as paymentStatus,clientmaster.code as clientCode,clientmaster.name");
			$join = array('clientmaster' => 'clientmaster.code = ordermaster.clientCode', 'orderstatusmaster' => 'ordermaster' . '.orderStatus=' . 'orderstatusmaster' . '.statusSName', 'paymentstatusmaster' => 'ordermaster' . '.paymentStatus=' . 'paymentstatusmaster' . '.statusSName');
			$joinType = array('clientmaster' => 'left', 'orderstatusmaster' => 'inner', 'paymentstatusmaster' => 'inner');
			$cond = array("ordermaster.deliveryBoyCode" => $postData['code'], "ordermaster.isActive" => 1);
			$orderBy = array('ordermaster' . ".id" => 'DESC');
			$like = array();
			$offset = "";
			$groupByColumn = array();
			$extraCondition = "";
			if ($orderStatus != "") {
				$extraCondition = " ordermaster.orderStatus='" . $orderStatus . "'";
				$limit = 1;
				//set limit from orderStatus
				switch ($orderStatus) {
					case 'PND':
						$limit = 1;
						break;
					case 'PLC':
						$limit = 1;
						break;
					case 'PRE':
						$limit = 1;
						break;
					case 'RFP':
						$limit = 1;
						break;
					case 'PUP':
						$limit = 1;
						break;
					case 'RCH':
						$limit = 1;
						break;
					case 'DEL':
						$limit = "";
						break;
					case 'RJT':
						$limit = "";
						break;
					case 'CAN':
						$limit = "";
						break;
				}
			}
			$resultQuery = $this->GlobalModel->selectQuery($orderColumns, 'ordermaster', $cond, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
			$r[] = $this->db->last_query();
			if ($resultQuery) {
				$clientOrderList = $resultQuery->result_array();
				$totalOrders = sizeof($clientOrderList);
				for ($i = 0; $i < sizeof($clientOrderList); $i++) {
					$order = $clientOrderList[$i];
					$linetableName = "orderlineentries";
					$lineorderColumns = array("orderlineentries.productCode,orderlineentries.weight,orderlineentries.productUom,orderlineentries.productPrice,orderlineentries.quantity,orderlineentries.totalPrice as productTotalPrice,productmaster.productName");
					$linecond = array('orderlineentries' . ".orderCode" => $clientOrderList[$i]['orderCode']);
					$lineorderBy = array('orderlineentries' . ".id" => 'ASC');
					$linejoin = array('productmaster' => 'orderlineentries' . '.productCode=' . 'productmaster' . '.code');
					$linejoinType = array('productmaster' => 'inner');
					$orderProductRes = $this->GlobalModel->selectQuery($lineorderColumns, $linetableName, $linecond, $lineorderBy, $linejoin, $linejoinType);
					if ($orderProductRes) {
						$productPrice = 0;
						$orderProductList = $orderProductRes->result_array();
						for ($j = 0; $j < sizeof($orderProductList); $j++) {
							$condition2 = array('productCode' => $orderProductList[$j]["productCode"]);
							$images_result = $this->ApiModel->selectData('productphotos', '', '', $condition2)->result_array();
							$productCode = $orderProductList[$j]["productCode"];
							$imageArray = array();
							for ($img = 0; $img < sizeof($images_result); $img++) {
								array_push($imageArray, base_url() . 'uploads/product/' . $productCode . '/' . $images_result[$img]['productPhoto']);
							}
							$orderProductList[$j]['images'] = $imageArray;
							unset($imageArray);
							$productPrice += $orderProductList[$j]["productTotalPrice"];
						}
						$order['totalItems'] = sizeof($orderProductList);
						$order['orderPrice'] = $productPrice;
						$dFormat = DateTime::createFromFormat('Y-m-d H:i:s', $clientOrderList[$i]['orderDate']);
						$oDt = $dFormat->format('d-m-Y H:i:s');
						$order['orderDate'] = $oDt;
						$order['orderedProduct'] = $orderProductList;
					}
					$clientOrderLista[] = $order;
				}
			}
			//print_r($data);
			// now check order in food 	   
			$orderColumns = array("'food' as orderType,vendorordermaster.code as orderCode,vendorordermaster.tax,vendorordermaster.totalPackgingCharges as totalPackagingCharges,vendorordermaster.discount,vendorordermaster.subTotal as orderPrice,vendorordermaster.vendorCode,vendorordermaster.shippingCharges as deliveryCharges,vendorordermaster.paymentmode,vendorordermaster.address, vendorordermaster.grandTotal as orderTotalPrice,vendorordermaster.addDate as orderDate, vendorordermaster.phone, vendorordermaster.latitude, vendorordermaster.longitude,vendororderstatusmaster.statusSName as orderStatus, paymentstatusmaster.statusName as paymentStatus, clientmaster.code as clientCode, clientmaster.name,vendorordermaster.trackingPort,vendorordermaster.reachStatus,ifnull(vendorordermaster.preparingMinutes,30) as  preparationTime,IFNULL(DATE_FORMAT(vendorordermaster.orderAcceptedTime, '%H:%i:%s'), '') as orderAcceptedTime");
			$join = array('clientmaster' => 'clientmaster.code = vendorordermaster.clientCode', 'vendororderstatusmaster' => 'vendorordermaster.orderStatus=vendororderstatusmaster.statusSName', 'paymentstatusmaster' => 'vendorordermaster.paymentStatus=paymentstatusmaster.statusSName');
			$joinType = array('clientmaster' => 'left', 'vendororderstatusmaster' => 'inner', 'paymentstatusmaster' => 'inner');
			$cond = array("vendorordermaster.deliveryBoyCode" => $postData['code'], "vendorordermaster.isActive" => 1);
			$orderBy = array('vendorordermaster' . ".id" => 'DESC');
			$like = array();
			$limit = "";
			$offset = "";
			$groupByColumn = array();
			$extraCondition = "";
			if ($orderStatus != "") {
				if ($orderStatus == "RCH")
					$extraCondition = " vendorordermaster.reachStatus='" . $orderStatus . "'";
				else
					$extraCondition = " vendorordermaster.orderStatus='" . $orderStatus . "'";
				//set limit from orderStatus
				switch ($orderStatus) {
					case 'PND':
						$limit = 1;
						break;
					case 'PLC':
						$limit = 1;
						break;
					case 'PRE':
						$limit = 1;
						break;
					case 'RFP':
						$limit = 1;
						break;
					case 'PUP':
						$limit = 1;
						break;
					case 'RCH':
						$limit = 1;
						break;
					case 'DEL':
						$limit = "";
						break;
					case 'RJT':
						$limit = "";
						break;
					case 'CAN':
						$limit = "";
						break;
				}
			}
			$resultQuery = $this->GlobalModel->selectQuery($orderColumns, 'vendorordermaster', $cond, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
			//echo $this->db->last_query();
			$r[] = $this->db->last_query();
			if ($resultQuery) {
				$clientOrderList = $resultQuery->result_array();
				$totalOrders = sizeof($clientOrderList);
				for ($i = 0; $i < sizeof($clientOrderList); $i++) {
					$order = $clientOrderList[$i];
					$order['trackingUrl'] = "";
					$order['trackingPort'] = "";
					if ($clientOrderList[$i]['trackingPort'] != '' && $clientOrderList[$i]['trackingPort'] != NULL) {
						$order['trackingUrl'] = "https://myvegiz.com";
						$order['trackingPort'] = $clientOrderList[$i]['trackingPort'];
					}
					// ge vendor data for the order when ordertype is food
					$vendorCode = $clientOrderList[$i]['vendorCode'];
					$venCondition['vendor.code'] = $vendorCode;
					$vendorData = $this->GlobalModel->selectQuery('vendor.*', 'vendor', $venCondition);
					if ($vendorData) {
						$vendor = $vendorData->result_array()[0];
						$vd['vendorCode'] = $vendorCode;
						$vd['vendorName'] = $vendor['entityName'];
						$vd['address'] = $vendor['address'];
						$vd['vendorContact'] = $vendor['ownerContact'];
						$vd['latitude'] = $vendor['latitude'];
						$vd['longitude'] = $vendor['latitude'];
						$path = "noimage";
						if ($vendor['entityImage'] != "") {
							$path = 'uploads/vendor/' . $vendorCode . '/' . $vendor['entityImage'];
							if (file_exists($path)) {
								$path = base_url($path);
							}
						}
						$vd['vendorImage'] = $path;
						$order['vendorDetails'] = $vd;
					}
					$linetableName = "vendororderlineentries";
					$lineorderColumns = array("vendororderlineentries.vendorItemCode,vendororderlineentries.addons,vendororderlineentries.addonsCode,vendororderlineentries.quantity,vendororderlineentries.priceWithQuantity,vendororderlineentries.itemPackagingCharges,vendoritemmaster.vendorCode,vendoritemmaster.itemName,vendoritemmaster.itemPhoto");
					$linecond = array("vendororderlineentries.orderCode" => $clientOrderList[$i]['orderCode']);
					$lineorderBy = array('vendororderlineentries' . ".id" => 'ASC');
					$linejoin = array('vendoritemmaster' => 'vendororderlineentries.vendorItemCode=vendoritemmaster.code');
					$linejoinType = array('vendoritemmaster' => 'inner');
					$orderProductRes = $this->GlobalModel->selectQuery($lineorderColumns, $linetableName, $linecond, $lineorderBy, $linejoin, $linejoinType);
					if ($orderProductRes) {
						$productPrice = 0;
						$itemsArray = array();
						$orderProductList = $orderProductRes->result_array();
						for ($j = 0; $j < sizeof($orderProductList); $j++) {
							$itemAr['vendorItemCode'] = $orderProductList[$j]["vendorItemCode"];
							$itemAr['itemName'] = $orderProductList[$j]["itemName"];
							$itemAr['addons'] = $orderProductList[$j]["addons"];
							$itemAr['addonsCode'] = $orderProductList[$j]["addonsCode"];
							$itemAr['quantity'] = $orderProductList[$j]["quantity"];
							$itemAr['priceWithQuantity'] = $orderProductList[$j]["priceWithQuantity"];
							$itemAr['itemPackagingCharges'] = $orderProductList[$j]["itemPackagingCharges"];
							if ($orderProductList[$j]["itemPhoto"] != "") {
								$path = 'partner/uploads/' . $orderProductList[$j]["vendorCode"] . '/vendoritem/' . $orderProductList[$j]["itemPhoto"];
								if (file_exists($path)) {
									$itemAr['itemImage'] = base_url($path);
								}
								else {
									$itemAr['itemImage'] = 'noimage';
								}
							}
							else {
								$itemAr['itemImage'] = 'noimage';
							}
							$resultArr = [];
							if ($orderProductList[$j]['addonsCode'] != '' && $orderProductList[$j]['addonsCode'] != NULL) {
								$addonsCode = rtrim($orderProductList[$j]['addonsCode'], ',');
								$savedaddonsCodes = explode(',', $addonsCode);
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
							$itemAr['addonsDetails'] = $resultArr;
							$itemsArray[] = $itemAr;
						}
						$order['totalItems'] = sizeof($orderProductList);
						$dFormat = DateTime::createFromFormat('Y-m-d H:i:s', $clientOrderList[$i]['orderDate']);
						$oDt = $dFormat->format('d-m-Y H:i:s');
						$order['orderDate'] = $oDt;
						$order['orderedItems'] = $itemsArray;
					}
					$clientOrderLista[] = $order;
				}
			//$data[] = $clientOrderList; 
			}
			//print_r($data);
			if (!empty($clientOrderLista)) {
				$finalResult['orders'] = $clientOrderLista;
				$this->response(array("status" => "200", "message" => " Order details", "result" => $finalResult), 200);
			}
			else {
				$this->response(array("status" => "300", "message" => "No Data Found", 'r' => $r), 200);
			}
		}
		else {
			$this->response(array("status" => "400", "message" => " * are Required field(s)."), 400);
		}
	}

	public function distanceInKms($lat1, $lon1, $lat2, $lon2)
	{
		$radius = 6371; // radius of the Earth in km
		$dLat = deg2rad($lat2 - $lat1);
		$dLon = deg2rad($lon2 - $lon1);
		$a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) * sin($dLon / 2);
		$c = 2 * atan2(sqrt($a), sqrt(1 - $a));
		$distance = $radius * $c;
		return round($distance, 2);
	}

	//update orders status
	public function updateOrderStatus_post()
	{
		$ip = $_SERVER['REMOTE_ADDR'];
		$postData = $this->post();
		if (isset($postData['code']) && $postData['code'] != "" && isset($postData['orderStatus']) && $postData['orderStatus'] != "" && isset($postData['orderCode']) && $postData['orderCode'] != "") {
			$orderStatus = $postData['orderStatus'];
			$orderCode = $postData['orderCode'];
			$code = $addID = $postData['code'];
			$trackingPort = '';
			if ($orderStatus == "PLC") {
				$ordData['orderStatus'] = 'PLC';
				$reason = "Deliveryboy accepted order";
			}
			else if ($orderStatus == "REL") {
				if (isset($postData['reason'])) {
					if ($postData['reason'] != "") {

						$reason = $postData['reason'];

						$ordData['orderStatus'] = 'PND';
						$ordData['deliveryBoyCode'] = null;

						//add order status with reason when releases 
						$dataBookLine['reason'] = $postData['reason'];

						$data['reason'] = $postData['reason'];
						$data['orderCode'] = $postData['orderCode'];
						$data['deliveryBoyCode'] = $code;
						$data['actionDate'] = date('Y-m-d H:i:s');
						$data['orderStatus'] = $orderStatus;
						$data['isActive'] = 1;
						$result = $this->GlobalModel->addWithoutYear($data, 'deliveryboystatuslines', 'DBSL');

						$dataUpCnt['orderCount'] = 0;
						$dataUpCnt['orderCode'] = null;
						$dataUpCnt['orderType'] = null;
						$delbActiveOrder = $this->GlobalModel->doEditWithField($dataUpCnt, 'deliveryBoyActiveOrder', 'deliveryBoyCode', $postData['code']);

						$FoodOrder = $this->GlobalModel->selectQuery("vendorordermaster.code", "vendorordermaster", array("vendorordermaster.code" => $orderCode));
						if ($FoodOrder) {
							$orderData['deliveryBoyCode'] = null;
							$orderData['orderStatus'] = 'PND';
							$delbActiveOrder = $this->GlobalModel->doEditWithField($orderData, 'vendorordermaster', 'code', $orderCode);
						}
						else {
							$VegGroOrder = $this->GlobalModel->selectQuery("ordermaster.code", "ordermaster", array("ordermaster.code" => $orderCode));
							if ($VegGroOrder) {
								$orderData['deliveryBoyCode'] = null;
								$orderData['orderStatus'] = 'PND';
								$delbActiveOrder = $this->GlobalModel->doEditWithField($orderData, 'ordermaster', 'code', $orderCode);
							}
						}
					}
					else {
						return $this->response(array("status" => "400", "message" => "PLease provide a valid reason to release this order."), 400);
					}
				}
				else {
					return $this->response(array("status" => "400", "message" => "PLease provide a valid reason to release this order."), 400);
				}
			}
			else if ($orderStatus == "PUP") {
				$ordData['orderStatus'] = 'PUP';
				$reason = "Order has been picked-up";
			}
			else if ($orderStatus == 'RCH') {
				//$ordData['orderStatus'] = 'RCH';
				$ordData['reachStatus'] = 'RCH';
				$reason = "Delivery person reached near the restaurant";
			}
			else if ($orderStatus == "DEL") {
				$ordData['orderStatus'] = 'DEL';
				//$ordData['trackingPort'] = NULL;
				$FoodOrder = $this->GlobalModel->selectQuery("vendorordermaster.code", "vendorordermaster", array("vendorordermaster.code" => $orderCode));
				if ($FoodOrder) {
					$orderColumns = array("clientmaster.name,vendorordermaster.*");
					$join = array('clientmaster' => 'clientmaster.code = vendorordermaster.clientCode');
					$joinType = array('clientmaster' => 'inner');
					$cond = array("vendorordermaster.code" => $orderCode);
					$tableName = 'vendorordermaster';
					$isRes = 1;
				}
				else {
					$orderColumns = array("clientmaster.name,ordermaster.*");
					$join = array('clientmaster' => 'clientmaster.code = ordermaster.clientCode');
					$joinType = array('clientmaster' => 'inner');
					$cond = array("ordermaster.code" => $orderCode);
					$tableName = 'ordermaster';
					$isRes = 0;
				}
				$clientname = '';
				$resultQuery = $this->GlobalModel->selectQuery($orderColumns, $tableName, $cond, array(), $join, $joinType, array(), '', '', array(), '');
				if ($resultQuery) {
					$clientname = $resultQuery->result_array()[0]['name'];
					if ($isRes == 1) {
						$trackingPort = $resultQuery->result_array()[0]['trackingPort'];
					}
				}
				$reason = $clientname . ' ,order (' . $orderCode . ') has been successfully delivered to the given address.';
				//reset delivery boy after deliver order to assign further order
				$restDelv['orderCode'] = null;
				$restDelv['orderType'] = null;
				$restDelv['editID'] = $addID;
				$restDelv['editIP'] = $ip;
				$restDelv['orderCount'] = 0;
				$delbActiveOrder = $this->GlobalModel->doEditWithField($restDelv, 'deliveryBoyActiveOrder', 'deliveryBoyCode', $postData['code']);
			}
			else {
				return $this->response(array("status" => "400", "message" => "Invalid Order Status"), 400);
			}
			#replace $ template in title 
			$statusDescription = "";
			// $statusDescription = $order_status_record->messageDescription;
			// $statusDescription = str_replace("$", $orderCode, $statusDescription);
			$dataBookLine = array(
				"orderCode" => $orderCode,
				"statusPutCode" => $postData['code'],
				"statusLine" => $orderStatus,
				"statusTime" => date("Y-m-d H:i:s"),
				"reason" => $reason,
				"statusTitle" => "",
				"statusDescription" => $statusDescription,
				"isActive" => 1
			);
			$bookLineResult = $this->GlobalModel->addWithoutYear($dataBookLine, 'bookorderstatuslineentries', 'BOL');
			if ($bookLineResult != 'false') {
				if ($orderStatus == "PLC") {
					$orderFrom = $this->GlobalModel->check_vege_food_order($orderCode);
					if ($orderFrom == "food") {
						if (isset($postData['latitude']) && $postData['latitude'] != "" && isset($postData['longitude']) && $postData['longitude'] != "") {
							$latidude = $postData['latitude'];
							$longitude = $postData['longitude'];
							$this->GlobalModel->doEdit(['delvLat' => $latidude, "delvLng" => $longitude], 'ordermaster', $orderCode);
						}
					}
					else {
						if (isset($postData['latitude']) && $postData['latitude'] != "" && isset($postData['longitude']) && $postData['longitude'] != "") {
							$latidude = $postData['latitude'];
							$longitude = $postData['longitude'];
							$this->GlobalModel->doEdit(['delvLat' => $latidude, "delvLng" => $longitude], 'ordermaster', $orderCode);
						}
					}
				}
				log_message("error", "status=>" . json_encode($dataBookLine));
				//check cuurent order for notification and delivery boy commision entry
				$cond1['ordermaster.code'] = $orderCode;
				$res = $this->GlobalModel->selectQuery("ordermaster.*", "ordermaster", $cond1);
				if ($res) {
					sleep(1);
					$clientCode = $res->result_array()[0]['clientCode'];
					$grandTotal = $res->result_array()[0]['totalPrice'];
					$lat1 = $res->result_array()[0]['latitude'];
					$lon1 = $res->result_array()[0]['longitude'];

					$delvLat = $res->result_array()[0]['delvLat'];
					$delvLng = $res->result_array()[0]['delvLng'];

					$cityCode = $res->result_array()[0]['cityCode'];

					log_message('error', "ORDER_DEBUG: orderCode=$orderCode, cityCode=$cityCode, grandTotal=$grandTotal");
					$allCharges = $this->db->get_where('deliverycomissionandcharges', ['cityCode' => $cityCode])->result_array();
					foreach ($allCharges as $ch) {
						log_message('error', "CHARGE_DEBUG: city=$cityCode, code=" . $ch['code'] . ", service=" . $ch['forWhichService'] . ", active=" . $ch['isActive']);
					}

					$orderUpdate = $this->GlobalModel->doEdit($ordData, 'ordermaster', $orderCode);
					if ($orderUpdate == 'true') {
						//update the status at firebase
						$this->firestore->update_order_status($postData['orderCode'], $postData['orderStatus']);

						$dvCondition['clientCode'] = $clientCode;
						$clientDevices = $this->GlobalModel->selectQuery("clientdevicedetails.firebaseId", "clientdevicedetails", $dvCondition);
						$DeviceIdsArr = array();
						if ($clientDevices) {
							sleep(1);
							foreach ($clientDevices->result() as $key) {
								$DeviceIdsArr[] = $key->firebaseId;
							}
						}
						// Calculate and save delivery boy commission for DEL status (independent of notifications)
						if ($orderStatus == "DEL") {
							$dBoyCommission = 0;
							$lat2 = 16.691307;
							$lon2 = 74.244865;

							$city = $this->db->select("latitude,longitude")->from("citymaster")->where("code", $cityCode)->get()->row_array();
							if (!empty($city)) {
								$lat2 = $city['latitude'];
								$lon2 = $city['longitude'];
								if ($delvLat == null or $delvLng == null) {
									$delvLat = $lat2;
									$delvLng = $lon2;
								}
							}
							$delv_city_pick_kms = $this->distanceInKms($delvLat, $delvLng, $lat1, $lon1);
							$city_pick_cust_kms = $this->distanceInKms($lat1, $lon1, $lat2, $lon2);
							$distanceinKms = $delv_city_pick_kms + $city_pick_cust_kms;
							$dBoyCommission = $this->calculate_deliveryboy_commission("deliveryboyr_commission_vegee", $distanceinKms, $cityCode);
							/*$extraWhere = " code in ('SET_9','SET_10','SET_11')";
							 $settingResult = $this->GlobalModel->selectQuery('settings.*', 'settings', [], ["id" => "ASC"], [], [], [], "", "", [], $extraWhere);
							 if ($settingResult) {
							 $baseKms = $baseCommission = $perKmCharge = 0;
							 foreach ($settingResult->result() as $set) {
							 if ($set->code == "SET_9") $baseKms = $set->settingValue;
							 if ($set->code == "SET_10") $baseCommission = $set->settingValue;
							 if ($set->code == "SET_11") $perKmCharge = $set->settingValue;
							 }
							 if ($distanceinKms > $baseKms) {
							 $extraKms = $distanceinKms - $baseKms;
							 $extracharges = number_format($extraKms * $perKmCharge, 2, '.', '');
							 $commission = number_format($baseCommission + $extracharges, 2, '.', '');
							 } else {
							 $commission = number_format($baseCommission, 2, '.', '');
							 }*/


							log_message("error", "Commission Earned by Delivery Executive $code Comission $dBoyCommission for Vege-Groc Order " . $orderCode);
							$dbcAdd['commissionAmount'] = $dBoyCommission;
							$dbcAdd['orderAmount'] = $grandTotal;
							$dbcAdd['commissionType'] = 'regular';
							$dbcAdd['orderCode'] = $orderCode;
							$dbcAdd['deliveryBoyCode'] = $code;
							$dbcAdd['orderType'] = 'vegetable';
							$dbcAdd['isActive'] = 1;
							$dbcAdd['isPaid'] = 0;
							$delboyCommission = $this->GlobalModel->addNew($dbcAdd, 'deliveryboyearncommission', 'DBEC');

						}

						// Send notifications if client has registered devices
						if (!empty($DeviceIdsArr)) {
							if ($orderStatus == "PLC") {
								$title = "New Order";
								$message = "Your order has been placed for order no. " . $orderCode;
								$this->sendNotification($DeviceIdsArr, $title, $message, $orderCode);
							}
							else if ($orderStatus == "PUP") {
								$title = "Order - " . $orderCode;
								$message = "The order has been picked up by successfully!";
								$this->sendNotification($DeviceIdsArr, $title, $message, $orderCode);
							}
							else if ($orderStatus == "RCH") {
								$title = "Order - " . $orderCode;
								$message = "Order has been reached nearby your location";
								$this->sendNotification($DeviceIdsArr, $title, $message, $orderCode);
							}
							else if ($orderStatus == "DEL") {
								$title = "Order - " . $orderCode;
								$message = "The order has been delivered successfully!";
								$this->sendNotification($DeviceIdsArr, $title, $message, $orderCode);
							}
							else {
							// no more notifications
							}
						}
					}
				}
				else {
					$cond2['vendorordermaster.code'] = $postData['orderCode'];
					$res = $this->GlobalModel->selectQuery("vendorordermaster.*", "vendorordermaster", $cond2);
					if ($res) {
						if ($trackingPort != '' && $trackingPort != NULL) {
							$this->db->query("update activeports set isConnected=0 where port='" . $trackingPort . "'");
						}
						$vendorCode = $res->result_array()[0]['vendorCode'];
						$clientCode = $res->result_array()[0]['clientCode'];
						$deliveryBoyCode = $res->result_array()[0]['deliveryBoyCode'];
						$grandTotal = $res->result_array()[0]['grandTotal'];
						$shippingCharges = $res->result_array()[0]['shippingCharges'];

						$lat1 = $res->result_array()[0]['latitude'];
						$lon1 = $res->result_array()[0]['longitude'];

						$delvLat = $res->result_array()[0]['delvLat'];
						$delvLng = $res->result_array()[0]['delvLng'];

						log_message('error', "FOOD_ORDER_DEBUG: orderCode=" . $postData['orderCode'] . ", vendorCode=$vendorCode, grandTotal=$grandTotal");
						$vendorInfo = $this->db->select('cityCode')->from('vendor')->where('code', $vendorCode)->get()->row_array();
						if ($vendorInfo) {
							$vCityCode = $vendorInfo['cityCode'];
							log_message('error', "V_CITY_DEBUG: city=$vCityCode");
							$allCharges = $this->db->get_where('deliverycomissionandcharges', ['cityCode' => $vCityCode])->result_array();
							foreach ($allCharges as $ch) {
								log_message('error', "V_CHARGE_DEBUG: city=$vCityCode, code=" . $ch['code'] . ", service=" . $ch['forWhichService'] . ", active=" . $ch['isActive']);
							}
						}

						$dataUpCnt['orderCode'] = $postData['orderCode'];
						$dataUpCnt['orderType'] = 'food';
						$dataupCnt['deliveryBoyCode'] = $code;
						$dataupCnt['addID'] = $postData['code'];
						$dataupCnt['addIP'] = $ip;
						$dataupCnt['isActive'] = 1;

						if ($orderStatus == "DEL") {

							$lat2 = 16.691307;
							$lon2 = 74.244865;

							$vendor = $this->db->select("latitude,longitude,cityCode")->from("vendor")->where("code", $vendorCode)->get()->row_array();
							if (!empty($vendor)) {
								$lat2 = $vendor['latitude'];
								$lon2 = $vendor['longitude'];
								$cityCode = $vendor['cityCode'];
								if ($delvLat == null or $delvLng == null) {
									$delvLat = $lat2;
									$delvLng = $lon2;
								}
							}

							$delv_city_pick_kms = $this->distanceInKms($delvLat, $delvLng, $lat1, $lon1);
							$city_pick_cust_kms = $this->distanceInKms($lat1, $lon1, $lat2, $lon2);
							$distanceinKms = $delv_city_pick_kms + $city_pick_cust_kms;
							$dBoyCommission = $this->calculate_deliveryboy_commission("deliveryboyr_commission_food", $distanceinKms, $cityCode);
							/*$extraWhere = " code in ('SET_9','SET_10','SET_11')";
							 $settingResult = $this->GlobalModel->selectQuery('settings.*', 'settings', [], ["id" => "ASC"], [], [], [], "", "", [], $extraWhere);
							 if ($settingResult) {
							 $baseKms = $baseCommission = $perKmCharge = 0;
							 foreach ($settingResult->result() as $set) {
							 if ($set->code == "SET_9") $baseKms = $set->settingValue;
							 if ($set->code == "SET_10") $baseCommission = $set->settingValue;
							 if ($set->code == "SET_11") $perKmCharge = $set->settingValue;
							 }
							 if ($distanceinKms > $baseKms) {
							 $extraKms = $distanceinKms - $baseKms;
							 $extracharges = number_format($extraKms * $perKmCharge, 2, '.', '');
							 $commission = number_format($baseCommission + $extracharges, 2, '.', '');
							 } else {
							 $commission = number_format($baseCommission, 2, '.', '');
							 }*/

							log_message("error", "Commission Earned by Delivery Executive $code Comission $dBoyCommission for Food Order " . $orderCode);

							$dbcAdd['commissionAmount'] = $dBoyCommission;
							$dbcAdd['orderAmount'] = $grandTotal;
							$dbcAdd['commissionType'] = 'regular';
							$dbcAdd['orderCode'] = $orderCode;
							$dbcAdd['deliveryBoyCode'] = $code;
							$dbcAdd['orderType'] = 'food';
							$dbcAdd['isActive'] = 1;
							$dbcAdd['isPaid'] = 0;
							$delboyCommission = $this->GlobalModel->addNew($dbcAdd, 'deliveryboyearncommission', 'DBEC');
							//}


							//vendor cumission
							$vendorComissionPercentage = 0;
							$subtotal = ($grandTotal - $shippingCharges);
							$vendorComissionResult = $this->GlobalModel->selectQuery("vendorconfiguration.defaultVendorCommission", "vendorconfiguration");
							if ($vendorComissionResult) {
								$vendorComissionPercentage = $vendorComissionResult->result_array()[0]["defaultVendorCommission"];
								$vendorAmount = 0;
								$vcomission = round($subtotal * ($vendorComissionPercentage / 100));
								$vendorAmount = ($subtotal - $vcomission);
								$vcData['comissionAmount'] = $vcomission;
								$vcData['deliveryBoyCode'] = $vendorCode;
								$vcData['orderCode'] = $orderCode;
								$vcData['comissionPercentage'] = $vendorComissionPercentage;
								$vcData['subTotal'] = $subtotal;
								$vcData['vendorAmount'] = $vendorAmount;
								$vcData['grandTotal'] = $grandTotal;
								$vcData['isActive'] = 1;
								$vcData['isPaid'] = 0;
								$vcData['commissionType'] = 'regular';
								$delboyCommission = $this->GlobalModel->addNew($vcData, 'vendorordercommission', 'VNDC');
							}

						/*$checkSlabs = $this->db->query("select ifnull(commissionRate,0) as commissionRate from vendorcommissionslabs where " . $subtotal . " between amountFrom and amountTo order by id desc limit 1");
						 if ($checkSlabs) {
						 if ($checkSlabs->num_rows() > 0) {
						 $vendorComissionPercentage = $checkSlabs->result_array()[0]['commissionRate'];
						 }
						 }
						 if ($vendorComissionPercentage == 0 || $vendorComissionPercentage == '' || $vendorComissionPercentage == NULL) {
						 $vendorComissionResult = $this->GlobalModel->selectQuery("vendorconfiguration.defaultVendorCommission", "vendorconfiguration");
						 $vendorComission = 0;
						 if ($vendorComissionResult) {
						 $vendorComissionPercentage = $vendorComissionResult->result_array()[0]["defaultVendorCommission"];
						 }
						 }
						 $vendorAmount = 0;
						 $vcomission = round($subtotal * ($vendorComissionPercentage / 100));
						 $vendorAmount = ($subtotal - $vcomission);
						 $vcData['comissionAmount'] = $vcomission;
						 $vcData['deliveryBoyCode'] = $vendorCode;
						 $vcData['orderCode'] = $orderCode;
						 $vcData['comissionPercentage'] = $vendorComissionPercentage;
						 $vcData['subTotal'] = $subtotal;
						 $vcData['vendorAmount'] = $vendorAmount;
						 $vcData['grandTotal'] = $grandTotal;
						 $vcData['isActive'] = 1;
						 $vcData['isPaid']=0;
						 $vcData['commissionType'] = 'regular';
						 $delboyCommission = $this->GlobalModel->addNew($vcData, 'vendorordercommission', 'VNDC');*/
						}
						$orderUpdate = $this->GlobalModel->doEdit($ordData, 'vendorordermaster', $postData['orderCode']);
						if ($orderUpdate == 'true') {
							sleep(1);
							if ($vendorCode != "") {
								$cond3['vendor.code'] = $vendorCode;
								$restaurant = $this->GlobalModel->selectQuery("vendor.firebaseId", "vendor", $cond3);
								if ($restaurant) {
									$vendorfirebaseId = $restaurant->result_array()[0]['firebaseId'];
									if ($orderStatus == "PLC") {
										$title = "New Order";
										$message = "New order has been placed for order no. " . $orderCode;
										//vendor notifications 
										if ($vendorfirebaseId) {
											$DeviceIdsArr[] = $vendorfirebaseId;
											$this->sendNotification($DeviceIdsArr, $title, $message, $orderCode);
										}
									}
									else if ($orderStatus == "PUP") {
										$title = "Order - " . $orderCode;
										$message = "Your order has been picked up!";
										//notifications to client										
										$dvCondition['clientCode'] = $clientCode;
										$clientDevices = $this->GlobalModel->selectQuery("clientdevicedetails.firebaseId", "clientdevicedetails", $dvCondition);
										if ($clientDevices) {
											foreach ($clientDevices->result() as $key) {
												$DeviceIdsArr[] = $key->firebaseId;
												$this->sendNotification($DeviceIdsArr, $title, $message, $orderCode);
											}
										}
									}
									else if ($orderStatus == "RCH") {
										$title = "Order - " . $orderCode;
										$message = "Delivery boy has reached near the restaurant...";
										//vendor notifications
										if ($vendorfirebaseId) {
											$DeviceIdsArr[] = $vendorfirebaseId;
											$this->sendNotification($DeviceIdsArr, $title, $message, $orderCode);
										}
										//client notifications										
										$dvCondition['clientCode'] = $clientCode;
										$clientDevices = $this->GlobalModel->selectQuery("clientdevicedetails.firebaseId", "clientdevicedetails", $dvCondition);
										if ($clientDevices) {
											foreach ($clientDevices->result() as $key) {
												$DeviceIdsArr[] = $key->firebaseId;
												$this->sendNotification($DeviceIdsArr, $title, $message, $orderCode);
											}
										}
									}
									else if ($orderStatus == "DEL") {
										$title = "Order - " . $orderCode;
										$message = "The order has been delivered successfully!";
										//vendor notifications
										if ($vendorfirebaseId) {
											$DeviceIdsArr[] = $vendorfirebaseId;
											$this->sendNotification($DeviceIdsArr, $title, $message, $orderCode);
										}
										//client notifications										
										$dvCondition['clientCode'] = $clientCode;
										$clientDevices = $this->GlobalModel->selectQuery("clientdevicedetails.firebaseId", "clientdevicedetails", $dvCondition);
										if ($clientDevices) {
											foreach ($clientDevices->result() as $key) {
												$DeviceIdsArr[] = $key->firebaseId;
												$this->sendNotification($DeviceIdsArr, $title, $message, $orderCode);
											}
										}
									}
									else {
									// no more notifications
									}
								}
							}
						}
					}
					else {
						$orderUpdate = 'false';
					}
				}
				if ($orderUpdate == 'true') {
					return $this->response(array("status" => "200", "message" => "Order Status updated successfully..."), 200);
				}
				else {
					return $this->response(array("status" => "300", "message" => "Failed to update order status"), 200);
				}
			}
			else {
				return $this->response(array("status" => "300", "message" => "Server seems busy! Please try later"), 200);
			}
		}
		else {
			$this->response(array("status" => "400", "message" => " * are Required field(s)."), 400);
		}
	}

	public function check_get($grandTotal)
	{
		$settingResult = $this->GlobalModel->selectQuery('settings.*', 'settings', array('settings.isActive' => 1));
		if ($settingResult) {
			$amountUpTo = $settingResult->result_array()[1]['settingValue'];
			$cumissionPercentage = $settingResult->result_array()[2]['settingValue'];
			$baseCumission = $settingResult->result_array()[3]['settingValue'];
			if ($grandTotal > $amountUpTo) {
				$percnVal = round($grandTotal * ($cumissionPercentage / 100));
				$delCom = $percnVal;
			}
			else {
				$delCom = $baseCumission;
			}
			$dataUpCnt['amountUpTo'] = $amountUpTo;
			$dataUpCnt['cumissionPercentage'] = $cumissionPercentage;
			$dataUpCnt['baseCumission'] = $baseCumission;
			$dataUpCnt['commissionAmount'] = $delCom;

			return $this->response(array("status" => "200", "message" => $dataUpCnt), 200);
		}
	}

	//Start Get Order List for User
	public function getOrderListByUser_post()
	{
		$postData = $this->post();
		if (isset($postData['code']) && $postData['code'] != '') {
			$orderColumns = array("ordermaster.code,ordermaster.clientCode,ordermaster.orderStatus,ordermaster.orderedTime,ordermaster.placedTime,ordermaster.shippedTime,ordermaster.address,ordermaster.phone,ordermaster.totalPrice,ordermaster.areaCode,ordermaster.editId,clientmaster.code,clientmaster.name");
			$cond = array();
			if (isset($postData['orderStatus']) && $postData['orderStatus'] != '') {
				$cond = array('ordermaster.orderStatus' => $postData['orderStatus'], 'ordermaster.deliveryBoyCode' => $postData['code'], 'ordermaster.isActive' => 1);
			}
			$orderBy = array('ordermaster' . ".id" => 'DESC');
			$join = array('clientmaster' => 'clientmaster.code = ordermaster.clientCode');
			$joinType = array('clientmaster' => 'left');
			$extraCondition = "ordermaster.orderStatus NOT IN('RJT','DEL','CAN')";
			$p_result = $this->GlobalModel->selectQuery($orderColumns, 'ordermaster', $cond, $orderBy, $join, $joinType, array(), "", "", array(), $extraCondition);
			//echo $this->db->last_query();
			if ($p_result) {
				$result['listRecords'] = $p_result->result();
				$this->response(array("status" => "200", "message" => " Order details", "result" => $result), 200);
			}
			else {
				$this->response(array("status" => "300", "message" => " No more Records"), 200);
			}
		}
		else {
			$this->response(array("status" => "400", "message" => " * are Required field(s)."), 400);
		}
	}

	//Confirm Placed Orders
	public function confirmOrderPlace_post()
	{
		$postData = $this->post();

		if (isset($postData["orderCode"]) && $postData["orderCode"] != '' && isset($postData['userCode']) && $postData['userCode'] != '' && isset($postData['orderStatus']) && $postData['orderStatus'] != '') {
			$orderCode = $postData["orderCode"];
			$timeStamp = date("Y-m-d h:i:s");
			if ($postData['orderStatus'] == 'PLC') {
				$orderData = $this->GlobalModel->selectDataByField('code', $orderCode, 'ordermaster');
				if ($orderData->result()[0]->orderStatus == 'PLC') {
					return $this->response(array("status" => "500", "message" => "Order has been placed already."), 200);
				}
				//bagCount				
				$toDate = date('Y-m-d');
				$cityCode = $orderData->result()[0]->cityCode;
				$checkExist = $this->GlobalModel->selectQuery('orderbagcount.*', 'orderbagcount', array('orderbagcount.cityCode' => $cityCode));
				if ($checkExist == false) {
					$datainsert = array(
						'cityCode' => $cityCode,
						'count' => 2,
						'addID' => $postData['userCode'],
						'addIP' => $_SERVER['REMOTE_ADDR'],
						'isActive' => 1,
						'toDate' => date('Y-m-d')
					);
					$insertResult = $this->GlobalModel->onlyinsert($datainsert, 'orderbagcount');
					$existsDate = date('Y-m-d');
					$existsCount = 1;
				}
				else {
					$existsDate = $checkExist->result()[0]->toDate;
					$existsCount = $checkExist->result()[0]->count;
				}
				if (strtotime($existsDate) == strtotime($toDate)) {
					if ($existsCount <= 100) {
						$data = array(
							'orderStatus' => 'PLC',
							'placedTime' => $timeStamp,
							'bagNumber' => $existsCount,
							'editID' => $postData['userCode'],
							'editDate' => $timeStamp
						);

						$existsCount++;

						$result = $this->GlobalModel->doEdit($data, 'ordermaster', $orderCode);
						if ($result != 'false') {
							$dataCount = array(
								'count' => $existsCount,
								'toDate' => $toDate
							);
							$res = $this->GlobalModel->doEditWithField($dataCount, 'orderbagcount', 'cityCode', $cityCode);
							//notification
							$orderData = $this->GlobalModel->selectDataByField('code', $orderCode, 'ordermaster');
							$clientCode = $orderData->result()[0]->clientCode;

							$random = rand(0, 999);
							$datamsg = array("title" => 'Order Placed', "message" => 'Your Order is placed,Your order id is ' . $orderCode, "order_id" => $orderCode, "random_id" => $random);

							$checkdevices = $this->GlobalModel->selectDataByField('code', $clientCode, 'clientmaster');
							$DeviceIdsArr[] = $checkdevices->result()[0]->firebaseId;

							$dataArr = array();
							$dataArr['device_id'] = $DeviceIdsArr;
							$dataArr['message'] = $datamsg['message']; //Message which you want to send
							$dataArr['title'] = $datamsg['title'];
							$dataArr['order_id'] = $datamsg['order_id'];
							$dataArr['random_id'] = $datamsg['random_id'];
							$dataArr['type'] = 'order';

							$notification['device_id'] = $DeviceIdsArr;
							$notification['message'] = $datamsg['message']; //Message which you want to send
							$notification['title'] = $datamsg['title'];
							$notification['order_id'] = $datamsg['order_id'];
							$notification['random_id'] = $datamsg['random_id'];
							$notification['type'] = 'order';
							$notify = $this->notificationlibv_3->pushNotification($dataArr, $notification);
							return $this->response(array("status" => "200", "message" => "Order Successfully Placed."), 200);
						}
						else {
							return $this->response(array("status" => "300", "message" => "Failed To delivered Order."), 200);
						}
					}
					else {
						$dataCount = array('count' => 1, 'toDate' => $toDate);
						$res = $this->GlobalModel->doEditWithField($dataCount, 'orderbagcount', 'cityCode', $cityCode);
						$data = array(
							'orderStatus' => 'PLC',
							'placedTime' => $timeStamp,
							'bagNumber' => 1,
							'editID' => $postData['userCode'],
							'editDate' => $timeStamp
						);
						$result = $this->GlobalModel->doEdit($data, 'ordermaster', $orderCode);
						if ($result != 'false') {
							//notification
							$orderData = $this->GlobalModel->selectDataByField('code', $orderCode, 'ordermaster');
							$clientCode = $orderData->result()[0]->clientCode;

							$random = rand(0, 999);
							$datamsg = array("title" => 'Order Placed', "message" => 'Your Order is placed,Your order id is ' . $orderCode, "order_id" => $orderCode, "random_id" => $random);

							$checkdevices = $this->GlobalModel->selectDataByField('code', $clientCode, 'clientmaster');
							$DeviceIdsArr[] = $checkdevices->result()[0]->firebaseId;

							$dataArr = array();
							$dataArr['device_id'] = $DeviceIdsArr;
							$dataArr['message'] = $datamsg['message']; //Message which you want to send
							$dataArr['title'] = $datamsg['title'];
							$dataArr['order_id'] = $datamsg['order_id'];
							$dataArr['random_id'] = $datamsg['random_id'];
							$dataArr['type'] = 'order';

							$notification['device_id'] = $DeviceIdsArr;
							$notification['message'] = $datamsg['message']; //Message which you want to send
							$notification['title'] = $datamsg['title'];
							$notification['order_id'] = $datamsg['order_id'];
							$notification['random_id'] = $datamsg['random_id'];
							$notification['type'] = 'order';

							$notify = $this->notificationlibv_3->pushNotification($dataArr, $notification);
							return $this->response(array("status" => "200", "message" => "Order Successfully Placed."), 200);
						}
						else {
							return $this->response(array("status" => "300", "message" => "Failed To delivered Order."), 200);
						}
					}
				}
				else {

					$dataCount = array('count' => 2, 'toDate' => $toDate);
					$res = $this->GlobalModel->doEditWithField($dataCount, 'orderbagcount', 'cityCode', $cityCode);
					$data = array(
						'orderStatus' => 'PLC',
						'placedTime' => $timeStamp,
						'bagNumber' => 1,
						'editID' => $postData['userCode'],
						'editDate' => $timeStamp
					);
					$result = $this->GlobalModel->doEdit($data, 'ordermaster', $orderCode);
					if ($result != 'false') {
						//notification
						$orderData = $this->GlobalModel->selectDataByField('code', $orderCode, 'ordermaster');
						$clientCode = $orderData->result()[0]->clientCode;

						$random = rand(0, 999);
						$datamsg = array("title" => 'Order Placed', "message" => 'Your Order is Placed,Your order id is ' . $orderCode, "order_id" => $orderCode, "random_id" => $random);

						$checkdevices = $this->GlobalModel->selectDataByField('code', $clientCode, 'clientmaster');
						$DeviceIdsArr[] = $checkdevices->result()[0]->firebaseId;

						$dataArr = array();
						$dataArr['device_id'] = $DeviceIdsArr;
						$dataArr['message'] = $datamsg['message']; //Message which you want to send
						$dataArr['title'] = $datamsg['title'];
						$dataArr['order_id'] = $datamsg['order_id'];
						$dataArr['random_id'] = $datamsg['random_id'];
						$dataArr['type'] = 'order';

						$notification['device_id'] = $DeviceIdsArr;
						$notification['message'] = $datamsg['message']; //Message which you want to send
						$notification['title'] = $datamsg['title'];
						$notification['order_id'] = $datamsg['order_id'];
						$notification['random_id'] = $datamsg['random_id'];
						$notification['type'] = 'order';

						$notify = $this->notificationlibv_3->pushNotification($dataArr, $notification);
						return $this->response(array("status" => "200", "message" => "Order Successfully Placed."), 200);
					}
					else {
						return $this->response(array("status" => "300", "message" => "Failed To delivered Order."), 200);
					}
				}
			}
			else if ($postData['orderStatus'] == 'DEL') {
				$data = array(
					'orderStatus' => $postData['orderStatus'],
					'paymentStatus' => 'PID',
					'deliveredTime' => $timeStamp,
					'editID' => $postData['userCode'],
					'editDate' => $timeStamp
				);

				$result = $this->GlobalModel->doEdit($data, 'ordermaster', $orderCode);

				if ($result != 'false') {
					$getLineData = $this->GlobalModel->selectDataByField('orderCode', $orderCode, 'orderlineentries');
					foreach ($getLineData->result() as $line) {
						$productCode = $line->productCode;
						$stock = ($line->quantity * $line->weight);
					//$consumeStock = $this->GlobalModel->stockChange($productCode,$stock,'consume');
					}


					/*---Notification Code ---11/11/2019*/
					$clnt = $this->GlobalModel->selectDataByField('code', $orderCode, 'ordermaster');

					/*---- Deivery Boy Commision -- */
					$delboyCode = $postData['userCode'];
					$userRole = '';
					$points = 0;
					$pamount = 0;
					$ip = $_SERVER['REMOTE_ADDR'];
					$datapoints = $this->GlobalModel->selectDataByField('code', $delboyCode, 'usermaster');
					if ($datapoints->num_rows() > 0) {
						foreach ($datapoints->result_array() as $rp) {
							$userRole = $rp['role'];
							$points = $rp['points'];
						}
						if ($userRole != 'USR') {
							$insertData = array('addIP' => $ip, 'orderCode' => $orderCode, 'userCode' => $delboyCode, 'addID' => $delboyCode, 'addDate' => date('Y-m-d h:i:s'), 'points' => $points, 'isActive' => 1);
							$presult = $this->GlobalModel->addNew($insertData, 'commissiontemp', 'CMN');
						}
					}

					$clientCode = $clnt->result()[0]->clientCode;

					$random = rand(0, 999);
					$datamsg = array("title" => 'Order Deliverd', "message" => 'Order successfully delivered.', "order_id" => $orderCode, "random_id" => $random);

					$checkdevices = $this->GlobalModel->selectDataByField('code', $clientCode, 'clientmaster');
					$DeviceIdsArr[] = $checkdevices->result()[0]->firebaseId;

					$dataArr = array();
					$dataArr['device_id'] = $DeviceIdsArr;
					$dataArr['message'] = $datamsg['message']; //Message which you want to send
					$dataArr['title'] = $datamsg['title'];
					$dataArr['order_id'] = $datamsg['order_id'];
					$dataArr['random_id'] = $datamsg['random_id'];
					$dataArr['type'] = 'order';

					$notification['device_id'] = $DeviceIdsArr;
					$notification['message'] = $datamsg['message']; //Message which you want to send
					$notification['title'] = $datamsg['title'];
					$notification['order_id'] = $datamsg['order_id'];
					$notification['random_id'] = $datamsg['random_id'];
					$notification['type'] = 'order';

					$notify = $this->notificationlibv_3->pushNotification($dataArr, $notification);

					return $this->response(array("status" => "200", "message" => "Order Successfully Delivered."), 200);
				}
				else {
					return $this->response(array("status" => "300", "message" => "Failed To delivered Order."), 200);
				}
			}
			else {
				$data = array(
					'orderStatus' => $postData['orderStatus'],
					'placedTime' => $timeStamp,
					'paymentStatus' => 'RJCT',
					'editID' => $postData['userCode'],
					'editDate' => $timeStamp
				);

				$result = $this->GlobalModel->doEdit($data, 'ordermaster', $orderCode);


				if ($result != 'false') {
					//get Data from OrderCode and send push notification
					$orderData = $this->GlobalModel->selectDataByField('code', $orderCode, 'ordermaster');
					$clientCode = $orderData->result()[0]->clientCode;
					$totalPrice = $orderData->result()[0]->totalPrice;

					//delivery boy penulty
					/*$settingData = $this->GlobalModel->selectQuery("settings.settingValue","settings",array("settings.code"=>"SET_9"));
					 if($settingData){
					 $dbPenulty = $settingData->result_array()[0]['settingValue'];
					 if($dbPenulty!=0 && $dbPenulty!='' && $dbPenulty!=NULL){
					 $commAmount = round(($totalPrice * $dbPenulty) / 100,2);
					 $dbcAdd['commissionAmount'] = $commAmount;
					 $dbcAdd['orderAmount'] = $totalPrice;
					 $dbcAdd['orderCode'] = $orderCode;
					 $dbcAdd['deliveryBoyCode'] = $postData['userCode'];
					 $dbcAdd['orderType'] = 'vegetable';
					 $dbcAdd['commissionType'] = 'penalty';
					 $dbcAdd['isActive'] = 1;
					 $delboyCommission = $this->GlobalModel->addNew($dbcAdd, 'deliveryboyearncommission', 'DBEC');
					 }
					 }*/
					//notification
					$random = rand(0, 999);
					$datamsg = array("title" => 'Order Rejected', "message" => 'Your Order is rejected,Your order id is ' . $orderCode, "order_id" => $orderCode, "random_id" => $random);

					$checkdevices = $this->GlobalModel->selectDataByField('code', $clientCode, 'clientmaster');
					$DeviceIdsArr[] = $checkdevices->result()[0]->firebaseId;

					$dataArr = array();
					$dataArr['device_id'] = $DeviceIdsArr;
					$dataArr['message'] = $datamsg['message']; //Message which you want to send
					$dataArr['title'] = $datamsg['title'];
					$dataArr['order_id'] = $datamsg['order_id'];
					$dataArr['random_id'] = $datamsg['random_id'];
					$dataArr['type'] = 'order';

					$notification['device_id'] = $DeviceIdsArr;
					$notification['message'] = $datamsg['message']; //Message which you want to send
					$notification['title'] = $datamsg['title'];
					$notification['order_id'] = $datamsg['order_id'];
					$notification['random_id'] = $datamsg['random_id'];
					$notification['type'] = 'order';

					$notify = $this->notificationlibv_3->pushNotification($dataArr, $notification);

					return $this->response(array("status" => "200", "message" => "Order Successfully Rejected."), 200);
				}
				else {
					return $this->response(array("status" => "300", "message" => "Failed To Reject Order."), 200);
				}
			}
		}
		else {
			return $this->response(array("status" => "400", "message" => "* are Required field(s)."), 400);
		}
	}

	//get client Delivered order list
	public function getDeliveredOrders_post()
	{
		$postData = $this->post();
		if (isset($postData['code']) && $postData['code'] != '' && isset($postData['offset']) && $postData['offset'] != "") {
			$response = array();
			$totalamount = 0;

			$orderColumns = array("ordermaster.code as orderCode,ordermaster.shippingCharges as deliveryCharges,ordermaster.paymentmode,ordermaster.address,ordermaster.phone,ordermaster.totalPrice as orderTotalPrice,ordermaster.addDate as orderDate, ordermaster.latitude,ordermaster.longitude, orderstatusmaster.statusName as orderStatus, paymentstatusmaster.statusName as paymentStatus,clientmaster.code as clientCode,clientmaster.name,ordermaster.editDate as deliveredDate");
			$join = array('clientmaster' => 'clientmaster.code = ordermaster.clientCode', 'orderstatusmaster' => 'ordermaster' . '.orderStatus=' . 'orderstatusmaster' . '.statusSName', 'paymentstatusmaster' => 'ordermaster' . '.paymentStatus=' . 'paymentstatusmaster' . '.statusSName');
			$joinType = array('clientmaster' => 'left', 'orderstatusmaster' => 'inner', 'paymentstatusmaster' => 'inner');
			$cond = array("ordermaster.orderStatus" => 'DEL', "ordermaster.deliveryBoyCode" => $postData['code'], "date(ordermaster.editDate) >=" => date('Y-m-d'));
			$orderBy = array('ordermaster' . ".id" => 'DESC');
			$like = array();
			$limit = "10";
			$offset = $postData['offset'];
			$groupByColumn = array();
			$extraCondition = ""; //ordermaster.orderStatus IN ('DEL') AND ordermaster.areaCode IN(".$areaCodes.")";

			$resultQuery = $this->GlobalModel->selectQuery($orderColumns, 'ordermaster', $cond, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
			//echo $this->db->last_query();
			if ($resultQuery) {
				$clientOrderList = $resultQuery->result_array();
				$totalOrders = sizeof($clientOrderList);
				for ($i = 0; $i < sizeof($clientOrderList); $i++) {
					$totalamount += $clientOrderList[$i]['orderTotalPrice'];
					$linetableName = "orderlineentries";
					$lineorderColumns = array("orderlineentries.productCode,orderlineentries.weight,orderlineentries.productUom,orderlineentries.productPrice,orderlineentries.quantity,orderlineentries.totalPrice as productTotalPrice,productmaster.productName");
					$linecond = array('orderlineentries' . ".orderCode" => $clientOrderList[$i]['orderCode']);
					$lineorderBy = array('orderlineentries' . ".id" => 'ASC');
					$linejoin = array('productmaster' => 'orderlineentries' . '.productCode=' . 'productmaster' . '.code');
					$linejoinType = array('productmaster' => 'inner');

					$orderProductRes = $this->GlobalModel->selectQuery($lineorderColumns, $linetableName, $linecond, $lineorderBy, $linejoin, $linejoinType);
					if ($orderProductRes) {
						$orderProductList = $orderProductRes->result_array();
						$productPrice = 0;
						for ($j = 0; $j < sizeof($orderProductList); $j++) {

							$condition2 = array('productCode' => $orderProductList[$j]["productCode"]);
							$images_result = $this->ApiModel->selectData('productphotos', '', '', $condition2)->result_array();
							$productCode = $orderProductList[$j]["productCode"];
							$imageArray = array();

							for ($img = 0; $img < sizeof($images_result); $img++) {
								array_push($imageArray, base_url() . 'uploads/product/' . $productCode . '/' . $images_result[$img]['productPhoto']);
							}

							$orderProductList[$j]['images'] = $imageArray;
							unset($imageArray);

							$productPrice += $orderProductList[$j]["productTotalPrice"];
						}
						$clientOrderList[$i]['orderPrice'] = $productPrice;
						$oFormat = DateTime::createFromFormat('Y-m-d H:i:s', $clientOrderList[$i]['orderDate']);
						$oDt = $oFormat->format('d-m-Y H:i:s');
						$clientOrderList[$i]['orderDate'] = $oDt;

						$clientOrderList[$i]['orderedProduct'] = $orderProductList;

						$dFormat = DateTime::createFromFormat('Y-m-d H:i:s', $clientOrderList[$i]['deliveredDate']);
						$dDt = $dFormat->format('d-m-Y H:i:s');
						$clientOrderList[$i]['deliveredDate'] = $dDt;
					}
				}
				$finalResult['orders'] = $clientOrderList;
				$this->response(array("status" => "200", 'totalPrice' => $totalamount, "message" => " Order details", "result" => $finalResult, "totalRecords" => $totalOrders), 200);
			}
			else {
				$this->response(array("status" => "300", "message" => " No more Records"), 200);
			}
		}
		else {
			$this->response(array("status" => "400", "message" => " * are Required field(s)."), 400);
		}
	}

	//get client Rejected order list
	public function getRejectedOrders_post()
	{
		$postData = $this->post();
		if (isset($postData['code']) && $postData['code'] != '' && isset($postData['offset']) && $postData['offset'] != "") {
			$areaLineData = $this->GlobalModel->selectDataByField('userCode', $postData["code"], 'useraddresslineentries');
			$response = array();
			$areaCodes = "";
			$size = sizeof($areaLineData->result());
			$count = 1;
			foreach ($areaLineData->result() as $row) {
				$comma = "";
				if ($size != $count) {
					$comma = ",";
				}
				$areaCodes .= "'" . $row->addressCode . "'" . $comma;
				$count++;
			}
			$orderColumns = array("ordermaster.code as orderCode,ordermaster.shippingCharges as deliveryCharges,ordermaster.paymentmode,ordermaster.address,ordermaster.phone,ordermaster.totalPrice as orderTotalPrice,ordermaster.addDate as orderDate, ordermaster.latitude,ordermaster.longitude, orderstatusmaster.statusName as orderStatus,clientmaster.code as clientCode,clientmaster.name");
			$join = array('clientmaster' => 'clientmaster.code = ordermaster.clientCode', 'orderstatusmaster' => 'ordermaster' . '.orderStatus=' . 'orderstatusmaster' . '.statusSName');
			$joinType = array('clientmaster' => 'left', 'orderstatusmaster' => 'inner');
			$cond = array("ordermaster.orderStatus" => "RJT", "ordermaster.editID" => $postData["code"]);
			$orderBy = array('ordermaster' . ".id" => 'DESC');
			$like = array();
			$limit = "10";
			$offset = $postData['offset'];
			$groupByColumn = array();
			$today = date("Y-m-d");
			$previousDate = date("Y-m-d", strtotime($today . "-2 days"));
			$extraCondition = "ordermaster.areaCode IN(" . $areaCodes . ") and ordermaster.addDate between '" . $previousDate . " 00:00:01' and '" . $today . " 23:59:59'";

			$resultQuery = $this->GlobalModel->selectQuery($orderColumns, 'ordermaster', $cond, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
			//$a= $this->db->last_query();
			if ($resultQuery) {
				$clientOrderList = $resultQuery->result_array();
				$totalOrders = sizeof($clientOrderList);
				for ($i = 0; $i < sizeof($clientOrderList); $i++) {
					$linetableName = "orderlineentries";
					$lineorderColumns = array("orderlineentries.productCode,orderlineentries.weight,orderlineentries.productUom,orderlineentries.productPrice,orderlineentries.quantity,orderlineentries.totalPrice as productTotalPrice,productmaster.productName");
					$linecond = array('orderlineentries' . ".orderCode" => $clientOrderList[$i]['orderCode']);
					$lineorderBy = array('orderlineentries' . ".id" => 'ASC');
					$linejoin = array('productmaster' => 'orderlineentries' . '.productCode=' . 'productmaster' . '.code');
					$linejoinType = array('productmaster' => 'inner');

					$orderProductRes = $this->GlobalModel->selectQuery($lineorderColumns, $linetableName, $linecond, $lineorderBy, $linejoin, $linejoinType);
					if ($orderProductRes) {
						$productPrice = 0;
						$orderProductList = $orderProductRes->result_array();
						for ($j = 0; $j < sizeof($orderProductList); $j++) {
							$condition2 = array('productCode' => $orderProductList[$j]["productCode"]);
							$images_result = $this->ApiModel->selectData('productphotos', '', '', $condition2)->result_array();
							$productCode = $orderProductList[$j]["productCode"];
							$imageArray = array();

							for ($img = 0; $img < sizeof($images_result); $img++) {
								array_push($imageArray, base_url() . 'uploads/product/' . $productCode . '/' . $images_result[$img]['productPhoto']);
							}

							$orderProductList[$j]['images'] = $imageArray;
							unset($imageArray);
							$productPrice += $orderProductList[$j]["productTotalPrice"];
						}
						$clientOrderList[$i]['orderPrice'] = $productPrice;
						$dFormat = DateTime::createFromFormat('Y-m-d H:i:s', $clientOrderList[$i]['orderDate']);
						$oDt = $dFormat->format('d-m-Y H:i:s');
						$clientOrderList[$i]['orderDate'] = $oDt;
						$clientOrderList[$i]['orderedProduct'] = $orderProductList;
					}
				}

				$finalResult['orders'] = $clientOrderList;
				$this->response(array("status" => "200", "message" => " Order details", "result" => $finalResult, "totalRecords" => $totalOrders), 200);
			}
			else {
				$this->response(array("status" => "400", "message" => " No more Records", ), 200);
			}
		}
		else {
			$this->response(array("status" => "400", "message" => " * are Required field(s)."), 400);
		}
	}

	// Start update profile
	public function updateFirebaseId_post()
	{
		$postData = $this->post();
		if (isset($postData["code"]) && $postData["code"] != '' && isset($postData["firebaseId"]) && $postData["firebaseId"] != '') {
			$dataMaster = [
				"firebase_id" => $postData["firebaseId"]
			];
			$resultMaster = $this->GlobalModel->doEdit($dataMaster, 'usermaster', $postData["code"]);
			if ($resultMaster != false) {
				return $this->response(array("status" => "200", "message" => "Firebase Id Update Successfully"), 200);
			}
			else {
				return $this->response(array("status" => "300", "message" => " Failed to update Firebase Id."), 200);
			}
		}
		else {
			$this->response(array("status" => "400", "message" => " * are required field(s)."), 400);
		}
	} // End update firebaseId

	//get client Delivered order list
	public function getCommissionRecords_post()
	{
		$postData = $this->post();
		if (isset($postData['code']) && $postData['code'] != '') {
			$commissiondata = $this->GlobalModel->selectDataByField('deliveryBoyCode', $postData["code"], 'deliveryboyearncommission');
			$size = sizeof($commissiondata->result());
			$points = 0;
			if ($size > 0) {
				foreach ($commissiondata->result() as $row) {
					$points += $row->commissionAmount;
				}
				$finalResult = array('totalPoints' => strval($points));
				$this->response(array("status" => "200", "message" => "Touch point Total Amount", "result" => $finalResult), 200);
			}
			else {
				$finalResult = array('totalPoints' => 0);
				$this->response(array("status" => "300", "message" => "Comimission Details", "result" => $finalResult), 200);
			}
		}
		else {
			$this->response(array("status" => "400", "message" => " * are Required field(s)."), 400);
		}
	}

	//Start is user active 
	public function isUserActive_post()
	{
		$postData = $this->post();
		if (isset($postData['code']) && $postData['code'] != '') {
			$tableName = "usermaster";
			$orderColumns = array("usermaster.isActive");
			$cond = array('usermaster' . ".code" => $postData["code"], 'usermaster.isActive' => 1);
			$member = $this->GlobalModel->selectQuery($orderColumns, $tableName, $cond);
			if ($member) {
				$this->response(array("status" => "200", "message" => "User Active"), 200);
			}
			else {
				$this->response(array("status" => "300", "message" => "User InActive"), 200);
			}
		}
		else {
			$this->response(array("status" => "400", "message" => " * are Required field(s)."), 400);
		}
	}

	//Start Get Order List for User
	public function getDeliveryBoyOrderList_post()
	{
		$postData = $this->post();
		if (isset($postData["deliveryBoyCode"]) && $postData["deliveryBoyCode"] != "") {
			$deliveryBoyCode = $postData["deliveryBoyCode"];
			$orderVendor = $this->GlobalModel->selectQuery('vendorordermaster.*', 'vendorordermaster', array('vendorordermaster.deliveryBoyCode' => $deliveryBoyCode, 'vendorordermaster.orderStatus' => 'PND'));
			if ($orderVendor) {
				$orderCode = $orderVendor->result_array()[0]['code'];
				$tableName = "vendorordermaster";
				$orderColumns = array("vendorordermaster.code as orderCode,vendorordermaster.shippingCharges as deliveryCharges,vendorordermaster.paymentmode,vendorordermaster.address,vendorordermaster.grandTotal as orderTotalPrice,vendorordermaster.addDate as orderDate, vendororderstatusmaster.statusSName as orderStatus, paymentstatusmaster.statusName as paymentStatus");
				$cond = array("vendorordermaster.code" => $orderCode, "vendorordermaster.isActive" => 1);
				$orderBy = array('vendorordermaster' . ".id" => 'DESC');
				$join = array('vendororderstatusmaster' => 'vendorordermaster.orderStatus=vendororderstatusmaster.statusSName', 'paymentstatusmaster' => 'vendorordermaster.paymentStatus=paymentstatusmaster.statusSName');
				$joinType = array('vendororderstatusmaster' => 'inner', 'paymentstatusmaster' => 'inner');
				$resultQuery = $this->GlobalModel->selectQuery($orderColumns, $tableName, $cond, $orderBy, $join, $joinType);
				$imageArray = array();
				if ($resultQuery) {
					$clientOrderList = $resultQuery->result_array();
					$totalOrders = sizeof($clientOrderList);
					for ($i = 0; $i < $totalOrders; $i++) {
						$linetableName = "vendororderlineentries";
						$lineorderColumns = array("vendororderlineentries.vendorItemCode,vendororderlineentries.quantity,vendororderlineentries.priceWithQuantity as priceWithQuantity,vendoritemmaster.itemName");
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
					return $this->response(array("status" => "200", "totalOrders" => $totalOrders, "type" => 'vendor', "result" => $finalResult), 200);
				}
				else {
					return $this->response(array("status" => "300", "message" => "Data not found."), 200);
				}
			}
			else {
				$orderVegiz = $this->GlobalModel->selectQuery('ordermaster.*', 'ordermaster', array('ordermaster.editID' => $deliveryBoyCode, 'ordermaster.orderStatus' => 'PND'));
				if ($orderVegiz) {
					$orderCode = $orderVegiz->result_array()[0]['code'];
					$tableName = "ordermaster";
					$orderColumns = array("ordermaster.code as orderCode,ordermaster.shippingCharges as deliveryCharges,ordermaster.paymentmode,ordermaster.address,ordermaster.phone,ordermaster.totalPrice as orderTotalPrice,ordermaster.addDate as orderDate, orderstatusmaster.statusName as orderStatus, paymentstatusmaster.statusName as paymentStatus,ordermaster.cityCode");
					$cond = array("ordermaster.code" => $orderCode, "ordermaster.isActive" => 1);
					$orderBy = array('ordermaster' . ".id" => 'DESC');
					$join = array('orderstatusmaster' => 'ordermaster' . '.orderStatus=' . 'orderstatusmaster' . '.statusSName', 'paymentstatusmaster' => 'ordermaster' . '.paymentStatus=' . 'paymentstatusmaster' . '.statusSName');
					$joinType = array('orderstatusmaster' => 'inner', 'paymentstatusmaster' => 'inner');
					$resultQuery = $this->GlobalModel->selectQuery($orderColumns, $tableName, $cond, $orderBy, $join, $joinType);
					if ($resultQuery) {
						$clientOrderList = $resultQuery->result_array();
						$totalOrders = sizeof($clientOrderList);
						for ($i = 0; $i < sizeof($clientOrderList); $i++) {
							$cityCode = $clientOrderList[$i]['cityCode'];
							$linetableName = "orderlineentries";
							$lineorderColumns = array("orderlineentries.productCode,orderlineentries.weight,orderlineentries.productUom,orderlineentries.productPrice ,orderlineentries.quantity,orderlineentries.totalPrice as productTotalPrice,productmaster.productName");
							$linecond = array("orderlineentries.orderCode" => $clientOrderList[$i]['orderCode']);
							$lineorderBy = array('orderlineentries' . ".id" => 'ASC');
							$linejoin = array('productmaster' => 'orderlineentries.productCode=productmaster.code');
							$linejoinType = array('productmaster' => 'inner');
							$orderProductRes = $this->GlobalModel->selectQuery($lineorderColumns, $linetableName, $linecond, $lineorderBy, $linejoin, $linejoinType);
							if ($orderProductRes) {
								$orderProductList = $orderProductRes->result_array();
								for ($j = 0; $j < sizeof($orderProductList); $j++) {
									$condition2 = array('productCode' => $orderProductList[$j]["productCode"]);
									$images_result = $this->ApiModel->selectData('productphotos', '', '', $condition2)->result_array();
									$productCode = $orderProductList[$j]["productCode"];
									$imageArray = array();
									for ($img = 0; $img < sizeof($images_result); $img++) {
										array_push($imageArray, base_url() . 'uploads/product/' . $productCode . '/' . $images_result[$img]['productPhoto']);
									}
									$orderProductList[$j]['images'] = $imageArray;
									unset($imageArray);
								}
								$clientOrderList[$i]['orderedProduct'] = $orderProductList;
								$dFormat = DateTime::createFromFormat('Y-m-d H:i:s', $clientOrderList[$i]['orderDate']);
								$oDt = $dFormat->format('d-m-Y H:i:s');
								$clientOrderList[$i]['orderDate'] = $oDt;
							}
						}
						$finalResult['orders'] = $clientOrderList;
						return $this->response(array("status" => "200", "totalOrders" => $totalOrders, "type" => 'vegiz', "result" => $finalResult), 200);
					}
					else {
						return $this->response(array("status" => "400", "message" => "Data not found."), 200);
					}
				}
				else {
					return $this->response(array("status" => "300", "message" => "Data not found."), 200);
				}
			}
		}
		else {
			return $this->response(array("status" => "400", "msg" => " * are required field(s)."), 400);
		}
	}

	public function maintenance_get()
	{
		$resultData = $this->GlobalModel->selectQuery('settings.*', 'settings', array('settings.settingName' => 'maintenance_mode'));
		$maintenance_mode['maintenance'] = $resultData->result_array()[0]['settingValue'];
		$maintenance_mode['messageTitle'] = $resultData->result_array()[0]['messageTitle'];
		$maintenance_mode['messageDescription'] = $resultData->result_array()[0]['messageDescription'];
		return $this->response(array("status" => "200", "result" => $maintenance_mode), 200);
	}

	public function getOrderStatusTypeList_post()
	{
		$postData = $this->post();
		if (isset($postData['code']) && $postData['code'] != "") {
			$resultData = $this->GlobalModel->selectQuery('
			.statusSName,vendororderstatusmaster.statusName', 'vendororderstatusmaster', array('vendororderstatusmaster.statusRole' => 'DELIVERYBOY'));
			if ($resultData) {
				$data['statusList'] = $resultData->result_array();
				return $this->response(array("status" => "200", "msg" => "Data Found", "result" => $data), 200);
			}
			else {
				return $this->response(array("status" => "300", "msg" => "No Data Found"), 200);
			}
		}
		else {
			return $this->response(array("status" => "400", "msg" => "* fields are required"), 200);
		}
	}

	public function getTouchPointHistoryList_post()
	{
		$postData = $this->post();
		if (isset($postData['code']) && $postData['code'] != "") {
			$resultData = $this->GlobalModel->selectQuery('deliveryboyearncommission.id,deliveryboyearncommission.code,deliveryboyearncommission.deliveryBoyCode,deliveryboyearncommission.orderCode,deliveryboyearncommission.commissionAmount,deliveryboyearncommission.orderType,deliveryboyearncommission.isActive,deliveryboyearncommission.addDate', 'deliveryboyearncommission', array('deliveryboyearncommission.deliveryBoyCode' => $postData['code'], 'deliveryboyearncommission.isActive' => 1));
			if ($resultData) {
				$data['historyList'] = $resultData->result_array();
				return $this->response(array("status" => "200", "msg" => "Data Found", "result" => $data), 200);
			}
			else {
				return $this->response(array("status" => "300", "msg" => "No Data Found"), 200);
			}
		}
		else {
			return $this->response(array("status" => "400", "msg" => "* fields are required"), 200);
		}
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
		log_message(
			'error',
			'Dboy Notification result -> ' . json_encode($notify, JSON_UNESCAPED_UNICODE)
		);
	}

	public function sendTestNoti_get()
	{
		$this->sendNotification(array("edrpdYLoRke1qb1QO0FsY_:APA91bEK6luDfy1_7xhEx4k8t1mI7T6k-1J5EjkXdVW4dDy4eYF4APvOTomHQHSGspcQWnUmLcdYvLD9Hd6EInUPIJRWI73rAukkhSwalOCGw8B18ACc2v0"), "hello", "how r you?", rand(000, 999));
	}

	public function getDeliveredOrdersList_post()
	{
		$postData = $this->post();
		if (isset($postData['code']) && $postData['code'] != "") {
			$today = date('Y-m-d');
			if (isset($postData['date'])) {
				if ($postData['date'] != "") {
					$today = date('Y-m-d', strtotime(str_replace("/", "-", $postData['date'])));
				}
			}
			$code = $postData['code'];
			$orderColumns = 'deliveryboyearncommission.*,(deliveryboyearncommission.orderAmount-deliveryboyearncommission.commissionAmount) as orderReturnAmount';
			$table = 'deliveryboyearncommission';
			$condition['deliveryboyearncommission.deliveryBoyCode'] = $postData['code'];
			$condition['date(deliveryboyearncommission.addDate)'] = $today;
			$extraCondition = "";
			$orderBy['deliveryboyearncommission.addDate'] = "DESC";
			$join = $joinType = $like = $groupBy = array();
			$limit = $offset = "";
			$resultData = $this->GlobalModel->selectQuery($orderColumns, $table, $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupBy, $extraCondition);
			if ($resultData) {
				$totalAmount = 0;
				$totalReturnAmount = 0;
				$data = $resultData->result_array();
				foreach ($resultData->result_array() as $r) {
					$totalAmount += $r['commissionAmount'];
					$totalReturnAmount += $r['orderReturnAmount'];
				}
				$res['deliveryChargesEarnedList'] = $data;
				return $this->response(array("status" => "200", "deliveryAmountEarned" => $totalAmount, "totalReturnAmount" => number_format($totalReturnAmount, 2), "msg" => "Data found", "result" => $res), 200);
			}
			else {
				return $this->response(array("status" => "300", "msg" => "No Data Found"), 200);
			}
		}
		else {
			return $this->response(array("status" => "400", "msg" => "* fields are required"), 200);
		}
	}

	public function updateLatLong_post()
	{
		$postData = $this->post();
		if (isset($postData['code']) && $postData['code'] != "" && isset($postData['latitude']) && $postData['latitude'] != "" && isset($postData['longitude']) && $postData['longitude'] != "") {
			$dataMaster = [
				"latitude" => $postData["latitude"],
				"longitude" => $postData["longitude"],
			];
			$resultMaster = $this->GlobalModel->doEdit($dataMaster, 'usermaster', $postData["code"]);
			if ($resultMaster != false) {
				return $this->response(array("status" => "200", "message" => "Record updated successfully"), 200);
			}
			else {
				return $this->response(array("status" => "300", "message" => " Failed to update record."), 200);
			}
		}
		else {
			return $this->response(array("status" => "400", "msg" => "* fields are required"), 200);
		}
	}

	public function getPenultyDetails_post()
	{
		$postData = $this->post();
		if (isset($postData['code']) && $postData['code'] != "") {
			$tableName = 'deliveryboyearncommission';
			$orderColumns = array("deliveryboyearncommission.id,deliveryboyearncommission.code,deliveryboyearncommission.deliveryBoyCode,deliveryboyearncommission.orderCode,commissionAmount,orderType");
			$condition = array('deliveryboyearncommission.commissionType' => 'penalty', 'deliveryboyearncommission.deliveryBoyCode' => $postData['code']);
			$orderBy = array('deliveryboyearncommission.id' => 'DESC');
			$totalAmount = 0;
			$Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, array(), array(), array(), "", "", array(), "");
			if ($Records) {
				foreach ($Records->result_array() as $Rec) {
					$arr = $Rec;
					$totalAmount = $totalAmount + $Rec['commissionAmount'];
				}
				return $this->response(array("status" => "200", "totalAmount" => $totalAmount, "result" => $arr), 200);
			}
			else {
				return $this->response(array("status" => "300", "message" => " No data found"), 200);
			}
		}
		else {
			return $this->response(array("status" => "400", "message" => " * fields are required."), 200);
		}
	}

	public function updateLocation_post()
	{
		$postData = $this->post();
		if (isset($postData['userCode']) && $postData['userCode'] != "" && isset($postData['orderCode']) && $postData['orderCode'] != "" && isset($postData['latitude']) && $postData['latitude'] != "" && $postData['longitude'] && $postData['longitude'] != "") {
			$currentTime = date('Y-m-d H:i:s');
			$dbCode = $postData['userCode'];
			$orderCode = $postData['orderCode'];
			$arr['latitude'] = $postData['latitude'];
			$arr['longitude'] = $postData['longitude'];
			$arr['addDate'] = $currentTime;
			$filename = 'assets/order_tracking/' . $orderCode . '.json';
			if (file_exists($filename)) {
				$jsonString = file_get_contents($filename);
				$data = json_decode($jsonString, true);
				array_unshift($data, $arr);
				$newJsonString = json_encode($data);
				file_put_contents($filename, $newJsonString);
			}
			else {
				$content = json_encode(array($arr));
				file_put_contents($filename, $content, FILE_APPEND | LOCK_EX);
			}
			$response['latitude'] = $postData['latitude'];
			$response['longitude'] = $postData['longitude'];
			$response['addDate'] = $currentTime;
			$response['success'] = true;
			return $this->response(array("status" => "200", "message" => "Location updated successfully", "result" => $response), 200);
		}
		else {
			return $this->response(array("status" => "400", "message" => " * fields are required."), 200);
		}
	}

	public function getOrdersByCode_post()
	{
		$postData = $this->post();
		if (isset($postData['deliveryBoyCode']) && $postData['deliveryBoyCode'] != '') {
			$clientOrderLista = array();
			$deliveryBoyCode = $postData["deliveryBoyCode"];
			$extraCondition = "vendorordermaster.orderStatus NOT IN ('DEL','CAN','RJT')";
			$orderVendor = $this->GlobalModel->selectQuery('vendorordermaster.*', 'vendorordermaster', array('vendorordermaster.deliveryBoyCode' => $deliveryBoyCode), array('vendorordermaster' . ".id" => 'DESC'), array(), array(), array(), "1", "", array(), $extraCondition);
			if ($orderVendor && $orderVendor->num_rows() > 0) {
				$orderCode = $orderVendor->result_array()[0]['code'];
				$tableName = "vendorordermaster";
				$orderColumns = array("'food' as orderType,vendorordermaster.code as orderCode,vendorordermaster.shippingCharges as deliveryCharges,vendorordermaster.paymentmode,vendorordermaster.clientCode as clientCode,vendorordermaster.phone,clientmaster.name as clientName,clientmaster.emailid,vendorordermaster.address as clientAddress,vendorordermaster.subTotal,vendorordermaster.tax,vendorordermaster.grandTotal as orderTotalPrice,ifnull(vendorordermaster.totalPackgingCharges,0) as totalPackgingCharges,vendorordermaster.preparingMinutes,vendorordermaster.discount,vendorordermaster.addDate as orderDate, vendororderstatusmaster.statusSName as orderStatus, IFNULL(vendorordermaster.reachStatus,'') as reachStatus, paymentstatusmaster.statusSName as paymentStatus,vendor.code as vendorCode,vendor.entityName as vendorName,vendor.address as vendorAddress,vendor.ownerContact as vendorContact,vendor.latitude as vendorLatitude,vendor.longitude as vendorLogitude,vendor.entityImage as image,bookorderstatuslineentries.statusTime,vendor.cityCode,ifnull(vendorordermaster.preparingMinutes,30) as  preparationTime,IFNULL(DATE_FORMAT(vendorordermaster.orderAcceptedTime, '%H:%i:%s'), '') as orderAcceptedTime");
				$cond = array("vendorordermaster.code" => $orderCode, "vendorordermaster.isActive" => 1);
				$extra = "vendorordermaster.orderStatus NOT IN ('DEL','CAN','RJT')";
				$orderBy = array('vendorordermaster' . ".id" => 'DESC');
				$join = array('clientmaster' => 'clientmaster.code=vendorordermaster.clientCode', 'vendor' => 'vendor.code = vendorordermaster.vendorCode', 'vendororderstatusmaster' => 'vendorordermaster.orderStatus=vendororderstatusmaster.statusSName', 'paymentstatusmaster' => 'vendorordermaster.paymentStatus=paymentstatusmaster.statusSName', 'bookorderstatuslineentries' => 'bookorderstatuslineentries.orderCode=vendorordermaster.code and bookorderstatuslineentries.statusLine=vendorordermaster.orderStatus');
				$joinType = array('clientmaster' => 'inner', 'vendor' => 'inner', 'vendororderstatusmaster' => 'inner', 'paymentstatusmaster' => 'inner', 'bookorderstatuslineentries' => 'inner');
				$resultQuery = $this->GlobalModel->selectQuery($orderColumns, $tableName, $cond, $orderBy, $join, $joinType, array(), "", "", array(), $extra);
				//echo $this->db->last_query();
				if ($resultQuery != false) {
					$clientOrderList = $resultQuery->result_array();
					$totalOrders = sizeof($clientOrderList);
					for ($i = 0; $i < $totalOrders; $i++) {
						$order = $clientOrderList[$i];
						$clientOrderList[$i]['orderType'] = "food";
						$clientOrderList[$i]['tax'] = number_format($clientOrderList[$i]['tax'] ?? 0, 2, '.', '');
						$linetableName = "vendororderlineentries";
						$lineorderColumns = array("vendororderlineentries.vendorItemCode,vendororderlineentries.addons,vendororderlineentries.addonsCode,vendororderlineentries.quantity,vendororderlineentries.priceWithQuantity,vendororderlineentries.itemPackagingCharges,vendoritemmaster.vendorCode,vendoritemmaster.itemName,vendoritemmaster.itemPhoto");
						$linecond = array("vendororderlineentries.orderCode" => $clientOrderList[$i]['orderCode']);
						$lineorderBy = array('vendororderlineentries' . ".id" => 'ASC');
						$linejoin = array('vendoritemmaster' => 'vendororderlineentries.vendorItemCode=vendoritemmaster.code');
						$linejoinType = array('vendoritemmaster' => 'inner');
						$orderProductRes = $this->GlobalModel->selectQuery($lineorderColumns, $linetableName, $linecond, $lineorderBy, $linejoin, $linejoinType);
						$orderProductList = array();
						if ($orderProductRes) {
							$orderProductList = $orderProductRes->result_array();
							for ($j = 0; $j < sizeof($orderProductList); $j++) {
								$itemAr['vendorItemCode'] = $orderProductList[$j]["vendorItemCode"];
								$itemAr['itemName'] = $orderProductList[$j]["itemName"];
								$itemAr['addons'] = $orderProductList[$j]["addons"];
								$itemAr['addonsCode'] = $orderProductList[$j]["addonsCode"];
								$itemAr['quantity'] = $orderProductList[$j]["quantity"];
								$itemAr['priceWithQuantity'] = $orderProductList[$j]["priceWithQuantity"];
								$itemAr['itemPackagingCharges'] = $orderProductList[$j]["itemPackagingCharges"];
								if ($orderProductList[$j]["itemPhoto"] != "") {
									$path = 'partner/uploads/' . $orderProductList[$j]["vendorCode"] . '/vendoritem/' . $orderProductList[$j]["itemPhoto"];
									if (file_exists($path)) {
										$itemAr['itemImage'] = base_url($path);
									}
									else {
										$itemAr['itemImage'] = 'noimage';
									}
								}
								else {
									$itemAr['itemImage'] = 'noimage';
								}
								$resultArr = [];
								if ($orderProductList[$j]['addonsCode'] != '' && $orderProductList[$j]['addonsCode'] != NULL) {
									$addonsCode = rtrim($orderProductList[$j]['addonsCode'], ',');
									$savedaddonsCodes = explode(',', $addonsCode);
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
								$itemAr['addonsDetails'] = $resultArr;
								$itemsArray[] = $itemAr;
							}
							$order["vendorImage"] = base_url() . 'uploads/vendor/' . $clientOrderList[$i]['vendorCode'] . '/' . $clientOrderList[$i]['image'];
							$order['totalItems'] = sizeof($orderProductList);
							$order['orderedItems'] = $itemsArray;
							$dFormat = DateTime::createFromFormat('Y-m-d H:i:s', $clientOrderList[$i]['orderDate']);
							$oDt = $dFormat->format('d-m-Y H:i:s');
							$order["orderDate"] = $oDt;
						}
						$clientOrderLista[] = $order;
					//$clientOrderList[$i]['orderedItems'] = $orderProductList;
					}
					$finalResult['orders'] = $clientOrderLista;
					return $this->response(array("status" => "200", "totalOrders" => $totalOrders, "type" => 'food', "result" => $finalResult), 200);
				}
				else {
					//vege grocery order check
					$this->getVegeGrocAssignedOrders($deliveryBoyCode);
				}
			}
			else {
				//vege grocery order check
				$this->getVegeGrocAssignedOrders($deliveryBoyCode);
			}
		}
		else {
			return $this->response(array("status" => "400", "message" => " * are Required field(s)."), 400);
		}
	}

	function getVegeGrocAssignedOrders($deliveryBoyCode)
	{
		$vLat = "16.704987";
		$vLng = "74.243253";
		$vAddress = $vCode = $vName = $vContact = "";
		$vImage = 'MYVEGIZ LOGO 3rd-01-cut.png';
		$vImageUrl = base_url('assets/images/MYVEGIZ LOGO 3rd-01-cut.png');
		$vendor = $this->db->select("deliverycharge.*")->from("deliverycharge")->where('id', 1)->get()->row_array();
		if (!empty($vendor)) {
			$vCode = $vendor['code'];
			$vName = $vendor['companyName'];
			$vContact = $vendor['contactNo'];
		}

		$extraCondition2 = "ordermaster.orderStatus NOT IN('DEL','CAN','RJT')";
		$orderVegiz = $this->GlobalModel->selectQuery('ordermaster.*', 'ordermaster', array('ordermaster.deliveryBoyCode' => $deliveryBoyCode), array('ordermaster' . ".id" => 'DESC'), array(), array(), array(), "1", "", array(), $extraCondition2);
		if ($orderVegiz && $orderVegiz->num_rows() > 0) {
			$orderCode = $orderVegiz->result_array()[0]['code'];
			$tableName = "ordermaster";
			$orderColumns = array("'vegetable' as orderType,ordermaster.code as orderCode,ordermaster.shippingCharges as deliveryCharges,ordermaster.latitude,ordermaster.longitude,ordermaster.paymentmode,ordermaster.clientCode as clientCode,ordermaster.phone,clientmaster.name as clientName,clientmaster.emailid,ordermaster.address as clientAddress,ordermaster.subTotal,ordermaster.totalPrice as orderTotalPrice,ifnull(ordermaster.packagingCharges,0) as totalPackgingCharges,ordermaster.discount,ordermaster.addDate as orderDate, orderstatusmaster.statusSName as orderStatus, ordermaster.reachStatus ,paymentstatusmaster.statusSName as paymentStatus,ordermaster.cityCode");
			$cond = array("ordermaster.code" => $orderCode, "ordermaster.isActive" => 1);
			$orderBy = array('ordermaster' . ".id" => 'DESC');
			$join = array('clientmaster' => 'clientmaster.code=ordermaster.clientCode', 'orderstatusmaster' => 'ordermaster' . '.orderStatus=' . 'orderstatusmaster' . '.statusSName', 'paymentstatusmaster' => 'ordermaster' . '.paymentStatus=' . 'paymentstatusmaster' . '.statusSName');
			$joinType = array('clientmaster' => 'inner', 'orderstatusmaster' => 'inner', 'paymentstatusmaster' => 'inner');
			$resultQuery = $this->GlobalModel->selectQuery($orderColumns, $tableName, $cond, $orderBy, $join, $joinType);
			if ($resultQuery) {
				$clientOrderList = $resultQuery->result_array();
				$totalOrders = sizeof($clientOrderList);
				for ($i = 0; $i < sizeof($clientOrderList); $i++) {
					$cityCode = $clientOrderList[$i]['cityCode'];

					$city = $this->db->select("latitude,longitude")->from("vegitablestorelocation")->where("vegitablestorelocation.code", "STR_1")->get()->row_array();
					if (!empty($city)) {
						$vLat = $city['latitude'] ?? "16.704987";
						$vLng = $city['longitude'] ?? "74.243253";
					}
					$clientOrderList[$i]['orderType'] = "vegetable";
					$clientOrderList[$i]['preparingMinutes'] = 0;
					$clientOrderList[$i]['tax'] = number_format(0, 2, '.', '');
					$clientOrderList[$i]['vendorLatitude'] = $vLat;
					$clientOrderList[$i]['vendorLogitude'] = $vLng;
					$clientOrderList[$i]['vendorCode'] = $vCode;
					$clientOrderList[$i]['vendorName'] = "MyVegiz";
					$clientOrderList[$i]['vendorAddress'] = "Mukt Sainik Colony, Kolhapur, Maharashtra 416005";
					$clientOrderList[$i]['vendorContact'] = "9373747055";
					$clientOrderList[$i]['image'] = $vImage;
					$clientOrderList[$i]['vendorImage'] = $vImageUrl;
					$clientOrderList[$i]['reachStatus'] = $clientOrderList[$i]['reachStatus'] ?? "";
					$clientOrderList[$i]['emailid'] = $clientOrderList[$i]['emailid'] ?? "";
					$clientOrderList[$i]['statusTime'] = "";
					$linetableName = "orderlineentries";
					$lineorderColumns = array("orderlineentries.productCode,orderlineentries.weight,orderlineentries.productUom,orderlineentries.productPrice ,orderlineentries.quantity,orderlineentries.totalPrice as productTotalPrice,productmaster.productName");
					$linecond = array("orderlineentries.orderCode" => $clientOrderList[$i]['orderCode']);
					$lineorderBy = array('orderlineentries' . ".id" => 'ASC');
					$linejoin = array('productmaster' => 'orderlineentries.productCode=productmaster.code');
					$linejoinType = array('productmaster' => 'inner');
					//$groupByColumn = array('orderlineentries.productCode');
					$orderProductRes = $this->GlobalModel->selectQuery($lineorderColumns, $linetableName, $linecond, $lineorderBy, $linejoin, $linejoinType);
					if ($orderProductRes) {
						$orderProductList = $orderProductRes->result_array();
						for ($j = 0; $j < sizeof($orderProductList); $j++) {
							$condition2 = array('productCode' => $orderProductList[$j]["productCode"]);
							$images_result = $this->ApiModel->selectData('productphotos', '', '', $condition2)->result_array();
							$productCode = $orderProductList[$j]["productCode"];
							$imageArray = array();
							for ($img = 0; $img < sizeof($images_result); $img++) {
								array_push($imageArray, base_url() . 'uploads/product/' . $productCode . '/' . $images_result[$img]['productPhoto']);
							}
							$orderProductList[$j]['images'] = $imageArray;
							unset($imageArray);
						}
						$clientOrderList[$i]['totalItems'] = sizeof($orderProductList);
						$clientOrderList[$i]['orderedProduct'] = $orderProductList;
						$dFormat = DateTime::createFromFormat('Y-m-d H:i:s', $clientOrderList[$i]['orderDate']);
						$oDt = $dFormat->format('d-m-Y H:i:s');
						$clientOrderList[$i]['orderDate'] = $oDt;

					}
				}
				$finalResult['orders'] = $clientOrderList;
				return $this->response(array("status" => "200", "totalOrders" => $totalOrders, "type" => 'vegetable', "result" => $finalResult), 200);
			}
			else {
				return $this->response(array("status" => "400", "message" => "Data not found."), 200);
			}
		}
		else {
			return $this->response(array("status" => "300", "message" => "Data not found."), 200);
		}
	}

	public function calculate_deliveryboy_commission($type, $distanceinKms, $cityCode)
	{
		$deliveryCharge = $dBoyCommission = $perKmCharges = $minimumKmForFixedCharges = $minimumChargesForFixedKm = 0;
		$chargesResult = $this->GlobalModel->selectQuery('deliverycomissionandcharges.*', 'deliverycomissionandcharges', array('deliverycomissionandcharges.cityCode' => $cityCode, 'deliverycomissionandcharges.forWhichService' => $type, 'deliverycomissionandcharges.isActive' => 1));

		// Fallback 1: Try without the extra 'r'
		if (!$chargesResult && strpos($type, 'deliveryboyr_') === 0) {
			$fallbackType = str_replace('deliveryboyr_', 'deliveryboy_', $type);
			$chargesResult = $this->GlobalModel->selectQuery('deliverycomissionandcharges.*', 'deliverycomissionandcharges', array('deliverycomissionandcharges.cityCode' => $cityCode, 'deliverycomissionandcharges.forWhichService' => $fallbackType, 'deliverycomissionandcharges.isActive' => 1));
			if ($chargesResult)
				$type = $fallbackType;
		}

		// Fallback 2: Broader search for ANY active delivery boy commission for this city
		if (!$chargesResult) {
			$chargesResult = $this->db->select('*')
				->from('deliverycomissionandcharges')
				->where('cityCode', $cityCode)
				->where('isActive', 1)
				->like('forWhichService', 'deliveryboy', 'after')
				->get();
			if ($chargesResult->num_rows() == 0) {
				// Try even broader
				$chargesResult = $this->db->select('*')
					->from('deliverycomissionandcharges')
					->where('cityCode', $cityCode)
					->where('isActive', 1)
					->group_start()
					->like('forWhichService', 'delivery', 'both')
					->like('forWhichService', 'commission', 'both')
					->group_end()
					->get();
			}
			if ($chargesResult && $chargesResult->num_rows() == 0)
				$chargesResult = false;
		}

		$fixedDeliveryFlag = 0;
		$debug = "DEBUG: type=$type, dist=$distanceinKms, city=$cityCode\n";
		if ($chargesResult) {
			$charge = $chargesResult->result()[0];
			$debug .= "FOUND: code=" . $charge->code . ", isFixed=" . $charge->isFixedChargesFlag . ", val=" . $charge->fixedChargesOrCommission . "\n";
			if ($charge->isFixedChargesFlag == 1) {
				$deliveryBoyCommission = $charge->fixedChargesOrCommission;
				$fixedDeliveryFlag = 1;
			}
			else {
				$minimumKmForFixedCharges = $charge->minimumKmForFixedCharges;
				$minimumChargesForFixedKm = $charge->minimumChargesForFixedKm;
				$perKmCharges = $charge->perKmCharges;

			}
		}
		else {
			$debug .= "NOT FOUND in DB\n";
			// Try a broader search to see what's there
			$all = $this->db->get_where('deliverycomissionandcharges', ['cityCode' => $cityCode])->result_array();
			foreach ($all as $a) {
				$debug .= "CITY_RECORD: code=" . $a['code'] . ", service=" . $a['forWhichService'] . ", active=" . $a['isActive'] . "\n";
			}
		}
		if ($fixedDeliveryFlag == 1) {

			$shortestdistance = $distanceinKms;
			$dBoyCommission = $deliveryBoyCommission;
		}
		else {
			if ($distanceinKms > $minimumKmForFixedCharges) {
				$finalDistance = $distanceinKms - $minimumKmForFixedCharges;
				$shortestdistance = $distanceinKms;
				$shippingCharges1 = $minimumChargesForFixedKm;
				$shippingCharges1 = $shippingCharges1 + ($finalDistance * $perKmCharges);
				$dBoyCommission = $shippingCharges1;
			}
			else {
				$shortestdistance = $distanceinKms;
				$dBoyCommission = $minimumChargesForFixedKm;
			}
		}
		$debug .= "RESULT: $dBoyCommission\n";
		file_put_contents('tmp_debug.txt', $debug, FILE_APPEND);
		log_message('error', "calculate_deliveryboy_commission: type=$type, cityCode=$cityCode, distance=$distanceinKms, result=$dBoyCommission");
		return $dBoyCommission;
	}

	public function resetPassword_post()
	{
		$postData = $this->post();
		if ($postData["userName"] != '') {
			$tableName = "usermaster";
			$orderColumns = array("usermaster.*");
			$cond = array('usermaster' . ".username" => $postData["userName"], 'usermaster.isActive' => 1);
			$member = $this->GlobalModel->selectQuery($orderColumns, $tableName, $cond);
			if ($member) {
				$userCode = $member->result()[0]->code;
				$insertArr = array(
					"userCode" => $userCode,
					"isActive" => 1
				);
				$insertResult = $this->GlobalModel->onlyinsert($insertArr, 'resetpassword');
				if ($insertResult != 'false') {
					return $this->response(array("status" => "200", "message" => " Reset password Request sent. default password is 123456 ... try after admin reset. Change your password after login."), 200);
				}
				else {
					return $this->response(array("status" => "400", "message" => " Opps...! Something went wrong please try again."), 200);
				}
			}
			else {
				$this->response(array("status" => "400", "message" => "Please Enter Registered username!"), 200);
			}
		}
		else {
			$this->response(array("status" => "400", "message" => "All * fileds are required"), 400);
		}
	}

}
