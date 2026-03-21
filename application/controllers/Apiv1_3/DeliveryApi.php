<?php
 
require(APPPATH.'/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;
//date_default_timezone_set('Asia/Kolkata');
class DeliveryApi extends REST_Controller {
    
    public function __construct()
    {
        parent::__construct();

        //$this->load->model('book_model');
		
		$this->load->helper('form','url','html');
   		$this->load->library('form_validation');
        $this->load->model('GlobalModel');
		$this->load->model('ApiModel');
		$this->load->library('notificationlibv_3');
    }
	
	// Get dashboard
	public function dashboard_post()
	{
		$postData = $this->post();
		if ($postData["code"] != '')
		{
			$areaLineData = $this->GlobalModel->selectDataByField('userCode',$postData["code"],'useraddresslineentries');
			$response=array();
			$areaCodes="";
			$size=sizeof($areaLineData->result());
			$count=1;
			foreach($areaLineData->result() as $row)
			{
				$comma="";
				if($size!=$count)
				{
					$comma=",";
				}
				$areaCodes.="'".$row->addressCode."'".$comma;
				$count++;
			}
				
			if($areaCodes!=""){
				$orderColumns = array("count(id) pCount");
				$cond=array("ordermaster.isActive"=>1,"ordermaster.orderStatus"=>"PND");
				$orderBy = array('ordermaster' . ".id" => 'ASC');
				$join = array();
				$joinType=array();
				$like=array();
				$limit="";
				$offset="";
				$groupByColumn=array();
				$extraCondition="ordermaster.areaCode IN(".$areaCodes.")";
				
				$p_result = $this->GlobalModel->selectQuery($orderColumns,'ordermaster',$cond,$orderBy,$join,$joinType,$like,$limit,$offset,$groupByColumn, $extraCondition);
				if($p_result)
				{
					$response["pendingCount"]=$p_result->result_array()[0]["pCount"];
				}
				else
				{
					$response["pendingCount"]=0;
				}
				
			}
			else
			{
				$response["pendingCount"]=0;
			}
			
			if($areaCodes!=""){
				$orderColumns = array("count(id) pCount");
				$cond=array("ordermaster.isActive"=>1,"ordermaster.orderStatus"=>"PLC","ordermaster.editID"=>$postData["code"]);
				$orderBy = array('ordermaster' . ".id" => 'ASC');
				$join = array();
				$joinType=array();
				$like=array();
				$limit="";
				$offset="";
				$groupByColumn=array();
				$extraCondition="ordermaster.areaCode IN(".$areaCodes.")";
				
				$p_result = $this->GlobalModel->selectQuery($orderColumns,'ordermaster',$cond,$orderBy,$join,$joinType,$like,$limit,$offset,$groupByColumn, $extraCondition);
				if($p_result)
				{
					$response["placeCount"]=$p_result->result_array()[0]["pCount"];
				}
				else
				{
					$response["placeCount"]=0;
				}
				
			}
			else
			{
				$response["placeCount"]=0;
			}
			
			//delivered count
			$orderColumns = array("count(id) dCount");
			$cond=array("ordermaster.isActive"=>1,"ordermaster.orderStatus"=>"DEL","ordermaster.editID"=>$postData["code"],"ordermaster.editDate >="=>date('Y/m/d'));
			$orderBy = array('ordermaster' . ".id" => 'ASC');
			$join = array();
			$joinType=array();
			$like=array();
			$limit="";
			$offset="";
			$groupByColumn=array();
			
			$p_result = $this->GlobalModel->selectQuery($orderColumns,'ordermaster',$cond,$orderBy);
			if($p_result)
			{
				$response["deliveredCount"]=$p_result->result_array()[0]["dCount"];
			}
			else
			{
				$response["deliveredCount"]=0;
			}
			
			//Rejected count
			if($areaCodes!=""){
				$orderColumns = array("count(id) rCount");
				$cond=array("ordermaster.isActive"=>1,"ordermaster.orderStatus"=>"RJT","ordermaster.editID"=>$postData["code"]);
				$orderBy = array('ordermaster' . ".id" => 'ASC');
				$join = array();
				$joinType=array();
				$like=array();
				$limit="";
				$offset="";
				$groupByColumn=array();
				$extraCondition="ordermaster.areaCode IN(".$areaCodes.")";
				
				$p_result = $this->GlobalModel->selectQuery($orderColumns,'ordermaster',$cond,$orderBy,$join,$joinType,$like,$limit,$offset,$groupByColumn, $extraCondition);
				if($p_result)
				{
					$response["rejectCount"]=$p_result->result_array()[0]["rCount"];
				}
				else
				{
					$response["rejectCount"]=0;
				}
				
			}
			else
			{
				$response["rejectCount"]=0;
			}
			
			$result["dashboard"]=$response;
			$this->response(array("status" => "200", "message" =>"success","result"=>$result), 200);
		}
		else
		{
			$this->response(array("status" => "400", "message" => " * are Required field(s)."), 400);
		}
	} 
	
	
	//login process
	public function loginProcess_post()
	{
		$postData = $this->post();
		
		if($postData["userName"] != '' && $postData["userPassword"] !='') 
		{
			
			
				$loginData = array(
					"username" => $postData["userName"],
					"password"=> md5($postData["userPassword"]),
					"role"=>"DLB",
					"isActive" => 1,
				);
				if($this->ApiModel->login_delivery($loginData))
				{
					$data = array(
						"username" => $postData["userName"]
					);
					$resultData = $this->ApiModel->read_Delivery_information($data);
					$empCode = $resultData[0]->empCode;
					$userCode = $resultData[0]->code;
					$empData = $this->GlobalModel->getAllDataFromField('employeemaster','code',$empCode)->result();
					
					
						$resultArray = array('code'=>$resultData[0]->code,
											'empCode'=>$resultData[0]->empCode,
											'userName'=>$resultData[0]->username,
											'role'=>$resultData[0]->role,
											'userEmail'=>$resultData[0]->userEmail,
											'profilePhoto'=>$resultData[0]->profilePhoto,
											'isActive'=>$resultData[0]->isActive,
											'empName'=>$empData[0]->firstName.' '.$empData[0]->lastName,
											'contactNumber'=>$empData[0]->contact1);
					$result['userData']=$resultArray;
					return $this->response(array("status" => "200","message"=>"Login Successfully...","result"=>$result), 200);
				}
				else
				{
					return $this->response(array("status" => "400", "message" => "incorrect username or Password"), 200);
				}
		}
		else
		{
			$this->response(array("status" => "400", "message" => " * are Required field(s)."), 400);
		}
	}//end login  Process
	
	
	public function deliveryProfileUpdate_post()
	{
		$postData = $this->post();
		
		if ($postData["code"] != '')
		{ 
			if($postData["userEmail"])
			{
				$e_id=filter_var($postData["userEmail"], FILTER_SANITIZE_EMAIL);
			}
			else
			{
				$e_id="";
			}
			
			
			$dataProfile=[
				'userEmail' =>$postData["userEmail"]
			]; 
			
			$resultData = $this->GlobalModel->selectDataById($postData["code"],'usermaster')->result_array();
			  
			if(sizeof($resultData) == 1)
			{
							
					$empData = array('contact1' => $postData['contactNumber']);
					$empRes = $this->GlobalModel->doEdit($empData, 'employeemaster', $resultData[0]['empCode']);

					//usermaster email update
					$resultMaster = $this->GlobalModel->doEdit($dataProfile,'usermaster',$postData["code"]);
				
				if($resultMaster != false || $filedoc != false || $empRes != false)
				{ 
					
						return $this->response(array("status" => "200", "message" => "Your profile has been updated successfully."), 200);

				}
				else
				{
					return $this->response(array("status" => "400", "message" => " Failed to update your profile."), 200);
				}
			}
			else
			{
				return $this->response(array("status" => "400", "message" => "User not registered. Please register user."), 200);
			}
		}	 
		else
		{
			$this->response(array("status" => "400", "message" => " * are required field(s)."), 400);
		}
	}  // End update profile
	
	
	//profile pic upload
	public function profilePicUpload_post()
	{
		$postData = $this->post();
		if ($postData["code"] != '')
		{
			$checkData=array("code"=>$postData["code"],"isDelete"=>0);
			if($this->GlobalModel->checkExistAndInsertRecords($checkData,'usermaster'))
			{
				return $this->response(array("status" => "406","message"=>"User Not Exist"), 200);
				exit();
			}
							$profilePhoto = "";
							$uploadRootDir = 'uploads/';
							$uploadDir = 'uploads/profilePhoto/';
							if (!empty($_FILES['file']['name']))
							{
								$tmpFile = $_FILES['file']['tmp_name'];
								$filename = $uploadDir . '/' . $postData["code"] . '.jpg';
								move_uploaded_file($tmpFile, $filename);
								if (file_exists($filename))
								{
									$profilePhoto = $postData["code"] . '.jpg';
								}
							}
							else
							{
								$dummyFile = 'profilePhoto.jpg';
								$tmpFile = $uploadRootDir . $dummyFile;
								$filename = $uploadDir . '/' . $postData["code"] . '.jpg';
								copy($tmpFile, $filename);
								if (file_exists($filename))
								{
								$profilePhoto = $postData["code"] . '.jpg';
								}
							}

							$subData = array(
								'profilePhoto' => $profilePhoto
							);
							
							//file data update
					$filedoc = $this->GlobalModel->doEdit($subData, 'usermaster', $postData["code"]);
					
					if($filedoc != false)
					{
						$this->response(array("status" => "200", "message" => "Profile photo uploaded successfully."), 200);
					}
					else
					{
						$this->response(array("status" => "400", "message" => "Error while uploading file..!!!Please try again."), 200);
					}
		}
		else
		{
			$this->response(array("status" => "400", "message" => " * are required field(s)."), 400);
		}
		
	}
		
	
	//forgotPwd_post and send message to register mobile number
	public function resetpassword_post()
	{
		$postData = $this->post();
		
		if($postData["userName"] != '' ) 
		{
			$tableName="usermaster";
			$orderColumns = array("usermaster.*" );
			$cond=array('usermaster' . ".username" => $postData["userName"],'usermaster.isActive'=>1);
			$member = $this->GlobalModel->selectQuery($orderColumns,$tableName,$cond);
			
			if($member)
			{
				$userCode = $member->result()[0]->code;
				
				$insertArr = array(
						"userCode"=> $userCode, 
						"isActive" => 1
					);
					$insertResult=$this->GlobalModel->onlyinsert($insertArr, 'resetpassword');
					
					if($insertResult != 'false') 
					{
						return $this->response(array("status" => "200","message"=>" Reset password Request sent. default password is 123456 ... try after admin reset. Change your password after login."), 200);
						exit();
					}
					else
					{
						return $this->response(array("status" => "400", "message" => " Opps...! Something went wrong please try again."), 200);
						exit();
					}
				
			}
			else
			{
				$this->response(array("status" => "400", "message" => "Please Enter Registered username!"), 200);
			}
						
		}
		else
		{
			$this->response(array("status" => "400", "message" => "All * fileds are required"), 400);
		}
	}
	
	
	// Start update password
	public function updatePassword_post()
	{
		$postData = $this->post();
		
		$memberCode=""; 
		$oldPassword="";
		$dbPassword="";
		$newPassword="";

		
		if ($postData["code"] != '' && $postData["oldPassword"] != '' && $postData["newPassword"] != '')
		{
			$oldPassword=md5($postData["oldPassword"]);  
			
			$resultData = $this->GlobalModel->selectDataById($postData["code"],'usermaster')->result_array();
			
			$dbPassword=$resultData[0]['password'];
				
			
			if($dbPassword == $oldPassword)
			{ 
				$passData=[
					"password" => md5($postData["newPassword"])
				];
				
				$passresult = $this->GlobalModel->doEdit($passData,'usermaster',$postData["code"]);
				
				if($passresult != false)
				{
					return $this->response(array("status" => "200", "message" => "Your password has been updated successfully."), 200);
				}
				else
				{
					return $this->response(array("status" => "400", "message" => " Failed to update your password."), 200);
				}
			}
			else
			{
				return $this->response(array("status" => "400", "message" => "You entered wrong current password."), 200);
			} 
		}
		else
		{
			$this->response(array("status" => "400", "message" => " * are required field(s)."), 400);
		}
	}  // End update password
	
	
	//get all delivery boy list 
	public function deliveryBoyList_get()
	{
		$res = $this->GlobalModel->getAllDataFromField('usermaster','role','DLB')->result();
		if($res)
		{
			$dataList = array();
			foreach($res as $dlb)
			{
				$empCode = $dlb->empCode;
				$empData = $this->GlobalModel->getAllDataFromField('employeemaster','code',$empCode)->result();
				
				$data = array('code'=>$dlb->code,
							'empCode' => $dlb->empCode,
							'role' => $dlb->role,
							'empName' => $empData[0]->firstName.' '.$empData[0]->lastName
						);
				array_push($dataList,$data);
			}
			$result['List'] = $dataList;
			return $this->response(array("status" => "200","message"=>"Delivery Boy List","result"=>$result), 200);
		}
		else
		{
			return $this->response(array("status" => "400","message"=>"No More Records"), 400);
		}
	}
	
	
	//get All assigned area to delivery Boy
	public function deliveryBoyAreaList_post()
	{
		$postData = $this->post();
		if($postData['userCode'] !='')
		{
			$res = $this->GlobalModel->getAllDataFromField('useraddresslineentries','userCode',$postData['userCode'])->result();
			if($res)
			{	
				$areaData = [];
				foreach($res as $row)
				{
					$addressCode = $row->addressCode;
					$addressData = $this->GlobalModel->getAllDataFromField('customaddressmaster','code',$addressCode)->result();
					foreach($addressData as $addr)
					{
						$Data =array(
							'place' => $addr->place,
							'district' => $addr->district,
							'state'=>$addr->state,
							'pincode'=>$addr->pincode,
							'userCode'=>$row->userCode,
							'areaCode'=>$addr->code
							);
						
						array_push($areaData,$Data);
						
					}
				}
				$result['areaList'] = $areaData;
				return $this->response(array("status" => "200","message"=>"Delivery Boy Assigned area","result"=>$result), 200);
			}
			else
			{
				return $this->response(array("status" => "300","message"=>"No More Records"), 200);
			}
		}
		else
		{
			return $this->response(array("status" => "400", "message" => " * are Required field(s)."), 400);
		}
	}
	
	
	//get Custom added address and area list
	public function getCustomAddressList_get()
	{
		$conditionColumns = array('isService','isActive');
		$conditionValues = array(1,1);
		$res = $this->GlobalModel->selectActiveDataByMultipleFields($conditionColumns,$conditionValues,'customaddressmaster');
		
		if($res)
		{
			$addressList=[];
			foreach($res->result() as $row)
			{
				$data = array(
						'addressCode' => $row->code,
						'place' => $row->place,
						'district' => $row->district,
						'taluka' => $row->taluka,
						'pincode' => $row->pincode,
						'state' => $row->state,
					);
					
					array_push($addressList,$data);
			}
			$result['addressList'] = $addressList;
			return $this->response(array("status" => "200", "message" => " Address List where Services Available","result" => $result), 200);
		}	
		else
		{
			$this->response(array("status" => "400", "message" => " * are Required field(s)."), 400);
		}
	}
	
	
	//get client profile address list
	public function getPendingOrders_post()
	{
		$postData = $this->post();
		
		if($postData['code'] !='' && $postData['offset']!="")
		{
			$orderStatus=$postData['orderStatus'];
			$areaCode=$postData['areaCode'];
			$m_condition="";
			if($orderStatus==""){
				$m_condition= " AND ordermaster.orderStatus IN ('PND','PLC')";
			}
			else if($orderStatus=="PND"){
				$m_condition= " AND ordermaster.orderStatus IN ('PND')";
			}
			else if($orderStatus=="PLC"){
				$m_condition= " AND ordermaster.orderStatus IN ('PLC') AND ordermaster.editId='".$postData['code']."'";
			}
			else
			{
				$m_condition="";
			}
			
			$areaLineData = $this->GlobalModel->selectDataByField('userCode',$postData["code"],'useraddresslineentries');
			$response=array();
			$areaCodes="";
			$size=sizeof($areaLineData->result());
			$count=1;
			if($size==0){
				$this->response(array("status" => "300", "message" => " Area Not Assigned Contact Admin... "), 200);
			}
			foreach($areaLineData->result() as $row)
			{
				$comma="";
				if($size!=$count)
				{
					$comma=",";
				}
				$areaCodes.="'".$row->addressCode."'".$comma;
				$count++;
			}
			
			$orderColumns = array("ordermaster.code as orderCode,ordermaster.shippingCharges as deliveryCharges,ordermaster.paymentmode,ordermaster.address,ordermaster.phone,ordermaster.totalPrice as orderTotalPrice,ordermaster.addDate as orderDate, ordermaster.latitude,ordermaster.longitude, ordermaster.bagNumber,orderstatusmaster.statusName as orderStatus, paymentstatusmaster.statusName as paymentStatus,clientmaster.code as clientCode,clientmaster.name" );
			$join = array('clientmaster'=>'clientmaster.code = ordermaster.clientCode','orderstatusmaster' => 'ordermaster' . '.orderStatus=' . 'orderstatusmaster' .'.statusSName','paymentstatusmaster' => 'ordermaster' . '.paymentStatus=' . 'paymentstatusmaster' .'.statusSName');
			$joinType=array('clientmaster' =>'left','orderstatusmaster' =>'inner','paymentstatusmaster' =>'inner');
			$cond=array("ordermaster".".areaCode"=>$areaCode);
			$orderBy = array('ordermaster' . ".id" => 'ASC');
			$like=array();
			$limit="";
			$offset=$postData['offset'];
			$groupByColumn=array();
			$extraCondition="ordermaster.areaCode IN(".$areaCodes.")".$m_condition;
			
			$resultQuery = $this->GlobalModel->selectQuery($orderColumns,'ordermaster',$cond,$orderBy,$join,$joinType,$like,$limit,$offset,$groupByColumn, $extraCondition);
			
			if($resultQuery)
			{
				$clientOrderList=$resultQuery->result_array();
				$totalOrders=sizeof($clientOrderList);
				for($i=0;$i<sizeof($clientOrderList);$i++)
				{
					$linetableName="orderlineentries";
					$lineorderColumns = array("orderlineentries.productCode,orderlineentries.weight,orderlineentries.productUom,orderlineentries.productPrice,orderlineentries.quantity,orderlineentries.totalPrice as productTotalPrice,productmaster.productName");
					$linecond=array('orderlineentries' . ".orderCode" => $clientOrderList[$i]['orderCode']);
					$lineorderBy = array('orderlineentries' . ".id" => 'ASC');
					$linejoin = array('productmaster' => 'orderlineentries' . '.productCode=' . 'productmaster' .'.code');
					$linejoinType=array('productmaster' =>'inner');
					
					$orderProductRes = $this->GlobalModel->selectQuery($lineorderColumns,$linetableName,$linecond,$lineorderBy,$linejoin,$linejoinType);
					if($orderProductRes){
						$productPrice=0;
						$orderProductList=$orderProductRes->result_array();
						for($j=0;$j<sizeof($orderProductList);$j++)
						{
							$condition2=array('productCode'=>$orderProductList[$j]["productCode"]); 
							$images_result = $this->ApiModel->selectData('productphotos','','',$condition2)->result_array();
							$productCode=$orderProductList[$j]["productCode"];
							$imageArray=array();
							
							for($img=0;$img<sizeof($images_result);$img++)
							{
								array_push($imageArray, base_url().'uploads/product/'.$productCode.'/'.$images_result[$img]['productPhoto']);
							}
							
							$orderProductList[$j]['images']=$imageArray;
							unset($imageArray);
							$productPrice += $orderProductList[$j]["productTotalPrice"];
						}
						$clientOrderList[$i]['orderPrice'] = $productPrice;
						$dFormat = DateTime::createFromFormat('Y-m-d H:i:s',$clientOrderList[$i]['orderDate']);
						$oDt = $dFormat->format('d-m-Y H:i:s');
						$clientOrderList[$i]['orderDate']=$oDt;
						$clientOrderList[$i]['orderedProduct'] = $orderProductList;
					}
					
					
				}
				
				$finalResult['orders']=$clientOrderList;
				$this->response(array("status" => "200", "message" => " Order details","result"=>$finalResult), 200);
			}
			else
			{
					$this->response(array("status" => "400", "message" => " No more Records"), 200);
			}
		}
		else
		{
			$this->response(array("status" => "400", "message" => " * are Required field(s)."), 400);
		}
	}
	
	public function getOrderListByUser_post()
	{
			$postData = $this->post();
						
			$orderColumns = array("ordermaster.code,ordermaster.clientCode,ordermaster.orderStatus,ordermaster.orderedTime,ordermaster.placedTime,ordermaster.shippedTime,ordermaster.address,ordermaster.phone,ordermaster.totalPrice,ordermaster.areaCode,ordermaster.editId,clientmaster.code,clientmaster.name");
			if($postData['orderStatus'] !='')
			{
				$cond=array('ordermaster.orderStatus' =>$postData['orderStatus'],'ordermaster.editId'=>$postData['code']);
			}
			else if($postData['areaCode'] !='')
			{
				$cond=array('ordermaster.areaCode' =>$postData['areaCode'],'ordermaster.editId'=>$postData['code']);
			}
			else if($postData['orderStatus'] !='' && $postData['areaCode'] !='')
			{
				$cond=array('ordermaster.orderStatus' =>$postData['orderStatus'],'ordermaster.areaCode' =>$postData['areaCode'],'ordermaster.editId'=>$postData['code']);
			}
			else
			{
				$cond=array('ordermaster.editId'=>$postData['code']);
			}
			
			$orderBy = array('ordermaster' . ".id" => 'ASC');
			$join = array('clientmaster'=>'clientmaster.code = ordermaster.clientCode');
			$joinType=array('clientmaster'=>'left');
			$like=array();
			$limit=array();
			$offset=array();
			$groupByColumn=array();
			$extraCondition="ordermaster.orderStatus NOT IN('RJT','DEL','CAN')";
			
			$p_result = $this->GlobalModel->selectQuery($orderColumns,'ordermaster',$cond,$orderBy,$join,$joinType,$like,$limit,$offset,$groupByColumn, $extraCondition);
			
			if($p_result)
			{
				$result['listRecords'] = $p_result->result();
				$this->response(array("status" => "200", "message" => " Order details","result"=>$result), 200);
			}
			else
			{
					$this->response(array("status" => "400", "message" => " No more Records"), 200);
			}
		
	}
	
	public function confirmOrderPlace_post()
	{
			$postData = $this->post();
		
			if($postData["orderCode"] != '' && $postData['userCode'] !='' && $postData['orderStatus'] !='')
			{
				$orderCode = $postData["orderCode"];
				$timeStamp=date("Y-m-d h:i:s");
				if($postData['orderStatus'] == 'PLC')
				{
					$orderData = $this->GlobalModel->selectDataByField('code',$orderCode,'ordermaster');
					if($orderData->result()[0]->orderStatus=='PLC') {
						return $this->response(array("status" => "500", "message" => "Order has been placed already."), 200);
					}
						//bagCount				
						$toDate = date('Y-m-d');
							  
						$checkExist = $this->GlobalModel->selectDataExcludeDelete('orderbagcount');
						$existsDate = $checkExist->result()[0]->toDate;
						$existsCount = $checkExist->result()[0]->count;
						
						if(strtotime($existsDate) == strtotime($toDate))
						{
							if($existsCount <= 100)
							{
								$data=array(
										'orderStatus'=>'PLC',
										'placedTime'=>$timeStamp,
										'bagNumber'=>$existsCount,
										'editID'=>$postData['userCode'],
										'editDate'=>$timeStamp
										); 
									
								$existsCount++;
								
								$result=$this->GlobalModel->doEdit($data,'ordermaster',$orderCode);
								if($result != 'false')
								{
									$dataCount = array('count'=>$existsCount,
											'toDate'=>$toDate);
									$res = $this->GlobalModel->doEditWithField($dataCount,'orderbagcount','id',1);
										
										//notification
									$orderData = $this->GlobalModel->selectDataByField('code',$orderCode,'ordermaster');
									$clientCode = $orderData->result()[0]->clientCode;
									
									$random=rand(0,999);
									$datamsg=array("title"=>'Order Placed',"message"=>'Your Order is placed,Your order id is '.$orderCode,"order_id"=>$orderCode,"random_id"=>$random);
									
									$checkdevices= $this->GlobalModel->selectDataByField('code',$clientCode,'clientmaster');
									$DeviceIdsArr[] = $checkdevices->result()[0]->firebaseId;
											
									$dataArr = array();
									$dataArr['device_id'] = $DeviceIdsArr;
									$dataArr['message'] = $datamsg['message'];//Message which you want to send
									$dataArr['title'] = $datamsg['title'];
									$dataArr['order_id'] = $datamsg['order_id'];
									$dataArr['random_id']=$datamsg['random_id'];
									$dataArr['type'] = 'order';	
									
									$notification['device_id'] = $DeviceIdsArr;
									$notification['message'] = $datamsg['message'];//Message which you want to send
									$notification['title'] = $datamsg['title'];
									$notification['order_id'] = $datamsg['order_id'];
									$notification['random_id']=$datamsg['random_id'];
									$notification['type'] = 'order';
									$notify = $this->notificationlibv_3->pushNotification($dataArr,$notification);
										
											/*if($notify)
											{
												echo $notify;
												
											}
											else
											{
												echo "error";
												
											}*/
									return $this->response(array("status" => "200", "message" => "Order Successfully Placed."), 200);
								}
								else
								{
									return $this->response(array("status" => "400", "message" => "Failed To delivered Order."), 200);
								}
								
							}
							else
							{
								$dataCount = array('count'=>1,
											'toDate'=>$toDate);
									$res = $this->GlobalModel->doEditWithField($dataCount,'orderbagcount','id',1);
									
								$data=array(
										'orderStatus'=>'PLC',
										'placedTime'=>$timeStamp,
										'bagNumber'=>1,
										'editID'=>$postData['userCode'],
										'editDate'=>$timeStamp
										);
								$result=$this->GlobalModel->doEdit($data,'ordermaster',$orderCode);
								if($result != 'false')
								{
									
									//notification
									$orderData = $this->GlobalModel->selectDataByField('code',$orderCode,'ordermaster');
									$clientCode = $orderData->result()[0]->clientCode;
									
									$random=rand(0,999);
									$datamsg=array("title"=>'Order Placed',"message"=>'Your Order is placed,Your order id is '.$orderCode,"order_id"=>$orderCode,"random_id"=>$random);
									
									$checkdevices= $this->GlobalModel->selectDataByField('code',$clientCode,'clientmaster');
									$DeviceIdsArr[] = $checkdevices->result()[0]->firebaseId;
											
									$dataArr = array();
									$dataArr['device_id'] = $DeviceIdsArr;
									$dataArr['message'] = $datamsg['message'];//Message which you want to send
									$dataArr['title'] = $datamsg['title'];
									$dataArr['order_id'] = $datamsg['order_id'];
									$dataArr['random_id']=$datamsg['random_id'];
									$dataArr['type'] = 'order';	
									
									$notification['device_id'] = $DeviceIdsArr;
									$notification['message'] = $datamsg['message'];//Message which you want to send
									$notification['title'] = $datamsg['title'];
									$notification['order_id'] = $datamsg['order_id'];
									$notification['random_id']=$datamsg['random_id'];
									$notification['type'] = 'order';
									
									$notify = $this->notificationlibv_3->pushNotification($dataArr,$notification);
									return $this->response(array("status" => "200", "message" => "Order Successfully Placed."), 200);
								}
								else
								{
									return $this->response(array("status" => "400", "message" => "Failed To delivered Order."), 200);
								}
								
							}
							
						}
						else
						{
							$dataCount = array('count'=>1,
											'toDate'=>$toDate);
							$res = $this->GlobalModel->doEditWithField($dataCount,'orderbagcount','id',1);
							$data=array(
										'orderStatus'=>'PLC',
										'placedTime'=>$timeStamp,
										'bagNumber'=>1,
										'editID'=>$postData['userCode'],
										'editDate'=>$timeStamp
										);
								$result=$this->GlobalModel->doEdit($data,'ordermaster',$orderCode);
								if($result != 'false')
								{
									//notification
									$orderData = $this->GlobalModel->selectDataByField('code',$orderCode,'ordermaster');
									$clientCode = $orderData->result()[0]->clientCode;
									
									$random=rand(0,999);
									$datamsg=array("title"=>'Order Placed',"message"=>'Your Order is Placed,Your order id is '.$orderCode,"order_id"=>$orderCode,"random_id"=>$random);
									
									$checkdevices= $this->GlobalModel->selectDataByField('code',$clientCode,'clientmaster');
									$DeviceIdsArr[] = $checkdevices->result()[0]->firebaseId;
											
									$dataArr = array();
									$dataArr['device_id'] = $DeviceIdsArr;
									$dataArr['message'] = $datamsg['message'];//Message which you want to send
									$dataArr['title'] = $datamsg['title'];
									$dataArr['order_id'] = $datamsg['order_id'];
									$dataArr['random_id']=$datamsg['random_id'];
									$dataArr['type'] = 'order';												
									
									$notification['device_id'] = $DeviceIdsArr;
									$notification['message'] = $datamsg['message'];//Message which you want to send
									$notification['title'] = $datamsg['title'];
									$notification['order_id'] = $datamsg['order_id'];
									$notification['random_id']=$datamsg['random_id'];
									$notification['type'] = 'order';	
									
									$notify = $this->notificationlibv_3->pushNotification($dataArr,$notification);
									return $this->response(array("status" => "200", "message" => "Order Successfully Placed."), 200);
								}
								else
								{
									return $this->response(array("status" => "400", "message" => "Failed To delivered Order."), 200);
								}
								
						}
				
				}
				else if($postData['orderStatus'] == 'DEL')
				{
					$data=array(
						'orderStatus'=>$postData['orderStatus'],
						'paymentStatus'=>'PID',
						'deliveredTime'=>$timeStamp,
						'editID'=>$postData['userCode'],
						'editDate'=>$timeStamp
						);
						
						$result=$this->GlobalModel->doEdit($data,'ordermaster',$orderCode);
						
						if($result != 'false')
						{
							$getLineData=$this->GlobalModel->selectDataByField('orderCode',$orderCode,'orderlineentries');
							foreach($getLineData->result() as $line){
							   $productCode=$line->productCode;
							   $stock=($line->quantity*$line->weight);
							   $consumeStock = $this->GlobalModel->stockChange($productCode,$stock,'consume');
							}
							
							
							/*---Notification Code ---11/11/2019*/
							$clnt = $this->GlobalModel->selectDataByField('code',$orderCode,'ordermaster');
							
							/*---- Deivery Boy Commision -- */
							$delboyCode = $postData['userCode'];
							$userRole ='';
							$points = 0;
							$pamount = 0;
							$ip =   $_SERVER['REMOTE_ADDR'];
							$datapoints =  $this->GlobalModel->selectDataByField('code',$delboyCode,'usermaster');
							if($datapoints->num_rows() >0 ){
								foreach($datapoints->result_array() as $rp ){
									$userRole = $rp['role'];
									$points = $rp['points'];
								}
								if($userRole!='USR'){
									$insertData = array('addIP'=>$ip,'orderCode'=>$orderCode,'userCode'=>$delboyCode,'addID'=>$delboyCode,'addDate'=>date('Y-m-d h:i:s'),'points'=>$points,'isActive'=>1);
									$presult = $this->GlobalModel->addNew($insertData,'commissiontemp','CMN');
								}
							}
							
							$clientCode = $clnt->result()[0]->clientCode;
						
							$random=rand(0,999);
							$datamsg=array("title"=>'Order Deliverd',"message"=>'Order successfully delivered.',"order_id"=>$orderCode,"random_id"=>$random);
						
							$checkdevices= $this->GlobalModel->selectDataByField('code',$clientCode,'clientmaster');
							$DeviceIdsArr[] = $checkdevices->result()[0]->firebaseId;
							
							$dataArr = array();
							$dataArr['device_id'] = $DeviceIdsArr;
							$dataArr['message'] = $datamsg['message'];//Message which you want to send
							$dataArr['title'] = $datamsg['title'];
							$dataArr['order_id'] = $datamsg['order_id'];
							$dataArr['random_id']=$datamsg['random_id'];
							$dataArr['type'] = 'order';	
							
							$notification['device_id'] = $DeviceIdsArr;
							$notification['message'] = $datamsg['message'];//Message which you want to send
							$notification['title'] = $datamsg['title'];
							$notification['order_id'] = $datamsg['order_id'];
							$notification['random_id']=$datamsg['random_id'];
							$notification['type'] = 'order';	
							
							$notify = $this->notificationlibv_3->pushNotification($dataArr,$notification);
							
							return $this->response(array("status" => "200", "message" => "Order Successfully Delivered."), 200);
						}
						else
						{
							return $this->response(array("status" => "400", "message" => "Failed To delivered Order."), 200);
						}
				}
				else
				{
					$data=array(
						'orderStatus'=>$postData['orderStatus'],
						'placedTime'=>$timeStamp,
						'paymentStatus'=>'RJCT',
						'editID'=>$postData['userCode'],
						'editDate'=>$timeStamp
						);
					
					$result=$this->GlobalModel->doEdit($data,'ordermaster',$orderCode);
					
						
					if($result!='false')
					{
					//get Data from OrderCode and send push notification
						$orderData = $this->GlobalModel->selectDataByField('code',$orderCode,'ordermaster');
						$clientCode = $orderData->result()[0]->clientCode;
						
						$random=rand(0,999);
						$datamsg=array("title"=>'Order Rejected',"message"=>'Your Order is rejected,Your order id is '.$orderCode,"order_id"=>$orderCode,"random_id"=>$random);
						
						$checkdevices= $this->GlobalModel->selectDataByField('code',$clientCode,'clientmaster');
						$DeviceIdsArr[] = $checkdevices->result()[0]->firebaseId;
						
						$dataArr = array();
						$dataArr['device_id'] = $DeviceIdsArr;
						$dataArr['message'] = $datamsg['message'];//Message which you want to send
						$dataArr['title'] = $datamsg['title'];
						$dataArr['order_id'] = $datamsg['order_id'];
						$dataArr['random_id']=$datamsg['random_id'];
						$dataArr['type'] = 'order';	
						
						$notification['device_id'] = $DeviceIdsArr;
						$notification['message'] = $datamsg['message'];//Message which you want to send
						$notification['title'] = $datamsg['title'];
						$notification['order_id'] = $datamsg['order_id'];
						$notification['random_id']=$datamsg['random_id'];
						$notification['type'] = 'order';	
						
						$notify = $this->notificationlibv_3->pushNotification($dataArr,$notification);
						
						return $this->response(array("status" => "200", "message" => "Order Successfully Rejected."), 200);
					}
					else
					{
						return $this->response(array("status" => "400", "message" => "Failed To Reject Order."), 200);
					}
				}
			}
			else
			{
				return $this->response(array("status" => "400", "message" => "* are Required field(s)."), 400);
			}
	}
	
	
	//get client Delivered order list
	public function getDeliveredOrders_post()
	{
		$postData = $this->post();
		
		if($postData['code'] !='' && $postData['offset']!="")
		{
			//$areaCode=$postData['areaCode'];
					
			$areaLineData = $this->GlobalModel->selectDataByField('userCode',$postData["code"],'useraddresslineentries');
			$response=array();
			$areaCodes="";
			$size=sizeof($areaLineData->result());
			$count=1;
			foreach($areaLineData->result() as $row)
			{
				$comma="";
				if($size!=$count)
				{
					$comma=",";
				}
				$areaCodes.="'".$row->addressCode."'".$comma;
				$count++;
			}
			
			$totalamount=0;
			
			if($areaCodes !='')
			{
					
					$orderColumns = array("ordermaster.code as orderCode,ordermaster.shippingCharges as deliveryCharges,ordermaster.paymentmode,ordermaster.address,ordermaster.phone,ordermaster.totalPrice as orderTotalPrice,ordermaster.addDate as orderDate, ordermaster.latitude,ordermaster.longitude, orderstatusmaster.statusName as orderStatus, paymentstatusmaster.statusName as paymentStatus,clientmaster.code as clientCode,clientmaster.name,ordermaster.editDate as deliveredDate" );
					$join = array('clientmaster'=>'clientmaster.code = ordermaster.clientCode','orderstatusmaster' => 'ordermaster' . '.orderStatus=' . 'orderstatusmaster' .'.statusSName','paymentstatusmaster' => 'ordermaster' . '.paymentStatus=' . 'paymentstatusmaster' .'.statusSName');
					$joinType=array('clientmaster' =>'left','orderstatusmaster' =>'inner','paymentstatusmaster' =>'inner');
					$cond=array("ordermaster.orderStatus"=>'DEL',"ordermaster.editID"=>$postData['code'],"ordermaster.editDate >="=>date('Y/m/d')); 
					$orderBy = array('ordermaster' . ".id" => 'DESC');
					$like=array();
					$limit="10";
					$offset=$postData['offset'];
					$groupByColumn=array();
					$extraCondition="";//ordermaster.orderStatus IN ('DEL') AND ordermaster.areaCode IN(".$areaCodes.")";
					
					$resultQuery = $this->GlobalModel->selectQuery($orderColumns,'ordermaster',$cond,$orderBy,$join,$joinType,$like,$limit,$offset,$groupByColumn, $extraCondition);
					
					if($resultQuery)
					{
						$clientOrderList=$resultQuery->result_array();
						$totalOrders=sizeof($clientOrderList);
						for($i=0;$i<sizeof($clientOrderList);$i++)
						{
							$totalamount += $clientOrderList[$i]['orderTotalPrice'];
							$linetableName="orderlineentries";
							$lineorderColumns = array("orderlineentries.productCode,orderlineentries.weight,orderlineentries.productUom,orderlineentries.productPrice,orderlineentries.quantity,orderlineentries.totalPrice as productTotalPrice,productmaster.productName");
							$linecond=array('orderlineentries' . ".orderCode" => $clientOrderList[$i]['orderCode']);
							$lineorderBy = array('orderlineentries' . ".id" => 'ASC');
							$linejoin = array('productmaster' => 'orderlineentries' . '.productCode=' . 'productmaster' .'.code');
							$linejoinType=array('productmaster' =>'inner');
							
							$orderProductRes = $this->GlobalModel->selectQuery($lineorderColumns,$linetableName,$linecond,$lineorderBy,$linejoin,$linejoinType);
							if($orderProductRes)
							{
								$orderProductList=$orderProductRes->result_array();
								$productPrice=0;
								for($j=0;$j<sizeof($orderProductList);$j++)
								{
									
									$condition2=array('productCode'=>$orderProductList[$j]["productCode"]); 
									$images_result = $this->ApiModel->selectData('productphotos','','',$condition2)->result_array();
									$productCode=$orderProductList[$j]["productCode"];
									$imageArray=array();
									
									for($img=0;$img<sizeof($images_result);$img++)
									{
										array_push($imageArray, base_url().'uploads/product/'.$productCode.'/'.$images_result[$img]['productPhoto']);
									}
									
									$orderProductList[$j]['images']=$imageArray;
									unset($imageArray);
									
									$productPrice += $orderProductList[$j]["productTotalPrice"];
								}
								$clientOrderList[$i]['orderPrice'] = $productPrice;
								$oFormat = DateTime::createFromFormat('Y-m-d H:i:s',$clientOrderList[$i]['orderDate']);
								$oDt = $oFormat->format('d-m-Y H:i:s');
								$clientOrderList[$i]['orderDate']=$oDt;
								
								$clientOrderList[$i]['orderedProduct'] = $orderProductList;
								
								$dFormat = DateTime::createFromFormat('Y-m-d H:i:s',$clientOrderList[$i]['deliveredDate']);
								$dDt = $dFormat->format('d-m-Y H:i:s');
								$clientOrderList[$i]['deliveredDate']=$dDt;
								
							}
							
						}
						
						$finalResult['orders']=$clientOrderList;
						$this->response(array("status" => "200", 'totalPrice' => $totalamount,"message" => " Order details","result"=>$finalResult,"totalRecords"=>$totalOrders), 200);
					}
					else
					{
							$this->response(array("status" => "400", "message" => " No more Records"), 200);
					}
					
			}
			else
			{
				$this->response(array("status" => "400", "message" => " No more Records"), 200);
			}
		}
		else
		{
			$this->response(array("status" => "400", "message" => " * are Required field(s)."), 400);
		}
	}
	
	
	
	//get client Rejected order list
	public function getRejectedOrders_post()
	{
		$postData = $this->post();
		
		if($postData['code'] !='' && $postData['offset']!="")
		{
			$areaLineData = $this->GlobalModel->selectDataByField('userCode',$postData["code"],'useraddresslineentries');
			$response=array();
			$areaCodes="";
			$size=sizeof($areaLineData->result());
			$count=1;
			foreach($areaLineData->result() as $row)
			{
				$comma="";
				if($size!=$count)
				{
					$comma=",";
				}
				$areaCodes.="'".$row->addressCode."'".$comma;
				$count++;
			}
			
			$orderColumns = array("ordermaster.code as orderCode,ordermaster.shippingCharges as deliveryCharges,ordermaster.paymentmode,ordermaster.address,ordermaster.phone,ordermaster.totalPrice as orderTotalPrice,ordermaster.addDate as orderDate, ordermaster.latitude,ordermaster.longitude, orderstatusmaster.statusName as orderStatus,clientmaster.code as clientCode,clientmaster.name" );
			$join = array('clientmaster'=>'clientmaster.code = ordermaster.clientCode','orderstatusmaster' => 'ordermaster' . '.orderStatus=' . 'orderstatusmaster' .'.statusSName');
			$joinType=array('clientmaster' =>'left','orderstatusmaster' =>'inner');
			$cond=array("ordermaster.orderStatus"=>"RJT","ordermaster.editID"=>$postData["code"]);
			$orderBy = array('ordermaster' . ".id" => 'DESC');
			$like=array();
			$limit="10";
			$offset=$postData['offset'];
			$groupByColumn=array();
			$extraCondition="ordermaster.areaCode IN(".$areaCodes.")";
			
			$resultQuery = $this->GlobalModel->selectQuery($orderColumns,'ordermaster',$cond,$orderBy,$join,$joinType,$like,$limit,$offset,$groupByColumn, $extraCondition);
			
			if($resultQuery)
			{
				$clientOrderList=$resultQuery->result_array();
				$totalOrders=sizeof($clientOrderList);
				for($i=0;$i<sizeof($clientOrderList);$i++)
				{
					$linetableName="orderlineentries";
					$lineorderColumns = array("orderlineentries.productCode,orderlineentries.weight,orderlineentries.productUom,orderlineentries.productPrice,orderlineentries.quantity,orderlineentries.totalPrice as productTotalPrice,productmaster.productName");
					$linecond=array('orderlineentries' . ".orderCode" => $clientOrderList[$i]['orderCode']);
					$lineorderBy = array('orderlineentries' . ".id" => 'ASC');
					$linejoin = array('productmaster' => 'orderlineentries' . '.productCode=' . 'productmaster' .'.code');
					$linejoinType=array('productmaster' =>'inner');
					
					$orderProductRes = $this->GlobalModel->selectQuery($lineorderColumns,$linetableName,$linecond,$lineorderBy,$linejoin,$linejoinType);
					if($orderProductRes){
						$productPrice=0;
						$orderProductList=$orderProductRes->result_array();
						for($j=0;$j<sizeof($orderProductList);$j++)
						{
							$condition2=array('productCode'=>$orderProductList[$j]["productCode"]); 
							$images_result = $this->ApiModel->selectData('productphotos','','',$condition2)->result_array();
							$productCode=$orderProductList[$j]["productCode"];
							$imageArray=array();
							
							for($img=0;$img<sizeof($images_result);$img++)
							{
								array_push($imageArray, base_url().'uploads/product/'.$productCode.'/'.$images_result[$img]['productPhoto']);
							}
							
							$orderProductList[$j]['images']=$imageArray;
							unset($imageArray);
							$productPrice += $orderProductList[$j]["productTotalPrice"];
						}
						$clientOrderList[$i]['orderPrice'] = $productPrice;
						$dFormat = DateTime::createFromFormat('Y-m-d H:i:s',$clientOrderList[$i]['orderDate']);
						$oDt = $dFormat->format('d-m-Y H:i:s');
						$clientOrderList[$i]['orderDate']=$oDt;
						$clientOrderList[$i]['orderedProduct'] = $orderProductList;
					}
				}
				
				$finalResult['orders']=$clientOrderList;
				$this->response(array("status" => "200", "message" => " Order details","result"=>$finalResult,"totalRecords"=>$totalOrders), 200);
			}
			else
			{
					$this->response(array("status" => "400", "message" => " No more Records"), 200);
			}
		}
		else
		{
			$this->response(array("status" => "400", "message" => " * are Required field(s)."), 400);
		}
	}
	
	
	// Start update profile
	public function updateFirebaseId_post()
	{
		$postData = $this->post();
		
		if ($postData["code"] != ''&& $postData["firebaseId"] != '')
		{
			
			$dataMaster=[
				"firebase_id" => $postData["firebaseId"]
			];
			
			$resultMaster = $this->GlobalModel->doEdit($dataMaster,'usermaster',$postData["code"]);
				
			if($resultMaster != false)
			{ 
				return $this->response(array("status" => "400", "message" => "Firebase Id Update Successfully"), 400);
			}
			else 
			{
				return $this->response(array("status" => "400", "message" => " Failed to update Firebase Id."), 400);
			}
		}
		else
		{
			$this->response(array("status" => "400", "message" => " * are required field(s)."), 400);
		}
	}  // End update firebaseId
	
	
		//get client Delivered order list
	public function getCommissionRecords_post()
	{
		$postData = $this->post();
		if($postData['code'] !='')
		{
			//$areaCode=$postData['areaCode'];
			$commissiondata = $this->GlobalModel->selectDataByField('userCode',$postData["code"],'commissiontemp');
			$size=sizeof($commissiondata->result());
			$points=0;
			if($size>0){
				foreach($commissiondata->result() as $row){
					$points += $row->points;
				}
				$finalResult = array('totalPoints'=>$points);
				$this->response(array("status" => "200", "message" => "Comimission Details","result"=>$finalResult), 200);
			}else {
				$finalResult = array('totalPoints'=>0);
				$this->response(array("status" => "200", "message" => "Comimission Details","result"=>$finalResult), 200);
			}
		} else {
			$this->response(array("status" => "400", "message" => " * are Required field(s)."), 400);
		}
	}
	
	public function isUserActive_post()
	{
		$postData = $this->post();
		if($postData['code'] !='')
		{
			$tableName="usermaster";
			$orderColumns = array("usermaster.isActive" );
			$cond=array('usermaster' . ".code" => $postData["code"],'usermaster.isActive'=>1);
			$member = $this->GlobalModel->selectQuery($orderColumns,$tableName,$cond);
			if($member->num_rows()){
				$this->response(array("status" => "200", "message" => "User Active"), 200);
			}
			else
			{
				$this->response(array("status" => "300", "message" => "User InActive"), 200);
			}
		} else {
			$this->response(array("status" => "400", "message" => " * are Required field(s)."), 400);
		}
	}
	
	
	public function test_get()
	{
		/* $resultData = $this->GlobalModel->selectDataById('USREM_10','usermaster')->result_array();
		print_r($resultData[0]['empCode']);
		$result = $this->GlobalModel->selectActiveData('useraddresslineentries')->result(); */
		$result=date('Y-m-d h:i:s');
		$this->response(array("status" => "400", "message" => $result), 400);
	}
}
?>