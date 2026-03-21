<?php
require(APPPATH . '/libraries/REST_Controller.php');

use Restserver\Libraries\REST_Controller;

class Payment extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('GlobalModel');
        $this->load->library('notificationlibv_3');
        $this->load->library('firestore');
        $this->load->library('assignorder');
    }
	
	public function orders_get(){
		
		$this->assignorder->allocate_delivery_boy_to_order("ORDERFD_0018");
	}

    function computeSignature($ts, $rawBody)
    {
        $signStr = $ts . $rawBody;
        $sec_key=CASHFREE_MODE=="TEST"?CASHFREE_TEST_CLIENT_SECRET:CASHFREE_LIVE_CLIENT_SECRET;
        $computeSig = base64_encode(hash_hmac('sha256', $signStr, $sec_key, true));
        return $computeSig;
    }

    public function process_post()
    {
        
        //$postData = '{"data":{"order":{"order_id":"ORDER37405","order_amount":110,"order_currency":"INR","order_tags":null},"payment":{"cf_payment_id":1489902414,"payment_status":"SUCCESS","payment_amount":110,"payment_currency":"INR","payment_message":"Transaction Successful","payment_time":"2023-01-17T17:49:09+05:30","bank_reference":"67710","auth_id":null,"payment_method":{"card":{"channel":null,"card_number":"XXXXXXXXXXXX1111","card_network":"visa","card_type":"credit_card","card_country":"IN","card_bank_name":"Others"}},"payment_group":"credit_card"},"customer_details":{"customer_name":"Akshay patil","customer_id":"CUST21_6","customer_email":"akshayp460@gmail.com","customer_phone":"8482940592"},"payment_gateway_details":null,"payment_offers":null},"event_time":"2023-01-17T17:49:29+05:30","type":"PAYMENT_SUCCESS_WEBHOOK"}';
        //$this->post();
        $postData = file_get_contents('php://input');
         log_message("error", "Payment webhook Data->". $postData);
         log_message("error", "Payment webhook HEADER->". json_encode(getallheaders()));
         
        $expectedSig = getallheaders()['X-Webhook-Signature'];
        $ts = getallheaders()['X-Webhook-Timestamp'];
        

     //   log_message("error", "webhook header->" . $expectedSig . "  -> " . $ts);
        if (!isset($expectedSig) || !isset($ts)) {
            echo "Bad Request";
            die();
        }

        $computeSig = $this->computeSignature($ts, $postData);
        if ($expectedSig != $computeSig) {
            log_message("error", "webhook signature failed");
            return $this->response(array("status" => "300", "message" => "not match"), 200);
        }

        log_message("error", "webhook captured " . $expectedSig . " ----- " . $computeSig);

        if (!empty($postData)) {
            $resultResponse = json_decode($postData);
            if (property_exists($resultResponse, 'data')) {
                $orderResult = $resultResponse->data->order;
                $orderPayment = $resultResponse->data->payment;

                $orderId = $orderResult->order_id;
				
                $referenceId = $orderPayment->cf_payment_id;
                $orderAmount = $orderPayment->payment_amount;
                $txStatus = $orderPayment->payment_status;
                 
                $timeStamp = date("Y-m-d h:i:s");
                $clientCode="";
                $random = rand(0, 999);
		        $random = date('his') . $random;
		
                $data=array('webhookResponse'=>$postData);
                if (strpos($orderId, "VG") !== false) {
                    	$table = "ordermaster";
            			$clms = "ordermaster.clientCode,ordermaster.code,ordermaster.paymentStatus"; 
            			$cond = array("ordermaster.orderId" =>$orderId);
            			$result = $this->GlobalModel->selectQuery($clms, $table, $cond);
            			log_message("error","payment status=>".$result->result_array()[0]["paymentStatus"]);
            			if ($result != false && $result->result_array()[0]["paymentStatus"]!="PID") {
            			   
            			    $clientCode=$result->result_array()[0]["clientCode"];
            			    $orderCode=$result->result_array()[0]["code"];
                            if($txStatus=="SUCCESS"){
    							$data['orderStatus'] = 'PND';
    							$data['paymentStatus'] = "PID";
    							$data['isActive'] = 1;
								$data['isDelete'] = 0;
    							$dataNoti = array('title' => 'Payment Successful', 'message' => $orderId . ' Order placed successfully.', 'unique_id' => $orderCode, 'random_id' => $random, 'type' => 'VendorOrder');
    							//$this->sendCustomerNotification($postData['clientCode'], $dataNoti);
    						
    							
    							$this->db->query("delete from clientcarts where clientCode='" . $clientCode . "'");  
    						
        						$dvCondition['clientCode'] = $clientCode;
        						$clientDevices = $this->GlobalModel->selectQuery("clientdevicedetails.firebaseId", "clientdevicedetails", $dvCondition);
        						$DeviceIdsArr = array();
								$dataArr = $notification = array();
        						if ($clientDevices) {        							
        							foreach ($clientDevices->result() as $key) {
        								$DeviceIdsArr[] = $key->firebaseId;
										 log_message("error", "firebaseId". $key->firebaseId);
        							}
        						}
    							
								$title = "Payment Successful";
            					$message = $orderCode . ' Order placed successfully.';
								log_message("error","DeviceId=>".json_encode($DeviceIdsArr));
								log_message("error","Title=>".$title);
								log_message("error","Message=>".$message);
								log_message("error","OrderId=>".$orderId);
								log_message("error","OrderCode=>".$orderCode);
                                
								/*$random = rand(0, 999);
							
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
								log_message("error", "Customer Notification result->".$notify);*/
								
								$this->sendNotification($DeviceIdsArr, $title, $message, $orderCode);
    							
                            }
                            else if($txStatus=="FAILED")
                            {
                                $data['orderStatus'] = 'CAN';
        						$data['paymentStatus'] = "RJCT";
            						
                            }
                            else
                            {
                                $data['orderStatus'] = 'CAN'; 
            					$data['paymentStatus'] = "RJCT";               
                            }                            
    
            			    $this->GlobalModel->doEdit($data, 'ordermaster',$orderCode);
            			    if($txStatus=="SUCCESS"){
            			    {
            			        $this->assignorder->allocate_delivery_boy_to_order($orderCode);
            			    }
            			     return $this->response(array("status" => "200", "message" => "Success"), 200);
            			    
                    }
                    else
                    {
                        //Invalid Order
                        log_message("error", "Order id not found in webhook");
        				return $this->response(array("status" => "300", "message" => "Order id not found in webhook"), 200);
                    }
                    
                }
            } else if(strpos($orderId, "FD") !== false){
				 //Food order
				$table = "vendorordermaster";
				$clms = "vendorordermaster.clientCode,vendorordermaster.code,vendorordermaster.paymentStatus";
				$cond = array("vendorordermaster.orderId" =>$orderId);
				$result = $this->GlobalModel->selectQuery($clms, $table, $cond);
				log_message("error","payment status=>".$result->result_array()[0]["paymentStatus"]);
					if ($result != false && $result->result_array()[0]["paymentStatus"]!="PID") { 
					   
					   $clientCode=$result->result_array()[0]["clientCode"];
					   $orderCode=$result->result_array()[0]["code"];
						if($txStatus=="SUCCESS"){
							$data['orderStatus'] = 'PND';
							$data['paymentStatus'] = "PID";
							$data['isActive'] = 1;
							$data['isDelete'] = 0;
							$this->assignorder->allocate_delivery_boy_to_order($orderCode);
							
							$dvCondition['clientCode'] = $clientCode;
							$clientDevices = $this->GlobalModel->selectQuery("clientdevicedetails.firebaseId", "clientdevicedetails", $dvCondition);
							$DeviceIdsArr=$dataArr = $notification = array();
							if ($clientDevices) {
								foreach ($clientDevices->result() as $key) {
									$DeviceIdsArr[] = $key->firebaseId;
									log_message("error", "firebaseId =>". $key->firebaseId);
								}
							}
							$title="Payment Successful";
							$message=$orderCode . ' Order placed successfully.';
							log_message("error","DeviceId=>".json_encode($DeviceIdsArr));
							log_message("error","Title=>".$title);
							log_message("error","Message=>".$message);
							log_message("error","OrderId=>".$orderId);
							log_message("error","OrderCode=>".$orderCode);
							
							/*$random = rand(0, 999);
							
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
	                        log_message("error", "Customer Notification result->".$notify);*/
			                $this->sendNotification($DeviceIdsArr, $title, $message, $orderCode);
							
						}
						else if($txStatus=="FAILED")
						{
							$data['orderStatus'] = 'CAN';
							$data['paymentStatus'] = "FLD";
								
						}
						else
						{
							$data['orderStatus'] = 'CAN';
							$data['paymentStatus'] = "FLD";
						}
						$this->GlobalModel->doEdit($data, 'vendorordermaster',$orderCode);
						return $this->response(array("status" => "200", "message" => "Success"), 200);
					}
					else
					{
						 log_message("error", "Order id not found in webhook");
						 return $this->response(array("status" => "300", "message" => "Order id not found in webhook"), 200);
					}
			} else {
                log_message("error", "Data not found in webhook");
                return $this->response(array("status" => "300", "message" => "Order id not found in webhook"), 200);
            }
        } else {
            log_message("error", "Empty webhook Data");
            return $this->response(array("status" => "300", "message" => "Empty webhook Data"), 200);
        }
      }
	}

    public function returnurl_get()
    {
        $getData = file_get_contents('php://input');
        log_message("error", "called returb url with => " . trim(stripslashes(json_encode($getData))));
        return $this->response(array("status" => "200", "message" => "Success"), 200);
    }
	
	
	private function sendNotification($DeviceIdsArr, $title, $message, $orderCode)
	{
		$random = rand(0, 999);
		$payload = [
			'device_id' => $DeviceIdsArr,
			'message' => $message,
			'title' => $title,
			'order_id' => $orderCode,
			'random_id' => $random,
			'type' => 'order'
		];
		log_message("error", "randomID". $random);
		$notify = $this->notificationlibv_3->sendNotification($payload, $payload);
		log_message("error", "Customer Notification result->".json_encode($notify));
	}
	
	public function sendNotification_old($DeviceIdsArr, $title, $message, $orderCode)
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
	    log_message("error", "Customer Notification result->".json_encode($notify));
	}
}
