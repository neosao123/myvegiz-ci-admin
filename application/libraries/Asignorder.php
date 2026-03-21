<?php
class Assignorder {
    private $_CI;
    public function __construct()
    {
        parent::__construct();
        $this->_CI = & get_instance();
        $this->_CI->load->model('GlobalModel');
        $this->_CI->load->library('notificationlibv_3');
    }

    function allocate_delivery_boy_to_order(string $orderCode)  {
        //get order details and restaurant code
        $condition1 = array('vendorordermaster.orderStatus' => "PND","vendorordermaster.code"=>$orderCode);
        $orderBy1 = array('vendorordermaster.id' => "ASC");
        $extraCondition1 = " (vendorordermaster.deliveryBoyCode IS NULL or vendorordermaster.deliveryBoyCode='')";
        $order_result = $this->_CI->GlobalModel->selectQuery('vendorordermaster.*', 'vendorordermaster', $condition1, $orderBy1, array(), array(), array(), $limit, "", array(), $extraCondition1);
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
                    $query = "SELECT usermaster.code, ( 3959 * acos( cos( radians(".$restaurant_lat.") ) * cos( radians( usermaster.latitude ) ) ";
                    $query .= " * cos( radians( usermaster.longitude ) - radians(".$restaurant_long.") ) + sin( radians(".$restaurant_lat.") ) * sin(radians(usermaster.latitude)) ) ) AS distance, ";
                    $query .= "FROM usermaster";
                    $query .= "INNER JOIN deliveryBoyActiveOrder ON deliveryBoyActiveOrder.deliveryBoyCode = usermaster.code";                    
                    $query .= "WHERE usermaster.role='DLB'";
                    $query .= " AND usermaster.isActive='1'";
                    $query .= " AND usermaster.deliveryType='food'";
                    $query .= " AND deliveryBoyActiveOrder.loginStatus='1'";
                    $query .= " AND deliveryBoyActiveOrder.orderCount='0'";
                    $query .= " AND deliveryBoyActiveOrder.isActive='1'";                    
                    $query .= " HAVING distance < 5 ORDER BY distance";

                    $query_result = $this->_CI->GlobalModel->directQuery($query);
                    if(!empty($query_result)>0) {
                        $row_count = count($query_result);                        
                        shuffle($query_result)
                        //set veriable for order assigned
                        $is_order_assigned = false;
                        //loop throuhj all delivery persons
                        foreach($query_result as $row) {
                            if(!$is_order_assigned) {                                
                                $deliveryBoyCode = $row['code'];
                                //check selected delivery person has already released order
                                $hasReleadedOrder = $this->_CI->GlobalModel->hasDeliveryboyReleasedOrder($deliveryBoyCode, $orderCode);
                                if (!$hasReleadedOrder) {
                                    $dataUpCnt['orderCount'] = 1;
                                    $dataUpCnt['orderCode'] = $orderCode;
                                    $dataUpCnt['orderType'] = 'food';
                                    $dataUpCnt['editDate'] = date('Y-m-d H:i:s');								
                                    $resultUpdateDB = $this->GlobalModel->doEdit($dataUpCnt, 'deliveryBoyActiveOrder', $actCode);
                                    if ($resultUpdateDB != 'false') {
                                        $is_order_assigned = true;
                                        log_message("error", "ASSIGN FOOD ORDER => Order code ->".$orderCode." to delivery boy -> ".$deliveryBoyCode." time->".date('Y-m-d H:i:s'));
                                        //set delivery person code to ordermaster
                                        $dataUpdateDb['deliveryBoyCode'] = $deliveryBoyCode;
                                        $dataUpdateDb['editDate'] = date('Y-m-d H:i:s');
                                        $dataUpdateDb['editIP'] = $_SERVER['REMOTE_ADDR'];
                                        $resultUpdateDB = $this->GlobalModel->doEdit($dataUpdateDb, 'vendorordermaster', $orderCode); 
                                        //send notification
                                        $random = rand(0, 999);
                                        $dataNoti = array("title" => 'New Order!', "message" => 'New Order Successfully Placed', "order_id" => $orderCode, "random_id" => $random, 'type' => 'order');
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
                                                $notify = $this->notificationlibv_3->sendDeliveryNotification($dataArr, $notification,"ringing");
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
    }   
}