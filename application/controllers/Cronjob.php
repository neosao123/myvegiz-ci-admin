<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Cronjob extends CI_Controller
{

	public function __construct() 
	{
		parent::__construct();
		$this->load->model('GlobalModel');
		$this->load->library('notificationlibv_3');
	}

	public function checkTime()
	{
		echo  date('Y-m-d H:i:s');
	}
	
	public function myVegizCronJobs()
	{
		$time = new DateTime();
		$txt = $time->format("Y/m/d H:i:s");
		//log_message('error',"Cron job runs.....".$txt);
        $time->setTimezone(new DateTimeZone("Asia/Calcutta"));
		$currentMinutes =$time->format("i");
		//$currentMinutes='20';
	    if(($currentMinutes%30==0) || ($currentMinutes=='00'))
		{			
			$this->changeDeliveryBoyStatus();
			sleep(1);
			$this->assignDeliveryBoy();
		}
		if(($currentMinutes%15==0) || ($currentMinutes=='00')) 
		{
			//echo 1;
			$this->updateVendorServiceablity(); 
		}
	}
	
	public function assignDeliveryBoy()
	{
		log_message("error", "Auto assign DB CRON_JOB_EXECUTED");
		$condition1 = array('vendorordermaster.isActive' => "1",'vendorordermaster.isDelete' => "0",'vendorordermaster.orderStatus' => "PND");
		$orderBy1 = array('vendorordermaster.id' => "ASC");
		$extraCondition1 = " (vendorordermaster.deliveryBoyCode IS NULL or vendorordermaster.deliveryBoyCode='')";
		$extraCondition1 .= " and (vendorordermaster.defaultStatus IS NULL or vendorordermaster.defaultStatus='')";
		$getPendingOrders = $this->GlobalModel->selectQuery('vendorordermaster.*,vendor.cityCode,vendor.latitude,vendor.longitude', 'vendorordermaster', $condition1, $orderBy1, array('vendor'=>'vendor.code=vendorordermaster.vendorCode'), array('vendor'=>'inner'),array(), "", "", array(), $extraCondition1);
		//log_message("error", "query ->".$this->db->last_query());
		if($getPendingOrders){
	        foreach($getPendingOrders->result_array() as $restaurant_data)
			{
            $latitude = $restaurant_data["latitude"];
            $longitude = $restaurant_data["longitude"];
			$orderCode=$restaurant_data["code"];
			$cityCode=$restaurant_data["cityCode"];
			if(($latitude!="" && $latitude!=null) && ($longitude!="" && $longitude!=null)) {
			   $this->db->select('deliveryBoyActiveOrder.*,usermaster.deliveryType,ROUND(6371 * acos(cos(radians('. $latitude .')) * cos(radians(usermaster.latitude)) * cos(radians(usermaster.longitude) - radians('. $longitude .')) + sin(radians('. $latitude . ')) * sin(radians(usermaster.latitude)))) AS distance');
			   $this->db->from("deliveryBoyActiveOrder");	
			   $this->db->join('usermaster', 'deliveryBoyActiveOrder.deliveryBoyCode=usermaster.code', 'inner');
			   $this->db->where('deliveryBoyActiveOrder.loginStatus', 1);
			   $this->db->where('deliveryBoyActiveOrder.orderCount',0);
			   $this->db->where('usermaster.isActive',1);
			   $this->db->where('deliveryBoyActiveOrder.isActive',1);
			   $this->db->where('usermaster.role','DLB');
			   $this->db->where('usermaster.deliveryType','food');
			   $this->db->where('usermaster.cityCode',$cityCode);
			   $this->db->where('usermaster.latitude IS NOT NULL');
               $this->db->where('usermaster.longitude IS NOT NULL');
			   $this->db->having('distance <', 15);
			   $this->db->order_by('distance','ASC');
			   $this->db->limit(1);
			   $result = $this->db->get();
			   //echo $this->db->last_query();
			   if($result && $result->num_rows() > 0){
				   $row_count = $result->num_rows(); 
				   $rows=$result->result_array();
                   //shuffle($rows);
                   $is_order_assigned = false;
                   foreach($rows as $row){
					  $actCode=$row["code"];
					  $deliveryBoyCode=$row["deliveryBoyCode"];					  
					  $hasReleadedOrder = $this->GlobalModel->hasDeliveryboyReleasedOrder($deliveryBoyCode, $orderCode);
					  if ($hasReleadedOrder == false) {
						    $is_order_assigned = true;
							$dataUpCnt['orderCount'] = 1;
							$dataUpCnt['orderCode'] = $orderCode;
							$dataUpCnt['orderType'] = 'food';
							$dataUpCnt['editDate'] = date('Y-m-d H:i:s');
							$dataUpCnt['editIP'] = $_SERVER['REMOTE_ADDR'];
							$resultUpdateDB = $this->GlobalModel->doEdit($dataUpCnt, 'deliveryBoyActiveOrder', $actCode);
							if ($resultUpdateDB != 'false') {
								log_message("error", "ASSIGN FOOD ORDER => Order code ->".$orderCode." to delivery boy -> ".$deliveryBoyCode." time->".date('Y-m-d H:i:s'));
								//$dataUpdateDb['orderStatus'] = "PLC";
								$dataUpdateDb['deliveryBoyCode'] = $deliveryBoyCode;
								$dataUpdateDb['editDate'] = date('Y-m-d H:i:s');
								$dataUpdateDb['editIP'] = $_SERVER['REMOTE_ADDR'];
								$resultUpdateDB = $this->GlobalModel->doEdit($dataUpdateDb, 'vendorordermaster', $orderCode); 
								//send notification to the delivery boy
								$random = rand(0, 999);
								$dataNoti = array("title" => 'New Order!', "message" => 'You have assigned new order.', "order_id" => $orderCode, "random_id" => $random, 'type' => 'order');
								$delBoy = $this->GlobalModel->selectQuery("usermaster.firebase_id", "usermaster", array("usermaster.code" => $deliveryBoyCode));
								if ($delBoy) {
									$firebaseId = $delBoy->result_array()[0]['firebase_id'];
									if ($firebaseId != "" && $firebaseId != null) {
										$DeviceIdsArr = [$firebaseId];
										$dataArr = array();
										$dataArr['device_id'] = $DeviceIdsArr;
										$dataArr['message'] = $dataNoti['message']; //Message which you want to send
										$dataArr['title'] = $dataNoti['title'];
										$dataArr['order_id'] = $dataNoti['order_id'];
										$dataArr['random_id'] = $dataNoti['random_id'];
										$dataArr['type'] = $dataNoti['type'];
										$notification['device_id'] = $DeviceIdsArr;
										$notification['message'] = $dataNoti['message']; //Message which you want to send
										$notification['title'] = $dataNoti['title'];
										$notification['order_id'] = $dataNoti['order_id'];
										$notification['random_id'] = $dataNoti['random_id'];
										$notification['type'] = $dataNoti['type'];
										$notify = $this->notificationlibv_3->sendDeliveryNotification($dataArr, $notification,"ringing");
										log_message("error", "Notification result->".$notify);
									}
								}
							} 
						   return true;
						}
				    }					   
			    }else{
					echo "failed";
				}
			 }
		   }
		}else{
			$condition1 = array('ordermaster.deliveryBoyCode'=>"",'ordermaster.orderStatus'=>"PND",'ordermaster.isActive'=>1,'ordermaster.isDelete'=>0);
			$orderBy1 = array("ordermaster.id"=>"ASC");
			$extraCondition = " (ordermaster.deliveryBoyCode is null or ordermaster.deliveryBoyCode='') && (ordermaster.deliverySlotCode='DSLT_1' || ordermaster.deliverySlotCode IS NULL || ordermaster.deliverySlotCode='') && (ordermaster.defaultStatus IS NULL or ordermaster.defaultStatus='')";
			$getPendingOrders = $this->GlobalModel->selectQuery('ordermaster.*','ordermaster',$condition1,$orderBy1,array(),array(),array(),"","",array(),$extraCondition);
			//log_message("error", "query ->".$this->db->last_query());
			if($getPendingOrders){
			   foreach($getPendingOrders->result_array() as $order_data)
			   {				
				//$order_data = $getPendingOrders->result()[0];
				$orderCode=$order_data["code"];
			    $cityCode=$order_data["cityCode"];
				$latitude="";
				$longitude="";
				$cond = array('vegitablestorelocation.code' => "STR_1");
				$query = $this->GlobalModel->selectQuery('vegitablestorelocation.*', 'vegitablestorelocation', $cond, array(), array(), array(), array(), "", "", array(),"");
				  if($query){
					  $get_data = $query->result()[0];
					  $latitude = $get_data->latitude;
					  $longitude =$get_data->longitude;			  
				  }
				if(($latitude!="" && $latitude!=null) && ($longitude!="" && $longitude!=null)) {
				   $this->db->select('deliveryBoyActiveOrder.*,usermaster.deliveryType,ROUND(6371 * acos(cos(radians('. $latitude .')) * cos(radians(usermaster.latitude)) * cos(radians(usermaster.longitude) - radians('. $longitude .')) + sin(radians('. $latitude . ')) * sin(radians(usermaster.latitude)))) AS distance');
				   $this->db->from("deliveryBoyActiveOrder");	
				   $this->db->join('usermaster', 'deliveryBoyActiveOrder.deliveryBoyCode=usermaster.code', 'inner');
				   $this->db->where('deliveryBoyActiveOrder.loginStatus', 1);
				   $this->db->where('deliveryBoyActiveOrder.orderCount',0);
				   $this->db->where('usermaster.isActive',1);
				   $this->db->where('deliveryBoyActiveOrder.isActive',1);
				   $this->db->where('usermaster.role','DLB');
				   $this->db->where('usermaster.deliveryType','food');
				   $this->db->where('usermaster.cityCode',$cityCode);
				   $this->db->where('usermaster.latitude IS NOT NULL');
                   $this->db->where('usermaster.longitude IS NOT NULL');
				   $this->db->having('distance <', 15);
				   $this->db->order_by('distance','ASC');
				   $this->db->limit(1);
				   $result = $this->db->get();
				   if($result && $result->num_rows() > 0){				   
					   $row_count = $result->num_rows(); 
					   $rows=$result->result_array();
					   //shuffle($rows);
					   $is_order_assigned = false;
					   foreach($rows as $row){
						    $actCode=$row["code"];
					        $deliveryBoyCode=$row["deliveryBoyCode"];
                           //check if deliveryBoy released the selected order
							 $hasReleadedOrder = $this->GlobalModel->hasDeliveryboyReleasedOrder($deliveryBoyCode, $orderCode);		
							 if ($hasReleadedOrder == false) 
							 {
								$dataUpCnt['orderCount'] = 1;
								$dataUpCnt['orderCode'] = $orderCode;
								$dataUpCnt['orderType'] = 'vegetable';
								$dataUpCnt['editDate'] = date('Y-m-d H:i:s');
								$dataUpCnt['editIP'] = $_SERVER['REMOTE_ADDR'];
								$resultUpdateDB = $this->GlobalModel->doEdit($dataUpCnt, 'deliveryBoyActiveOrder', $actCode);
								if($resultUpdateDB != 'false')
								{
									log_message("error", "ASSIGN VEGEE ORDER => Order code ->".$orderCode." to delivery boy -> ".$deliveryBoyCode." time->".date('Y-m-d H:i:s'));
									//$dataUpdateDb['orderStatus'] = "PLC"; 
									$dataUpdateDb['deliveryBoyCode'] = $deliveryBoyCode;
									$dataUpdateDb['editDate'] = date('Y-m-d H:i:s');
									$dataUpdateDb['editIP'] = $_SERVER['REMOTE_ADDR'];
									$resultUpdateDB = $this->GlobalModel->doEdit($dataUpdateDb, 'ordermaster', $orderCode);	
									
									//send notificaiton to delivery boy
									$random = rand(0, 999);
									$dataNoti = array("title" => 'New Order!', "message" => 'You have assigned new order.', "order_id" => $orderCode, "random_id" => $random, 'type' => 'order');
									$delBoy = $this->GlobalModel->selectQuery("usermaster.firebase_id","usermaster",array("usermaster.code"=>$deliveryBoyCode));
									if($delBoy){
										$firebaseId =$delBoy->result_array()[0]['firebase_id'];
										if($firebaseId!="" && $firebaseId!=null){
											$DeviceIdsArr = [$firebaseId];
											$dataArr = array();
											$dataArr['device_id'] = $DeviceIdsArr;
											$dataArr['message'] = $dataNoti['message']; //Message which you want to send
											$dataArr['title'] = $dataNoti['title'];
											$dataArr['order_id'] = $dataNoti['order_id'];
											$dataArr['random_id'] = $dataNoti['random_id'];
											$dataArr['type'] = $dataNoti['type'];
											$notification['device_id'] = $DeviceIdsArr;
											$notification['message'] = $dataNoti['message']; //Message which you want to send
											$notification['title'] = $dataNoti['title'];
											$notification['order_id'] = $dataNoti['order_id'];
											$notification['random_id'] = $dataNoti['random_id'];
											$notification['type'] = $dataNoti['type'];
							 
											$notify = $this->notificationlibv_3->sendDeliveryNotification($dataArr, $notification,"ringing");
											log_message("error", "Notification result->".$notify);
										}
									}
								}
						   }						   
					   }
				   }else{
					   echo "failed";
				   }				   
				}
			 }
		   }			
		}		
	}
	
	public function assignDeliveryBoy_old(){
		log_message("error", "Auto assign DB CRON_JOB_EXECUTED");
		$condition1 = array('vendorordermaster.isActive' => "1",'vendorordermaster.isDelete' => "0",'vendorordermaster.orderStatus' => "PND");
		$orderBy1 = array('vendorordermaster.id' => "ASC");
		$extraCondition1 = " (vendorordermaster.deliveryBoyCode IS NULL or vendorordermaster.deliveryBoyCode='')";
		$extraCondition1 .= " and (vendorordermaster.defaultStatus IS NULL or vendorordermaster.defaultStatus='')";
		$getPendingOrders = $this->GlobalModel->selectQuery('vendorordermaster.*,vendor.cityCode,vendor.latitude,vendor.longitude', 'vendorordermaster', $condition1, $orderBy1, array('vendor'=>'vendor.code=vendorordermaster.vendorCode'), array('vendor'=>'inner'),array(), "", "", array(), $extraCondition1);
		if($getPendingOrders) {
		    foreach ($getPendingOrders->result_array() as $r) {
				$cityCode=$r["cityCode"];
				$orderCode=$r["code"];
				$latitude=$r["latitude"];
				$longitude=$r["longitude"];
				$join['usermaster'] = "deliveryBoyActiveOrder.deliveryBoyCode=usermaster.code";
				$joinType['usermaster'] = "inner";
				$condition = array('deliveryBoyActiveOrder.loginStatus' => 1, 'deliveryBoyActiveOrder.orderCount' => '0', 'usermaster.isActive' => 1,'deliveryBoyActiveOrder.isActive' => 1, 'usermaster.role' => 'DLB','usermaster.deliveryType'=>'food','usermaster.cityCode'=>$cityCode); 
				$orderBy = array('distance' => "ASC");
				$checkData = $this->GlobalModel->selectQuery('deliveryBoyActiveOrder.*,usermaster.deliveryType,ROUND(6371 * acos(cos(radians('. $latitude .')) * cos(radians(usermaster.latitude)) * cos(radians(usermaster.longitude) - radians('. $longitude .')) + sin(radians('. $latitude . ')) * sin(radians(usermaster.latitude)))) AS distance', 'deliveryBoyActiveOrder', $condition,$orderBy,$join,$joinType,array(), 1, "", array(), "");
				if($checkData){
					foreach ($checkData->result_array() as $item) {
					  $actCode=$item["code"];
					  $deliveryBoyCode=$item["deliveryBoyCode"];					  
					  $hasReleadedOrder = $this->GlobalModel->hasDeliveryboyReleasedOrder($deliveryBoyCode, $orderCode);
					  if ($hasReleadedOrder == false) {
							$dataUpCnt['orderCount'] = 1;
							$dataUpCnt['orderCode'] = $orderCode;
							$dataUpCnt['orderType'] = 'food';
							$dataUpCnt['editDate'] = date('Y-m-d H:i:s');
							$dataUpCnt['editIP'] = $_SERVER['REMOTE_ADDR'];
							$resultUpdateDB = $this->GlobalModel->doEdit($dataUpCnt, 'deliveryBoyActiveOrder', $actCode);
							if ($resultUpdateDB != 'false') {
								log_message("error", "ASSIGN FOOD ORDER => Order code ->".$orderCode." to delivery boy -> ".$deliveryBoyCode." time->".date('Y-m-d H:i:s'));
								//$dataUpdateDb['orderStatus'] = "PLC";
								$dataUpdateDb['deliveryBoyCode'] = $deliveryBoyCode;
								$dataUpdateDb['editDate'] = date('Y-m-d H:i:s');
								$dataUpdateDb['editIP'] = $_SERVER['REMOTE_ADDR'];
								$resultUpdateDB = $this->GlobalModel->doEdit($dataUpdateDb, 'vendorordermaster', $orderCode); 
								//send notification to the delivery boy
								$random = rand(0, 999);
								$dataNoti = array("title" => 'New Order!', "message" => 'New Order Successfully Placed', "order_id" => $orderCode, "random_id" => $random, 'type' => 'order');
								$delBoy = $this->GlobalModel->selectQuery("usermaster.firebase_id", "usermaster", array("usermaster.code" => $deliveryBoyCode));
								if ($delBoy) {
									$firebaseId = $delBoy->result_array()[0]['firebase_id'];
									if ($firebaseId != "" && $firebaseId != null) {
										$DeviceIdsArr = [$firebaseId];
										$dataArr = array();
										$dataArr['device_id'] = $DeviceIdsArr;
										$dataArr['message'] = $dataNoti['message']; //Message which you want to send
										$dataArr['title'] = $dataNoti['title'];
										$dataArr['order_id'] = $dataNoti['order_id'];
										$dataArr['random_id'] = $dataNoti['random_id'];
										$dataArr['type'] = $dataNoti['type'];
										$notification['device_id'] = $DeviceIdsArr;
										$notification['message'] = $dataNoti['message']; //Message which you want to send
										$notification['title'] = $dataNoti['title'];
										$notification['order_id'] = $dataNoti['order_id'];
										$notification['random_id'] = $dataNoti['random_id'];
										$notification['type'] = $dataNoti['type'];
										$notify = $this->notificationlibv_3->sendDeliveryNotification($dataArr, $notification,"ringing");
									    log_message("error", "Notification result->".$notify);
									}
								}
							} 
						}
					}
				}else{
					echo "failed";
				}
			}
		}else{
			$condition1 = array('ordermaster.deliveryBoyCode'=>"",'ordermaster.orderStatus'=>"PND",'ordermaster.isActive'=>1,'ordermaster.isDelete'=>0);
			$orderBy1 = array("ordermaster.id"=>"ASC");
			$extraCondition = " (ordermaster.deliveryBoyCode is null or ordermaster.deliveryBoyCode='') && (ordermaster.deliverySlotCode='DSLT_1' || ordermaster.deliverySlotCode IS NULL || ordermaster.deliverySlotCode='') && (ordermaster.defaultStatus IS NULL or ordermaster.defaultStatus='')";
			$getPendingOrders = $this->GlobalModel->selectQuery('ordermaster.*','ordermaster',$condition1,$orderBy1,array(),array(),array(),"","",array(),$extraCondition);
			if($getPendingOrders){
				foreach ($getPendingOrders->result_array() as $r) {
					$cityCode=$r["cityCode"];
					$orderCode=$r["code"];
					$latitude="16.702972269583295";
					$longitude="74.24128320572082";
					$join['usermaster'] = "deliveryBoyActiveOrder.deliveryBoyCode=usermaster.code";
					$joinType['usermaster'] = "inner";
					$condition = array('deliveryBoyActiveOrder.loginStatus' => 1, 'deliveryBoyActiveOrder.orderCount' => '0', 'usermaster.isActive' => 1,'deliveryBoyActiveOrder.isActive' => 1, 'usermaster.role' => 'DLB','usermaster.deliveryType'=>'food','usermaster.cityCode'=>$cityCode); 
					$orderBy = array('distance' => "ASC");
					$checkData = $this->GlobalModel->selectQuery('deliveryBoyActiveOrder.*,usermaster.deliveryType,ROUND(6371 * acos(cos(radians('. $latitude .')) * cos(radians(usermaster.latitude)) * cos(radians(usermaster.longitude) - radians('. $longitude .')) + sin(radians('. $latitude . ')) * sin(radians(usermaster.latitude)))) AS distance', 'deliveryBoyActiveOrder', $condition,$orderBy,$join,$joinType,array(), 1, "", array(), "");			
				    if($checkData){
					foreach ($checkData->result_array() as $item) {
					  $actCode=$item["code"];
					  $deliveryBoyCode=$item["deliveryBoyCode"];
					  
					 //check if deliveryBoy released the selected order
					 $hasReleadedOrder = $this->GlobalModel->hasDeliveryboyReleasedOrder($deliveryBoyCode, $orderCode);		
					 if ($hasReleadedOrder == false) 
					 {
						$dataUpCnt['orderCount'] = 1;
						$dataUpCnt['orderCode'] = $orderCode;
						$dataUpCnt['orderType'] = 'vegetable';
						$dataUpCnt['editDate'] = date('Y-m-d H:i:s');
						$dataUpCnt['editIP'] = $_SERVER['REMOTE_ADDR'];
						$resultUpdateDB = $this->GlobalModel->doEdit($dataUpCnt, 'deliveryBoyActiveOrder', $actCode);
						if($resultUpdateDB != 'false')
						{
							log_message("error", "ASSIGN VEGEE ORDER => Order code ->".$orderCode." to delivery boy -> ".$deliveryBoyCode." time->".date('Y-m-d H:i:s'));
							//$dataUpdateDb['orderStatus'] = "PLC"; 
							$dataUpdateDb['deliveryBoyCode'] = $deliveryBoyCode;
							$dataUpdateDb['editDate'] = date('Y-m-d H:i:s');
							$dataUpdateDb['editIP'] = $_SERVER['REMOTE_ADDR'];
							$resultUpdateDB = $this->GlobalModel->doEdit($dataUpdateDb, 'ordermaster', $orderCode);	
							
							//send notificaiton to delivery boy
							$random = rand(0, 999);
							$dataNoti = array("title" => 'New Order!', "message" => 'New Order Successfully Placed', "order_id" => $orderCode, "random_id" => $random, 'type' => 'order');
							$delBoy = $this->GlobalModel->selectQuery("usermaster.firebase_id","usermaster",array("usermaster.code"=>$deliveryBoyCode));
							if($delBoy){
								$firebaseId =$delBoy->result_array()[0]['firebase_id'];
								if($firebaseId!="" && $firebaseId!=null){
									$DeviceIdsArr = [$firebaseId];
									$dataArr = array();
									$dataArr['device_id'] = $DeviceIdsArr;
									$dataArr['message'] = $dataNoti['message']; //Message which you want to send
									$dataArr['title'] = $dataNoti['title'];
									$dataArr['order_id'] = $dataNoti['order_id'];
									$dataArr['random_id'] = $dataNoti['random_id'];
									$dataArr['type'] = $dataNoti['type'];
									$notification['device_id'] = $DeviceIdsArr;
									$notification['message'] = $dataNoti['message']; //Message which you want to send
									$notification['title'] = $dataNoti['title'];
									$notification['order_id'] = $dataNoti['order_id'];
									$notification['random_id'] = $dataNoti['random_id'];
									$notification['type'] = $dataNoti['type'];
					 
									$notify = $this->notificationlibv_3->sendDeliveryNotification($dataArr, $notification,"ringing");
									log_message("error", "Notification result->".$notify);
								}
							}
						}
					  } 
					}
				  }
				}				
			}						
		}		
	}
	

	public function assignDeliveryBoyOld()
	{
		die;
		log_message("error", "Auto assign DB CRON_JOB_EXECUTED");
		//get all delivery boys to assign order 'usermaster.deliveryType' => 'food',
		$join['usermaster'] = "deliveryBoyActiveOrder.deliveryBoyCode=usermaster.code";
		$joinType['usermaster'] = "inner";
		$condition = array('deliveryBoyActiveOrder.loginStatus' => 1, 'deliveryBoyActiveOrder.orderCount' => '0', 'usermaster.isActive' => 1,'deliveryBoyActiveOrder.isActive' => 1, 'usermaster.role' => 'DLB','usermaster.deliveryType'=>'food'); 
		$checkData = $this->GlobalModel->selectQuery('deliveryBoyActiveOrder.*,usermaster.deliveryType,usermaster.cityCode,usermaster.latitude,usermaster.longitude', 'deliveryBoyActiveOrder', $condition,array(),$join,$joinType);
		if ($checkData) {
			foreach ($checkData->result_array() as $d) { 
				$deliveryBoyCode = $d['deliveryBoyCode']; 
				$deliveryBoyCity = $d['cityCode']; 
				$actCode = $d['code'];
				if ($d['orderCode'] == "" || $d['orderCode'] == null ) {
					$limit = 1;
					$condition1 = array('vendorordermaster.isActive' => "1",'vendorordermaster.isDelete' => "0",'vendorordermaster.orderStatus' => "PND","vendor.cityCode"=>$deliveryBoyCity);
					$orderBy1 = array('vendorordermaster.id' => "ASC");
					$extraCondition1 = " (vendorordermaster.deliveryBoyCode IS NULL or vendorordermaster.deliveryBoyCode='')";
					$extraCondition1 .= " and (vendorordermaster.defaultStatus IS NULL or vendorordermaster.defaultStatus='')";
					$getPendingOrders = $this->GlobalModel->selectQuery('vendorordermaster.*', 'vendorordermaster', $condition1, $orderBy1, array('vendor'=>'vendor.code=vendorordermaster.vendorCode'), array('vendor'=>'inner'),array(), $limit, "", array(), $extraCondition1);
					if ($getPendingOrders && $d['deliveryType']="food") {						
						foreach ($getPendingOrders->result_array() as $r) {
							$orderCode = $r['code'];

							//check if deliveryBoy released the selected order
							$hasReleadedOrder = $this->GlobalModel->hasDeliveryboyReleasedOrder($deliveryBoyCode, $orderCode);
                            
							if ($hasReleadedOrder == false) {
								$dataUpCnt['orderCount'] = 1;
								$dataUpCnt['orderCode'] = $orderCode;
								$dataUpCnt['orderType'] = 'food';
								$dataUpCnt['editDate'] = date('Y-m-d H:i:s');
								$dataUpCnt['editIP'] = $_SERVER['REMOTE_ADDR'];
								$resultUpdateDB = $this->GlobalModel->doEdit($dataUpCnt, 'deliveryBoyActiveOrder', $actCode);
								if ($resultUpdateDB != 'false') {
								    log_message("error", "ASSIGN FOOD ORDER => Order code ->".$orderCode." to delivery boy -> ".$deliveryBoyCode." time->".date('Y-m-d H:i:s'));
									//$dataUpdateDb['orderStatus'] = "PLC";
									$dataUpdateDb['deliveryBoyCode'] = $deliveryBoyCode;
									$dataUpdateDb['editDate'] = date('Y-m-d H:i:s');
									$dataUpdateDb['editIP'] = $_SERVER['REMOTE_ADDR'];
									$resultUpdateDB = $this->GlobalModel->doEdit($dataUpdateDb, 'vendorordermaster', $orderCode); 
									//send notification to the delivery boy
									$random = rand(0, 999);
									$dataNoti = array("title" => 'New Order!', "message" => 'New Order Successfully Placed', "order_id" => $orderCode, "random_id" => $random, 'type' => 'order');
									$delBoy = $this->GlobalModel->selectQuery("usermaster.firebase_id", "usermaster", array("usermaster.code" => $deliveryBoyCode));
									if ($delBoy) {
										$firebaseId = $delBoy->result_array()[0]['firebase_id'];
										if ($firebaseId != "" && $firebaseId != null) {
											$DeviceIdsArr = [$firebaseId];
											$dataArr = array();
											$dataArr['device_id'] = $DeviceIdsArr;
											$dataArr['message'] = $dataNoti['message']; //Message which you want to send
											$dataArr['title'] = $dataNoti['title'];
											$dataArr['order_id'] = $dataNoti['order_id'];
											$dataArr['random_id'] = $dataNoti['random_id'];
											$dataArr['type'] = $dataNoti['type'];
											$notification['device_id'] = $DeviceIdsArr;
											$notification['message'] = $dataNoti['message']; //Message which you want to send
											$notification['title'] = $dataNoti['title'];
											$notification['order_id'] = $dataNoti['order_id'];
											$notification['random_id'] = $dataNoti['random_id'];
											$notification['type'] = $dataNoti['type'];
											$notify = $this->notificationlibv_3->sendDeliveryNotification($dataArr, $notification,"ringing");
										}
									}
								} 
							}
						}
					}
					else 
					{
						$condition1 = array('ordermaster.cityCode'=>$deliveryBoyCity,'ordermaster.deliveryBoyCode'=>"",'ordermaster.orderStatus'=>"PND",'ordermaster.isActive'=>1,'ordermaster.isDelete'=>0);
						$orderBy1 = array("ordermaster.id"=>"ASC");
						$extraCondition = " (ordermaster.deliveryBoyCode is null or ordermaster.deliveryBoyCode='') && (ordermaster.deliverySlotCode='DSLT_1' || ordermaster.deliverySlotCode IS NULL || ordermaster.deliverySlotCode='') && (ordermaster.defaultStatus IS NULL or ordermaster.defaultStatus='')";
						$getPendingOrders = $this->GlobalModel->selectQuery('ordermaster.*','ordermaster',$condition1,$orderBy1,array(),array(),array(),$limit,"",array(),$extraCondition);
						
						//echo $this->db->last_query();
						if($getPendingOrders){
							
							foreach($getPendingOrders->result_array() as $r){
								$orderCode = $r['code'];
								
								//check if deliveryBoy released the selected order
								$hasReleadedOrder = $this->GlobalModel->hasDeliveryboyReleasedOrder($deliveryBoyCode, $orderCode);
								
								if ($hasReleadedOrder == false) 
								{
									$dataUpCnt['orderCount'] = 1;
									$dataUpCnt['orderCode'] = $orderCode;
									$dataUpCnt['orderType'] = 'vegetable';
									$dataUpCnt['editDate'] = date('Y-m-d H:i:s');
									$dataUpCnt['editIP'] = $_SERVER['REMOTE_ADDR'];
									$resultUpdateDB = $this->GlobalModel->doEdit($dataUpCnt, 'deliveryBoyActiveOrder', $actCode);
									if($resultUpdateDB != 'false')
									{
									    log_message("error", "ASSIGN VEGEE ORDER => Order code ->".$orderCode." to delivery boy -> ".$deliveryBoyCode." time->".date('Y-m-d H:i:s'));
										//$dataUpdateDb['orderStatus'] = "PLC"; 
										$dataUpdateDb['deliveryBoyCode'] = $deliveryBoyCode;
										$dataUpdateDb['editDate'] = date('Y-m-d H:i:s');
										$dataUpdateDb['editIP'] = $_SERVER['REMOTE_ADDR'];
										$resultUpdateDB = $this->GlobalModel->doEdit($dataUpdateDb, 'ordermaster', $orderCode);	
										
										//send notificaiton to delivery boy
										$random = rand(0, 999);
										$dataNoti = array("title" => 'New Order!', "message" => 'New Order Successfully Placed', "order_id" => $orderCode, "random_id" => $random, 'type' => 'order');
										$delBoy = $this->GlobalModel->selectQuery("usermaster.firebase_id","usermaster",array("usermaster.code"=>$deliveryBoyCode));
										if($delBoy){
											$firebaseId =$delBoy->result_array()[0]['firebase_id'];
											if($firebaseId!="" && $firebaseId!=null){
												$DeviceIdsArr = [$firebaseId];
												$dataArr = array();
												$dataArr['device_id'] = $DeviceIdsArr;
												$dataArr['message'] = $dataNoti['message']; //Message which you want to send
												$dataArr['title'] = $dataNoti['title'];
												$dataArr['order_id'] = $dataNoti['order_id'];
												$dataArr['random_id'] = $dataNoti['random_id'];
												$dataArr['type'] = $dataNoti['type'];
												$notification['device_id'] = $DeviceIdsArr;
												$notification['message'] = $dataNoti['message']; //Message which you want to send
												$notification['title'] = $dataNoti['title'];
												$notification['order_id'] = $dataNoti['order_id'];
												$notification['random_id'] = $dataNoti['random_id'];
												$notification['type'] = $dataNoti['type'];
							     
												$notify = $this->notificationlibv_3->sendDeliveryNotification($dataArr, $notification,"ringing");
												log_message("error", "Notification result->".$notify);
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
		else
		{
		    echo "failed";
		}
	}

	public function updateVendorServiceablity()
	{
		log_message("error","Vendor Service update");
		$dayofweek = strtolower(date('l'));
		$currtime = date('H:i:s');
		$condition = array('vendorhours.weekDay' => $dayofweek); //,'vendor.isServiceable'=>'0'		 
		$extraCondition = " (CAST( '" . $currtime . "' AS time ) BETWEEN `vendorhours`.`fromTime` AND `vendorhours`.`toTime` OR ( NOT CAST( '" . $currtime . "' AS time ) BETWEEN `vendorhours`.`toTime` AND `vendorhours`.`fromTime` AND `vendorhours`.`fromTime` > `vendorhours`.`toTime`))";
		$joinType['vendorhours'] = "inner";
		$join["vendorhours"] = "vendor.code=vendorhours.vendorCode";
		$like =  $orderBy = array();
		$groupBy = array("vendorhours.vendorCode");
		$Records = $this->GlobalModel->selectQuery("vendorhours.vendorCode,vendor.isServiceable", "vendor", $condition, $orderBy, $join, $joinType, $like, "", "", $groupBy, $extraCondition);
		//echo $this->db->last_query();
		$inVendorCode = "";
		if ($Records) {
			$Records = $Records->result_array();  
			//print_r($Records);
			//exit; 
			foreach ($Records as $r) {
				$inVendorCode != "" && $inVendorCode .= ",";
				$inVendorCode .= "'" . $r['vendorCode'] . "'";
			}
			if ($inVendorCode != "") { 
				$this->db->query("update vendor set isServiceable=1 where code in (" . $inVendorCode . ") and manualIsServiceable=1");
				$this->db->query("update vendor set isServiceable=0 where code not in (" . $inVendorCode . ") and manualIsServiceable=1");
			}
		} else { 
			//  $condition = array('vendorhours.weekDay'=>$dayofweek,'vendor.isServiceable'=>'1');	 
			//  $Records = $this->GlobalModel->selectQuery("vendorhours.vendorCode,vendor.isServiceable","vendor",$condition,$orderBy,$join,$joinType,$like,"","",$groupBy,$extraCondition);
			//  if($Records){
			//      $Records =$Records->result_array();
			//      foreach($Records as $r){ 
			// 				$inVendorCode != "" && $inVendorCode .= ",";
			// 				$inVendorCode .= "'".$r['vendorCode']."'"; 
			// 			}
			// 			if($inVendorCode!=""){
			//     $this->db->query("update vendor set isServiceable=0 where code not in (".$inVendorCode.") and manualIsServiceable=1");
			//   }	
			//  }
		}
	
    }
	
	public function changeDeliveryBoyStatus()
	{
		log_message("error","Auto login DB CRON JOB EXECUTED");
		$currtime = date('H:i:s');
		$condition=array("usermaster.isActive" => 1);
		$orderby=array('usermaster' . '.id' => 'DESC');
		$joinType= array("deliveryBoyActiveOrder" => "inner");
		$join = array("deliveryBoyActiveOrder" => "usermaster.code=deliveryBoyActiveOrder.deliveryBoyCode");
		$extraCondition = "(usermaster.isDelete=0 OR usermaster.isDelete IS NULL) AND (deliveryBoyActiveOrder.loginStatus='0')";
		$extraCondition .=" AND (CAST( '" . $currtime . "' AS time ) BETWEEN `usermaster`.`availableStartTime` AND `usermaster`.`availableEndTime`)";
		$Records = $this->GlobalModel->selectQuery("usermaster.*,deliveryBoyActiveOrder.loginStatus,deliveryBoyActiveOrder.deliveryBoyCode", "usermaster", $condition,$orderby, $join, $joinType, array(), "", "", array(), $extraCondition);
		if($Records)
		{
			foreach($Records->result_array() as $r)
			{
				$DBcode=$r['deliveryBoyCode'];
				$data=array('loginStatus'=>1);
				$this->GlobalModel->doEditWithField($data,'deliveryBoyActiveOrder','deliveryBoyCode',$DBcode); 
			}
			log_message("error","No of auto login DB : ".COUNT($Records->result_array()));
		} 
	} 
}