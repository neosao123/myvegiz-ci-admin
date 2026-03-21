<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pendingorders extends CI_Controller
{
	var $session_key;
	public function __construct()
	{
		parent::__construct();
		$this->load->model('GlobalModel');
		$this->load->model('GlobalModel1');
		$this->load->library('notificationlibv_3');
		$this->session_key = $this->session->userdata('partner_key' . SESS_KEY_PARTNER);
		if (!isset($this->session->userdata['part_logged_in' .  $this->session_key]['code'])) {
			redirect('Login', 'refresh');
		}
	}


	public function index()
	{
		$data['clientmaster'] = $this->GlobalModel->selectData('clientmaster');
		$data['city'] = $this->GlobalModel->selectActiveData('citymaster');
		$data['orderStatus'] = $this->GlobalModel->selectDataExcludeDelete('vendororderstatusmaster');
		$data['error'] = $this->session->flashdata('response');
		//$data['vendorordermaster'] = $this->GlobalModel->selectData('vendorordermaster');
		$data['orderCode'] = $this->GlobalModel->selectQuery('vendorordermaster.*', 'vendorordermaster', array('vendorordermaster.isActive' => 1));
		$this->load->view('header');
		$this->load->view('pendingorder/pendingOrderList', $data);
		$this->load->view('footer');

		// $this->session_key = $this->session->userdata('key'.SESS_KEY);
		// $this->load->view('header');
		// $this->load->view('pendingorder/pendingOrderList');
		// $this->load->view('footer');
	}

	public function getOrderList()
	{
		$addID = $this->session->userdata['part_logged_in' . $this->session_key]['code'];

		$fromDate = $this->input->get('fromDate');
		$toDate = $this->input->get('toDate');
		if ($fromDate != '') {
			// $startDate = date('Y-m-d',strtotime($startDate));
			// $endDate = date('Y-m-d',strtotime($endDate));
			$fromDate = date('Y-m-d', strtotime(str_replace('/', '-', $fromDate)));
			$toDate = date('Y-m-d', strtotime(str_replace('/', '-', $toDate)));
			$fromDate = $fromDate . " 00:00:00";
			$toDate = $toDate . " 23:59:59";
		}

		$orderCode = $this->input->get('orderCode');
		$orderStatus = $this->input->get('orderStatus');
		if ($orderStatus == "") {
			$orderStatus = "PLC";
		}
		$tableName = "vendorordermaster";
		$orderColumns = array("vendorordermaster.*,clientmaster.name,clientmaster.mobile,bookorderstatuslineentries.statusTime,bookorderstatuslineentries.statusLine");
		// $orderColumns = array("vendorordermaster.*,clientmaster.name,clientmaster.mobile");
		$condition = array('vendorordermaster.vendorCode' => $addID, 'vendorordermaster.code' => $orderCode, 'bookorderstatuslineentries.statusLine' => $orderStatus/*,'vendorordermaster.addDate'=>$fromDate,'vendorordermaster.addDate'=>$toDate*/);
		// ,'vendorordermaster.orderStatus'=>'PLC','vendorordermaster.orderStatus'=>'RJT'
		$orderBy = array('vendorordermaster' . '.id' => 'DESC');
		$joinType = array('clientmaster' => 'inner', 'bookorderstatuslineentries' => 'inner');
		// $joinType=array('clientmaster'=>'inner');
		$join = array('clientmaster' => 'clientmaster.code=vendorordermaster.clientCode', 'bookorderstatuslineentries' => 'bookorderstatuslineentries.orderCode=vendorordermaster.code');
		// $join = array('clientmaster'=>'clientmaster.code=vendorordermaster.clientCode');
		$groupByColumn = array();
		$limit = $this->input->GET("length");
		$offset = $this->input->GET("start");
		$extraCondition = " (vendorordermaster.isDelete=0 OR vendorordermaster.isDelete IS NULL)";
		// if($startDate!=""){
		// $extraCondition="bookorderstatuslineentries.statusTime between '".$startDate . "' AND '". $endDate."' and (vendorordermaster.isDelete = 0 OR vendorordermaster.isDelete IS NULL)";
		// }
		$like = array();
		$Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
		// print_r($this->db->last_query());
		// exit;
		//echo $this->db->last_query();     
		$data = array();
		$srno = $_GET['start'] + 1;
		if ($Records) {
			foreach ($Records->result() as $row) {
				$statusTime = $row->statusTime;
				
				$DBName = "";
				if($row->deliveryBoyCode !='' or $row->deliveryBoyCode !=null)
				{
				$tableName1 = "usermaster";
				$orderColumns1 = array("usermaster.*");
				$condition1 = array('usermaster.code' => $row->deliveryBoyCode);
				$orderBy1 = array('usermaster' . '.id' => 'DESC');
				$joinType1 = array(); 
				$join1 = array();
				$DBData = $this->GlobalModel->selectQuery($orderColumns1, $tableName1, $condition1, $orderBy1, $join1, $joinType1);
					if ($DBData) {
						$DBName = $DBData->result_array()[0]['name'];
					}
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
						$orderStatus = "Pending";
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
						$orderStatus = "Rejected";
						$chkRJT = 'checked';
						$orderDate = date('d-m-Y h:i:s', strtotime($statusTime));
						break;
					case "PRE":
						$orderStatus = "Preparing";
						$chkRJT = 'checked';
						$orderDate = date('d-m-Y h:i:s', strtotime($statusTime));
						break;
				}

				$actionHtml = '  <a class="dropdown-item  blue" href="' . base_url() . 'index.php/Pendingorders/view/' . $row->code . '"><i class="ti-eye"></i> Open</a>';

				if ($row->orderStatus == 'PLC' || $row->orderStatus == 'RJT' || $row->orderStatus == 'CAN') {
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
			// $dataCount=sizeof($this->GlobalModel->selectQuery($orderColumns,$tableName,$condition,$orderBy,$join,$joinType,array(),'','','',$extraCondition)->result());
			$dataCount1 = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, array(), '', '', '', $extraCondition);
			if ($dataCount1) {
				$dataCount = sizeOf($dataCount1->result());
			} else {
				$dataCount = 0;
			}
			$output = array(
				"draw"			  =>     intval($_GET["draw"]),
				"recordsTotal"    =>      $dataCount,
				"recordsFiltered" =>     $dataCount,
				"data"            =>     $data
			);
			echo json_encode($output);
		} else {
			$dataCount = 0;
			$data = array();
			$output = array(
				"draw"			  =>     intval($_GET["draw"]),
				"recordsTotal"    =>     $dataCount,
				"recordsFiltered" =>     $dataCount,
				"data"            =>     $data
			);
			echo json_encode($output); 
		}
	}

	public function view()
	{
		$addID = $this->session->userdata['part_logged_in' . $this->session_key]['code'];

		$data['discountInPercent'] = 0;
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
		$extraCondition = " vendorordermaster.isDelete=0 OR vendorordermaster.isDelete IS NULL";
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
		$this->load->view('pendingorder/view', $data);
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
					$data[] = array($srno, $row->vendorItemCode, $itemName.'<br>'.$addonText, $row->salePrice, $row->quantity, $row->priceWithQuantity);
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
		$timeStamp = date("Y-m-d h:i:s");
		$string = "";
		$string = "Placed";
		$addID = $this->session->userdata['part_logged_in' . $this->session_key]['code']; 
		$userName = $this->session->userdata['part_logged_in' . $this->session_key]['username'];
		$role = 'VENDOR';
		$ip = $_SERVER['REMOTE_ADDR'];
		$text = $role . " " . $userName . ' ' . $string . ' Order "' . $orderCode . '" from ' . $ip;
		$log_text = array('addId' => $addID, 'logText' => $text);
		$actvity = $this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
		$timeStamp = date("Y-m-d h:i:s");

		$data = array('orderStatus' => 'PRE', 'editID' => $timeStamp, 'editDate' => $timeStamp);
		$result = $this->GlobalModel->doEdit($data, 'vendorordermaster', $orderCode);

		if ($result != 'false') {
			$bookLineResult='true';
			$order_status = $this->GlobalModel->selectQuery("vendororderstatusmaster.*", "vendororderstatusmaster",array("vendororderstatusmaster.statusSName" => "PRE"));
			if ($order_status && count($order_status->result_array())>0) {
				$order_status_record = $order_status->result()[0];
				$statusTitle = $order_status_record->messageTitle;
				#replace $ template in title 
				$statusDescription = $order_status_record->messageDescription;
				$statusDescription = str_replace("$", $orderCode, $statusDescription);
				$dataBookLine = array(
					"orderCode" => $orderCode,
					"statusPutCode" => $addID,
					"statusLine" => 'PRE',
					"reason" => 'Accepted Order and Preapring it',
					"statusTime" => $timeStamp,
					"statusTitle"=>$statusTitle,
					"statusDescription"=>$statusDescription,
					"isActive" => 1
				);
				$bookLineResult = $this->GlobalModel->addWithoutYear($dataBookLine, 'bookorderstatuslineentries', 'BOL');
			}
			//notification			
			$orderData = $this->GlobalModel->selectQuery("vendorordermaster.*", 'vendorordermaster', array("vendorordermaster.code"=>$orderCode));
			if($orderData){
				$type=0;
				$url=$port='';
				$orderData = $orderData->result_array()[0];
				//set client code and delivery boy
				$clientCode = $orderData['clientCode'];
				$deliveryBoyCode = $orderData['deliveryBoyCode'];
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
				//set notification to customer
				$clientdata = $this->GlobalModel->selectQuery("clientdevicedetails.firebaseId","clientdevicedetails",array("clientdevicedetails.clientCode"=>$clientCode));
				if($clientdata){
					$DeviceIdsArr =array();
					foreach($clientdata->result_array() as $c){
						$DeviceIdsArr[] = $c['firebaseId'];
					} 
					$message = 'Order No-'.$orderCode.' is set for preparing.';
					$title = 'Order Accepted';
					$this->sendFirebaseNotification($DeviceIdsArr,$title,$message,$orderCode,'normal',1,$url,$port);
				} 
				//send notification to delivery boy
				$userData = $this->GlobalModel->selectQuery("usermaster.firebase_id","usermaster",array("usermaster.code"=>$clientCode));
				if($userData){
					$DeviceIdsArr =array();
					foreach($clientdata->result_array() as $c){
						$DeviceIdsArr[] = $c['firebase_id'];
					}
					$message = 'Order No-'.$orderCode.' is confirmed set for preparing.';
					$title = 'Order Confirmed';
					$this->sendFirebaseNotification($DeviceIdsArr,$title,$message,$orderCode,"forDB",1,$url,$port);
				} 				 
			} 
			$response['status'] = true;
			$response['message'] = "Order Confirmed Successfully.";
		} else {
			$response['status'] = false;
			$response['message'] = "Failed To Confirm Order";
		}
		$this->session->set_flashdata('response', json_encode($response));
		redirect(base_url() . 'index.php/Pendingorders');
	}

	public function reject()
	{
		$addID = $this->session->userdata['part_logged_in' . $this->session_key]['code'];
		$code = $this->input->post('code');
		$ip = $_SERVER['REMOTE_ADDR'];
		$orderCode = $code;
		$type="";
		$url=$port='';
		$timeStamp = date("Y-m-d h:i:s");
		$data = array('orderStatus' => 'RJT', 'paymentStatus' => 'RJCT', 'editDate' => $timeStamp);
		$result = $this->GlobalModel->doEdit($data, 'vendorordermaster', $code);
		//$result='true';
		if ($result == 'true') {
			$order_status = $this->GlobalModel->selectQuery("vendororderstatusmaster.*", "vendororderstatusmaster",array("vendororderstatusmaster.statusSName" => "RJT"));
			if ($order_status && count($order_status->result_array())>0) { 
				$order_status_record = $order_status->result()[0];
				$statusTitle = $order_status_record->messageTitle;
				#replace $ template in title 
				$statusDescription = $order_status_record->messageDescription;
				$statusDescription = str_replace("$", $code, $statusDescription);
				$dataBookLine = array(
					"orderCode" => $code,
					"statusPutCode" => $addID,
					"statusLine" => 'RJT',
					"reason" => 'Order Rejected By Vendor',
					"statusTime" => $timeStamp,
					"statusTitle"=>$statusTitle,
					"statusDescription"=>$statusDescription,
					"isActive" => 1
				);
				$bookLineResult = $this->GlobalModel->addWithoutYear($dataBookLine, 'bookorderstatuslineentries', 'BOL');
			}
			//notification			
			$orderData = $this->GlobalModel->selectQuery("vendorordermaster.*", 'vendorordermaster', array("vendorordermaster.code"=>$orderCode));
			//echo $this->db->last_query();
			if($orderData){                             
				$orderData = $orderData->result_array()[0];
				//set client code and delivery boy
				$orderStatus = $orderData['orderStatus'];
				$clientCode = $orderData['clientCode'];
				$deliveryBoyCode = $orderData['deliveryBoyCode'];				
				$vendCode = $orderData['vendorCode']; 
				$subTotal=$orderData['subTotal'];
				$grandTotal=$orderData['grandTotal']; 
				if($orderStatus!='PND')
				{
					$DBFlag=0;
					$restoFlag=0;
					if($orderStatus=='PLC')
					{  
						$DBFlag=1;
					}
					else
					{
						$restoFlag=1;
						$DBFlag=1;
					}  
					if($DBFlag==1){ 
						$dbCode = $deliveryBoyCode;
						$orderColumns = array("usermaster.firebase_id");
						$cond = array('usermaster' . '.isActive' => 1, "usermaster.code" => $dbCode);
						$resultDBoy = $this->GlobalModel->selectQuery($orderColumns, 'usermaster', $cond);
						if($resultDBoy)
						{ 
							//remove delivery boy current active order
							$dataUpCnt['orderCount'] = 0;
							$dataUpCnt['orderCode'] = null;
							$dataUpCnt['orderType'] = null;
							$delbActiveOrder = $this->GlobalModel->doEditWithField($dataUpCnt, 'deliveryBoyActiveOrder', 'deliveryBoyCode', $dbCode);
							$dBoyRelease['deliveryBoyCode']=null;
							$resultRelase = $this->GlobalModel->doEdit($dBoyRelease, 'vendorordermaster', $orderCode);
						}
					}
					if($restoFlag==1)
					{ 
						//add 10 ruppes comission to delivery
						$dataUpCnt['commissionAmount'] = 10;
						$dataUpCnt['orderCode'] = $orderCode;
						$dataUpCnt['orderType'] = 'food';
						$dataupCnt['deliveryBoyCode'] = $deliveryBoyCode;
						$dataupCnt['addID'] = $addID;
						$dataupCnt['addIP'] = $ip;
						$dataupCnt['isActive'] = 1;
						$dataupCnt['deliveryBoyCode'] = $deliveryBoyCode;
						$delbActiveOrder = $this->GlobalModel->addNew($dataUpCnt, 'deliveryboyearncommission', 'DBEC');
						
						
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
				}
				//set notification to customer
				$clientdata = $this->GlobalModel->selectQuery("clientdevicedetails.firebaseId","clientdevicedetails",array("clientdevicedetails.clientCode"=>$clientCode));
				if($clientdata){
					$DeviceIdsArr =array();
					foreach($clientdata->result_array() as $c){
						$DeviceIdsArr[] = $c['firebaseId'];
					} 
					$message = 'Apologies! The order you placed for No-'.$orderCode.' is rejected. Please try later';
					$title = 'Order Accepted';
					$this->sendFirebaseNotification($DeviceIdsArr,$title,$message,$orderCode,'normal');
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
			$addID = $this->session->userdata['part_logged_in' . $this->session_key]['code']; 
			$userName = $this->session->userdata['part_logged_in' . $this->session_key]['username'];
			$role = 'VENDOR';
			$ip = $_SERVER['REMOTE_ADDR'];
			$text = $role . " " . $userName . ' Rejected Order "' . $code . '" from ' . $ip;
			$log_text = array('addId' => $addID, 'logText' => $text);
			echo $this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
		} else {
			echo 'false';
		}
	}
	
	public function sendFirebaseNotification(?array $DeviceIdsArr,$title,$message,$orderId,?string $forDB, ?string $type,?string $url,$port){
		$random = rand(0, 999);
		$random = date('his').$random; 
		$dataArr = array();
		$dataArr['device_id'] = $DeviceIdsArr;
		$dataArr['message'] = $message; //Message which you want to send  
		$dataArr['title'] = $title;
		$dataArr['order_id'] = $orderId;
		$dataArr['random_id'] = $random;
		$dataArr['type'] = 'order';
		if($type==1){
			$dataArr['url'] = $url;
			$dataArr['port'] = $port;
		}
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