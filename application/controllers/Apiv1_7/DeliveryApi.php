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
	}

	//Get dashboard
	public function dashboard_post()
	{
		$postData = $this->post();
		if ($postData["code"] != '') {
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

			if ($areaCodes != "") {
				$orderColumns = array("count(id) pCount");
				$cond = array("ordermaster.isActive" => 1, "ordermaster.orderStatus" => "PND");
				$orderBy = array('ordermaster' . ".id" => 'ASC');
				$join = array();
				$joinType = array();
				$like = array();
				$limit = "";
				$offset = "";
				$groupByColumn = array();
				$extraCondition = "ordermaster.areaCode IN(" . $areaCodes . ")";

				$p_result = $this->GlobalModel->selectQuery($orderColumns, 'ordermaster', $cond, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
				if ($p_result) {
					$response["pendingCount"] = $p_result->result_array()[0]["pCount"];
				} else {
					$response["pendingCount"] = 0;
				}
			} else {
				$response["pendingCount"] = 0;
			}

			if ($areaCodes != "") {
				$orderColumns = array("count(id) pCount");
				$cond = array("ordermaster.isActive" => 1, "ordermaster.orderStatus" => "PLC", "ordermaster.editID" => $postData["code"]);
				$orderBy = array('ordermaster' . ".id" => 'ASC');
				$join = array();
				$joinType = array();
				$like = array();
				$limit = "";
				$offset = "";
				$groupByColumn = array();
				$extraCondition = "ordermaster.areaCode IN(" . $areaCodes . ")";

				$p_result = $this->GlobalModel->selectQuery($orderColumns, 'ordermaster', $cond, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
				if ($p_result) {
					$response["placeCount"] = $p_result->result_array()[0]["pCount"];
				} else {
					$response["placeCount"] = 0;
				}
			} else {
				$response["placeCount"] = 0;
			}

			//delivered count
			$orderColumns = array("count(id) dCount");
			$cond = array("ordermaster.isActive" => 1, "ordermaster.orderStatus" => "DEL", "ordermaster.editID" => $postData["code"], "ordermaster.editDate >=" => date('Y/m/d'));
			$orderBy = array('ordermaster' . ".id" => 'ASC');
			$join = array();
			$joinType = array();
			$like = array();
			$limit = "";
			$offset = "";
			$groupByColumn = array();

			$p_result = $this->GlobalModel->selectQuery($orderColumns, 'ordermaster', $cond, $orderBy);
			if ($p_result) {
				$response["deliveredCount"] = $p_result->result_array()[0]["dCount"];
			} else {
				$response["deliveredCount"] = 0;
			}

			//Rejected count
			if ($areaCodes != "") {
				$orderColumns = array("count(id) rCount");
				$cond = array("ordermaster.isActive" => 1, "ordermaster.orderStatus" => "RJT", "ordermaster.editID" => $postData["code"]);
				$orderBy = array('ordermaster' . ".id" => 'ASC');
				$join = array();
				$joinType = array();
				$like = array();
				$limit = "";
				$offset = "";
				$groupByColumn = array();
				$extraCondition = "ordermaster.areaCode IN(" . $areaCodes . ")";

				$p_result = $this->GlobalModel->selectQuery($orderColumns, 'ordermaster', $cond, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
				if ($p_result) {
					$response["rejectCount"] = $p_result->result_array()[0]["rCount"];
				} else {
					$response["rejectCount"] = 0;
				}
			} else {
				$response["rejectCount"] = 0;
			}

			$result["dashboard"] = $response;
			$this->response(array("status" => "200", "message" => "success", "result" => $result), 200);
		} else {
			$this->response(array("status" => "400", "message" => " * are Required field(s)."), 400);
		}
	}

	//login process
	public function loginProcess_post()
	{
		$postData = $this->post();

		if ($postData["userName"] != '' && $postData["userPassword"] != '') {
			$loginData = array(
				"username" => $postData["userName"],
				"password" => md5($postData["userPassword"]),
				"role" => "DLB",
				"isActive" => 1,
			);
			if ($this->ApiModel->login_delivery($loginData)) {
				$data = array(
					"username" => $postData["userName"]
				);
				$resultData = $this->ApiModel->read_Delivery_information($data);
				$empCode = $resultData[0]->empCode;
				$userCode = $resultData[0]->code;
				$empData = $this->GlobalModel->getAllDataFromField('employeemaster', 'code', $empCode)->result();

				$loginStatus = 0;
				$res = $this->GlobalModel->selectQuery('deliveryBoyActiveOrder.loginStatus', 'deliveryBoyActiveOrder', array('deliveryBoyActiveOrder.deliveryBoyCode' => $userCode));
				if ($res) {
					$loginStatus = $res->result_array()[0]['loginStatus'];
				} else {
					$dataDbActive['deliveryBoyCode'] = $userCode;
					$dataDbActive['orderCount'] = 0;
					$dataDbActive['loginStatus'] = 0;
					$dataDbActive['isActive'] = 1;
					$resultDbActive = $this->GlobalModel->addWithoutYear($dataDbActive, 'deliveryBoyActiveOrder', 'DBA');
				}

				$resultArray = array(
					'code' => $resultData[0]->code,
					'empCode' => $resultData[0]->empCode,
					'userName' => $resultData[0]->username,
					'role' => $resultData[0]->role,
					'userEmail' => $resultData[0]->userEmail,
					'profilePhoto' => $resultData[0]->profilePhoto,
					'isActive' => $resultData[0]->isActive,
					'empName' => $empData[0]->firstName . ' ' . $empData[0]->lastName,
					'contactNumber' => $empData[0]->contact1,
					'loginStatus' => $loginStatus
				);



				$result['userData'] = $resultArray;
				return $this->response(array("status" => "200", "message" => "Login Successfully...", "result" => $result), 200);
			} else {
				return $this->response(array("status" => "400", "message" => "incorrect username or Password"), 200);
			}
		} else {
			return $this->response(array("status" => "400", "message" => " * are Required field(s)."), 400);
		}
	} //end login  Process

	//delivery boy accept order api
	public function deliveryAccpetUpdate_post()
	{
		$postData = $this->post();
		if ($postData['code'] != "" && $postData['status'] != "") {
			if ($postData['status'] == 1) {
				$dataupdate['loginStatus'] = 1;
			} else {
				$dataupdate['loginStatus'] = 0;
			}
			$userCode = $postData['code'];
			$dataupdate['editID'] = $userCode;
			$dataupdate['editIP'] = $_SERVER['REMOTE_ADDR'];
			$res = $this->GlobalModel->doEditWithField($dataupdate, 'deliveryBoyActiveOrder', 'deliveryBoyCode', $userCode);
			if ($res != 'false') {
				return $this->response(array("status" => "200", "message" => "Status updated successfully"), 200);
			} else {
				return $this->response(array("status" => "300", "message" => " Failed to update status."), 200);
			}
		} else {
			return $this->response(array("status" => "400", "message" => " * are Required field(s)."), 400);
		}
	}

	//profile update 
	public function deliveryProfileUpdate_post()
	{
		$postData = $this->post();

		if ($postData["code"] != '') {
			if ($postData["userEmail"]) {
				$e_id = filter_var($postData["userEmail"], FILTER_SANITIZE_EMAIL);
			} else {
				$e_id = "";
			}


			$dataProfile = [
				'userEmail' => $postData["userEmail"]
			];

			$resultData = $this->GlobalModel->selectDataById($postData["code"], 'usermaster')->result_array();

			if (sizeof($resultData) == 1) {

				$empData = array('contact1' => $postData['contactNumber']);
				$empRes = $this->GlobalModel->doEdit($empData, 'employeemaster', $resultData[0]['empCode']);

				//usermaster email update
				$resultMaster = $this->GlobalModel->doEdit($dataProfile, 'usermaster', $postData["code"]);

				if ($resultMaster != false || $filedoc != false || $empRes != false) {

					return $this->response(array("status" => "200", "message" => "Your profile has been updated successfully."), 200);
				} else {
					return $this->response(array("status" => "400", "message" => " Failed to update your profile."), 200);
				}
			} else {
				return $this->response(array("status" => "400", "message" => "User not registered. Please register user."), 200);
			}
		} else {
			$this->response(array("status" => "400", "message" => " * are required field(s)."), 400);
		}
	}

	//profile pic upload
	public function profilePicUpload_post()
	{
		$postData = $this->post();
		if ($postData["code"] != '') {
			$checkData = array("code" => $postData["code"], "isDelete" => 0);
			if ($this->GlobalModel->checkExistAndInsertRecords($checkData, 'usermaster')) {
				return $this->response(array("status" => "406", "message" => "User Not Exist"), 200);
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
			} else {
				$dummyFile = 'profilePhoto.jpg';
				$tmpFile = $uploadRootDir . $dummyFile;
				$filename = $uploadDir . '/' . $postData["code"] . '.jpg';
				copy($tmpFile, $filename);
				if (file_exists($filename)) {
					$profilePhoto = $postData["code"] . '.jpg';
				}
			}

			$subData = array(
				'profilePhoto' => $profilePhoto
			);

			//file data update
			$filedoc = $this->GlobalModel->doEdit($subData, 'usermaster', $postData["code"]);

			if ($filedoc != false) {
				$this->response(array("status" => "200", "message" => "Profile photo uploaded successfully."), 200);
			} else {
				$this->response(array("status" => "400", "message" => "Error while uploading file..!!!Please try again."), 200);
			}
		} else {
			$this->response(array("status" => "400", "message" => " * are required field(s)."), 400);
		}
	}

	//forgotPwd_post and send message to register mobile number
	public function resetpassword_through_mail_post()
	{
		$today = date('Y-m-d') . ' ' . date("h:i:s");
		$postData = $this->post();
		if ($postData["userName"] != '') {
			$tableName = "usermaster";
			$orderColumns = array("usermaster.*");
			$cond = array('usermaster' . ".username" => trim($postData["userName"]), 'usermaster.isActive' => 1);
			$userData = $this->GlobalModel->selectQuery($orderColumns, $tableName, $cond);
			if ($userData) {
				$dbResult = $userData->result_array()[0];
				$email = $dbResult['userEmail'];
				$empCode = $dbResult['empCode'];
				$code = $dbResult['code'];
				$Records = $this->GlobalModel->selectQuery('employeemaster.*', 'employeemaster', array('employeemaster.code' => $empCode));
				if ($Records) {
					$res = $Records->result_array()[0];

					$data['fullName'] =  $res['firstName'] . ' ' . $res['middleName'] . ' ' . $res['lastName'];

					$fullName = $res['firstName'] . ' ' . $res['middleName'] . ' ' . $res['lastName'];

					$token = $this->passwordlib->base64url_encode($email . '/' . $code);

					$subData = array('resetToken' => $token);

					$updateData = $this->GlobalModel->doEdit($subData, 'usermaster', $code);

					$data['sendLink'] = base_url() . 'Resetpassword/deResetPassword/' . $token;

					$sendLink = base_url() . 'Resetpassword/deResetPassword/' . $token;

					$to = $email;

					$subject = 'Reset Your Password';

					$message = '<html><body><p>Hi...' . $fullName . ',</p>
						<p>We have received a request to reset password. If you did not make the request just ignore this email. Otherwise, you can reset your password using this link.</p>
						<p><a  href="' . $sendLink . '" target="_blank" style="margin-bottom:8px;background:green; width:50%; padding: 8px 12px; border: 1px solid green;border-radius: 2px;font-family: Helvetica, Arial, sans-serif;font-size: 14px; color: #ffffff;text-decoration: none;font-weight:bold;display: inline-block; text-align:center;">RESET PASSWORD</a></p></body></html>';

					$result = $this->sendemail->sendMailOnly($to, $subject, $message);

					if ($result) {
						return $this->response(array("status" => "200", "message" => "Reset email send Successfully"), 200);
					} else {
						return $this->response(array("status" => "300", "message" => "Problem During Sending Mail"), 200);
					}
				} else {
					return $this->response(array("status" => "300", "message" => "Not a Registered Customer."), 200);
				}
			} else {
				return $this->response(array("status" => "300", "message" => " * Fields are mandatory!"), 200);
			}
		}
	}

	public function resetpassword_post()
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
				} else {
					return $this->response(array("status" => "400", "message" => " Opps...! Something went wrong please try again."), 200);
				}
			} else {
				$this->response(array("status" => "400", "message" => "Please Enter Registered username!"), 200);
			}
		} else {
			$this->response(array("status" => "400", "message" => "All * fileds are required"), 400);
		}
	}

	//Start update password
	public function updatePassword_post()
	{
		$postData = $this->post();

		$memberCode = "";
		$oldPassword = "";
		$dbPassword = "";
		$newPassword = "";


		if ($postData["code"] != '' && $postData["oldPassword"] != '' && $postData["newPassword"] != '') {
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
				} else {
					return $this->response(array("status" => "400", "message" => " Failed to update your password."), 200);
				}
			} else {
				return $this->response(array("status" => "400", "message" => "You entered wrong current password."), 200);
			}
		} else {
			$this->response(array("status" => "400", "message" => " * are required field(s)."), 400);
		}
	}  // End update password

	//get all delivery boy list 
	public function deliveryBoyList_get()
	{
		$res = $this->GlobalModel->getAllDataFromField('usermaster', 'role', 'DLB')->result();
		if ($res) {
			$dataList = array();
			foreach ($res as $dlb) {
				$empCode = $dlb->empCode;
				$empData = $this->GlobalModel->getAllDataFromField('employeemaster', 'code', $empCode)->result();

				$data = array(
					'code' => $dlb->code,
					'empCode' => $dlb->empCode,
					'role' => $dlb->role,
					'empName' => $empData[0]->firstName . ' ' . $empData[0]->lastName
				);
				array_push($dataList, $data);
			}
			$result['List'] = $dataList;
			return $this->response(array("status" => "200", "message" => "Delivery Boy List", "result" => $result), 200);
		} else {
			return $this->response(array("status" => "400", "message" => "No More Records"), 400);
		}
	}

	//get All assigned area to delivery Boy
	public function deliveryBoyAreaList_post()
	{
		$postData = $this->post();
		if ($postData['userCode'] != '') {
			$res = $this->GlobalModel->getAllDataFromField('useraddresslineentries', 'userCode', $postData['userCode'])->result();
			if ($res) {
				$areaData = [];
				foreach ($res as $row) {
					$addressCode = $row->addressCode;
					$addressData = $this->GlobalModel->getAllDataFromField('customaddressmaster', 'code', $addressCode)->result();
					foreach ($addressData as $addr) {
						$Data = array(
							'place' => $addr->place,
							'district' => $addr->district,
							'state' => $addr->state,
							'pincode' => $addr->pincode,
							'userCode' => $row->userCode,
							'areaCode' => $addr->code
						);

						array_push($areaData, $Data);
					}
				}
				$result['areaList'] = $areaData;
				return $this->response(array("status" => "200", "message" => "Delivery Boy Assigned area", "result" => $result), 200);
			} else {
				return $this->response(array("status" => "300", "message" => "No More Records"), 200);
			}
		} else {
			return $this->response(array("status" => "400", "message" => " * are Required field(s)."), 400);
		}
	}

	//get Custom added address and area list
	public function getCustomAddressList_get()
	{
		$conditionColumns = array('isService', 'isActive');
		$conditionValues = array(1, 1);
		$res = $this->GlobalModel->selectActiveDataByMultipleFields($conditionColumns, $conditionValues, 'customaddressmaster');

		if ($res) {
			$addressList = [];
			foreach ($res->result() as $row) {
				$data = array(
					'addressCode' => $row->code,
					'place' => $row->place,
					'district' => $row->district,
					'taluka' => $row->taluka,
					'pincode' => $row->pincode,
					'state' => $row->state,
				);

				array_push($addressList, $data);
			}
			$result['addressList'] = $addressList;
			return $this->response(array("status" => "200", "message" => " Address List where Services Available", "result" => $result), 200);
		} else {
			$this->response(array("status" => "400", "message" => " * are Required field(s)."), 400);
		}
	}

	//get client profile address list 
	public function getOrdersByStatus_post()
	{
		$postData = $this->post();
		log_message("error", "getOrdersByStatus=>" . json_encode($postData));
		if ($postData['code'] != '' && $postData['orderStatus'] != "") {
			$clientOrderLista  = array();
			//set order status
			$orderStatus = $postData['orderStatus'];
			//first check in the vegetable - grocery table
			$orderColumns = array("'vegetable' as orderType,ordermaster.code as orderCode, 0 as tax,0 as totalPackagingCharges, ordermaster.discount,0 as subTotal,ordermaster.shippingCharges as deliveryCharges,ordermaster.paymentmode,ordermaster.address,ordermaster.phone,ordermaster.totalPrice as orderTotalPrice,ordermaster.addDate as orderDate, ordermaster.latitude,ordermaster.longitude, orderstatusmaster.statusName as orderStatus, paymentstatusmaster.statusName as paymentStatus,clientmaster.code as clientCode,clientmaster.name");
			$join = array('clientmaster' => 'clientmaster.code = ordermaster.clientCode', 'orderstatusmaster' => 'ordermaster' . '.orderStatus=' . 'orderstatusmaster' . '.statusSName', 'paymentstatusmaster' => 'ordermaster' . '.paymentStatus=' . 'paymentstatusmaster' . '.statusSName');
			$joinType = array('clientmaster' => 'left', 'orderstatusmaster' => 'inner', 'paymentstatusmaster' => 'inner');
			$cond = array("ordermaster.deliveryBoyCode" => $postData['code'],"ordermaster.isActive" => 1); 
			$orderBy = array('ordermaster' . ".id" => 'ASC');
			$like = array();
			$offset = "";
			$groupByColumn = array();
			$extraCondition = "";
			if ($orderStatus != "") {
				//if order status == PRE or RFP
				/*if ($orderStatus == "PRE" || $orderStatus == "RFP") {
					$extraCondition = " (ordermaster.orderStatus='PRE' or ordermaster.orderStatus='RFP')";
				} else {*/
					$extraCondition = " ordermaster.orderStatus='" . $orderStatus . "'";
				//}
				$limit=1;
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
			$resultQuery = $this->GlobalModel->selectQuery($orderColumns, 'ordermaster', $cond, $orderBy, $join, $joinType, $like, $limit, $offset,	$groupByColumn, $extraCondition);
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
			$orderColumns = array("'food' as orderType,vendorordermaster.code as orderCode,vendorordermaster.tax,vendorordermaster.totalPackgingCharges as totalPackagingCharges,vendorordermaster.discount,vendorordermaster.subTotal,vendorordermaster.vendorCode,vendorordermaster.shippingCharges as deliveryCharges,vendorordermaster.paymentmode,vendorordermaster.address, vendorordermaster.grandTotal as orderTotalPrice,vendorordermaster.addDate as orderDate, vendorordermaster.phone, vendorordermaster.latitude, vendorordermaster.longitude,vendororderstatusmaster.statusSName as orderStatus, paymentstatusmaster.statusName as paymentStatus, clientmaster.code as clientCode, clientmaster.name");
			$join = array('clientmaster' => 'clientmaster.code = vendorordermaster.clientCode', 'vendororderstatusmaster' => 'vendorordermaster.orderStatus=vendororderstatusmaster.statusSName', 'paymentstatusmaster' => 'vendorordermaster.paymentStatus=paymentstatusmaster.statusSName');
			$joinType = array('clientmaster' => 'left', 'vendororderstatusmaster' => 'inner', 'paymentstatusmaster' => 'inner');
			$cond = array("vendorordermaster.deliveryBoyCode" => $postData['code'],"vendorordermaster.isActive" => 1); 
			$orderBy = array('vendorordermaster' . ".id" => 'ASC');
			$like = array();
			$limit = "";
			$offset = "";
			$groupByColumn = array();
			$extraCondition = "";
			if ($orderStatus != "") {
				//if order status == PRE or RFP
				/*if ($orderStatus == "PRE" || $orderStatus == "RCH") {
					$extraCondition = " (vendorordermaster.orderStatus='PRE' or vendorordermaster.orderStatus='RCH')";
				} else {*/
					$extraCondition = " vendorordermaster.orderStatus='" . $orderStatus . "'";
				//}
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
			$r[] = $this->db->last_query();
			if ($resultQuery) {
				$clientOrderList = $resultQuery->result_array();
				$totalOrders = sizeof($clientOrderList);
				for ($i = 0; $i < sizeof($clientOrderList); $i++) {
					$order = $clientOrderList[$i];
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
							$itemAr['itemName'] =  $orderProductList[$j]["itemName"];
							$itemAr['addons'] = $orderProductList[$j]["addons"];
							$itemAr['addonsCode'] = $orderProductList[$j]["addonsCode"];
							$itemAr['quantity'] = $orderProductList[$j]["quantity"];
							$itemAr['priceWithQuantity'] = $orderProductList[$j]["priceWithQuantity"];
							$itemAr['itemPackagingCharges'] = $orderProductList[$j]["itemPackagingCharges"];
							if ($orderProductList[$j]["itemPhoto"] != "") {
								$path = 'partner/uploads/' . $orderProductList[$j]["vendorCode"] . '/vendoritem/' . $orderProductList[$j]["itemPhoto"];
								if (file_exists($path)) {
									$itemAr['itemPhoto'] = base_url($path);
								} else {
									$itemAr['itemPhoto'] = 'noimage';
								}
							} else {
								$itemAr['itemPhoto'] = 'noimage';
							}
							$itemsArray[] = $itemAr;
						}
						$order['orderedProduct'] = $itemsArray;
					}
					$clientOrderLista[] = $order;
				}
				//$data[] = $clientOrderList; 
			}
			//print_r($data);
			if (!empty($clientOrderLista)) {
				$finalResult['orders'] = $clientOrderLista;
				$this->response(array("status" => "200", "message" => " Order details", "result" => $finalResult), 200);
			} else {
				$this->response(array("status" => "300", "message" => "No Data Found",'r'=>$r), 200);
			}
		} else {
			$this->response(array("status" => "400", "message" => " * are Required field(s)."), 400);
		}
	}

	//update orders status
	public function updateOrderStatus_post()
	{
		$ip = $_SERVER['REMOTE_ADDR'];
		$postData = $this->post();
		if ($postData['code'] != "" && $postData['orderStatus'] != "" && $postData['orderCode'] != "") {
			$orderStatus = $postData['orderStatus'];
			$orderCode = $postData['orderCode'];
			$code =	$addID =  $postData['code'];
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
						} else {
							$VegGroOrder = $this->GlobalModel->selectQuery("ordermaster.code", "ordermaster", array("ordermaster.code" => $orderCode));
							if ($VegGroOrder) {
								$orderData['deliveryBoyCode'] = null;
								$orderData['orderStatus'] = 'PND';
								$delbActiveOrder = $this->GlobalModel->doEditWithField($orderData, 'ordermaster', 'code', $orderCode);
							}
						}
					} else {
						return $this->response(array("status" => "400", "message" => "PLease provide a valid reason to release this order."), 400);
					}
				} else {
					return $this->response(array("status" => "400", "message" => "PLease provide a valid reason to release this order."), 400);
				}
			} 
			else if ($orderStatus == "PUP") {
				$ordData['orderStatus'] = 'PUP';
				$reason = "Order has been picked-up";
			} 
			else if ($orderStatus == 'RCH') {
				$ordData['orderStatus'] = 'RCH';
				$reason = "Delivery person reached near the restaurant";
			} 
			else if ($orderStatus == "DEL") {
				$ordData['orderStatus'] = 'DEL';
				$reason = 'Order Delivered! Dear User your order ('.$orderCode.') has been successfully delivered to the given address.';
				//reset delivery boy after deliver order to assign further order
				$restDelv['orderCode'] = null;
				$restDelv['orderType'] = null;
				$restDelv['editID'] = $addID;
				$restDelv['editIP'] = $ip;
				$restDelv['orderCount'] = 0;
				$delbActiveOrder = $this->GlobalModel->doEditWithField($restDelv, 'deliveryBoyActiveOrder', 'deliveryBoyCode', $postData['code']);
			} else {
				return $this->response(array("status" => "400", "message" => "Invalid Order Status"), 400);
			}

			$dataBookLine = array(
				"orderCode" => $orderCode,
				"statusPutCode" => $postData['code'],
				"statusLine" => $orderStatus,
				"statusTime" => date("Y-m-d h:i:s"),
				"reason" => $reason,
				"isActive" => 1
			);

			$bookLineResult = $this->GlobalModel->addWithoutYear($dataBookLine, 'bookorderstatuslineentries', 'BOL');
			if ($bookLineResult != 'false') {
				log_message("error", "status=>" . json_encode($dataBookLine));
				//check cuurent order for notification and delivery boy commision entry
				$cond['ordermaster.code'] = $orderCode;
				$res = $this->GlobalModel->selectQuery("ordermaster.*", "ordermaster", $cond);
				if ($res) {
					sleep(1);
					$clientCode = $res->result_array()[0]['clientCode'];
					$grandTotal = $res->result_array()[0]['totalPrice'];
					$orderUpdate = $this->GlobalModel->doEdit($ordData, 'ordermaster', $orderCode);
					if ($orderUpdate == 'true') {
						
						
						
						$dvCondition['clientCode'] = $clientCode;
						$clientDevices = $this->GlobalModel->selectQuery("clientdevicedetails.firebaseId", "clientdevicedetails", $dvCondition);
						$DeviceIdsArr = array();
						if ($clientDevices) {
							sleep(1);
							foreach ($clientDevices->result() as $key) {
								$DeviceIdsArr[] = $key->firebaseId;
							}
						}
						if (!empty($DeviceIdsArr)) {
							if ($orderStatus == "PLC") {
								$title = "New Order";
								$message = "Your order has been placed for order no. " . $orderCode;
								$this->sendNotification($DeviceIdsArr, $title, $message, $orderCode);
							} else if ($orderStatus == "PUP") {
								$title = "Order - " . $orderCode;
								$message = "The order has been picked up by successfully!";
								$this->sendNotification($DeviceIdsArr, $title, $message, $orderCode);
							} else if ($orderStatus == "RCH") {
								$title = "Order - " . $orderCode;
								$message = "Order has been reached nearby your location";
								$this->sendNotification($DeviceIdsArr, $title, $message, $orderCode);
							} else if ($orderStatus == "DEL") {
								
								$settingResult = $this->GlobalModel->selectQuery('settings.*', 'settings', array('settings.isActive' => 1));
								if($settingResult){
									$baseCumission=$settingResult->result_array()[7]['settingValue'];
									
									$delCom = $baseCumission;
									$this->GlobalModel->deleteForeverFromField('orderCode',$orderCode,'deliveryboyearncommission');
									
									log_message("error", "touch point removed and delivery boy cumission=>" . "Cumission ".$delCom." is added to delivery boy code : ".$code." and order code : ".$orderCode);
									
									$dbcAdd['commissionAmount'] = $delCom;
									$dbcAdd['orderCode'] = $orderCode;
									$dbcAdd['deliveryBoyCode'] = $code;
									$dbcAdd['orderType'] = 'vegetable';
									$dbcAdd['isActive'] = 1;
									$delboyCommission = $this->GlobalModel->addNew($dbcAdd, 'deliveryboyearncommission', 'DBEC');
								}
								
								$title = "Order - " . $orderCode;
								$message = "The order has been delivered successfully!";
								$this->sendNotification($DeviceIdsArr, $title, $message, $orderCode);
							} else {
								// no more notifications
							}
						}
					}
				} 
				else {
					$cond1['vendorordermaster.code'] = $postData['orderCode'];
					$res = $this->GlobalModel->selectQuery("vendorordermaster.*", "vendorordermaster", $cond1);
					sleep(1);
					if ($res) {
						$vendorCode  = $res->result_array()[0]['vendorCode'];
						$clientCode  = $res->result_array()[0]['clientCode'];
						$deliveryBoyCode  = $res->result_array()[0]['deliveryBoyCode'];
						$grandTotal  = $res->result_array()[0]['grandTotal'];
						$shippingCharges  = $res->result_array()[0]['shippingCharges'];
						$dataUpCnt['orderCode'] = $postData['orderCode'];
						$dataUpCnt['orderType'] = 'food';
						$dataupCnt['deliveryBoyCode'] = $code;
						$dataupCnt['addID'] = $postData['code'];
						$dataupCnt['addIP'] = $ip;
						$dataupCnt['isActive'] = 1; 
					
						if ($orderStatus == "DEL") {
							
							$settingResult = $this->GlobalModel->selectQuery('settings.*', 'settings', array('settings.isActive' => 1));
							if($settingResult){
								$amountUpTo=$settingResult->result_array()[1]['settingValue'];
								$cumissionPercentage=$settingResult->result_array()[2]['settingValue'];
								$baseCumission=$settingResult->result_array()[3]['settingValue'];
								if ($grandTotal > $amountUpTo) {
									$percnVal = round($grandTotal * ($cumissionPercentage / 100));
									$delCom = $percnVal;
								} else {
									$delCom = $baseCumission;
								}
								$this->GlobalModel->deleteForeverFromField('orderCode',$orderCode,'deliveryboyearncommission');
								log_message("error", "food touchpoint removed and delivery boy cumission=>" . "Cumission ".$delCom." is added to delivery boy code : ".$code." and order code : ".$orderCode);
								
								$dbcAdd['commissionAmount'] = $delCom;
								$dbcAdd['orderCode'] = $orderCode;
								$dbcAdd['deliveryBoyCode'] = $code;
								$dbcAdd['orderType'] = 'food';
								$dbcAdd['isActive'] = 1;
								$delboyCommission = $this->GlobalModel->addNew($dbcAdd, 'deliveryboyearncommission', 'DBEC');
							}
							
							
							//vendor cumission
							$vendorComissionResult = $this->GlobalModel->selectQuery("vendorconfiguration.defaultVendorCommission", "vendorconfiguration");
							$vendorComission=0;
							if($vendorComissionResult){
								$subtotal=($grandTotal-$shippingCharges);
								$vendorComissionPercentage = $vendorComissionResult->result_array()[0]["defaultVendorCommission"];
								
								$vendorAmount=0;
								if($subtotal<200)
								{
									$vendorComissionPercentage=20;
									$vcomission = round($subtotal * ($vendorComissionPercentage / 100));
								}
								else
								{
									$vcomission = round($subtotal * ($vendorComissionPercentage / 100));
								}
								$vendorAmount=($subtotal-$vcomission);
								$vcData['comissionAmount'] = $vcomission;
								$vcData['commissionType'] = 'regular';
								$vcData['deliveryBoyCode'] = $vendorCode;
								$vcData['orderCode'] = $orderCode;
								$vcData['comissionPercentage'] = $vendorComissionPercentage;
								$vcData['subTotal'] = $subtotal;
								$vcData['vendorAmount'] = $vendorAmount;
								$vcData['grandTotal'] = $grandTotal;
								$vcData['isActive'] = 1;
								$delboyCommission = $this->GlobalModel->addNew($vcData, 'vendorordercommission', 'VNDC');
							}
						}
						$orderUpdate = $this->GlobalModel->doEdit($ordData, 'vendorordermaster', $postData['orderCode']);
						if ($orderUpdate == 'true') {
							sleep(1);
							if ($vendorCode != "") {
								$cond2['vendor.code'] = $vendorCode;
								$restaurant = $this->GlobalModel->selectQuery("vendor.firebaseId", "vendor", $cond2);
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
									} else if ($orderStatus == "PUP") {
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
									} else if ($orderStatus == "RCH") {
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
									} else if ($orderStatus == "DEL") {
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
									} else {
										// no more notifications
									}
								}
							}
						}
					} else {
						$orderUpdate = 'false';
					}
				}
				if ($orderUpdate == 'true') {
					return $this->response(array("status" => "200", "message" => "Order Status updated succesfully..."), 200);
				} else {
					return $this->response(array("status" => "300", "message" => "Failed to update order status"), 200);
				}
			} else {
				return $this->response(array("status" => "300", "message" => "Server seems busy! Please try later"), 200);
			}
		} else {
			$this->response(array("status" => "400", "message" => " * are Required field(s)."), 400);
		}
	}
	
	public function check_get($grandTotal)
	{
		$settingResult = $this->GlobalModel->selectQuery('settings.*', 'settings', array('settings.isActive' => 1));
								if($settingResult){
									$amountUpTo=$settingResult->result_array()[1]['settingValue'];
									$cumissionPercentage=$settingResult->result_array()[2]['settingValue'];
									$baseCumission=$settingResult->result_array()[3]['settingValue'];
									if ($grandTotal > $amountUpTo) {
										$percnVal = round($grandTotal * ($cumissionPercentage / 100));
										$delCom = $percnVal;
									} else {
										$delCom = $baseCumission;
									}
									$dataUpCnt['amountUpTo'] = $amountUpTo;
									$dataUpCnt['cumissionPercentage'] = $cumissionPercentage;
									$dataUpCnt['baseCumission'] = $baseCumission;
									$dataUpCnt['commissionAmount'] = $delCom;
									
									return $this->response(array("status" => "200", "message" =>$dataUpCnt ), 200);
								}
	}

	//Start Get Order List for User
	public function getOrderListByUser_post()
	{
		$postData = $this->post();

		$orderColumns = array("ordermaster.code,ordermaster.clientCode,ordermaster.orderStatus,ordermaster.orderedTime,ordermaster.placedTime,ordermaster.shippedTime,ordermaster.address,ordermaster.phone,ordermaster.totalPrice,ordermaster.areaCode,ordermaster.editId,clientmaster.code,clientmaster.name");
		if ($postData['orderStatus'] != '') {
			$cond = array('ordermaster.orderStatus' => $postData['orderStatus'], 'ordermaster.editId' => $postData['code'],'ordermaster.isActive' => 1);
		} else if ($postData['areaCode'] != '') {
			$cond = array('ordermaster.areaCode' => $postData['areaCode'], 'ordermaster.editId' => $postData['code'],'ordermaster.isActive' => 1);
		} else if ($postData['orderStatus'] != '' && $postData['areaCode'] != '') {
			$cond = array('ordermaster.orderStatus' => $postData['orderStatus'], 'ordermaster.areaCode' => $postData['areaCode'], 'ordermaster.editId' => $postData['code'],'ordermaster.isActive' => 1);
		} else {
			$cond = array('ordermaster.editId' => $postData['code'],'ordermaster.isActive' => 1);
		}

		$orderBy = array('ordermaster' . ".id" => 'ASC');
		$join = array('clientmaster' => 'clientmaster.code = ordermaster.clientCode');
		$joinType = array('clientmaster' => 'left');
		$like = array();
		$limit = array();
		$offset = array();
		$groupByColumn = array();
		$extraCondition = "ordermaster.orderStatus NOT IN('RJT','DEL','CAN')";

		$p_result = $this->GlobalModel->selectQuery($orderColumns, 'ordermaster', $cond, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);

		if ($p_result) {
			$result['listRecords'] = $p_result->result();
			$this->response(array("status" => "200", "message" => " Order details", "result" => $result), 200);
		} else {
			$this->response(array("status" => "400", "message" => " No more Records"), 200);
		}
	}

	//Confirm Placed Orders
	public function confirmOrderPlace_post()
	{
		$postData = $this->post();

		if ($postData["orderCode"] != '' && $postData['userCode'] != '' && $postData['orderStatus'] != '') {
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
				} else {
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
						} else {
							return $this->response(array("status" => "400", "message" => "Failed To delivered Order."), 200);
						}
					} else {
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
						} else {
							return $this->response(array("status" => "400", "message" => "Failed To delivered Order."), 200);
						}
					}
				} else {

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
					} else {
						return $this->response(array("status" => "400", "message" => "Failed To delivered Order."), 200);
					}
				}
			} else if ($postData['orderStatus'] == 'DEL') {
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
					$ip =   $_SERVER['REMOTE_ADDR'];
					$datapoints =  $this->GlobalModel->selectDataByField('code', $delboyCode, 'usermaster');
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
				} else {
					return $this->response(array("status" => "400", "message" => "Failed To delivered Order."), 200);
				}
			} else {
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
				} else {
					return $this->response(array("status" => "400", "message" => "Failed To Reject Order."), 200);
				}
			}
		} else {
			return $this->response(array("status" => "400", "message" => "* are Required field(s)."), 400);
		}
	}

	//get client Delivered order list
	public function getDeliveredOrders_post()
	{
		$postData = $this->post();

		if ($postData['code'] != '' && $postData['offset'] != "") {
			//$areaCode=$postData['areaCode'];

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

			$totalamount = 0;

			if ($areaCodes != '') {

				$orderColumns = array("ordermaster.code as orderCode,ordermaster.shippingCharges as deliveryCharges,ordermaster.paymentmode,ordermaster.address,ordermaster.phone,ordermaster.totalPrice as orderTotalPrice,ordermaster.addDate as orderDate, ordermaster.latitude,ordermaster.longitude, orderstatusmaster.statusName as orderStatus, paymentstatusmaster.statusName as paymentStatus,clientmaster.code as clientCode,clientmaster.name,ordermaster.editDate as deliveredDate");
				$join = array('clientmaster' => 'clientmaster.code = ordermaster.clientCode', 'orderstatusmaster' => 'ordermaster' . '.orderStatus=' . 'orderstatusmaster' . '.statusSName', 'paymentstatusmaster' => 'ordermaster' . '.paymentStatus=' . 'paymentstatusmaster' . '.statusSName');
				$joinType = array('clientmaster' => 'left', 'orderstatusmaster' => 'inner', 'paymentstatusmaster' => 'inner');
				$cond = array("ordermaster.orderStatus" => 'DEL', "ordermaster.editID" => $postData['code'], "ordermaster.editDate >=" => date('Y/m/d'));
				$orderBy = array('ordermaster' . ".id" => 'DESC');
				$like = array();
				$limit = "10";
				$offset = $postData['offset'];
				$groupByColumn = array();
				$extraCondition = ""; //ordermaster.orderStatus IN ('DEL') AND ordermaster.areaCode IN(".$areaCodes.")";

				$resultQuery = $this->GlobalModel->selectQuery($orderColumns, 'ordermaster', $cond, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);

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
				} else {
					$this->response(array("status" => "400", "message" => " No more Records"), 200);
				}
			} else {
				$this->response(array("status" => "400", "message" => " No more Records"), 200);
			}
		} else {
			$this->response(array("status" => "400", "message" => " * are Required field(s)."), 400);
		}
	}

	//get client Rejected order list
	public function getRejectedOrders_post()
	{
		$postData = $this->post();

		if ($postData['code'] != '' && $postData['offset'] != "") {
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
			} else {
				$this->response(array("status" => "400", "message" => " No more Records", "q" => $a), 200);
			}
		} else {
			$this->response(array("status" => "400", "message" => " * are Required field(s)."), 400);
		}
	}

	// Start update profile
	public function updateFirebaseId_post()
	{
		$postData = $this->post();

		if ($postData["code"] != '' && $postData["firebaseId"] != '') {

			$dataMaster = [
				"firebase_id" => $postData["firebaseId"]
			];

			$resultMaster = $this->GlobalModel->doEdit($dataMaster, 'usermaster', $postData["code"]);

			if ($resultMaster != false) {
				return $this->response(array("status" => "400", "message" => "Firebase Id Update Successfully"), 400);
			} else {
				return $this->response(array("status" => "400", "message" => " Failed to update Firebase Id."), 400);
			}
		} else {
			$this->response(array("status" => "400", "message" => " * are required field(s)."), 400);
		}
	}  // End update firebaseId

	//get client Delivered order list
	public function getCommissionRecords_post()
	{
		$postData = $this->post();
		if ($postData['code'] != '') {
			$commissiondata = $this->GlobalModel->selectDataByField('deliveryBoyCode', $postData["code"], 'deliveryboyearncommission');
			$size = sizeof($commissiondata->result());
			$points = 0;
			if ($size > 0) {
				foreach ($commissiondata->result() as $row) {
					$points += $row->commissionAmount;
				}
				$finalResult = array('totalPoints' => $points);
				$this->response(array("status" => "200", "message" => "Touch point Total Amount", "result" => $finalResult), 200);
			} else {
				$finalResult = array('totalPoints' => 0);
				$this->response(array("status" => "200", "message" => "Comimission Details", "result" => $finalResult), 200);
			}
		} else {
			$this->response(array("status" => "400", "message" => " * are Required field(s)."), 400);
		}
	}

	//Start is user active 
	public function isUserActive_post()
	{
		$postData = $this->post();
		if ($postData['code'] != '') {
			$tableName = "usermaster";
			$orderColumns = array("usermaster.isActive");
			$cond = array('usermaster' . ".code" => $postData["code"], 'usermaster.isActive' => 1);
			$member = $this->GlobalModel->selectQuery($orderColumns, $tableName, $cond);
			if ($member) {
				$this->response(array("status" => "200", "message" => "User Active"), 200);
			} else {
				$this->response(array("status" => "300", "message" => "User InActive"), 200);
			}
		} else {
			$this->response(array("status" => "400", "message" => " * are Required field(s)."), 400);
		}
	}

	//Start Get Order List for User
	public function getDeliveryBoyOrderList_post()
	{
		$postData = $this->post();
		if ($postData["deliveryBoyCode"] != "") {
			$deliveryBoyCode = $postData["deliveryBoyCode"];
			$orderVendor = $this->GlobalModel->selectQuery('vendorordermaster.*', 'vendorordermaster', array('vendorordermaster.deliveryBoyCode' => $deliveryBoyCode, 'vendorordermaster.orderStatus' => 'PND'));
			if ($orderVendor) {
				$orderCode = $orderVendor->result_array()[0]['code'];
				$tableName = "vendorordermaster";
				$orderColumns = array("vendorordermaster.code as orderCode,vendorordermaster.shippingCharges as deliveryCharges,vendorordermaster.paymentmode,vendorordermaster.address,vendorordermaster.grandTotal as orderTotalPrice,vendorordermaster.addDate as orderDate, vendororderstatusmaster.statusSName as orderStatus, paymentstatusmaster.statusName as paymentStatus");
				$cond = array("vendorordermaster.code" => $orderCode,"vendorordermaster.isActive" => 1); 
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
				} else {
					return $this->response(array("status" => "300", "message" => "Data not found."), 200);
				}
			} else {
				$orderVegiz = $this->GlobalModel->selectQuery('ordermaster.*', 'ordermaster', array('ordermaster.editID' => $deliveryBoyCode, 'ordermaster.orderStatus' => 'PND'));
				if ($orderVegiz) {
					$orderCode = $orderVegiz->result_array()[0]['code'];
					$tableName = "ordermaster";
					$orderColumns = array("ordermaster.code as orderCode,ordermaster.shippingCharges as deliveryCharges,ordermaster.paymentmode,ordermaster.address,ordermaster.phone,ordermaster.totalPrice as orderTotalPrice,ordermaster.addDate as orderDate, orderstatusmaster.statusName as orderStatus, paymentstatusmaster.statusName as paymentStatus,ordermaster.cityCode");
					$cond = array("ordermaster.code" => $orderCode,"ordermaster.isActive" => 1);
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
					} else {
						return $this->response(array("status" => "400", "message" => "Data not found."), 200);
					}
				} else {
					return $this->response(array("status" => "300", "message" => "Data not found."), 200);
				}
			}
		} else {
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
		if ($postData['code'] != "") {
			$resultData = $this->GlobalModel->selectQuery('vendororderstatusmaster.statusSName,vendororderstatusmaster.statusName', 'vendororderstatusmaster', array('vendororderstatusmaster.statusRole' => 'DELIVERYBOY'));
			if ($resultData) {
				$data['statusList'] = $resultData->result_array();
				return $this->response(array("status" => "200", "msg" => "Data Found", "result" => $data), 200);
			} else {
				return $this->response(array("status" => "300", "msg" => "No Data Found"), 200);
			}
		} else {
			return $this->response(array("status" => "400", "msg" => "* fields are required"), 200);
		}
	}


	public function getTouchPointHistoryList_post()
	{
		$postData = $this->post();
		if ($postData['code'] != "") {
			$resultData = $this->GlobalModel->selectQuery('deliveryboyearncommission.*', 'deliveryboyearncommission', array('deliveryboyearncommission.deliveryBoyCode' => $postData['code'], 'deliveryboyearncommission.isActive' => 1));
			if ($resultData) {
				$data['historyList'] = $resultData->result_array();
				return $this->response(array("status" => "200", "msg" => "Data Found", "result" => $data), 200);
			} else {
				return $this->response(array("status" => "300", "msg" => "No Data Found"), 200);
			}
		} else {
			return $this->response(array("status" => "400", "msg" => "* fields are required"), 200);
		}
	}


	public function sendNotification($DeviceIdsArr = array(), $title, $message, $orderCode)
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
	}

	public function getDeliveredOrdersList_post()
	{
		$postData = $this->post();
		if ($postData['code'] != "") {
			$today = date('Y-m-d');

			if (isset($postData['date'])) {
				if ($postData['date'] != "") {
					$today = date('Y-m-d', strtotime(str_replace("/", "-", $postData['date'])));
				}
			}

			$code = $postData['code'];
			$orderColumns = 'view_deliverycommission.*';
			$table = 'view_deliverycommission';
			$condition['view_deliverycommission.deliveryBoyCode'] = $postData['code'];
			$condition['view_deliverycommission.addDate'] = $today;
			$extraCondition = "";
			$orderBy['view_deliverycommission.addDate'] = "ASC";
			$join = $joinType = $like = $groupBy = array();
			$limit = $offset = "";
			$resultData = $this->GlobalModel->selectQuery($orderColumns, $table, $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupBy, $extraCondition);
			if ($resultData) {
				$totalAmount = 0;
				$totalReturnAmount = 0;
				$data = $resultData->result_array();
				foreach ($resultData->result_array() as $r) {
					$totalAmount += $r['commissionAmount'];
					$totalReturnAmount+=$r['orderReturnAmount'];
				}
				$res['deliveryChargesEarnedList'] = $data;
				return $this->response(array("status" => "200", "deliveryAmountEarned" => $totalAmount,"totalReturnAmount"=>$totalReturnAmount, "msg" => "Data found", "result" => $res), 200);
			} else {
				return $this->response(array("status" => "300", "msg" => "No Data Found"), 200);
			}
		} else {
			return $this->response(array("status" => "400", "msg" => "* fields are required"), 200);
		}
	}
}