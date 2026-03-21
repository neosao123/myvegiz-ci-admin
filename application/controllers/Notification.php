<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Notification extends CI_Controller
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
		$data['productmaster'] = $this->GlobalModel->selectData('productmaster');
		$data['clientmaster'] = $this->GlobalModel->selectData('clientmaster');
		$this->load->view('dashboard/header');
		$this->load->view('dashboard/pushnotification/notificationCreate', $data);
		$this->load->view('dashboard/footer');
	}


	public function notificationprocess()
	{
		if (isset($_POST['simple'])) {
			$data['listLength'] = 0;

			$title = $this->input->post('title');
			$msg = $this->input->post('msg');
			$place_id = $this->input->post('place');
			$imgName = $this->input->post('img');
			$cityCode = $this->input->post('cityCode');
			$city = "";
			if (isset($cityCode)) {
				foreach ($cityCode as $ad) {
					$city != "" && $city .= ",";
					$city .= "'" . $ad . "'";
				}
			}
			$data['cityCodes'] = $city;
			if ($city != "") {
				$result = $this->db->query("Select clientdevicedetails.firebaseId from clientdevicedetails inner join clientmaster on clientmaster.code=clientdevicedetails.clientCode where clientmaster.isActive=1 and clientmaster.cityCode in (" . $city . ")  and  clientdevicedetails.firebaseId IS NOT NULL and clientdevicedetails.firebaseId <> ''");
				if ($result->num_rows() > 0) {
					$data['firebaseIds'] = $result;
					$data['listLength'] = sizeof($result->result_array());
				}
				$data['notificationData'] = array("title" => $title, "message" => $msg, "image" => $imgName, "product_id" => $place_id, 'type' => 'productOffer', 'clientCode' => "");

				$this->load->view('dashboard/pushnotification/notification', $data);
			} else {
				$this->session->set_flashdata('message', json_encode("city not selected"));
				redirect('Notification/listRecords', 'refresh');
			}
		} else if (isset($_POST['custom'])) {
			$data['listLength'] = 0;
			$title = $this->input->post('title');
			$msg = $this->input->post('msg');
			//$place_id = $this->input->post('place');
			$imgName = $this->input->post('img');
			$cityCode = $this->input->post('cityCode2');
			$clientcode = $this->input->post('client');
			$place_id = $this->input->post('place');
			$city = "";
			if (isset($cityCode)) {
				foreach ($cityCode as $ad) {
					$city != "" && $city .= ",";
					$city .= "'" . $ad . "'";
				}
			}

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

			$data['cityCodes'] = $city;
			if ($city != "") {
				if ($clientcode != "") {
					$result = $this->db->query("Select firebaseId from clientdevicedetails where  clientCode='" . $clientcode . "'  and  clientdevicedetails.firebaseId IS NOT NULL and clientdevicedetails.firebaseId <> ''");
				} else {
					$result = $this->db->query("Select clientdevicedetails.firebaseId from clientdevicedetails inner join clientmaster on clientmaster.code=clientdevicedetails.clientCode where clientmaster.isActive=1 and clientmaster.cityCode in (" . $city . ")  and  clientdevicedetails.firebaseId IS NOT NULL and clientdevicedetails.firebaseId <> ''");
				}

				if ($result->num_rows() > 0) {
					$data['firebaseIds'] = $result;
					$data['listLength'] = sizeof($result->result_array());
				}

				$data['notificationData'] = array("title" => $title, "message" => $msg, "image" => $imgName, "product_id" => $place_id, 'type' => 'productOffer', 'clientCode' => $clientcode);
				$this->load->view('dashboard/pushnotification/notification', $data);
			} else {
				$this->session->set_flashdata('message', json_encode("city not selected"));
				redirect('Notification/listRecords', 'refresh');
			}
		} else {
			//$this->session->set_flashdata('message', json_encode($response));  
			redirect('Notification/listRecords', 'refresh');
		}
	}

	public function sendCommonNotification()
	{
		$cityCodes = $this->input->post('cityCodes');
		$title = $this->input->post('title');
		$message = $this->input->post('message');
		$image = $this->input->post('image');
		$product_id = $this->input->post('product_id');
		$clientCode = $this->input->post('clientCode');
		$type = $this->input->post('type');
		$firebaseIds = $this->input->post('firebaseIdsArray');
		//echo $image;
		$random = rand(1, 999);
		if ($firebaseIds != "") {
			$DeviceIdsArr = array();
			foreach ($firebaseIds as $rowData) {
				$DeviceIdsArr[] = $rowData;
			}
			$dataArr = array();
			$dataArr['device_id'] = $DeviceIdsArr;
			$dataArr['message'] = $message; //Message which you want to send
			$dataArr['title'] = $title;
			$dataArr['image'] = $image;
			$dataArr['product_id'] = $product_id;
			$dataArr['random_id'] = $random;
			$dataArr['type'] = $type;

			$notification['device_id'] = $DeviceIdsArr;
			$notification['message'] = $message; //Message which you want to send
			$notification['title'] = $title;
			$notification['image'] = $image;
			$notification['product_id'] = $product_id;
			$notification['random_id'] = $random;
			$notification['type'] = $type;
			$notify = $this->notificationlibv_3->sendNotification($dataArr, $notification);
			print_r($notify);
			if ($notify) {
				
				// return 'Notification Send Successfully';
			} else {
				// return 'Notification Failed';
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
			$result = $this->db->query("Select clientdevicedetails.firebaseId from clientdevicedetails inner join clientmaster ON clientdevicedetails.clientCode=clientmaster.code where clientmaster.isActive=1 and cityCode in (" . $city . ")");
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
			redirect('Notification/listRecords', 'refresh');
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
			redirect('Notification/listRecords', 'refresh');
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
		//print_r($this->db->last_query());		 
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
			$extraCondition .=" AND(productmaster.isDelete=0 or productmaster.isDelete is null)";
			$like = array();
			$limit = "";
			$offset = "";
			$groupByColumn = array();
			$Result = $this->GlobalModel->selectQuery($orderColumns, $table, $conditions, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
			// print_r($Result->result_array());
			 // echo $this->db->last_query();
			// exit(); 
			if ($Result) {
				foreach ($Result->result() as $r) {
					$html .= '<option value="' . $r->code . '" id="' . $r->code . '">' . $r->productName . '</option>';
				}
			}
		}
		echo $html;
	}
	public function getCustomersListByCity()
	{
		$cityCode = $this->input->GET('cityCode');
		$city = "";
		if (isset($cityCode)) {
			foreach ($cityCode as $ad) {
				$city != "" && $city .= ",";
				$city .= "'" . $ad . "'";
			}
		}

		$html = "<option value=''>Select Customer</option>";
		// $cityCode = $this->input->GET('cityCode');
		if ($cityCode != "") {
			$table = "clientmaster";
			$orderColumns = "clientmaster.code,clientmaster.name";
			$orderBy = array("clientmaster.id" => 'desc');
			// $conditions = array("clientmaster.cityCode"=>$cityCode);
			$conditions = array();
			$joinType = array();
			$join = array();
			$extraCondition = " clientmaster.cityCode in (" . $city . ")";
			$like = array();
			$limit = "";
			$offset = "";
			$groupByColumn = array();
			$Result = $this->GlobalModel->selectQuery($orderColumns, $table, $conditions, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
			if ($Result) {
				foreach ($Result->result() as $r) {
					$html .= '<option value="' . $r->code . '" id="' . $r->code . '">' . $r->name . '</option>';
				}
			}
		}
		echo $html;
	}
}
