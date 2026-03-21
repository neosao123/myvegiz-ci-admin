<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Confirmorder extends CI_Controller
{
	var $session_key;
	public function __construct()
	{
		parent::__construct();
		$this->load->model('GlobalModel');
		$this->load->model('GlobalModel1');
		$this->load->library('notificationlibv_3');
		$this->session_key = $this->session->userdata('partner_key' . SESS_KEY_PARTNER);
	}

	public function index()
	{
		$data['clientmaster'] = $this->GlobalModel->selectData('clientmaster');
		$data['city'] = $this->GlobalModel->selectActiveData('citymaster');
		$data['orderStatus'] = $this->GlobalModel->selectDataExcludeDelete('vendororderstatusmaster');
		$data['orderCode'] = $this->GlobalModel->selectQuery('vendorordermaster.*', 'vendorordermaster', array('vendorordermaster.isActive' => 1));
		$data['error'] = $this->session->flashdata('response');
		$table_name = 'usermaster';
		$orderColumns = array("usermaster.*");
		$condFor = array('usermaster' . '.isDelete' => 0, 'usermaster' . '.isActive' => 1, 'usermaster.role' => "DLB");
		$data['user'] = $this->GlobalModel->selectQuery($orderColumns, $table_name, $condFor);

		$this->load->view('header');
		$this->load->view('confirmorderlist/confirmOrderList', $data);
		$this->load->view('footer');
	}

	public function getPlacedOrders()
	{
		$addID = $this->session->userdata['part_logged_in' . $this->session_key]['code'];

		$productCode = $this->input->get('productCode');
		$productCategory = $this->input->get('categoryCode');
		$fromDate = $this->input->get('fromDate');
		$toDate = $this->input->get('toDate');
		$orderCode = $this->input->get('orderCode');
		$orderStatus = $this->input->get('orderStatus');
		if ($orderStatus == "") {
			$orderStatus = "PRE";
		}
		$tableName = "vendorordermaster";
		$orderColumns = array("vendorordermaster.*,clientmaster.name,clientmaster.mobile,bookorderstatuslineentries.statusTime,bookorderstatuslineentries.statusLine");
		$condition = array('vendorordermaster.vendorCode' => $addID, 'vendorordermaster.code' => $orderCode,'bookorderstatuslineentries.statusLine' => $orderStatus);
		$orderBy = array('vendorordermaster' . '.id' => 'DESC');
		$joinType = array('clientmaster' => 'inner', 'bookorderstatuslineentries' => 'inner'); 
		$join = array('clientmaster' => 'clientmaster.code=vendorordermaster.clientCode', 'bookorderstatuslineentries' => 'bookorderstatuslineentries.orderCode=vendorordermaster.code');
		$groupByColumn = array();
		$limit = $this->input->GET("length");
		$offset = $this->input->GET("start");
		$extraCondition = " (vendorordermaster.isDelete=0 OR vendorordermaster.isDelete IS NULL)"; 
		if ($fromDate != '') {
			$fromDate = date('Y-m-d', strtotime(str_replace('/', '-', $fromDate)));
			$toDate = date('Y-m-d', strtotime(str_replace('/', '-', $toDate)));
			$fromDate = $fromDate . " 00:00:00";
			$toDate = $toDate . " 23:59:59";
			$extraCondition =	" (vendorordermaster.addDate between '".$fromDate."' and  '". $toDate ."') and (vendorordermaster.isDelete=0 OR vendorordermaster.isDelete IS NULL)";
		} 
		$like = array();
		$Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
		$dataCount = 0;
		$data =[];
		$data = array();
		$srno = $_GET['start'] + 1;
		//ECHO $this->db->last_query();
		if ($Records) {
			// echo '<pre>';
			// print_r($Records->result());
			// echo '</pre>';
			foreach ($Records->result() as $row) {
				$statusTime = $row->statusTime; 
				$dbCode = $row->deliveryBoyCode;
				$DBName = "";
				$tableName1 = "usermaster";
				$orderColumns1 = array("usermaster.*");
				$condition1 = array('usermaster.code' => $dbCode);
				$orderBy1 = array();
				$joinType1 = array();
				$join1 = array();
				$DBData = $this->GlobalModel->selectQuery($orderColumns1, $tableName1, $condition1, $orderBy1, $join1, $joinType1);
				if ($DBData) {
					$DBName = $DBData->result_array()[0]['name'];
				}

				if ($row->isActive == 1) {
					$status = "<span class='label label-sm label-success'>Active</span>";
				} else {
					$status = "<span class='label label-sm label-warning'>Inactive</span>";
				}

				$orderDate = date('d-m-Y h:i:s', strtotime($row->addDate));
				$orderStatus = $row->orderStatus;
				$odStatus = $row->orderStatus;
				switch ($orderStatus) {
					case "PND":
						$orderStatus = "Pending";
						$orderDate = date('d-m-Y h:i:s', strtotime($row->addDate));
						break;
					case "PLC":
						$orderStatus = "Placed";
						$orderDate = date('d-m-Y h:i:s', strtotime($statusTime));
						break;
					case "SHP":
						$orderStatus = "Shipped";
						$chkSHP = 'checked';
						$orderDate = date('d-m-Y h:i:s', strtotime($statusTime));
						break;
					case "DEL":
						$orderStatus = "Delivered";
						$chkDEL = 'checked';
						$orderDate = date('d-m-Y h:i:s', strtotime($statusTime));
						break;
					case "CAN":
						$orderStatus = "Cancelled By User";
						$orderDate = date('d-m-Y h:i:s', strtotime($statusTime));
						break;
					case "RJT":
						$orderStatus = "Reject";
						$chkRJT = 'checked';
						$orderDate = date('d-m-Y h:i:s', strtotime($statusTime));
						break;
					case "PRE":
						$orderStatus = "Preparing";
						$chkRJT = 'checked';
						$orderDate = date('d-m-Y h:i:s', strtotime($statusTime));
						break;
					case "RFP":
						$orderStatus = "Ready for Pickup";
						$chkRJT = 'checked';
						$orderDate = date('d-m-Y h:i:s', strtotime($statusTime));
						break;
				    case "PUP":
						$orderStatus = "On the Way"; 
						$chkRJT = 'checked';
						$orderDate = date('d-m-Y h:i:s', strtotime($statusTime)); 
						break;
				}
				$actionHtml = '  <a class="dropdown-item  blue" href="' . base_url() . 'Confirmorder/view/' . $row->code . '"><i class="ti-eye"></i> Open</a>';

				if ($row->orderStatus == 'PRE' || $row->orderStatus == 'DEL' || $row->orderStatus == 'RFP' || $row->orderStatus == 'PUP' ||  $row->orderStatus == 'RCH' ) {
					$data[] = array(
						$srno,
						$row->code,
						$row->name,
						$row->address,
						$row->mobile,
						$orderStatus,
						$row->grandTotal,
						$orderDate,
						$DBName,
						$actionHtml
					);
				}
				$srno++;
				
			}
			$dataCount=sizeof($this->GlobalModel->selectQuery($orderColumns,$tableName,$condition,$orderBy,$join,$joinType,array(),'','','',$extraCondition)->result());
		}
		$output = array(
			"draw"			  =>     intval($_GET["draw"]),
			"recordsTotal"    =>      $dataCount,
			"recordsFiltered" =>     $dataCount,
			"data"            =>     $data
		);
		echo json_encode($output);
	}

	public function view()
	{
		$addID = $this->session->userdata['part_logged_in' . $this->session_key]['code']; 
		$data['orderData'] = false;
		$code = $this->uri->segment(3);
		$tableName = "vendorordermaster";
		$orderColumns = array("vendorordermaster.*,clientmaster.name,clientmaster.mobile");
		$condition = array('vendorordermaster.vendorCode' => $addID, 'vendorordermaster.code' => $code);
		$orderBy = array('vendorordermaster' . '.id' => 'DESC');
		$joinType = array('clientmaster' => 'inner');
		$join = array('clientmaster' => 'clientmaster.code=vendorordermaster.clientCode');
		$groupByColumn = array();
		$limit = $this->input->GET("length");
		$offset = $this->input->GET("start");
		$extraCondition = " (vendorordermaster.isDelete=0 OR vendorordermaster.isDelete IS NULL)"; 
		$like = array();
		$Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
		if ($Records) {
			$data['query'] = $Records;
			$coupanCode = $Records->result_array()[0]['coupanCode'];
			$dataCC = $this->GlobalModel->selectQuery('vendoroffer.discount', 'vendoroffer', array('vendoroffer.coupanCode' => $coupanCode, 'vendoroffer.vendorCode' => $addID));
			if ($dataCC) {
				$data['discountInPercent'] = $dataCC->result_array()[0]['discount'];
			}
		}
		$data['orderStatus'] = $this->GlobalModel->selectDataExcludeDelete('vendororderstatusmaster');
		$data['paymentStatus'] = $this->GlobalModel->selectDataExcludeDelete('paymentstatusmaster');
		$this->load->view('header');
		$this->load->view('confirmorderlist/view', $data);
		$this->load->view('footer');
	}

	public function getOrderDetails()
	{
		$addID = $this->session->userdata['part_logged_in' . $this->session_key]['code'];

		$orderCode = $this->input->get('orderCode');
		$noPic = $this->input->get('noPic');
		$tableName = 'vendororderlineentries';
		$orderColumns = array("vendororderlineentries.*,vendoritemmaster.itemName,vendoritemmaster.salePrice,vendoritemmaster.itemPhoto");
		$condition = array('vendororderlineentries.orderCode' => $orderCode);
		$orderBy = array('vendororderlineentries.id' => 'desc');
		$joinType = array('vendoritemmaster' => 'inner');
		$join = array('vendoritemmaster' => 'vendoritemmaster.code=vendororderlineentries.vendorItemCode');
		$groupByColumn = array();
		$limit = $this->input->GET("length");
		$offset = $this->input->GET("start");
		$srno = $offset + 1;
		$extraCondition = "vendororderlineentries.isActive=1";
		$like = array();
		$data = array();
		$addonText='';
		$Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
		if ($Records) {
			foreach ($Records->result() as $row) {
				$addonText='';
				if($row->addonsCode!='' && $row->addonsCode!=NULL){
					$row->addonsCode = rtrim($row->addonsCode,',');
					$savedaddonsCodes = explode(',',$row->addonsCode);
					foreach($savedaddonsCodes as $addon){
						$joinType1 = array('customizedcategory' => 'inner');
						$condition1 = array('customizedcategorylineentries.code'=>$addon);
						$join1 = array('customizedcategory' => "customizedcategory.code=customizedcategorylineentries.customizedCategoryCode");
						$getAddonDetails = $this->GlobalModel->selectQuery("customizedcategory.categoryTitle,customizedcategory.categoryType,customizedcategorylineentries.subCategoryTitle,customizedcategorylineentries.price","customizedcategorylineentries",$condition1, array(), $join1, $joinType1, array(), array(),'',array(),'');
						$prevMainCateg=$prevMainCateg1='';
						if($getAddonDetails){
							foreach($getAddonDetails->result() as $ad){
								$mainCategory = $ad->categoryTitle;
								$addonText.='<ul>
									<li><b>'.$ad->categoryTitle.' - '.ucfirst($ad->categoryType).
									'</b><ul>
										<li>'.$ad->subCategoryTitle.' - '.$ad->price.'</li>
									</ul>
							           </li>
								</ul>';
							}
						}
					}
				}
				$start = '<div class="d-flex align-items-center">';
				$end = ' <h5 class="m-b-0 font-16 font-medium">' . $row->itemName . '</h5></div></div>';
				$itemPhotoCheck = $row->itemPhoto;
				if ($itemPhotoCheck != "") {
					$itemPhoto = base_url() . 'uploads/' . $addID . '/vendoritem/' . $itemPhotoCheck;
					$photo = '<div class="m-r-10"><img src="' . $itemPhoto . '?' . time() . '" alt="user" class="circle" width="45"></div><div class="">';
					$itemName = $start . $photo . $end;
					$data[] = array($srno, $row->vendorItemCode, $itemName.'<br>'.$addonText, $row->salePrice, $row->quantity, $row->priceWithQuantity);
				} else {
					$itemName = ' <h5 class="m-b-0 font-16 font-medium">' . $row->itemName . '</h5></div></div>';
					$data[] = array($srno, $row->vendorItemCode, $itemName,'<br>'.$addonText, $row->salePrice, $row->quantity, $row->priceWithQuantity);
				}
				$srno++;
			}
		}
		// $dataCount = sizeOf($this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, '', '', $groupByColumn, $extraCondition)->result());
		$dataCount = 0;
		$dataCount1 = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, '', '', $groupByColumn, $extraCondition);
		if ($dataCount1) {
			$dataCount = sizeOf($dataCount1->result());
		}
		$output = array("draw" => intval($_GET["draw"]), "recordsTotal" => $dataCount, "recordsFiltered" => $dataCount, "data" => $data);
		echo json_encode($output);
	}

	public function confirm()
	{
		$orderCode = $this->input->post('orderCode');
		$deliveryBoyCode = $this->input->post('deliveryBoyCode');
		$orderStatus = $this->input->post('orderStatus');

		$timeStamp = date("Y-m-d h:i:s");
		$string = "";
		$string = "Placed";
		$addID = $this->session->userdata['part_logged_in' . $this->session_key]['code'];
	 
		$userName = $this->session->userdata['part_logged_in' . $this->session_key]['username'];
	 	$role = "Vendor";	 
		$ip = $_SERVER['REMOTE_ADDR'];
		$text = $role . " " . $userName . ' ' . $string . ' Order "' . $orderCode . '" from ' . $ip;
		$log_text = array('addId' => $addID, 'logText' => $text);
		$actvity = $this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
		$timeStamp = date("Y-m-d h:i:s");

		$data = array('orderStatus' => $orderStatus, 'editID' => $timeStamp, 'editDate' => $timeStamp);
		$result = $this->GlobalModel->doEdit($data, 'vendorordermaster', $orderCode);

		if ($result != 'false') {
			if ($orderStatus == 'RFP') {
				$order_status = $this->model->selectQuery("vendororderstatusmaster.*", "vendororderstatusmaster",array("vendororderstatusmaster.statusSName" =>$orderStatus));
				if ($order_status && count($order_status->result_array())>0) {
					$order_status_record = $order_status->result()[0];
					$statusTitle = $order_status_record->messageTitle;
					#replace $ template in title 
					$statusDescription = $order_status_record->messageDescription;
					$statusDescription = str_replace("$", $orderCode, $statusDescription);
					$dataBookLine = array(
						"orderCode" => $orderCode,
						"statusPutCode" => $addID,
						"statusLine" => $orderStatus,
						"reason" => 'Food is Ready',
						"statusTime" => $timeStamp,
						"statusTitle"=>$statusTitle,
						"statusDescription"=>$statusDescription,
						"isActive" => 1
					);
					$bookLineResult = $this->GlobalModel->addWithoutYear($dataBookLine, 'bookorderstatuslineentries', 'BOL'); 
				}
				//notification
				$message = 'Food is Ready please Pick-up for Delivery';
				$title = 'Food Ready';
				 	
				$orderData = $this->GlobalModel->selectQuery("vendorordermaster.*", 'vendorordermaster', array("vendorordermaster.code"=>$orderCode));
				if($orderData){
					$orderData = $orderData->result_array()[0];
					//set client code and delivery boy
					$orderStatus = $orderData['orderStatus'];
					$clientCode = $orderData['clientCode'];
					$deliveryBoyCode = $orderData['deliveryBoyCode'];				
					$vendCode = $orderData['vendorCode']; 
					//set notification to customer
					$clientdata = $this->GlobalModel->selectQuery("clientdevicedetails.firebaseId","clientdevicedetails",array("clientdevicedetails.clientCode"=>$clientCode));
					if($clientdata){
						$DeviceIdsArr =array();
						foreach($clientdata->result_array() as $c){
							$DeviceIdsArr[] = $c['firebaseId'];
						} 
						$message = 'Apologies! The order you placed for No-'.$orderCode.' is rejected. Please try later';
						$title = 'Order Accepted';
						$this->sendFirebaseNotification($DeviceIdsArr,$title,$message,$orderCode);
					} 
					//send notification to delivery boy
					$userData = $this->GlobalModel->selectQuery("usermaster.firebase_id","usermaster",array("usermaster.code"=>$clientCode));
					if($userData){
						$DeviceIdsArr =array();
						foreach($clientdata->result_array() as $c){
							$DeviceIdsArr[] = $c['firebase_id'];
						}
						$message = 'Order No-'.$orderCode.' is being rejected. ';
						$title = 'Order Confirmed';
						$this->sendFirebaseNotification($DeviceIdsArr,$title,$message,$orderCode,'forDB');
					} 	
				} 
			}
			$response['status'] = true;
			$response['message'] = "Order Status Changed Successfully.";
		} else {
			$response['status'] = false;
			$response['message'] = "Failed To Change Status";
		}
		$this->session->set_flashdata('response', json_encode($response));
		redirect(base_url() . 'Confirmorder');
	}
	
	
	public function updateOrderStatus()
	{
		$orderCode = $this->input->post('orderCode');
		//$deliveryBoyCode = $this->input->post('deliveryBoyCode');
		$orderStatus = $this->input->post('orderStatus');

		$timeStamp = date("Y-m-d H:i:s");
		$string = "Placed";
		$addID = $this->session->userdata['part_logged_in' . $this->session_key]['code'];
	 
		$userName = $this->session->userdata['part_logged_in' . $this->session_key]['username'];
	 	$role = "Vendor";	 
		$ip = $_SERVER['REMOTE_ADDR'];
		$text = $role . " " . $userName . ' ' . $string . ' Order "' . $orderCode . '" from ' . $ip;
		$log_text = array('addId' => $addID, 'logText' => $text);
		$actvity = $this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');

		$data = array('orderStatus' => $orderStatus, 'editID' => $addID, 'editDate' => $timeStamp);
		if($orderStatus=="PRE")
		{
			$data["preparingMinutes"]=25;
		}
		
		$result = $this->GlobalModel->doEdit($data, 'vendorordermaster', $orderCode);

		if ($result != 'false') {
			if($orderStatus=="PRE")
			{
				$statusReason="Food is preparing...";
			}
			else if("RJT")
			{
				$statusReason="Rejected by vendor";
			}
			$bookLineResult='true';
			$order_status = $this->GlobalModel->selectQuery("vendororderstatusmaster.*", "vendororderstatusmaster",array("vendororderstatusmaster.statusSName" => $orderStatus));
			if ($order_status && count($order_status->result_array())>0) {
				$order_status_record = $order_status->result()[0];
				$statusTitle = $order_status_record->messageTitle;
				#replace $ template in title 
				$statusDescription = $order_status_record->messageDescription;
				$statusDescription = str_replace("$", $orderCode, $statusDescription);
				$dataBookLine = array(
					"orderCode" => $orderCode,
					"statusPutCode" => $addID,
					"statusLine" => $orderStatus,
					"reason" => $statusReason,
					"statusTime" => $timeStamp, 
					"statusTitle"=>$statusTitle,
					"statusDescription"=>$statusDescription,
					"isActive" => 1
				);
				$bookLineResult = $this->GlobalModel->addWithoutYear($dataBookLine, 'bookorderstatuslineentries', 'BOL'); 
			}
			if($bookLineResult!='false'){
				$orderData = $this->GlobalModel->selectQuery("vendorordermaster.*", 'vendorordermaster', array("vendorordermaster.code"=>$orderCode));
				if($orderData){
					$orderData = $orderData->result_array()[0];
					//set client code and delivery boy
					$orderStatus = $orderData['orderStatus'];
					$clientCode = $orderData['clientCode'];
					$deliveryBoyCode = $orderData['deliveryBoyCode'];	  			
					$vendCode = $orderData['vendorCode']; 
					//set notification to customer
					$clientdata = $this->GlobalModel->selectQuery("clientdevicedetails.firebaseId","clientdevicedetails",array("clientdevicedetails.clientCode"=>$clientCode));
					if($clientdata){
						$DeviceIdsArr =array();
						foreach($clientdata->result_array() as $c){ 
							$DeviceIdsArr[] = $c['firebaseId'];
						}
						if ($orderStatus == 'PRE') { 
							$message = 'Be patient, we are preparing your delicious food!';
							$title = 'Food Preparing';
							
							//Delivery boy touch point cumission
							$settingResult = $this->GlobalModel->selectQuery('settings.*', 'settings', array('settings.code' => 'SET_5','settings.isActive' => 1));
							if($settingResult){
								$touchPoint=$settingResult->result_array()[0]['settingValue'];
								$dataUpCnt['commissionAmount'] = $touchPoint;
								$dataUpCnt['deliveryBoyCode'] = $deliveryBoyCode;
								$dataUpCnt['orderCode'] = $orderCode;
								$dataUpCnt['orderType'] = "food";
								$dataUpCnt['isActive'] = 1;
								$delboyCommission = $this->GlobalModel->addNew($dataUpCnt, 'deliveryboyearncommission', 'DBEC');
							}
						}
						else if($orderStatus == 'RJT')
						{
							$message = 'We apologise for the inconvenience, we regret to inform you that we are unable to complete your order #'.$orderCode;
							$title = 'Reject Order'; 
                            
                            $dataRjt['orderCode'] = null;
							$dataRjt['editID'] = $addID;
							$dataRjt['editIP'] = $ip;
							$dataRjt['orderCount'] = 0; 
							$dataRjt['orderType']=null;
							$delbRejectOrder = $this->GlobalModel->doEditWithField($dataRjt, 'deliveryBoyActiveOrder', 'deliveryBoyCode', $deliveryBoyCode);
                            
							$dBoyRelease['deliveryBoyCode']=null;
							$resultRelase = $this->GlobalModel->doEdit($dBoyRelease, 'vendorordermaster', $orderCode);							
						}
						else {
							$message = 'Yay, Your food is ready for delivery!!';
							$title = 'Food Ready'; 
						}
				
						$this->sendFirebaseNotification($DeviceIdsArr,$title,$message,$orderCode);
					}
					//send notification to delivery boy
					$userData = $this->GlobalModel->selectQuery("usermaster.firebase_id","usermaster",array("usermaster.code"=>$deliveryBoyCode));
					if($userData){
						$DeviceIdsArr =array();
						foreach($userData->result_array() as $c){
							$DeviceIdsArr[] = $c['firebase_id'];
						}
						
						if ($orderStatus == 'PRE') { 
							$message = 'Be ready, we are preparing customer order #'.$orderCode.' Yay, you have received touch point amount';
							$title = 'Order Accepted';
						}
						else if($orderStatus == 'RJT')
						{
							$message = 'Order ('.$orderCode.') is rejected. ';
							$title = 'Reject Order'; 		
						}
						else {
							$message = 'Food is Ready! Please Pick-up for Delivery';
							$title = 'Food Ready'; 
						}
					 	$this->sendFirebaseNotification($DeviceIdsArr,$title,$message,$orderCode);
					}
				}
				$response['status'] = true;
				$response['message'] = "Order Status Changed Successfully.";
			} else {
				$response['status'] = false;
				$response['message'] = "Failed To Change Status.";
			}
		} else {
			$response['status'] = false;
			$response['message'] = "Failed To Change Status";
		}
		echo json_encode($response);
	}
	
	public function sendFirebaseNotification(?array $DeviceIdsArr,$title,$message,$orderId,$forDB="normal"){
		$random = rand(0, 999);
		$random = date('his').$random; 
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
		if($forDB=="forDB"){
			$notify = $this->notificationlibv_3->sendDeliveryNotification($dataArr, $notification,"ringing");
		}
		else{
			$notify = $this->notificationlibv_3->pushNotification($dataArr, $notification);
		}
	}
}
