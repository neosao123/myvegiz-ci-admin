<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Home extends CI_Controller
{
	var $session_key;
	public function __construct()
	{
		parent::__construct();
		$this->load->model('GlobalModel');
		$this->load->library('notificationlibv_3');
		$this->load->library('firestore');		
		$this->session_key = $this->session->userdata('partner_key' . SESS_KEY_PARTNER);
		if (!isset($this->session->userdata['part_logged_in' .  $this->session_key]['code'])) {
			redirect('Login', 'refresh');
		}
	}

	public function index()
	{
		$this->load->view('header');
		$this->load->view('welcome');
		$this->load->view('footer');
	}

	public function getDashboardOrders()
	{
		if (!isset($this->session->userdata['part_logged_in' . $this->session_key]['code'])) {
			redirect('Login', 'refresh');
		} else {
			$addID  = $this->session->userdata['part_logged_in' . $this->session_key]['code'];
			$today = date('Y-m-d');
			$orderColumns = array("vendorordermaster.*,clientmaster.name,clientmaster.mobile,bookorderstatuslineentries.statusTime,bookorderstatuslineentries.statusLine");
			$table = "vendorordermaster";
			$condition['vendorordermaster.vendorCode'] = $addID;
			//$condition['vendorordermaster.paymentStatus'] = "PID";
			$orderBy = array('vendorordermaster' . '.id' => 'DESC');
			$join = array('clientmaster' => 'clientmaster.code=vendorordermaster.clientCode', 'bookorderstatuslineentries' => 'bookorderstatuslineentries.orderCode=vendorordermaster.code and bookorderstatuslineentries.statusLine=vendorordermaster.orderStatus');
			$joinType = array('clientmaster' => 'inner', 'bookorderstatuslineentries' => 'inner');
			$limit = "";
			$offset = "";
			$fromDate = $today . " 00:00:00";
			$toDate = $today . " 23:59:59";
			$groupByColumn = array("vendorordermaster.code");
			//$groupByColumn = array();
			$extraCondition =	" vendorordermaster.orderStatus in ('PLC','PRE','RFP','RCH') and (vendorordermaster.deliveryBoyCode!='' or vendorordermaster.deliveryBoyCode is not null ) and (vendorordermaster.isDelete=0 OR vendorordermaster.isDelete IS NULL)";
			// and (vendorordermaster.addDate between '" . $fromDate . "' and  '" . $toDate . "')
			$like = array();  
			$ordersArray = array();
			$Records = $this->GlobalModel->selectQuery($orderColumns, $table, $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
			//echo $this->db->last_query();
			if ($Records) {  
				foreach ($Records->result() as $row) {
					$orderCode = $row->code;
					//deliveryBoy Details
					$dbCode = $row->deliveryBoyCode;
					$orderDate = date('Y-m-d h:i:s',strtotime($row->addDate));
					$orderAcceptDateTime = $row->statusTime;
					$preparingMinutes = $row->preparingMinutes;
					$DBName = "";
					$DBContact = "";
					$tableName1 = "usermaster";
					$orderColumns1 = array("usermaster.*");
					$condition1 = array('usermaster.code' => $dbCode);
					$orderBy1 = array();
					$joinType1 = array();
					$join1 = array();
					$DBData = $this->GlobalModel->selectQuery($orderColumns1, $tableName1, $condition1, $orderBy1, $join1, $joinType1);
					
					if ($DBData) {
						$DBName = $DBData->result_array()[0]['name'];
						$DBContact = $DBData->result_array()[0]['mobile'] ;
					}

					//empty order array
					$particulars = $order = array();

					// get order products
					$tableName = 'vendororderlineentries';
					$orderColumns = array("vendororderlineentries.*,vendoritemmaster.itemName,vendoritemmaster.salePrice,vendoritemmaster.itemPhoto");
					$condition = array('vendororderlineentries.orderCode' => $orderCode);
					$orderBy = array('vendororderlineentries.id' => 'desc');
					$joinType = array('vendoritemmaster' => 'inner');
					$join = array('vendoritemmaster' => 'vendoritemmaster.code=vendororderlineentries.vendorItemCode');
					$extraCondition = "vendororderlineentries.isActive=1";
					$items = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, array(), "", "", array(), $extraCondition);
					
					$srno = 0;
					if ($items) {
						foreach ($items->result() as $rr) {
							$itemPhotoCheck = $rr->itemPhoto;
							$itemPhoto = "nophoto";
							if ($itemPhotoCheck != "") {
								$path = 'uploads/' . $addID . '/vendoritem/' . $itemPhotoCheck;
								if (file_exists($path)) {
									$itemPhoto = base_url($path);
								}
							}
							$particulars[] = array("itemName" => $rr->itemName, "itemPhoto" => $itemPhoto, "quantity" => $rr->quantity, "pricewithQty" => $rr->priceWithQuantity);
							$srno++;
						}
					}
					
					//assignOrdeValues
					$order['orderCode'] = $row->code;
					$order['clientName'] = $row->name;
					$order['reachStatus'] = $row->reachStatus;
					$order['orderStatus'] = $row->orderStatus;
					$order['coupanCode'] = $row->coupanCode;
					$order['discount'] = $row->discount;
					$order['tax'] = $row->tax;
					$order['totalPackgingCharges'] = $row->totalPackgingCharges;
					$order['shippingCharges'] = $row->shippingCharges;
					
					$amount = $row->grandTotal;
					$grandTotal = round($amount * (18/100));
					$amount = ($amount) - $row->shippingCharges;
					$order['totalAmount'] = $amount;
					$order['actualAmount'] = $amount - $row->tax - $row->totalPackgingCharges +($row->discount);
					$order['orderDate'] = $orderDate;
					$order['prepareDateTime'] = $orderAcceptDateTime;
					$order['preparingMinutes'] = $preparingMinutes;
					$order['deliveryBoy'] = $DBName;
					$order['deliveryBoyContact'] = $DBContact; 
					$order['noofItems'] = $srno;
					$order['particulars'] = $particulars;
					$order['statusLine'] = $row->statusLine;

					$ordersArray[] = $order;
				}
				$data["ordersData"] = $ordersArray;
			} else {
				$data["ordersData"] = false;
			}
			$this->load->view("recentorders", $data);
		}
	}
	
	public function updatePreparingTime()
	{
		$timeStamp = date("Y-m-d h:i:s");
		$addID  = $this->session->userdata['part_logged_in' . $this->session_key]['code'];
		$orderCode = $this->input->post('orderCode');
		$preparingTime = $this->input->post('preparingTime');
		$previousTime = $this->input->post('previousTime');
		$newTime=($preparingTime+$previousTime);
		$data = array('preparingMinutes' => $newTime, 'editID' => $addID, 'editDate' => $timeStamp);
		$result = $this->GlobalModel->doEdit($data, 'vendorordermaster', $orderCode);
		$response['status'] = true;
		$response['message'] = "Preparing Time Changed";
		echo json_encode($response);
	}
	
	
	public function updateOrderStatus()
	{
		$orderCode = $this->input->post('orderCode');
		$deliveryBoyCode = $this->input->post('deliveryBoyCode');
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
			if($orderStatus=="PRE")	{
				$statusReason="Food is preparing...";
			} else if($orderStatus=="RJT"){
				$statusReason="Rejected by vendor";
			} else {
				$statusReason="Ready for picked up";
			}
			
			$this->firestore->update_order_status($orderCode,$orderStatus,'food');
			
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
					$subTotal=$orderData['subTotal'];
					$grandTotal=$orderData['grandTotal'];
					//set notification to customer
					$clientdata = $this->GlobalModel->selectQuery("clientdevicedetails.firebaseId","clientdevicedetails",array("clientdevicedetails.clientCode"=>$clientCode));
					if($clientdata){
						$DeviceIdsArr =array();
						foreach($clientdata->result_array() as $c){
							$DeviceIdsArr[] = $c['firebaseId'];
						}
						$type=0;
						$port='';
						$message1='';
						$url='';
						if ($orderStatus == 'PRE') { 
						    $type=1;
							$message = "Be patient, we are preparing your delicious food!";
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
							//$message = $message.$message1;
							$checkActiveConnectedPort = $this->GlobalModel->selectQuery('activeports.port,activeports.id','activeports',array('activeports.status'=>1,"activeports.isConnected"=>'0',"activeports.isJunk"=>'0'),array('activeports.id'=>'ASC'),array(),array(),array(),'1');
							if($checkActiveConnectedPort){
								if($checkActiveConnectedPort->num_rows()>0){
									$port=$checkActiveConnectedPort->result_array()[0]['port'];
									$url = "https://myvegiz.com";
									$id=$checkActiveConnectedPort->result_array()[0]['id'];
									$updateOrder['trackingPort']=$port;
									$update = $this->GlobalModel->doEdit($updateOrder,'vendorordermaster',$orderCode);
									$updatePort['isConnected']=1;
									$this->db->query("update activeports set isConnected=1 where id='".$id."'");
									//$message1 = "\nTracking Details : \nURL : ".$url."\nPORT : ".$port;
								}
							}
						}
						else if($orderStatus == 'RJT')
						{
							$message = 'We apologise for the inconvenience, we regret to inform you that we are unable to complete your order #'.$orderCode;
							$title = 'Reject Order'; 
                            $ip = $_SERVER['REMOTE_ADDR'];								
							$dataRjt['orderCode'] = null;
							$dataRjt['editID'] = $addID;
							$dataRjt['editIP'] = $ip;
							$dataRjt['orderCount'] = 0; 
							$dataRjt['orderType']=null;
							$delbRejectOrder = $this->GlobalModel->doEditWithField($dataRjt, 'deliveryBoyActiveOrder', 'deliveryBoyCode', $deliveryBoyCode);
                            
							$dBoyRelease['deliveryBoyCode']=null;
							$resultRelase = $this->GlobalModel->doEdit($dBoyRelease, 'vendorordermaster', $orderCode);
							
							//vendor penulty
							$settingData = $this->GlobalModel->selectQuery("settings.settingValue","settings",array("settings.code"=>"SET_10"));
							if($settingData)
							{
								$vendorPenulty = $settingData->result_array()[0]['settingValue'];
								if($vendorPenulty!=0 && $vendorPenulty!='' && $vendorPenulty!=NULL){
									$commAmount = round(($grandTotal * $vendorPenulty) / 100,2);
									$vcData['comissionPercentage'] = $vendorPenulty;
									$vcData['comissionAmount'] = $commAmount;
									$vcData['subTotal'] = $subTotal;
									$vcData['vendorAmount'] = $subTotal-$commAmount;
									$vcData['grandTotal'] = $grandTotal;
									$vcData['commissionType'] = 'penalty';
									$vcData['deliveryBoyCode'] =$vendCode;
									$vcData['orderCode'] = $orderCode;
									$vcData['isActive'] = 1;
									$delboyCommission = $this->GlobalModel->addNew($vcData, 'vendorordercommission', 'VNDC');
								}
							}
							
								  
						}
						else {
							$message = 'Yay, Your food is ready for delivery!!';
							$title = 'Food Ready'; 
						}
				
						$this->sendFirebaseNotification($DeviceIdsArr,$title,$message,$orderCode,$type,$url,$port);
					}
					$type=0;
					//send notification to delivery boy
					$userData = $this->GlobalModel->selectQuery("usermaster.firebase_id","usermaster",array("usermaster.code"=>$deliveryBoyCode));
					if($userData){
						$DeviceIdsArr =array();
						foreach($userData->result_array() as $c){
							$DeviceIdsArr[] = $c['firebase_id'];
						}
						
						if ($orderStatus == 'PRE') { 
						$type=1;
							$message = "Be ready, we are preparing customer order #".$orderCode." Yay, you have received touch point amount";
							//$message = $message.$message1;
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
					 	$this->sendFirebaseNotification($DeviceIdsArr,$title,$message,$orderCode,$type,$url,$port);
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
	
	public function sendFirebaseNotification($DeviceIdsArr,$title,$message,$orderId,$trackingNoti='',$url='',$port=''){
		$random = rand(0, 999);
		$random = date('his').$random; 
		$dataArr = array();
		$dataArr['device_id'] = $DeviceIdsArr;
		$dataArr['message'] = $message; //Message which you want to send
		$dataArr['title'] = $title;
		$dataArr['order_id'] = $orderId;
		$dataArr['random_id'] = $random;
		$dataArr['type'] = 'order';
		if($trackingNoti==1){
			$dataArr['url'] = $url;
			$dataArr['port'] = $port;
		}
		$notification['device_id'] = $DeviceIdsArr;
		$notification['message'] = $message; //Message which you want to send
		$notification['title'] = $title;
		$notification['order_id'] = $orderId;
		$notification['random_id'] = $random;
		$notification['type'] = 'order';
		$notify = $this->notificationlibv_3->pushNotification($dataArr, $notification);
	}
	
	public function getRestaurantStatus()
	{
	    $addID  = $this->session->userdata['part_logged_in' . $this->session_key]['code'];
	    $condition["vendor.code"] =$addID;
	    $result = $this->GlobalModel->selectQuery("vendor.isServiceable","vendor",$condition);
	    $res['settingValue'] = 0;
	    if($result){
	        $vendorStatus = $result->result()[0]->isServiceable;
	        $res['settingValue'] = $vendorStatus;
	    }
	    echo json_encode($res);
	}
	
	public function updateRestaurantStatus()
	{
	    $addID  = $this->session->userdata['part_logged_in' . $this->session_key]['code'];
	    $data["isServiceable"] =$this->input->post('settingValue');
	    $data["editID"] =$addID;
	    $data["editIP"] =$_SERVER['REMOTE_ADDR'];
	    $result = $this->GlobalModel->doEdit($data,"vendor",$addID); 
	    if($result!='false'){
	       echo true;
	    } else{
	        echo  false;
	    } 
	}
}