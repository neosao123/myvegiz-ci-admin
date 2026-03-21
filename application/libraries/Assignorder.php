<?php
class Assignorder { 
    private $_CI;
    public function __construct()
    {
        $this->_CI = & get_instance();
        $this->_CI->load->model('GlobalModel');
        $this->_CI->load->library('notificationlibv_3');
        //$this->_CI->load->database();
    }

    function allocate_delivery_boy_to_order(string $orderCode)  {
		
        //get order details and restaurant code
	  if(strpos($orderCode, "FD") !== false){
        $condition1 = array('vendorordermaster.orderStatus' => "PND","vendorordermaster.code"=>$orderCode);
        $orderBy1 = array('vendorordermaster.id' => "ASC");
        $extraCondition1 = " (vendorordermaster.deliveryBoyCode IS NULL or vendorordermaster.deliveryBoyCode='')";
        $order_result = $this->_CI->GlobalModel->selectQuery('vendorordermaster.*', 'vendorordermaster', $condition1, $orderBy1, array(), array(), array(), "", "", array(), $extraCondition1);
        if($order_result) {            
            $order_data = $order_result->result()[0];
            $restaurant_code = $order_data->vendorCode;
            //get restaurant lat & long
            $restaurant_result = $this->_CI->GlobalModel->selectQuery('vendor.latitude,vendor.longitude','vendor',['vendor.code'=>$restaurant_code]);
            if($restaurant_result) {
                $restaurant_data = $restaurant_result->result()[0];
                $restaurant_lat = $restaurant_data->latitude;
                $restaurant_long = $restaurant_data->longitude;
                //check restaurant has lat and long
                if(($restaurant_lat!="" && $restaurant_long!=null) && ($restaurant_long!="" && $restaurant_long!=null)) {
                    // get nearest delivery boys list
                    //for miles se 3959 for kilometers use 6371
                    //we are using 6371
                    $query = "SELECT deliveryBoyActiveOrder.deliveryBoyCode,deliveryBoyActiveOrder.code as actCode,usermaster.code, ( 6371 * acos( cos( radians(".$restaurant_lat.") ) * cos( radians( usermaster.latitude ) ) ";
                    $query .= " * cos( radians( usermaster.longitude ) - radians(".$restaurant_long.") ) + sin( radians(".$restaurant_lat.") ) * sin(radians(usermaster.latitude)) ) ) AS distance ";
                    $query .= " FROM usermaster";
                    $query .= " INNER JOIN deliveryBoyActiveOrder ON deliveryBoyActiveOrder.deliveryBoyCode = usermaster.code";                    
                    $query .= " WHERE usermaster.role='DLB'";
                    $query .= " AND usermaster.isActive='1'";
                    $query .= " AND usermaster.deliveryType='food'";
                    $query .= " AND deliveryBoyActiveOrder.loginStatus='1'";
                    $query .= " AND deliveryBoyActiveOrder.orderCount='0'";
                    $query .= " AND deliveryBoyActiveOrder.isActive='1'";
                    $query .= " AND usermaster.latitude IS NOT NULL";
                    $query .= " AND usermaster.longitude IS NOT NULL";                    
                    $query .= " HAVING distance < 15 ORDER BY distance ASC LIMIT 1";
                    $query_result = $this->_CI->GlobalModel->directQuery($query);
                    log_message("error",$this->_CI->db->last_query());
					if($query_result) {
                        $row_count = count($query_result);                         
                        //shuffle($query_result);
                        //set veriable for order assigned
                        $is_order_assigned = false;
                        //loop throuhj all delivery persons
                        foreach($query_result as $row) {
                            if(!$is_order_assigned) {                                
                                $deliveryBoyCode = $row['deliveryBoyCode'];
								log_message("error", "deliveryBoyCode " . $deliveryBoyCode);
								$actCode=$row["actCode"];
                                //check selected delivery person has already released order
                                $hasReleadedOrder = $this->_CI->GlobalModel->hasDeliveryboyReleasedOrder($deliveryBoyCode, $orderCode);
                                log_message("error", "Realeased Order Result " . $hasReleadedOrder);
								if ($hasReleadedOrder==false) {
                                    $dataUpCnt['orderCount'] = 1;
                                    $dataUpCnt['orderCode'] = $orderCode;
                                    $dataUpCnt['orderType'] = 'food';
                                    $dataUpCnt['editDate'] = date('Y-m-d H:i:s');								
                                    $resultUpdateDB = $this->_CI->GlobalModel->doEdit($dataUpCnt, 'deliveryBoyActiveOrder', $actCode);
                                    if ($resultUpdateDB != 'false') {
                                        $is_order_assigned = true;
                                        log_message("error", "ASSIGN FOOD ORDER => Order code ->".$orderCode." to delivery boy -> ".$deliveryBoyCode." time->".date('Y-m-d H:i:s'));
                                        //set delivery person code to ordermaster
                                        $dataUpdateDb['deliveryBoyCode'] = $deliveryBoyCode;
                                        $dataUpdateDb['editDate'] = date('Y-m-d H:i:s');
                                        $dataUpdateDb['editIP'] = $_SERVER['REMOTE_ADDR'];
                                        $resultUpdateDB = $this->_CI->GlobalModel->doEdit($dataUpdateDb, 'vendorordermaster', $orderCode); 
                                       
									   //send notification
                                        $random = rand(0, 999);
                                        $dataNoti = array("title" => 'New Order!', "message" => 'You have assigned new order.', "order_id" => $orderCode, "random_id" => $random, 'type' => 'order');
                                        $delBoy = $this->_CI->GlobalModel->selectQuery("usermaster.firebase_id", "usermaster", array("usermaster.code" => $deliveryBoyCode));
                                        if ($delBoy) {
                                            $firebaseId = $delBoy->result_array()[0]['firebase_id'];
                                            if ($firebaseId != "" && $firebaseId != null) {
                                                $DeviceIdsArr = [$firebaseId];
                                                $dataArr = array();
                                                $dataArr['device_id'] = $DeviceIdsArr;
                                                $dataArr['message'] = $dataNoti['message']; 
                                                $dataArr['title'] = $dataNoti['title'];
                                                $dataArr['order_id'] = $dataNoti['order_id'];
                                                $dataArr['random_id'] = $dataNoti['random_id'];
                                                $dataArr['type'] = $dataNoti['type'];
                                                $notification['device_id'] = $DeviceIdsArr;
                                                $notification['message'] = $dataNoti['message'];
                                                $notification['title'] = $dataNoti['title'];
                                                $notification['order_id'] = $dataNoti['order_id'];
                                                $notification['random_id'] = $dataNoti['random_id'];
                                                $notification['type'] = $dataNoti['type'];
                                                $notify = $this->_CI->notificationlibv_3->sendDeliveryNotification($dataArr, $notification,"ringing");
                                                
												log_message("error", "Notification result -> " . json_encode($notify));
											}
                                        }
                                        return true;
                                    }
                                }
                            }
                        }                    
                    }
                }
            }
         }
        return false;
	  }else if(strpos($orderCode, "VG") !== false){
		 
		  $latitude="";
		  $longitude="";
		  $cond = array('vegitablestorelocation.code' => "STR_1");
		  $query = $this->_CI->GlobalModel->selectQuery('vegitablestorelocation.*', 'vegitablestorelocation', $cond, array(), array(), array(), array(), "", "", array(),"");
		  if($query){
			  $get_data = $query->result()[0];
			  $latitude = $get_data->latitude;
              $longitude =$get_data->longitude;			  
		  }
		  log_message("error", "Latitude ->".$latitude." longitude -> ".$longitude." time->".date('Y-m-d H:i:s'));
		   $condition1 = array('ordermaster.orderStatus' => "PND","ordermaster.code"=>$orderCode);
		   $orderBy1 = array('ordermaster.id' => "ASC");
		   $extraCondition1 = " (ordermaster.deliveryBoyCode IS NULL or ordermaster.deliveryBoyCode='')";
		   $order_result = $this->_CI->GlobalModel->selectQuery('ordermaster.*', 'ordermaster', $condition1, $orderBy1, array(), array(), array(), "", "", array(), $extraCondition1);
		   if($order_result) {            
				$order_data = $order_result->result()[0];
				$cityCode=  $order_data->cityCode;
				if(($latitude!="" && $latitude!=null) && ($longitude!="" && $longitude!=null)) {
				    $query = "SELECT deliveryBoyActiveOrder.deliveryBoyCode,deliveryBoyActiveOrder.code as actCode,usermaster.code, ROUND(6371 * acos(cos(radians(". $latitude .")) * cos(radians(usermaster.latitude)) * cos(radians(usermaster.longitude) - radians(". $longitude .")) + sin(radians(". $latitude . ")) * sin(radians(usermaster.latitude)))) AS distance ";
                    $query .= " FROM usermaster";
                    $query .= " INNER JOIN deliveryBoyActiveOrder ON deliveryBoyActiveOrder.deliveryBoyCode = usermaster.code";                    
                    $query .= " WHERE usermaster.role='DLB'";
                    $query .= " AND usermaster.isActive='1'";
					$query .= " AND usermaster.cityCode='".$cityCode."'";				
                    $query .= " AND usermaster.deliveryType='food'";
                    $query .= " AND deliveryBoyActiveOrder.loginStatus='1'";
                    $query .= " AND deliveryBoyActiveOrder.orderCount='0'";
                    $query .= " AND deliveryBoyActiveOrder.isActive='1'"; 
                    $query .= " AND usermaster.latitude IS NOT NULL";
                    $query .= " AND usermaster.longitude IS NOT NULL";					
                    $query .= " HAVING distance < 15 ORDER BY distance ASC LIMIT 1";
                    $query_result = $this->_CI->GlobalModel->directQuery($query);
                    
					log_message("error",$this->_CI->db->last_query());
					if($query_result) {
                        $row_count = count($query_result);
					    //shuffle($query_result);
					    foreach($query_result as $row){
						    $actCode=$row["actCode"];
					        $deliveryBoyCode=$row["deliveryBoyCode"];
                            log_message("error", "deliveryBoyCode " . $deliveryBoyCode);
						   //check if deliveryBoy released the selected order
							 $hasReleadedOrder = $this->_CI->GlobalModel->hasDeliveryboyReleasedOrder($deliveryBoyCode, $orderCode);		
							 
							 log_message("error", "Realeased Order Result " . $hasReleadedOrder);
							 if ($hasReleadedOrder == false) 
							 {
								$dataUpCnt['orderCount'] = 1;
								$dataUpCnt['orderCode'] = $orderCode;
								$dataUpCnt['orderType'] = 'vegetable';
								$dataUpCnt['editDate'] = date('Y-m-d H:i:s');
								$dataUpCnt['editIP'] = $_SERVER['REMOTE_ADDR'];
								$resultUpdateDB = $this->_CI->GlobalModel->doEdit($dataUpCnt, 'deliveryBoyActiveOrder', $actCode);
								log_message("error",$this->_CI->db->last_query());
								if($resultUpdateDB != 'false')
								{
									log_message("error", "ASSIGN VEGEE ORDER => Order code ->".$orderCode." to delivery boy -> ".$deliveryBoyCode." time->".date('Y-m-d H:i:s'));
									//$dataUpdateDb['orderStatus'] = "PLC"; 
									$dataUpdateDb['deliveryBoyCode'] = $deliveryBoyCode;
									$dataUpdateDb['editDate'] = date('Y-m-d H:i:s');
									$dataUpdateDb['editIP'] = $_SERVER['REMOTE_ADDR'];
									$resultUpdateDB = $this->_CI->GlobalModel->doEdit($dataUpdateDb, 'ordermaster',$orderCode);	
									log_message("error",$this->_CI->db->last_query());
									//send notificaiton to delivery boy
									$random = rand(0, 999);
									$dataNoti = array("title" => 'New Order!', "message" => 'You have assigned new order.', "order_id" => $orderCode, "random_id" => $random, 'type' => 'order');
									$delBoy = $this->_CI->GlobalModel->selectQuery("usermaster.firebase_id","usermaster",array("usermaster.code"=>$deliveryBoyCode));
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
							 
											$notify = $this->_CI->notificationlibv_3->sendDeliveryNotification($dataArr, $notification,"ringing");
											log_message("error", "Notification result -> " . json_encode($notify));
										}
									}
								}
						   }						   
					   }
				   }				   
			   }
		   }
		 return false;
	  }else{
		  return false;
	  }
    }   
}