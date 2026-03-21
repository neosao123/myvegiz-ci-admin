<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Usermaster extends CI_Controller {
    var $privilege;
	
    public function __construct() {
        parent::__construct();
        $this->load->helper('form', 'url', 'html');
        $this->load->library('form_validation');
        $this->load->model('GlobalModel');
        $this->load->model('GlobalModel1');
        $this->load->model('Testing');
        $this->session_key = $this->session->userdata('key' . SESS_KEY);
        if (!isset($this->session->userdata['logged_in' . $this->session_key]['code'])) {
            redirect('Admin/login', 'refresh');
        }
    }
	
    public function add() {
        $data['emp'] = $this->GlobalModel->selectData('employeemaster');
        $data['userRole'] = $this->GlobalModel->selectData('userrolemaster');
        $data['city'] = $this->GlobalModel->selectActiveData('citymaster');        
        $this->load->view('dashboard/header');
        $this->load->view('dashboard/usermaster/add', $data);
        $this->load->view('dashboard/footer');
    }
	
    public function listRecords() {
        $data['error'] = $this->session->flashdata('response');
        $data['query'] = $this->GlobalModel->selectData('usermaster');
        $data['employee'] = $this->GlobalModel->selectData('employeemaster');
        $data['userCount'] = $this->GlobalModel->getCountOfPerticularValue('usermaster', 'role', 'DLB');
        $data['city'] = $this->GlobalModel->selectActiveData('citymaster');
        $this->load->view('dashboard/header');
        $this->load->view('dashboard/usermaster/list', $data);
        $this->load->view('dashboard/footer');
    }
	
    public function getUserName() {
        $empCode = $this->input->get('empCode');
        $dataQuery = $this->GlobalModel->selectDataById($empCode, 'employeemaster');
        $firstName = '';
        $email = '';
        foreach ($dataQuery->result() as $row) {
            $firstName = $row->firstName;
            $email = $row->email;
        }
        $data = ["firstName" => $firstName, "email" => $email];
        echo json_encode($data);
    }
	
    public function getUserMasterList() {
        $empCode = $this->input->get('employeeCode');
        $username = $this->input->get('userName');
        $role = $this->input->get('userRole');
        $cityCode = $this->input->get('cityCode');
        $tableName = "usermaster";
        $search = $this->input->GET("search") ['value'];
        $orderColumns = array("usermaster.*,citymaster.cityName,employeemaster.firstName");
        $condition = array('usermaster.place' => $empCode, 'usermaster.username' => $username, 'usermaster.role' => $role, 'citymaster.cityName' => $cityCode);
        $orderBy = array('usermaster' . '.id' => 'DESC');
        $joinType = array('citymaster' => 'left','employeemaster'=>'inner');
        $join = array('citymaster' => 'usermaster.cityCode=citymaster.code','employeemaster' => 'usermaster.empCode=employeemaster.code');
        $groupByColumn = array();
        $limit = $this->input->GET("length");
        $offset = $this->input->GET("start");
        $extraCondition = " (usermaster.isDelete ='0' or usermaster.isDelete IS NULL) ";
        $like = array();
        $Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
        $srno = $_GET['start'] + 1;
        $data = array();
		//print_r($this->db->last_query());
		$dataCount = 0;
		if($Records){
			foreach ($Records->result() as $row) {
				if ($row->role == "ADM") {
					$role = "<span class='label label-sm label-primary'>Admin</span>";
				} else if ($row->role == "DLB") {
					$role = "<span class='label label-sm label-primary'>Delivery Boy</span>";
					
					if($row->deliveryType =='food'){
					    $role .= "<div class='mt-2'>Accetps : <span class='badge badge-info'>Food/Vege/Grocery Orders</span></div>";
					} else {
					    $role .= "<div class='mt-2'>Accetps : <span class='badge badge-success'>Vege/Grocery Orders</span></div>";
					}
					
				} else {
					$role = "<span class='label label-sm label-primary'>User</span>";
				}
				if ($row->isActive == "1") {
					$status = "<span class='label label-sm label-success'>Active</span>";
				} else {
					$status = "<span class='label label-sm label-warning'>Inactive</span>";
				}
				$empCode = $row->empCode;
				$actionHtml = '
					<div class="btn-group">
						<button type="button" class="btn btn-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<i class="ti-settings"></i>
						</button>
						<div class="dropdown-menu animated slideInUp" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 35px, 0px);">
							  <a class="dropdown-item  blue" data-toggle="modal" data-target="#responsive-modal" data-seq="' . $row->code . '" href><i class="ti-eye"></i> Open</a>
							  <a class="dropdown-item" href="' . base_url() . 'Usermaster/edit/' . $row->code . '"><i class="ti-pencil-alt"></i> Edit</a>
							  <a class="dropdown-item mywarning " data-seq="' . $row->code . '" id="' . $row->code . '"><i class="ti-trash" ></i> Delete</a>
						</div>
					</div>';
				$data[] = array($srno, $row->code,$row->cityName, $row->firstName, $row->username, $role, $status, $actionHtml);
				$srno++;
			}
			$dataCount = sizeof($this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, "", "", $groupByColumn, $extraCondition)->result_array());
		}
       
        $output = array("draw" => intval($_GET["draw"]), "recordsTotal" => $dataCount, "recordsFiltered" => $dataCount, "data" => $data);
        echo json_encode($output);
    }
    public function save() {
        $empCode = $this->input->post("empCode");
        $username = $this->input->post("userName");
        $userRole = trim($this->input->post("userRole"));
        $dataQuery = $this->GlobalModel->selectDataById($userRole, 'userrolemaster');
        $rights = $dataQuery->result() [0]->rights;
        // Activity Track Starts
        $addID = $this->session->userdata['logged_in' . $this->session_key]['code'];
        $sessionUserRole = $this->session->userdata['logged_in' . $this->session_key]['role'];
        $userA = $this->session->userdata['logged_in' . $this->session_key]['username'];
        $role = "";
        switch ($sessionUserRole) {
            case "ADM":
                $role = "Admin";
            break;
            case "USR":
                $role = "User";
            break;
        }
        $ip = $_SERVER['REMOTE_ADDR'];
        $text = $role . " " . $userA . ' added new user "' . $username . '" from ' . $ip;
        $log_text = array('code' => "demo", 'addID' => $addID, 'logText' => $text);
        // Activity Track Ends
        $result = sizeof($this->GlobalModel->selectActiveDataByField('empCode', $empCode, 'usermaster')->result());
        if ($result != false) {
            $data = array('errormessage' => 'This User is Already Exist!');
            $data['emp'] = $this->GlobalModel->selectData('employeemaster');
			$data['city'] = $this->GlobalModel->selectActiveData('citymaster');
            $data['userRole'] = $this->GlobalModel->selectData('userrolemaster'); 
            $this->load->view('dashboard/header');
            $this->load->view('dashboard/usermaster/add', $data);
            $this->load->view('dashboard/footer');
        } else {
            $userResult = sizeof($this->GlobalModel->selectActiveDataByField('username', $username, 'usermaster')->result());
            if ($userResult != false) {
                $data = array('errormessage' => 'Reserved User Name. Choose another User Name');
                $data['emp'] = $this->GlobalModel->selectData('employeemaster');
                $data['userRole'] = $this->GlobalModel->selectData('userrolemaster');
				$data['city'] = $this->GlobalModel->selectActiveData('citymaster'); 
                $this->load->view('dashboard/header');
                $this->load->view('dashboard/usermaster/add', $data);
                $this->load->view('dashboard/footer');
            } else {
                $this->form_validation->set_rules('empCode', 'Employee Name', 'required');
                $this->form_validation->set_rules('userName', 'User Name', 'required');
                $this->form_validation->set_rules('userEmail', 'Email', 'required');
                $this->form_validation->set_rules('password', 'User Password', 'required'); 
                $this->form_validation->set_rules('confirmPassword', 'Confirm Password', 'required');
                $this->form_validation->set_rules('userRole', 'User Role', 'required');
				if($userRole=='DLB'){
					$this->form_validation->set_rules('cityCode', 'City', 'required');
				}
                if ($this->form_validation->run() == FALSE) {
                    $data['error_message'] = '* Fields are Required!';
                    $data['emp'] = $this->GlobalModel->selectData('employeemaster');
                    $data['userRole'] = $this->GlobalModel->selectData('userrolemaster');
                    $table_name = 'customaddressmaster';
                    $orderColumns = array("customaddressmaster.*");
                    $cond = array('customaddressmaster' . '.isDelete' => 0, 'customaddressmaster' . '.isActive' => 1, 'customaddressmaster.isService' => 1);
                    $data['address'] = $this->GlobalModel->selectQuery($orderColumns, $table_name, $cond);
                    $this->load->view('dashboard/header');
                    $this->load->view('dashboard/usermaster/add', $data);
                    $this->load->view('dashboard/footer');
                } else {
                    if (sizeof($this->GlobalModel->selectDataByFieldWithoutisDelete('empCode', $empCode, 'usermaster')->result()) == 0) {
                        $data = array('empCode' => $empCode, 'deliveryType'=>$this->input->post('deliveryType') ,'cityCode' => $this->input->post('cityCode'), 'username' => $username, 'role' => $userRole, 'userEmail' => trim($this->input->post("userEmail")), 'password' => md5($this->input->post("password")), 'addID' => $addID, 'addIP' => $ip);
						$data['isActive'] = $this->input->post('isActive');
                        $result = $this->GlobalModel->addWithoutYear($data, 'usermaster', 'USREM');
                        if ($result != 'false') { 
                            $profilePhoto = "";
                            $uploadRootDir = 'uploads/';
                            $uploadDir = 'uploads/profilePhoto/';
                            if (!empty($_FILES['profilePhoto']['name'])) {
                                $tmpFile = $_FILES['profilePhoto']['tmp_name'];
                                $filename = $result . time() . '.jpg';
                                $path = $uploadDir . '/' . $filename;
                                move_uploaded_file($tmpFile, $path);
                                $profilePhoto = $filename;
                                $subData = array('profilePhoto' => $filename);
                                $filedoc = $this->GlobalModel->doEdit($subData, 'usermaster', $result);
                                $statusFlag = 1;
                            }
                            $subData = array('profilePhoto' => $profilePhoto);
                            $filedoc = $this->GlobalModel->doEdit($subData, 'usermaster', $result);
							
							$dataDbActive['deliveryBoyCode'] = $result;
							$dataDbActive['orderCount'] = 0;
							$dataDbActive['loginStatus'] = 0;
							$dataDbActive['isActive'] = 1;
							$resultDbActive = $this->GlobalModel->addWithoutYear($dataDbActive, 'deliveryBoyActiveOrder', 'DBA');
							
                            $this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
                            redirect('Usermaster/listRecords');
                        } else {
                            $response['status'] = false;
                            $response['message'] = "Failed To Add User Master";
                            $this->session->set_flashdata('response', json_encode($response));
                            redirect('Usermaster/listRecords');
                        }
                    } else {
                        $updateData = array('username' => $username, 'role' => $userRole, 'userEmail' => trim($this->input->post("userEmail")), 'password' => md5($this->input->post("password")), 'points' => trim($this->input->post('points')), 'editID' => $addID, 'editIP' => $ip, 'isDelete' => 0, 'isActive' => 0);
                        $updateres = $this->GlobalModel->doEditWithField($updateData, 'usermaster', 'empCode', $empCode);
                        if ($updateres == 'true') {
                            $address = $this->input->post('addressCode');
                            $addressData = array();
                            for ($i = 0;$i < sizeof($address);$i++) {
                                $addressData = array('userCode' => $result, 'addressCode' => $address[$i], 'isActive' => $this->input->post("isActive"));
                                $addLineDataResult = $this->GlobalModel->addWithoutYear($addressData, 'useraddresslineentries', 'USRADR');
                                if ($addLineDataResult == 'true') {
                                    $addResultFlag = true;
                                }
                            }
                            $this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
                            // redirect('usermaster/addUserRights/'.$empCode);
                            //$this->addUserRights($empCode);
                            redirect('Usermaster/listRecords');
                        } else {
                            $response['status'] = false;
                            $response['message'] = "Failed To Add User Master";
                            $this->session->set_flashdata('response', json_encode($response));
                            redirect('Usermaster/listRecords');
                        }
                    }
                }
            }
        }
    }
    public function addUserRights($empCode) {
        $data['empCode'] = $empCode;
        $this->load->view('dashboard/header');
        $this->load->view('dashboard/usermaster/userAccess', $data);
        $this->load->view('dashboard/footer');
    }
    public function saveRights() {
        $flag = false;
        $empCode = $this->input->post("empCode");
        $userRights = $this->input->post("userrights");
        $userAction = $this->input->post("useraction");
        $this->GlobalModel->deleteForeverFromField('empCode', $empCode, 'userrightsmaster');
        $this->GlobalModel->deleteForeverFromField('empCode', $empCode, 'userrightsactionmaster');
        $data = array('empCode' => $empCode, 'empRights' => $userRights, 'isActive' => 1);
        $code = $this->GlobalModel->addWithoutYear($data, 'userrightsmaster', 'RIGT');
        if ($code != 'false') {
            $actionData = json_decode($userAction, true);
            foreach ($actionData['actionData'] as $action) {
                $dataAction = array('empCode' => $empCode, 'controllerKey' => $action['controllerKey'], 'empPrivilege' => json_encode($action['controllerData']), 'isActive' => 1);
                $actionResult = $this->GlobalModel->addWithoutYear($dataAction, 'userrightsactionmaster', 'URAP');
                if ($actionResult != 'false') {
                    $flag = true;
                }
            }
        }
        if ($flag) {
            $updateData = array('isActive' => 1);
            $updateres = $this->GlobalModel->doEditWithField($updateData, 'usermaster', 'empCode', $empCode);
            $response['status'] = true;
            $response['message'] = "User Rights Successfully Added";
        } else {
            $response['status'] = false;
            $response['message'] = "Failed to add user rights";
        }
        echo json_encode($response);
    }
    public function edit($code = NULL) {
        $data['query'] = $this->GlobalModel->edit('usermaster', $code);
        $empCode = $data['query']->result() [0]->empCode;
		$cityCode = $data['query']->result() [0]->cityCode;
		$data['city'] = $this->GlobalModel->selectActiveData('citymaster');
        $data['emp'] = $this->GlobalModel->selectDataByField('code', $empCode, 'employeemaster'); 
        $table_name = 'customaddressmaster';
        $orderColumns = array("customaddressmaster.*");
        $cond = array('customaddressmaster' . '.isDelete' => 0, 'customaddressmaster' . '.isActive' => 1, 'customaddressmaster.isService' => 1,'customaddressmaster.cityCode'=>$cityCode);
        $data['address'] = $this->GlobalModel->selectQuery($orderColumns, $table_name, $cond);
        $data['userRole'] = $this->GlobalModel->selectData('userrolemaster');
        $data['usRole'] = $this->session->userdata['logged_in' . $this->session_key]['role'];
        $this->load->view('dashboard/header');
        $this->load->view('dashboard/usermaster/edit', $data);
        $this->load->view('dashboard/footer');
    }
    public function update() {
        $code = $this->input->post("code");
        $userName = $this->input->post("userName");
        $password = $this->input->post("password");
        $empCode = $this->input->post("empCode");
		$uRole = $this->input->post('userRole');
        //Activity Track Starts
        $statusFlag = 0;
        $addID = $this->session->userdata['logged_in' . $this->session_key]['code'];
        $userRole = $this->session->userdata['logged_in' . $this->session_key]['role'];
        $userA = $this->session->userdata['logged_in' . $this->session_key]['username'];
        $role = "";
        switch ($userRole) {
            case "ADM":
                $role = "Admin";
            break;
            case "USR":
                $role = "User";
            break;
        }
        $ip = $_SERVER['REMOTE_ADDR'];
        $text = $role . " " . $userA . ' updated user "' . $userName . '" from ' . $ip;
        $log_text = array('code' => "demo", 'addID' => $addID, 'logText' => $text);
        //Activity Track Ends
        $this->form_validation->set_rules('empCode', 'Employee Name', 'required');
        $this->form_validation->set_rules('userName', 'User Name', 'required');
        $this->form_validation->set_rules('userEmail', 'Email', 'required');
        $this->form_validation->set_rules('userRole', 'User Role', 'required'); 
		if($uRole=='DLB'){
			$this->form_validation->set_rules('cityCode', 'City', 'required');
		}
        if ($this->form_validation->run() == FALSE) {
            $data['error_message'] = '* Fields are Required!';
            $data['emp'] = $this->GlobalModel->selectDataByField('code', $empCode, 'employeemaster');
            $data['userRole'] = $this->GlobalModel->selectData('userrolemaster');
            $data['query'] = $this->GlobalModel->edit('usermaster', $code);
			$data['city'] = $this->GlobalModel->selectActiveData('citymaster'); 
            $table_name = 'customaddressmaster';
            $orderColumns = array("customaddressmaster.*");
            $cond = array('customaddressmaster' . '.isDelete' => 0, 'customaddressmaster' . '.isActive' => 1, 'customaddressmaster.isService' => 1);
            $data['address'] = $this->GlobalModel->selectQuery($orderColumns, $table_name, $cond);
            $data['usRole'] = $this->session->userdata['logged_in' . $this->session_key]['role'];
            $this->load->view('dashboard/header');
            $this->load->view('dashboard/usermaster/edit', $data);
            $this->load->view('dashboard/footer');
        } else {
            $data = array('empCode' => trim($this->input->post("empCode")), 'cityCode' => $this->input->post('cityCode'), 'username' => trim($this->input->post("userName")), 'role' => trim($this->input->post("userRole")), 'userEmail' => trim($this->input->post("userEmail")), 'editID' => $addID, 'editIP' => $ip, 'isActive' => $this->input->post("isActive"));
            if($password!="") {
                $data['password'] = md5($password);
            }
            $data = array('empCode' => trim($this->input->post("empCode")), 'deliveryType'=>$this->input->post('deliveryType') ,'cityCode' => $this->input->post('cityCode'), 'username' => trim($this->input->post("userName")), 'role' => trim($this->input->post("userRole")), 'userEmail' => trim($this->input->post("userEmail")), 'points' => trim($this->input->post('points')), 'editID' => $addID, 'editIP' => $ip, 'isActive' => $this->input->post("isActive"));
            $result = $this->GlobalModel->doEdit($data, 'usermaster', $code);            
            $exists = $this->GlobalModel->selectQuery("deliveryBoyActiveOrder.*","deliveryBoyActiveOrder",array("deliveryBoyActiveOrder.deliveryBoyCode"=>$code));
            if(!$exists){
                $dataDbActive['deliveryBoyCode'] = $code;
                $dataDbActive['orderCount'] = 0;
                $dataDbActive['loginStatus'] = 0;
                $dataDbActive['isActive'] = 1;
                $resultDbActive = $this->GlobalModel->addWithoutYear($dataDbActive, 'deliveryBoyActiveOrder', 'DBA');
            }   
            $profilePhoto = "";
            $uploadRootDir = 'uploads/';
            $uploadDir = 'uploads/profilePhoto/';
            if (!empty($_FILES['profilePhoto']['name'])) {
                $tmpFile = $_FILES['profilePhoto']['tmp_name'];
                $filename = $code . time() . '.jpg';
                $path = $uploadDir . '/' . $filename;
                move_uploaded_file($tmpFile, $path);
                $profilePhoto = $filename;
                $subData = array('profilePhoto' => $filename);
                $filedoc = $this->GlobalModel->doEdit($subData, 'usermaster', $code);
                $statusFlag = 1;
            } 
            if ($result != 'false' || $statusFlag == 1) {
                $response['status'] = true;
                $response['message'] = "User Successfully Updated.";
                $this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
            } else {
                $response['status'] = false;
                $response['message'] = "No change In User";
            }
            $this->session->set_flashdata('response', json_encode($response));
        } //else conditions
        redirect(base_url('/Usermaster/listRecords'), 'refresh');
    }
    public function delete() {
        $code = $this->input->post('code');
        //Activity Track Starts
        $addID = $this->session->userdata['logged_in' . $this->session_key]['code'];
        $userRole = $this->session->userdata['logged_in' . $this->session_key]['role'];
        $userA = $this->session->userdata['logged_in' . $this->session_key]['username'];
        $role = "";
        switch ($userRole) {
            case "ADM":
                $role = "Admin";
            break;
            case "USR":
                $role = "User";
            break;
        }
        $ip = $_SERVER['REMOTE_ADDR'];
        $dataQ = $this->GlobalModel->selectDataByField('code', $code, 'usermaster');
        $userName = '';
        foreach ($dataQ->result() as $row) {
            $userName = $row->username;
        }
        $text = $role . " " . $userA . ' deleted user "' . $userName . '" from ' . $ip;
        $log_text = array('code' => "demo", 'addID' => $addID, 'logText' => $text);
        $this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
		
		$dataC['loginStatus'] = 0;
		$this->GlobalModel->doEditWithField($dataC, 'deliveryBoyActiveOrder', 'deliveryBoyCode', $code);
		$this->GlobalModel->deleteWithField('deliveryBoyCode', $code, 'deliveryBoyActiveOrder');
		
        $data = array('deleteID' => $addID, 'deleteIP' => $ip);
        $resultData = $this->GlobalModel->doEdit($data, 'usermaster', $code);
        //Activity Track Ends
        echo $this->GlobalModel->delete($code, 'usermaster');
        // redirect(base_url() . '/usermaster/listRecords', 'refresh');
        
    }
    public function view() {
        $code = $this->input->get('code');
        //Activity Track Starts
        $addID = $this->session->userdata['logged_in' . $this->session_key]['code'];
        $userRole = $this->session->userdata['logged_in' . $this->session_key]['role'];
        $userA = $this->session->userdata['logged_in' . $this->session_key]['username'];
        $role = "";
        switch ($userRole) {
            case "ADM":
                $role = "Admin";
            break;
            case "USR":
                $role = "User";
            break;
        }
        $ip = $_SERVER['REMOTE_ADDR'];
        //Activity Track Ends
        $dataQuery = $this->GlobalModel->selectDataById($code, 'usermaster');
        $empCode = $dataQuery->result() [0]->empCode;
		$cityCode = $dataQuery->result() [0]->cityCode;
		if(isset($cityCode)) {
			$cityQuery = $this->GlobalModel->selectDataById($cityCode, 'citymaster');
			$cityName = $cityQuery->result() [0]->cityName;
		} else {
			$cityName = "";
		}
        $empQuery = $this->GlobalModel->selectDataById($empCode, 'employeemaster');
        $empName = $empQuery->result() [0]->firstName;
        foreach ($dataQuery->result() as $row) {
            $activeStatus = "";
            if ($row->isActive == "1") {
                $activeStatus = '<span class="label label-sm label-success">Active</span>';
            } else {
                $activeStatus = '<span class="label label-sm label-warning">Inactive</span>';
            }
            $role = "";
            if ($row->role == "ADM") {
                $role = " <span class='label label-sm  label-primary' font-size: 175% >Admin</span>";
            } else if ($row->role == "DLB") {
                $role = " <span class='label label-sm label-primary'font-size: 200%>Delivery Boy</span>";
            } else {
                $role = " <span class='label label-sm label-primary'font-size: 200%>User</span>";
            }
            $profilePhoto = $row->profilePhoto;
            $modelHtml = "";
            $profilePhotoUrl = $profilePhoto != "" ? base_url() . 'uploads/profilePhoto/' . $profilePhoto : base_url() . 'assets/admin/assets/images/users/1.jpg';
            $modelHtml.= '<div class=" el-element-overlay">	
							<div class="card">
								<div class="el-card-item">
									<div class="col-md-4 mb-3 el-card-avatar el-overlay-1 ml-auto"> <img src ="' . $profilePhotoUrl . '" alt="Profile Photo"/>
										<div class="el-overlay">
												<ul class="list-style-none el-info">
													<li class="el-item"><a class="btn default btn-outline image-popup-vertical-fit el-link" href=' . $profilePhotoUrl . ' target="_blank"><i class="fa fa-image"></i></a></li>
												</ul>
										   </div>
										</div>
										<div class="el-card-content">
											   <h5 class="m-b-0 " align="right">Profile Photo</h5> 
										   </div>
									   </div>
									</div>
								</div>
							</div> 
							<div class="form-row">
								<div class="col-md-6 mb-3">
									<label><b> User Code:</b> </label>
									<input type="text" value="' . $row->code . '" class="form-control-line"  readonly>
								</div>							
								<div class="col-md-6 mb-3">
									<label><b>Employee Name:</b> </label>
									<input type="text" value="' . $empName . '" class="form-control-line"  readonly>
								</div>
								<div class="col-md-6 mb-3">
									<label><b> City :</b> </label>
									<input type="text" value="' . $cityName . '" class="form-control-line"  readonly>
								</div> 
								<div class="col-md-6 mb-3">
									<label><b> User Name: </b></label>
									<input type="text" class="form-control-line" value="' . $row->username . '" readonly>
								</div>
								<div class="col-md-6 mb-3">
									<label><b>Email: </b></label>
									<input type="text" class="form-control-line" value="' . $row->userEmail . '" readonly>
								</div>
								<div class="col-md-6 mb-3">
									<label><b>Delivery For: </b></label>
									<input type="text" class="form-control-line" value="' . $row->deliveryType=='food' ? 'Food Orders' :'Vegetable/Grocery Orders' . '" readonly>
								</div>
							</div>
   						<div class="form-group" height="100px">' . $role . '</div>';
            $modelHtmlStatus = '';
            if ($userRole == "ADM") {
                $modelHtmlStatus.= '<div class="form-group">' . $activeStatus . '</div>';
            }
            //for activity
            $text = $role . " " . $userA . ' viewed user "' . $row->username . '" from ' . $ip;
            $log_text = array('code' => "demo", 'addID' => $addID, 'logText' => $text);
            $this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
            //Activity Track Ends 
        }
        $modelHtml.= $modelHtmlStatus;
        echo $modelHtml;
    }
    public function userAccessList() {
        $this->load->view('dashboard/header');
        $this->load->view('dashboard/usermaster/userAccess');
        $this->load->view('dashboard/footer');
    } // End User Access Function
    public function getUserAccessList() {
        $modelHtml = '<table id="datatableUserAccess" class="table table-striped table-bordered ">
            <tr class="text-center">
                <th><input type="checkbox" value="1" id="checkAll_module" name="checkAll_module"> Module</th>
                <th><input type="checkbox" value="1" id="checkAll_submodule" name="checkAll_submodule">Sub Module</th>
                <th><input type="checkbox" value="1" id="checkAll_submoduleaction" name="checkAll_submoduleaction">Access</th>
            </tr>';
        $recordsModule = $this->Testing->selectUserAccessActiveDataSequence('u_modulemaster');
        foreach ($recordsModule->result() as $moduleRow) {
            $modelHtml.= '<tr>';
            $code = $moduleRow->code;
            $modelHtml.= '<td><input type="checkbox" class="mainmodules" value="1" id="chkModule' . $code . '" name="chkModule' . $code . '"> &nbsp' . $moduleRow->moduleName . '</td>';
            $recordsSubModule = $this->Testing->selectUserAccessActiveDataByFieldSequence('moduleCode', $code, 'u_submodulemaster');
            $modelHtml.= '<td>
							<table id="" class="table table-striped table-bordered ">';
            $data = array();
            foreach ($recordsSubModule->result() as $subModuleRow) {
                $subModuleCode = $subModuleRow->code;
                $modelHtml.= '<tr class=""><td><input type="checkbox" class="submodules" value="1" id="chkSubModule' . $subModuleCode . '" name="chkSubModule' . $subModuleCode . '"> &nbsp' . $subModuleRow->subModuleName . '</td></tr>';
                array_push($data, $subModuleCode);
            }
            $modelHtml.= '</table>
				
					</td>';
            $modelHtml.= '<td>
							<table id="" class="table table-striped table-bordered ">';
            for ($i = 0;$i < count($data);$i++) {
                $recordsSubActionModule = $this->GlobalModel->selectActiveDataByField('subModuleCode', $data[$i], 'u_submoduleactionmaster');
                $modelHtml.= '<tr>';
                foreach ($recordsSubActionModule->result() as $subActionModuleRow) {
                    $subActionCode = $subActionModuleRow->code;
                    $modelHtml.= '<td><input type="checkbox" class="submodulesaction" value="1" id="chkSubActionModule' . $subActionCode . '" name="chkSubActionModule' . $subActionCode . '"> &nbsp' . $subActionModuleRow->actionName . '</td>';
                }
                $modelHtml.= '</tr>';
            }
            $modelHtml.= '</table></td>';
            $modelHtml.= '</tr>';
        }
        $modelHtml.= '</table>';
        print_r($modelHtml);
    } // End Get User Access List
    public function getAllPrivileges() {
        echo $this->UserModel->getAllModules();
    }
    public function userAccessEditList($empCode = null) {
        $data['empCode'] = $empCode;
        $this->load->view('dashboard/header');
        $this->load->view('dashboard/usermaster/userAccessEdit', $data);
        $this->load->view('dashboard/footer');
    } // End User Access Edit View Function
    public function deleteUserImage() {
        $code = $this->input->post('code');
        $name = $this->input->post('name');
        $rest = $this->db->query("update usermaster set profilePhoto = '' where code='" . $code . "'");
        if ($this->db->affected_rows() > 0) {
            unlink(base_url() . 'uploads/profilePhoto/' . $name);
            echo 'true';
        } else {
            echo 'false';
        }
    }
    // End User Access Edit View Function
    public function getUserAccessEditList() {
        $empCode = $this->input->get('empCode');
        $data['userRights'] = $this->GlobalModel->selectActiveDataByField('empCode', $empCode, 'userrightsmaster')->result() ['0']->emprights;
        $data['userAction'] = $this->GlobalModel->selectActiveDataByField('empCode', $empCode, 'userrightsactionmaster')->result();
        print_r(json_encode($data));
    } // End Get User Access Edit List
    public function checkPassword() { ////nitin more 19-12-2018
        $code = $this->input->get('code');
        $password = $this->input->get('password');
        $password = md5($password);
        $Result = $this->GlobalModel->selectDataByField('code', $code, 'usermaster');
        $dbPassword = $Result->result() [0]->password;
        if ($dbPassword == $password) {
            echo 'false';
        } else {
            echo 'true';
        }
    }
    public function checkUserName() {
        $userName = $this->input->get('userName');
        $Result = $this->GlobalModel->selectDataByField('username', $userName, 'usermaster');
        $name = $Result->result();
        $count = sizeof($name);
        if ($count == '0') {
            echo 'false';
        } else {
            echo 'true';
        }
    }
    public function test() {
        $data = $this->GlobalModel->selectData('usermaster');
        print_r($data->result());
    }	 
	public function myProfile($code = NULL)
	{
	    $data['error_message'] = '';     
	    $data['query'] = $this->GlobalModel->edit('usermaster', $code);
        $empCode = $data['query']->result() [0]->empCode; 
        $data['emp'] = $this->GlobalModel->selectDataByField('code', $empCode, 'employeemaster'); 
        $data['userRole'] = $this->GlobalModel->selectData('userrolemaster');
        $data['usRole'] = $this->session->userdata['logged_in' . $this->session_key]['role'];
        $this->load->view('dashboard/header');
        $this->load->view('dashboard/usermaster/myProfile', $data);
        $this->load->view('dashboard/footer');
	} 
	public function updateProfile()
	{
	    $code = $this->input->post("code");
        $userName = $this->input->post("userName");
        $empCode = $this->input->post("empCode");
		$uRole = $this->input->post('userRole');
        //Activity Track Starts
        $statusFlag = 0;
        $addID = $this->session->userdata['logged_in' . $this->session_key]['code'];
        $userRole = $this->session->userdata['logged_in' . $this->session_key]['role'];
        $userA = $this->session->userdata['logged_in' . $this->session_key]['username'];
        $role = "";
        switch ($userRole) {
            case "ADM":
                $role = "Admin";
            break;
            case "USR":
                $role = "User";
            break;
        }
        $ip = $_SERVER['REMOTE_ADDR'];
        $text = $role . " " . $userA . ' updated user "' . $userName . '" from ' . $ip;
        $log_text = array('code' => "demo", 'addID' => $addID, 'logText' => $text);
        //Activity Track Ends
        $this->form_validation->set_rules('empCode', 'Employee Name', 'required');
        $this->form_validation->set_rules('userName', 'User Name', 'required');
        $this->form_validation->set_rules('userEmail', 'Email', 'required');  
        if ($this->form_validation->run() == FALSE) {
            $data['error_message'] = '* Fields are Required!';
            $data['query'] = $this->GlobalModel->edit('usermaster', $code);
            $empCode = $data['query']->result() [0]->empCode; 
            $data['emp'] = $this->GlobalModel->selectDataByField('code', $empCode, 'employeemaster'); 
            $data['userRole'] = $this->GlobalModel->selectData('userrolemaster');
            $data['usRole'] = $this->session->userdata['logged_in' . $this->session_key]['role'];
            $this->load->view('dashboard/header');
            $this->load->view('dashboard/usermaster/myProfile', $data);
            $this->load->view('dashboard/footer');
        } else {
            $password = $this->input->post("password");
            $data = array('empCode' => trim($this->input->post("empCode")),'username' => trim($this->input->post("userName")), 'userEmail' => trim($this->input->post("userEmail")), 'editID' => $addID, 'editIP' => $ip);
            if (trim($password)!="")  $data['password'] = md5($password); 
            $result = $this->GlobalModel->doEdit($data, 'usermaster', $code);
            $profilePhoto = "";
            $uploadRootDir = 'uploads/';
            $uploadDir = 'uploads/profilePhoto/';
            if (!empty($_FILES['profilePhoto']['name'])) {
                $tmpFile = $_FILES['profilePhoto']['tmp_name'];
                $filename = $code . time() . '.jpg';
                $path = $uploadDir . '/' . $filename;
                move_uploaded_file($tmpFile, $path);
                $profilePhoto = $filename;
                $subData = array('profilePhoto' => $filename);
                $filedoc = $this->GlobalModel->doEdit($subData, 'usermaster', $code);
                $statusFlag = 1;
            }
            if ($result != 'false' || $statusFlag == 1) {
                $response['status'] = true;
                $response['message'] = "User Successfully Updated.";
                $this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
            } else {
                $response['status'] = false;
                $response['message'] = "No change In User";
            }
            $this->session->set_flashdata('response', json_encode($response));
        } //else conditions
        redirect(base_url() . '/usermaster/myProfile/'.$code, 'refresh');
	}
}
?>