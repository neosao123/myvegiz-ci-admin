<?php

defined('BASEPATH') or exit('No direct script access allowed');

class CustomerNotification extends CI_Controller
{
	var $session_key;
	public function __construct()
	{
		parent::__construct();
		$this->load->helper('form', 'url', 'html');
		$this->load->library('form_validation');
		$this->load->model('GlobalModel');
		$this->load->model('GlobalModel1');
		$this->load->model('Testing');
		$this->load->library('notificationlibv_3');
		// $this->load->library('Notification_Lib');
		$this->session_key = $this->session->userdata('key' . SESS_KEY);
		if (!isset($this->session->userdata['logged_in' . $this->session_key]['code'])) {
			redirect('Admin/login', 'refresh');
		}
	}

	public function listRecords()
	{
		$data['city'] = $this->GlobalModel->selectActiveData('citymaster');
		$data['productmaster'] = $this->GlobalModel->selectActiveData('productmaster');
		$this->load->view('dashboard/header');
		$this->load->view('dashboard/pushcustomer_notification/notificationCreate', $data);
		$this->load->view('dashboard/footer');
	}


	public function notificationprocess()
	{
	   
		if (isset($_POST['simple'])) {
			$data['listLength'] = 0;

			$title = $this->input->post('title');
			$msg = $this->input->post('msg');
			
		    $cityCode = $this->input->post('cityCode');
			$areaCode = $this->input->post('addressCode');
			$clientcode = $this->input->post('clientCodes');
			
			$area = "";
			if (isset($areaCode)) {
				foreach ($areaCode as $ad) {
					$area != "" && $area .= ",";
					$area .= "'" . $ad . "'";
				}
			}
			
			$clients = "";
			if (isset($clientcode)) {
				foreach ($clientcode as $ad) {
					$clients != "" && $clients .= ",";
					$clients .= "'" . $ad . "'";
				}
			}
            $imgName ="";
			$uploadRootDir = FCPATH . 'uploads';
			$uploadDir = FCPATH . 'uploads/notificationimg/';
			if (isset($_FILES['notificationImage']['name'])) {
				$tmpFile = $_FILES['notificationImage']['tmp_name'];
				$ext = pathinfo($_FILES['notificationImage']['name'], PATHINFO_EXTENSION);
				$filename = $uploadDir . '/' . $cityCode . '-NTFY.' . $ext;
				move_uploaded_file($tmpFile, $filename);
				if (file_exists($filename)) {
					$imgName = base_url() . '/uploads/notificationimg/' . $cityCode . '-NTFY.' . $ext;
				}
			}

			if ($cityCode != "") {
				if ($clients != "") {
				    //pass clients
				} else if($area!=""){
					$resultArea = $this->db->query("Select distinct(clientmaster.code) from clientmaster inner join clientaddresslineentries on clientmaster.code=clientaddresslineentries.clientCode and clientaddresslineentries.isActive=1 where areaCode in (" . $area . ") AND clientmaster.isActive=1");
				    if ($resultArea) {
            			foreach ($resultArea->result() as $rowData) {
            					$clients != "" && $clients .= ",";
					            $clients .= "'" . $rowData->code . "'";
            			}
				    }
				}
                else
                {
                    $resultCity = $this->db->query("Select clientmaster.code from clientmaster where  cityCode ='".$cityCode."'");
                    if ($resultCity) {
            			foreach ($resultCity->result() as $rowData) {
            					$clients != "" && $clients .= ",";
					            $clients .= "'" . $rowData->code . "'";
            			}
				    }
                }
          
                $resultForCount = $this->db->query("Select firebaseId from clientdevicedetails where clientCode in (" . $clients . ") and  firebaseId IS NOT NULL and firebaseId <> ''");
             
                $data['firebaseIds']=array();
				if ($resultForCount != false) {
				    
					$data['listLength'] = sizeof($resultForCount->result_array());
        		    $data['firebaseIds']=$resultForCount;
        		   
    		    	$data['notificationData'] = array("title" => $title, "message" => $msg, "image" => $imgName, 'type' => 'notification');
    		    	
			        $this->load->view('dashboard/pushcustomer_notification/notification', $data);
				}
				else
				{
				    	$this->session->set_flashdata('message', json_encode("Customer with firebase id not found......!"));
				        redirect('CustomerNotification/listRecords', 'refresh');
				}
			} else {
				$this->session->set_flashdata('message', json_encode("city not selected"));
				redirect('CustomerNotification/listRecords', 'refresh');
			}
		}  else {
			//$this->session->set_flashdata('message', json_encode($response));  
			redirect('CustomerNotification/listRecords', 'refresh');
		}
	}

	public function sendCommonNotification()
	{
		$firebaseIds = $this->input->post('firebaseIdsArray');
		$title = $this->input->post('title');
		$message = $this->input->post('message');
		$image = $this->input->post('image');
		$type = $this->input->post('type');
		$start = $this->input->post('startVal');
		$end = $this->input->post('endVal');
		$random = rand(1, 999);
		
		if ($firebaseIds!="") {
			$DeviceIdsArr = array();
			foreach ($firebaseIds as $rowData) {
				$DeviceIdsArr[] = $rowData;
			}

			$dataArr = array();
			$dataArr['device_id'] = $DeviceIdsArr;
			$dataArr['message'] = $message; //Message which you want to send
			$dataArr['title'] = $title;
			$dataArr['image'] = $image;
			$dataArr['product_id'] = "";
			$dataArr['random_id'] = $random;
			$dataArr['type'] = $type;

			$notification['device_id'] = $DeviceIdsArr;
			$notification['message'] = $message; //Message which you want to send
			$notification['title'] = $title;
			$notification['image'] = $image;
			$notification['product_id'] = "";
			$notification['random_id'] = $random;
			$notification['type'] = $type;

			$notify = $this->notificationlibv_3->sendNotification($dataArr, $notification);
			
			$notifyresponse = json_encode($notify);
            log_message("error",$notifyresponse);
			if ($notify) {
				//print_r($notify);
			} else {
			    return 'Notification Failed';
			}
		}
	}

	public function simpleNotify()
	{
		if (isset($_POST['simple'])) {
			$cityCode = $this->input->post('cityCode');
			$city = "";
			if (isset($cityCode)) {
				foreach ($cityCode as $ad) {
					$city != "" && $city .= ",";
					$city .= "'" . $ad . "'";
				}
			}

			$title = $this->input->post('title');
			$msg = $this->input->post('msg');
			$place_id = $this->input->post('place');
			$imgName = $this->input->post('img');
			// $cityCode = trim($this->input->post('cityCode'));
			$random = rand(0, 999);
			$data = array("title" => $title, "message" => $msg, "image" => $imgName, "product_id" => $place_id, "random_id" => $random, 'type' => 'productOffer');
			$DeviceIdsArr = array();
			// $result = $this->db->query("Select firebaseId from clientmaster where isActive=1 and cityCode='".$cityCode."'");
			$result = $this->db->query("Select firebaseId from clientmaster where isActive=1 and cityCode in (" . $city . ")");
			// print_r($result->result_array()[0]['firebaseId']);
			// print_r($this->db->last_query());
			// exit();
			if ($result->num_rows() > 0) {
				$fbData = $result;
				$listLength = sizeof($result->result_array());
				$modVal = $listLength / 100;
				if ($modVal > 0 && $modVal < 100) {
					foreach ($fbData->result() as $rowData) {
						$DeviceIdsArr[] = $rowData->firebaseId;
					}
					$dataArr = array();
					$dataArr['device_id'] = $DeviceIdsArr;
					$dataArr['message'] = $data['message']; //Message which you want to send
					$dataArr['title'] = $data['title'];
					$dataArr['image'] = $data['image'];
					$dataArr['product_id'] = $data['product_id'];
					$dataArr['random_id'] = $data['random_id'];
					$dataArr['type'] = $data['type'];

					$notification['device_id'] = $DeviceIdsArr;
					$notification['message'] = $data['message']; //Message which you want to send
					$notification['title'] = $data['title'];
					$notification['image'] = $data['image'];
					$notification['product_id'] = $data['product_id'];
					$notification['random_id'] = $data['random_id'];
					$notification['type'] = $data['type'];


					$notify = $this->notificationlibv_3->sendNotification($dataArr, $notification);
					if ($notify) {
						$response = 'Notification Send Successfully';
					} else {
						$response = 'Something Error Occure while Send Notification..!!';
					}
				} else {
					for ($i = 1; $i <= $modVal; $i++) {
						$offset = $i * 100;
						// $result = $this->db->query("SELECT firebaseId FROM clientmaster where isActive=1 and cityCode ='".$cityCode."' limit ".$offset.",100");
						$result = $this->db->query("SELECT firebaseId FROM clientmaster where isActive=1 and cityCode in (" . $city . ") limit " . $offset . ",100");
						if ($result) {
							$DeviceIdsArr = array();
							foreach ($result->result() as $rowData) {
								$DeviceIdsArr[] = $rowData->firebaseId;
							}
							$dataArr = array();
							$dataArr['device_id'] = $DeviceIdsArr;
							$dataArr['message'] = $data['message']; //Message which you want to send
							$dataArr['title'] = $data['title'];
							$dataArr['image'] = $data['image'];
							$dataArr['product_id'] = $data['product_id'];
							$dataArr['random_id'] = $data['random_id'];
							$dataArr['type'] = $data['type'];

							$notification['device_id'] = $DeviceIdsArr;
							$notification['message'] = $data['message']; //Message which you want to send
							$notification['title'] = $data['title'];
							$notification['image'] = $data['image'];
							$notification['product_id'] = $data['product_id'];
							$notification['random_id'] = $data['random_id'];
							$notification['type'] = $data['type'];

							$notify = $this->notificationlibv_3->sendNotification($dataArr, $notification);
							if ($notify) {
								$response = 'Notification Send Successfully';
							} else {
								$response = 'Something Error Occure while Send Notification..!!';
							}
						}
					}
				}
			} else {
				$response = 'Something went wrong!Please try later.';
			}
			$this->session->set_flashdata('message', json_encode($response));
			redirect('CustomerNotification/listRecords', 'refresh');
		}
	}

	public function customNotify()
	{
		if (isset($_POST['custom'])) {
			$cityCode = $this->input->post('cityCode2');
			$city = "";
			if (isset($cityCode)) {
				foreach ($cityCode as $ad) {
					$city != "" && $city .= ",";
					$city .= "'" . $ad . "'";
				}
			}

			$title = $this->input->post('title');
			$msg = $this->input->post('msg');
			$place_id = $this->input->post('place');
			$clientcode = trim($this->input->post('client'));
			$imgName = $this->input->post('img');
			// $cityCode = trim($this->input->post('cityCode2'));
			/*-- File Upload --*/
			$uploadRootDir = FCPATH . 'uploads';
			$uploadDir = FCPATH . 'uploads/notificationimg/';
			if (isset($_FILES['catimg']['name'])) {
				$tmpFile = $_FILES['catimg']['tmp_name'];
				$ext = pathinfo($_FILES['catimg']['name'], PATHINFO_EXTENSION);
				$filename = $uploadDir . '/' . $place_id . '-NTFY.' . $ext;
				move_uploaded_file($tmpFile, $filename);
				if (file_exists($filename)) {
					$imgName = base_url() . '/uploads/notificationimg/' . $place_id . '-NTFY.' . $ext;
				}
			}

			if ($cityCode != "") {
				if ($clientcode != "") {
					// $result = $this->db->query("Select firebaseId from clientmaster where isActive=1 and cityCode='".$cityCode."' and code='".$clientcode."'");
					$result = $this->db->query("Select firebaseId from clientmaster where isActive=1 and cityCode in (" . $city . ") and code='" . $clientcode . "'");
				} else {
					// $result = $this->db->query("Select firebaseId from clientmaster where isActive=1 and cityCode='".$cityCode."'");    
					$result = $this->db->query("Select firebaseId from clientmaster where isActive=1 and cityCode in (" . $city . ") ");
				}

				if ($result->num_rows() > 0) {
					$fbData = $result;
					$listLength = sizeof($result->result_array());
					$modVal = $listLength / 100;
					if ($modVal > 0 && $modVal < 100) {
						foreach ($fbData->result() as $rowData) {
							$DeviceIdsArr[] = $rowData->firebaseId;
						}
						$random = rand(0, 999);
						$data = array("title" => $title, "message" => $msg, "image" => $imgName, "product_id" => $place_id, "random_id" => $random, 'type' => 'productOffer');
						$dataArr = array();
						$dataArr['device_id'] = $DeviceIdsArr;
						$dataArr['message'] = $data['message']; //Message which you want to send
						$dataArr['title'] = $data['title'];
						$dataArr['image'] = $data['image'];
						$dataArr['product_id'] = $data['product_id'];
						$dataArr['random_id'] = $random;
						$dataArr['type'] = $data['type'];

						$notification['device_id'] = $DeviceIdsArr;
						$notification['message'] = $data['message']; //Message which you want to send
						$notification['title'] = $data['title'];
						$notification['image'] = $data['image'];
						$notification['product_id'] = $data['product_id'];
						$notification['random_id'] = $data['random_id'];
						$notification['type'] = $data['type'];

						$notify = $this->notificationlibv_3->sendNotification($dataArr, $notification);
						if ($notify) {
							$response = 'Notification Send Successfully';
						} else {
							$response = 'Something Error Occure while Send Notification..!!';
						}
					} else {
						for ($i = 1; $i <= $modVal; $i++) {
							$offset = $i * 100;
							// $result = $this->db->query("SELECT firebaseId FROM clientmaster where isActive=1 and cityCode ='".$cityCode."' limit ".$offset.",100");
							$result = $this->db->query("SELECT firebaseId FROM clientmaster where isActive=1 and cityCode in (" . $city . ") limit " . $offset . ",100");
							if ($result) {
								$DeviceIdsArr = array();
								foreach ($result->result() as $rowData) {
									$DeviceIdsArr[] = $rowData->firebaseId;
								}
								$random = rand(0, 999);
								$data = array("title" => $title, "message" => $msg, "image" => $imgName, "product_id" => $place_id, "random_id" => $random, 'type' => 'productOffer');
								$dataArr = array();
								$dataArr['device_id'] = $DeviceIdsArr;
								$dataArr['message'] = $data['message']; //Message which you want to send
								$dataArr['title'] = $data['title'];
								$dataArr['image'] = $data['image'];
								$dataArr['product_id'] = $data['product_id'];
								$dataArr['random_id'] = $random;
								$dataArr['type'] = $data['type'];

								$notification['device_id'] = $DeviceIdsArr;
								$notification['message'] = $data['message']; //Message which you want to send
								$notification['title'] = $data['title'];
								$notification['image'] = $data['image'];
								$notification['product_id'] = $data['product_id'];
								$notification['random_id'] = $data['random_id'];
								$notification['type'] = $data['type'];

								$notify = $this->notificationlibv_3->sendNotification($dataArr, $notification);
								$notifyresponse = json_encode($notify);
                    			log_message("error",$notifyresponse);
								if ($notify) {
									$response = 'Notification Send Successfully';
								} else {
									$response = 'Something Error Occure while Send Notification..!!';
								}
							} else {
								$response = 'No records found to send notification!';
							}
						}
					}
				} else {
					$response = 'Please select City before sending notification!';
				}
			} else {
				$response = 'Please select City before sending notification!';
			}

			$this->session->set_flashdata('message', json_encode($response));
			redirect('CustomerNotification/listRecords', 'refresh');
		}
	}


	public function getProductImg()
	{
		$code =  $this->input->get('id');
		$imgpath_jpg = 'uploads/product/' . $code . '/0.jpg';
		$imgpath_png = 'uploads/product/' . $code . '/0.png';
		if (file_exists($imgpath_jpg) == true) {
			$result["status"] = 'yes';
			$result["img"]	= "0.jpg";
		} else if (file_exists($imgpath_png) == true) {
			$result["status"] = 'yes';
			$result["img"]	= "0.png";
		} else {
			$result["status"] = 'no';
			$result["img"] = "";
		}
		echo json_encode($result);
	}


	//05/11/2019
	public function getOrderNotificationList()
	{
		$currentDate = date('Y-m-d');

		$addID = $this->session->userdata['logged_in' . $this->session_key]['code'];

		$userRole = $this->session->userdata['logged_in' . $this->session_key]['role'];
		$userName = $this->session->userdata['logged_in' . $this->session_key]['username'];

		$table_name = 'ordermaster';
		$orderColumns = array('ordermaster.*, clientmaster.name');
		$cond = array('ordermaster' . '.isActive' => '1');
		$orderBy = array('ordermaster' . '.id' => 'DESC');
		$joinType = array('clientmaster' => 'inner');
		$join = array('clientmaster' => 'ordermaster' . '.clientCode=' . 'clientmaster' . '.code');
		$like = array();
		$limit = 10; //1
		$offset = array();
		$groupByColumn = array();
		$extraCondition = " (ordermaster.orderStatus = 'PLC' OR ordermaster.orderStatus='PND' OR ordermaster.orderStatus='DEL' OR ordermaster.orderStatus='RJT') AND (ordermaster.placedTime BETWEEN '" . $currentDate . " 00:00:00' AND '" . $currentDate . " 23:59:59.999' OR ordermaster.shippedTime BETWEEN '" . $currentDate . " 00:00:00' AND '" . $currentDate . " 23:59:59.999' OR ordermaster.deliveredTime BETWEEN '" . $currentDate . " 00:00:00' AND '" . $currentDate . " 23:59:59.999') and ordermaster.editDate BETWEEN '" . $currentDate . " 00:00:00' AND '" . $currentDate . " 23:59:59.999' AND (ordermaster.isDelete=0 OR ordermaster.isDelete IS NULL)";
		$Records = $this->GlobalModel->selectQuery($orderColumns, $table_name, $cond, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);	  
		$actionHtml = "";
		if ($Records) {
			$srno = 1;
			$data = array();
			$count = sizeof($Records->result());
			if ($count > 5) {
				$count = $count . "+";
			}
			$actionHtml .= '<span class="with-arrow"><span class="bg-primary"></span></span>
                                <ul class="list-style-none">
                                    <li>
                                        <div class="drop-title bg-primary text-white">
                                            <h4 class="m-b-0 m-t-5">' . $count . ' &nbspNew</h4>
                                            <span class="font-light">Notifications</span>
                                        </div>
                                    </li>
									<div class="message-center notifications  ps-theme-default">';

			foreach ($Records->result() as $row) {


				$status = '';
				if ($row->orderStatus == 'PLC') {
					$status = '<label class="label label-success">Placed</label>';
				} else if ($row->orderStatus == 'SHP') {
					$status = '<label class="label label-success">Shipped</label>';
				} else if ($row->orderStatus == 'DEL') {
					$status = '<label class="label label-success">Delivered</label>';
				} else {
					$status = '<label class="label label-success">Cancelled</label>';
				}


				$actionHtml .= '            
					        <li class="border-top p-1"> 
					        <div class="row">
					          <div class="col-sm-2 ">
								<span> </span>
							  </div>

							   <div class="col-sm-10">
							   <div class="mail-contnet">
									
								<h5 class="message-title">' . $row->name . ' </h5> <h6>Phone Number:- ' . $row->phone . '</h6> <h6>Order ID:- ' . $row->code . '</h6> <span class="mail-desc"></span><br><span class="time">' . $status . '</span> </div>
							   	</div>
								
					        </div>
							</li>';
			}

			$actionHtml .= '</div> </ul>';
		} else {
			$actionHtml = '<span class="with-arrow"><span class="bg-primary"></span></span>
                                <ul class="list-style-none">
                                    <li>
                                        <div class="drop-title bg-primary text-white">
                                            <h4 class="m-b-0 m-t-5"> 0 &nbspNew</h4>
                                            <span class="font-light">No Notifications Received Yet!</span>
                                        </div>
                                    </li>
									 </ul>';
		}
		echo $actionHtml;
	}


	public function getOrderNotificationCount()
	{
		$currentDate = date('Y-m-d');
		$addID = $this->session->userdata['logged_in' . $this->session_key]['code'];
		$userRole = $this->session->userdata['logged_in' . $this->session_key]['role'];
		$userName = $this->session->userdata['logged_in' . $this->session_key]['username'];
		$table_name = 'ordermaster';
		$orderColumns = array('ordermaster.*, clientmaster.name');
		$cond = array('ordermaster' . '.isActive' => '1');
		$orderBy = array();
		$joinType = array('clientmaster' => 'inner');
		$join = array('clientmaster' => 'ordermaster' . '.clientCode=' . 'clientmaster' . '.code');
		$like = array();
		$limit = 10; //1
		$offset = array();
		$groupByColumn = array();
		$extraCondition = " (ordermaster.orderStatus = 'PLC' OR ordermaster.orderStatus='PND' OR ordermaster.orderStatus='DEL' OR ordermaster.orderStatus='RJT') AND (ordermaster.placedTime BETWEEN '" . $currentDate . "' AND '" . $currentDate . " 23:59:59.999' OR ordermaster.shippedTime BETWEEN '" . $currentDate . "' AND '" . $currentDate . " 23:59:59.999' OR ordermaster.deliveredTime BETWEEN '" . $currentDate . "' AND '" . $currentDate . " 23:59:59.999') AND ordermaster.editDate BETWEEN '" . $currentDate . "' AND '" . $currentDate . " 23:59:59.999' AND (ordermaster.isDelete=0 OR ordermaster.isDelete IS NULL)";
		$Records = $this->GlobalModel->selectQuery($orderColumns, $table_name, $cond, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
		if ($Records) {
			$srno = 1;
			$data = array();
			$count = sizeof($Records->result());
			if ($count > 5) {
				$count = $count . "+";
			}
		} else {
			$count = 0;
		}
		echo $count;
	}

	public function getProductListByCity()
	{
		$cityCode = $this->input->GET('cityCode');
		$city = "";
		if (isset($cityCode)) {
			foreach ($cityCode as $ad) {
				$city != "" && $city .= ",";
				$city .= "'" . $ad . "'";
			}
		}

		$html = "";
		if ($cityCode != "") {
			$table = "productratelineentries";
			$orderColumns = "DISTINCT productmaster.code,productmaster.productName";
			$orderBy = array("productmaster.id" => 'asc');
			// $conditions = array("productratelineentries.cityCode"=>$cityCode);
			$conditions = array();
			$joinType = array("productmaster" => 'inner');
			$join = array("productmaster" => 'productratelineentries.productCode=productmaster.code');
			$extraCondition = " productratelineentries.cityCode in (" . $city . ")";
			$like = array();
			$limit = "";
			$offset = "";
			$groupByColumn = array();
			$Result = $this->GlobalModel->selectQuery($orderColumns, $table, $conditions, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
			// print_r($Result->result_array());
			// print_r($this->db->last_query());
			// exit(); 
			if ($Result) {
				foreach ($Result->result() as $r) {
					$html .= '<option value="' . $r->code . '" id="' . $r->code . '">' . $r->productName . '</option>';
				}
			}
		}
		echo $html;
	}
	
	public function getAddressByCity()
	{
		$html=array();
		 $search=$this->input->get('search');
		$cityCode= $this->input->get('cityCode');
		if($cityCode!=""){
		     $like = array('customaddressmaster.place' => $search . '~both');
			$address = $this->GlobalModel->selectQuery("customaddressmaster.*,citymaster.cityName","customaddressmaster",array("customaddressmaster.cityCode"=>$cityCode),array("customaddressmaster.id"=>'desc'),array("citymaster"=>'customaddressmaster.cityCode=citymaster.code'),array('citymaster'=>'left'),$like);
			if($address){
				foreach($address->result() as $addr){
					$html[] = array('id'=>$addr->code,'text'=>$addr->place.', '.$addr->cityName);
				}
			}  
		} 
		echo  json_encode($html);
	}
	
	public function getCustomersListByCityArea()
	{
	    $search=$this->input->get('search');
	    $cityCode = $this->input->GET('cityCode');
		$areaCodes = $this->input->GET('areaCodes');
		$area = "";
		if(isset($areaCodes) && $areaCodes!="") {
			foreach ($areaCodes as $ad) {
				$area != "" && $area .= ",";
				$area .= "'" . $ad . "'";
			}
		}

		//$html = "<option value=''>Select Customer</option>";
		$html =array();
		if ($cityCode != "") {
		    
			$table = "clientmaster";
			$orderColumns = "DISTINCT clientmaster.code,clientmaster.name,,clientmaster.mobile";
			$orderBy = array("clientmaster.id" => 'desc');
			$conditions = array("clientmaster.cityCode"=>$cityCode);
			$joinType = array("clientprofile"=>"left");
			$join = array("clientprofile"=>"clientmaster.code=clientprofile.clientCode");
			$extraCondition ="";
			if($area!=""){
			    $extraCondition = " clientprofile.areaCode in (" . $area . ")";
			}
		    $like = array('clientmaster.name' => $search . '~both');
			$limit = "";
			$offset = "";
			$groupByColumn = array();
			$Result = $this->GlobalModel->selectQuery($orderColumns, $table, $conditions, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
		// print_r($this->db->last_query());
		// print_r($Result);
		// exit;
			if ($Result) {
				foreach ($Result->result() as $r) {
					//$html .= '<option value="' . $r->code . '" id="' . $r->code . '">' . $r->name . '</option>';
					$html[] = array('id'=>$r->code,'text'=>$r->name.' ('.$r->mobile.')');
				}
			}
			
		}
		echo  json_encode($html);
	}
}
