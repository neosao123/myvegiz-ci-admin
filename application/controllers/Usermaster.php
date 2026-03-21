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
        $data['userRole'] = $this->GlobalModel->selectData('userrolemaster');
        $data['city'] = $this->GlobalModel->selectActiveData('citymaster'); 
		$data['userCount'] = $this->GlobalModel->getCountOfPerticularValue('usermaster', 'role', 'DLB');		
        $this->load->view('dashboard/header');
        $this->load->view('dashboard/usermaster/add', $data);
        $this->load->view('dashboard/footer');
    }
	
    public function listRecords() {
        $data['error'] = $this->session->flashdata('response');
        $data['query'] = $this->GlobalModel->selectActiveData('usermaster');
        $data['city'] = $this->GlobalModel->selectActiveData('citymaster');
        $this->load->view('dashboard/header');
        $this->load->view('dashboard/usermaster/list', $data);
        $this->load->view('dashboard/footer');
    }
	
    public function getUserMasterList() {
        $username = $this->input->get('userName');
        $role = $this->input->get('userRole');
		$sessionUserRole = $this->session->userdata['logged_in' . $this->session_key]['role'];
        $cityCode = $this->input->get('cityCode');
        $tableName = "usermaster";
        $search = $this->input->GET("search") ['value'];
        $orderColumns = array("usermaster.*,citymaster.cityName,usermaster.name,deliveryBoyActiveOrder.loginStatus");
        $condition = array( 'usermaster.code' => $username, 'usermaster.role' => $role, 'usermaster.cityCode' => $cityCode);
        $orderBy = array('usermaster' . '.id' => 'DESC');
        $joinType = array('citymaster' => 'left','deliveryBoyActiveOrder'=>'left');
        $join = array('citymaster' => 'usermaster.cityCode=citymaster.code','deliveryBoyActiveOrder' => 'usermaster.code=deliveryBoyActiveOrder.deliveryBoyCode');
        $groupByColumn = array();
        $limit = $this->input->GET("length");
        $offset = $this->input->GET("start");
        $extraCondition = " (usermaster.isDelete ='0' or usermaster.isDelete IS NULL) ";
        $like = array();
        $Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
        $srno = $_GET['start'] + 1;
        $data = array();
		
		$dataCount = 0;
		if($Records){
			foreach ($Records->result() as $row) {
				if ($row->role == "ADM") {
					$role = "<span class='label label-sm label-primary'>Admin</span>";
				} else if ($row->role == "DLB") {
					$role = "<span class='label label-sm label-primary'>Delivery Boy</span>";
					if($row->deliveryType =='food'){
					    $role .= "<div class='mt-2'>Accepts : <span class='badge badge-info'>Food/Vege/Grocery Orders</span></div>";
					} else {
					    $role .= "<div class='mt-2'>Accepts : <span class='badge badge-success'>Slot Orders</span></div>";
					}
					
				} else {
					$role = "<span class='label label-sm label-primary'>User</span>";
				}
				if ($row->isActive == "1") {
					$status = "<span class='label t1 label-sm label-success'>Active</span>";
				} else {
					$status = "<span class='label t1 label-sm label-warning'>Inactive</span>";
				}
				if($row->role=='DLB'){
					if ($row->loginStatus == "1") {
						$status .= "<span class='label t1 label-sm label-success'>Online</span>";
					} else{
						$status .= "<span class='label t1 label-sm label-warning'>Offline</span>"; 	
					}
				}
				$actionHtml = '
					<div class="btn-group">
						<button type="button" class="btn btn-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<i class="ti-settings"></i>
						</button>
						<div class="dropdown-menu animated slideInUp" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 35px, 0px);">
							  <a class="dropdown-item  blue" data-toggle="modal" data-target="#responsive-modal" data-seq="' . $row->code . '" href><i class="ti-eye"></i> Open</a>
							  <a class="dropdown-item" href="' . base_url() . 'Usermaster/edit/' . $row->code . '"><i class="ti-pencil-alt"></i> Edit</a>
							  <a class="dropdown-item mywarning " data-seq="' . $row->code . '" id="' . $row->code . '"><i class="ti-trash" ></i> Delete</a>';
							  if($sessionUserRole =='ADM' && $row->role=='USR' || $row->role=='USR'){
								  $actionHtml .= '<a title="Edit User Rights" class="dropdown-item green" href="'.base_url().'Usermaster/userAccessEditList/'.$row->code.'"><i class="ti-pencil-alt"></i> User Rights</a>';
							  }
						$actionHtml .= '</div>
					</div>';
				$data[] = array($srno, $row->code,$row->cityName, $row->name, $row->username, $role, $status, $actionHtml);
				$srno++;
			}
			$dataCount = sizeof($this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, "", "", $groupByColumn, $extraCondition)->result_array());
		}
       
        $output = array("draw" => intval($_GET["draw"]), "recordsTotal" => $dataCount, "recordsFiltered" => $dataCount, "data" => $data);
        echo json_encode($output);
    }
    public function save() {
        $name = $this->input->post("name");
        $username = $this->input->post("userName");
        $userRole = trim($this->input->post("userRole"));
        $mobile=$this->input->post("mobilenumber");
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
		$emailResult="";
		if($this->input->post("userEmail")!=""){
		$emailResult = sizeof($this->GlobalModel->selectActiveDataByField('username', $this->input->post("userEmail"), 'usermaster')->result());
		}		
		$mobileResult = sizeof($this->GlobalModel->selectActiveDataByField('username', $mobile, 'usermaster')->result());
		$userResult = sizeof($this->GlobalModel->selectActiveDataByField('username', $username, 'usermaster')->result());	
        if ($userResult != false) 
		{
            $data = array('errormessage' => 'Username already exists..Please provide another');
            $data['userRole'] = $this->GlobalModel->selectData('userrolemaster');
			$data['city'] = $this->GlobalModel->selectActiveData('citymaster'); 
			$data['userCount'] = $this->GlobalModel->getCountOfPerticularValue('usermaster', 'role', 'DLB');	
            $this->load->view('dashboard/header');
            $this->load->view('dashboard/usermaster/add', $data);
            $this->load->view('dashboard/footer');
        }else if($emailResult==1){
		    $data = array('errormessage' => 'Email already exists.');
            $data['userRole'] = $this->GlobalModel->selectData('userrolemaster');
			$data['city'] = $this->GlobalModel->selectActiveData('citymaster'); 
			$data['userCount'] = $this->GlobalModel->getCountOfPerticularValue('usermaster', 'role', 'DLB');	
            $this->load->view('dashboard/header');
            $this->load->view('dashboard/usermaster/add', $data);
            $this->load->view('dashboard/footer');
		}else if($mobileResult==1){
			$data = array('errormessage' => 'Mobile already exists.');
            $data['userRole'] = $this->GlobalModel->selectData('userrolemaster');
			$data['city'] = $this->GlobalModel->selectActiveData('citymaster'); 
			$data['userCount'] = $this->GlobalModel->getCountOfPerticularValue('usermaster', 'role', 'DLB');	
            $this->load->view('dashboard/header');
            $this->load->view('dashboard/usermaster/add', $data);
            $this->load->view('dashboard/footer');
		}
		else {
			$this->form_validation->set_rules('name', 'Name', 'required');
            $this->form_validation->set_rules('userName', 'User Name', 'required');
			$this->form_validation->set_rules('mobilenumber', 'Mobilenumber', 'required');
            $this->form_validation->set_rules('password', 'User Password', 'required'); 
            $this->form_validation->set_rules('confirmPassword', 'Confirm Password', 'required');
            $this->form_validation->set_rules('userRole', 'User Role', 'required');
			if($userRole=='DLB'){
				$this->form_validation->set_rules('cityCode', 'City', 'required');
			}
            if ($this->form_validation->run() == FALSE) {
                $data['error_message'] = '* Fields are Required!';
                $data['userRole'] = $this->GlobalModel->selectData('userrolemaster');
				$data['userCount'] = $this->GlobalModel->getCountOfPerticularValue('usermaster', 'role', 'DLB');	
                $this->load->view('dashboard/header');
                $this->load->view('dashboard/usermaster/add', $data);
                $this->load->view('dashboard/footer');
            } else {
				$startT=$this->input->post('fromTime');
				$endT=$this->input->post('toTime');
				$startTime = date('H:i:s',strtotime($startT));
                $endTime = date('H:i:s',strtotime($endT));
				$data = array(
					'name' => $name, 
					'deliveryType'=>$this->input->post('deliveryType') ,
					'cityCode' => $this->input->post('cityCode'),
                    'availableStartTime'=>$startTime,
                    'availableEndTime'=>$endTime,					
					'username' => $username, 
					'role' => $userRole, 
					'userEmail' => trim($this->input->post("userEmail")),
                    'mobile'=>trim($this->input->post("mobilenumber")),					
					'password' => md5($this->input->post("password")), 
					'addID' => $addID, 
					'addIP' => $ip,
					'isActive' => $this->input->post('isActive')
				);
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
				
					if($userRole=='DLB'){
						$dataDbActive['deliveryBoyCode'] = $result;
						$dataDbActive['orderCount'] = 0;
						$dataDbActive['loginStatus'] = 0;
						$dataDbActive['isActive'] = 1;
						$resultDbActive = $this->GlobalModel->addWithoutYear($dataDbActive, 'deliveryBoyActiveOrder', 'DBA');
					}
					$this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
					redirect('Usermaster/listRecords');
                } else {
					$response['status'] = false;
					$response['message'] = "Failed To Add User";
					$this->session->set_flashdata('response', json_encode($response));
					redirect('Usermaster/listRecords');
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
  
    public function edit($code = NULL) {
		$res = $this->GlobalModel->edit('usermaster', $code);
		if($res->result()[0]->role=='DLB'){
            $data['query'] = $this->GlobalModel->edit('usermaster', $code);
			//$data['query'] = $this->GlobalModel->selectPerticularFieldFromAnotherTable1('usermaster','deliveryBoyActiveOrder','code','deliveryBoyCode','loginStatus',$code);
            $cityCode = $res->result()[0]->cityCode;
		}else{
			$data['query'] = $this->GlobalModel->edit('usermaster', $code);
			$cityCode = $data['query']->result()[0]->cityCode;
		}
		
		$data['city'] = $this->GlobalModel->selectActiveData('citymaster');
        $table_name = 'customaddressmaster';
        $orderColumns = array("customaddressmaster.*");
        $cond = array('customaddressmaster' . '.isDelete' => 0, 'customaddressmaster' . '.isActive' => 1, 'customaddressmaster.isService' => 1,'customaddressmaster.cityCode'=>$cityCode);
        $data['address'] = $this->GlobalModel->selectQuery($orderColumns, $table_name, $cond);
        $data['userRole'] = $this->GlobalModel->selectData('userrolemaster');
        $data['usRole'] = $this->session->userdata['logged_in' . $this->session_key]['role'];
		$data['userCount'] = $this->GlobalModel->getCountOfPerticularValue('usermaster', 'role', 'DLB');
        $this->load->view('dashboard/header');
        $this->load->view('dashboard/usermaster/edit', $data);
        $this->load->view('dashboard/footer');
    }
    public function update() {
        $code = $this->input->post("code");
        $userName = $this->input->post("userName");
        $password = $this->input->post("password");
        $name = $this->input->post("name");
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
        $this->form_validation->set_rules('name', 'Name', 'required');
        $this->form_validation->set_rules('userName', 'User Name', 'required');
		$this->form_validation->set_rules('mobilenumber', 'Mobilenumber', 'required');
        $this->form_validation->set_rules('userRole', 'User Role', 'required'); 
		if($uRole=='DLB'){
			$this->form_validation->set_rules('cityCode', 'City', 'required');
		}
		$condition2 = array('userName' =>$userName, 'code!=' => $code);
		$result = $this->GlobalModel->checkDuplicateRecordNew($condition2, 'usermaster');
		$condition3 = array('mobile' =>trim($this->input->post("mobilenumber")), 'code!=' => $code);
		$result3 = $this->GlobalModel->checkDuplicateRecordNew($condition3, 'usermaster');
	    $result4="";
		if($this->input->post("userEmail")!=""){
		 $condition4 = array('userEmail' =>$this->input->post("userEmail"), 'code!=' => $code);
		  $result4 = $this->GlobalModel->checkDuplicateRecordNew($condition4, 'usermaster');
		}
		
		
		if ($result==1) {
			$data['error_message'] = 'Duplicate Username';
			$data['userRole'] = $this->GlobalModel->selectData('userrolemaster');
			$data['query'] = $this->GlobalModel->edit('usermaster', $code);
			$data['city'] = $this->GlobalModel->selectActiveData('citymaster'); 
			$data['usRole'] = $this->session->userdata['logged_in' . $this->session_key]['role'];
			$this->load->view('dashboard/header');
			$this->load->view('dashboard/usermaster/edit', $data);
			$this->load->view('dashboard/footer');
		}else if($result3 == 1){
			$data['error_message'] = 'Duplicate Mobile';
			$data['userRole'] = $this->GlobalModel->selectData('userrolemaster');
			$data['query'] = $this->GlobalModel->edit('usermaster', $code);
			$data['city'] = $this->GlobalModel->selectActiveData('citymaster'); 
			$data['usRole'] = $this->session->userdata['logged_in' . $this->session_key]['role'];
			$this->load->view('dashboard/header');
			$this->load->view('dashboard/usermaster/edit', $data);          
			$this->load->view('dashboard/footer'); 
		}else if($result4 == 1){
			$data['error_message'] = 'Duplicate Email';
			$data['userRole'] = $this->GlobalModel->selectData('userrolemaster');
			$data['query'] = $this->GlobalModel->edit('usermaster', $code);
			$data['city'] = $this->GlobalModel->selectActiveData('citymaster'); 
			$data['usRole'] = $this->session->userdata['logged_in' . $this->session_key]['role'];
			$this->load->view('dashboard/header');
			$this->load->view('dashboard/usermaster/edit', $data);
			$this->load->view('dashboard/footer');
		}
		else{
			 if ($this->form_validation->run() == FALSE) {
				$data['error_message'] = '* Fields are Required!';
				$data['userRole'] = $this->GlobalModel->selectData('userrolemaster');
				$data['query'] = $this->GlobalModel->edit('usermaster', $code);
				$data['city'] = $this->GlobalModel->selectActiveData('citymaster'); 
				$data['usRole'] = $this->session->userdata['logged_in' . $this->session_key]['role'];
				$this->load->view('dashboard/header');
				$this->load->view('dashboard/usermaster/edit', $data);
				$this->load->view('dashboard/footer');
			} else {
				$startT=$this->input->post('fromTime');
				$endT=$this->input->post('toTime');
				$startTime = date('H:i:s',strtotime($startT));
				$endTime = date('H:i:s',strtotime($endT));
				$data = array(
					'name' => trim($this->input->post("name")), 
					'deliveryType'=>$this->input->post('deliveryType') ,
					'cityCode' => $this->input->post('cityCode'), 
					'mobile'=>$this->input->post('mobilenumber'), 
					'availableStartTime'=>$startTime,
					'availableEndTime'=>$endTime,
					'username' => trim($this->input->post("userName")), 
					'role' => trim($this->input->post("userRole")), 
					'userEmail' => trim($this->input->post("userEmail")), 
					'editID' => $addID, 
					'editIP' => $ip, 
					'isActive' => $this->input->post("isActive")
				);
				if($password!="") {
					$data['password'] = md5($password);
				}
				$result = $this->GlobalModel->doEdit($data, 'usermaster', $code);            
				$exists = $this->GlobalModel->selectQuery("deliveryBoyActiveOrder.*","deliveryBoyActiveOrder",array("deliveryBoyActiveOrder.deliveryBoyCode"=>$code));
				if(!$exists){
					$dataDbActive['deliveryBoyCode'] = $code;
					$dataDbActive['orderCount'] = 0;
					$dataDbActive['loginStatus'] = 0;
					$dataDbActive['isActive'] = 1;
					$resultDbActive = $this->GlobalModel->addWithoutYear($dataDbActive, 'deliveryBoyActiveOrder', 'DBA');
				}else{
					$dataDbActive['loginStatus'] = trim($this->input->post("isOnline")??"");
					$resultDbActive = $this->GlobalModel->doEditWithField($dataDbActive,'deliveryBoyActiveOrder','deliveryBoyCode',$code);
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
		$cityCode = $dataQuery->result() [0]->cityCode;
		if(isset($cityCode) && $cityCode!='') {
			$cityQuery = $this->GlobalModel->selectDataById($cityCode, 'citymaster');
			$cityName = $cityQuery->result() [0]->cityName;
		} else {
			$cityName = "";
		}
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
			$deliveryFor = '';
			if($row->role=='DLB'){
				if($row->deliveryType=='food'){ $deliveryFor = 'FoodVegetable/Grocery Orders'; } else{ $deliveryFor = 'Slot Orders';}
			}
			$startTime=$endTime='';
			if($row->availableStartTime!="" && $row->availableStartTime!=NULL ){
				$startTime = date('h:i A',strtotime($row->availableStartTime));
			}
			if($row->availableEndTime!="" && $row->availableEndTime!=NULL){
				$endTime = date('h:i A',strtotime($row->availableEndTime));
				
			}
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
									<label><b>Name:</b> </label>
									<input type="text" value="' . $row->name . '" class="form-control-line"  readonly>
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
									<label><b> Mobile number: </b></label>
									<input type="text" class="form-control-line" value="' . $row->mobile . '" readonly>
								</div>
								<div class="col-md-6 mb-3">
									<label><b>Email: </b></label>
									<input type="text" class="form-control-line" value="' . $row->userEmail . '" readonly>
								</div>
								<div class="col-md-6 mb-3">
									<label><b>Delivery For: </b></label>
									<input type="text" class="form-control-line" value="' . $deliveryFor . '" readonly>
								</div>
								<div class="col-md-6 mb-3"> 
									<label><b>Start Time: </b></label>
									<input type="text" class="form-control-line" value="'.$startTime.'" readonly>
								</div>
								<div class="col-md-6 mb-3">
									<label><b>End Time: </b></label>
									<input type="text" class="form-control-line" value="'.$endTime.'" readonly>
								</div>
							</div>
   						<div class="form-group" height="100px">User Role:' . $role . '</div>';
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
	  public function userAccessEditList($userCode=null)
    {
		$data['s_status'] = $this->session->flashdata('s_status');
        $data['userCode']=$userCode;
		$this->load->view('dashboard/header');
        $checkUserRole = $this->GlobalModel->selectQuery("usermaster.role","usermaster",array("usermaster.code"=>$userCode));
		if($checkUserRole){
			$userRole = $checkUserRole->result_array()[0]['role'];
			if($userRole=='USR' || $userRole=='ADM'){
				$this->load->view('dashboard/usermaster/userAccessEdit',$data);
			}
		}
        $this->load->view('dashboard/footer');
        
    }// End User Access Edit View Function 
    
	public function getMenuList()
    {
		$jsonString = file_get_contents(modules_file_path);
		$jsonFileData = json_decode($jsonString, true);
        $modelHtml='<table id="datatableUserAccess" class="table table-sm table-stripped table-bordered " style="width:100%;">
            <thead>
                <tr class="text-center">
                    <th><input type="checkbox" value="1" id="checkAll_module" name="checkAll_module"> Module</th>
                    <th><input type="checkbox" value="1" id="checkAll_submodule" name="checkAll_submodule"> Sub Module</th>
                </tr>
            </thead>'; 
        foreach($jsonFileData as $key => $value){
            $modelHtml.='<tr>';
            $modelHtml.='<td><input type="checkbox" class="mainmodules" value="1"> &nbsp'.$key.'</td>';
            $modelHtml.='<td>
                <table id="" class="table table-sm table-bordered">'; 
            foreach($value['menus'] as $submodule => $submoduleValue){
				if($this->isMultiDimensional($submoduleValue)){
					$modelHtml.='<tr class=""><td style=""><input type="checkbox" class="submodules" value="1"> &nbsp'.$submodule.'</td></tr>';
					foreach($submoduleValue as $submodule1 => $submoduleValue1){
						$modelHtml.='<tr class=""><td style="padding-left: 30px;"><input type="checkbox" class="submodules" value="1"> &nbsp'.$submodule1.'</td></tr>';
					}
				}else{
					if($submoduleValue['isActive']==1){
						$modelHtml.='<tr class=""><td><input type="checkbox" class="submodules" value="1"> &nbsp'.$submodule.'</td></tr>';
					}
				}
            }
            $modelHtml.='</table>
            </td></tr>';
        }
        $modelHtml.='</table>';
        print_r($modelHtml);  
    
    }
	
	function isMultiDimensional($arrayInput) {
		$rv = array_filter($arrayInput, 'is_array');
		if(count($rv)>0) return true;
		return false;
	}
    
	public function getUserAccessList(){
        $modelHtml='<table id="datatableUserAccess" class="table table-sm table-stripped table-bordered " style="width:100%;">
			<thead>
				<tr class="text-center">
					<th><input type="checkbox" value="1" id="checkAll_module" name="checkAll_module"> Module</th>
					<th><input type="checkbox" value="1" id="checkAll_submodule" name="checkAll_submodule"> Sub Module</th>
				</tr>
			</thead>'; 
		$recordsModule=$this->GlobalModel->selectUserAccessActiveDataSequence('u_modulemaster');
        foreach($recordsModule->result() as $moduleRow){
				$modelHtml.='<tr>';
				$code=$moduleRow->code;
				$modelHtml.='<td><input type="checkbox" class="mainmodules" value="1" id="chkModule'.$code.'" name="chkModule'.$code.'"> &nbsp'.$moduleRow->moduleName.'</td>';
				$recordsSubModule=$this->GlobalModel->selectUserAccessActiveDataByFieldSequence('moduleCode',$code,'u_submodulemaster');
				$modelHtml.='<td>
					<table id="" class="table table-bordered ">'; 
				foreach($recordsSubModule->result() as $subModuleRow){
					$subModuleCode=$subModuleRow->code;
					$modelHtml.='<tr class=""><td><input type="checkbox" class="submodules" value="1" id="chkSubModule'.$subModuleCode.'" name="chkSubModule'.$subModuleCode.'"> &nbsp'.$subModuleRow->subModuleName.'</td></tr>';
				}		
				$modelHtml.='</table></td></tr>';				
        }
        $modelHtml.='</table>';
        print_r($modelHtml);  
    } // End Get User Access List
	
    public function getAllPrivileges()
    {
        echo $this->GlobalModel->getAllModules();
    }


    public function saveRights()
    {
        $userCode=$this->input->post("userCode");
        $userRights=$this->input->post("userrights");
		$filename= 'assets/rights/'.$userCode.'.json'; 
		$insert=0;
		$resultArr = json_decode($userRights,true);
		if(empty($resultArr['ModulesData'])){
			unlink($filename);
			$insert=1;
		}else{
			$json = json_encode($userRights);
			if(file_put_contents($filename, $json)){  	
				$insert=1;
			}
		}
		if($insert==1){
            $response['status']=true;
            $response['message']= "User Rights Successfully Added";
        }else{
            $response['status']=false;
            $response['message']="Failed to add user rights";
        }
        echo json_encode($response);
    }

   public function getUserAccessEditList()
    {
        $userCode=$this->input->get('userCode');
		$filename= 'assets/rights/'.$userCode.'.json'; 
		if(file_exists($filename)){
			$json = file_get_contents($filename);
			if($json!=""){
				print_r(json_decode($json,true));
			}else{
				return false;
			}
        }else{
            return false;
        }
    
    } // End Get User Access Edit List
	
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
	/* for online/offline check added by ani on 13-3-2021 */
	 public function checkDeliveryBoyOrders() {
        $deliveryBoyCode = $this->input->get('code');
        $userRole = $this->input->get('userRole');
	    $Result = $this->GlobalModel->selectDataByField('deliveryBoyCode', $deliveryBoyCode, 'deliveryBoyActiveOrder');
		$res = $Result->result()[0]->orderCount;
		if ($res == '0') {
			echo 'false';
	    } else {
			echo 'true';
		}
    }
	/* for online/offline check added by ani on 13-3-2021 end*/
    public function test() {
        $data = $this->GlobalModel->selectData('usermaster');
        print_r($data->result());
    }	 
	public function myProfile($code = NULL)
	{
	    $data['error_message'] = '';     
	    $data['query'] = $this->GlobalModel->edit('usermaster', $code);
        $empCode = $data['query']->result() [0]->empCode; 
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
		$userEmail = $this->input->post("userEmail");
        $name = $this->input->post("name");
        $mobilenumber = $this->input->post("mobilenumber");
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
        $this->form_validation->set_rules('name', 'Name', 'required');
        $this->form_validation->set_rules('mobilenumber', 'Mobile Number', 'required');
        $this->form_validation->set_rules('userName', 'User Name', 'required');
        $this->form_validation->set_rules('userEmail', 'Email', 'required');  
        
		$condition2 = array('userEmail' =>$userEmail, 'code!=' => $code);
		$condition3 = array('mobile' =>$mobilenumber, 'code!=' => $code);
		$result = $this->GlobalModel->checkDuplicateRecordNew($condition2, 'usermaster');
		$result1 = $this->GlobalModel->checkDuplicateRecordNew($condition3, 'usermaster');
		if($result == 1){
			$data['error_message'] = 'Duplicate Email Address';
			$data['query'] = $this->GlobalModel->edit('usermaster', $code);
			$data['userRole'] = $this->GlobalModel->selectData('userrolemaster');
			$data['usRole'] = $this->session->userdata['logged_in' . $this->session_key]['role'];
			$this->load->view('dashboard/header');
			$this->load->view('dashboard/usermaster/myProfile', $data);
			$this->load->view('dashboard/footer');
		}else if($result1==1){
		    $data['error_message'] = 'Duplicate Mobile Number';
			$data['query'] = $this->GlobalModel->edit('usermaster', $code);
			$data['userRole'] = $this->GlobalModel->selectData('userrolemaster');
			$data['usRole'] = $this->session->userdata['logged_in' . $this->session_key]['role'];
			$this->load->view('dashboard/header');
			$this->load->view('dashboard/usermaster/myProfile', $data);
			$this->load->view('dashboard/footer');
	    }else{
			if ($this->form_validation->run() == FALSE) {
				$data['error_message'] = '* Fields are Required!';
				$data['query'] = $this->GlobalModel->edit('usermaster', $code);
				$data['userRole'] = $this->GlobalModel->selectData('userrolemaster');
				$data['usRole'] = $this->session->userdata['logged_in' . $this->session_key]['role'];
				$this->load->view('dashboard/header');
				$this->load->view('dashboard/usermaster/myProfile', $data);
				$this->load->view('dashboard/footer');
			} else {
				$password = $this->input->post("password");
				$data = array('name' => trim($this->input->post("name")),'mobile' => trim($this->input->post("mobilenumber")),'username' => trim($this->input->post("userName")), 'userEmail' => trim($this->input->post("userEmail")), 'editID' => $addID, 'editIP' => $ip);
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
	
	public function duplicateMobileNumber()
	{
		$mobile = $this->input->get("mobile");
		$condition=array('mobile'=>$mobile);
		$duplicateCon = $this->GlobalModel->checkDuplicateRecordNew($condition,'usermaster');
		
		if($duplicateCon == true)
		{
			$res['message']="Mobile number already exist.";
			$res['status']=true;
		}
		else
		{
			$res['message']="";
			$res['status']=false;
		}
		echo json_encode($res);
		
	}
	
	public function duplicateEmail()
	{
		$email = $this->input->get("email");
		$condition=array('userEmail'=>$email);
		$duplicateCon = $this->GlobalModel->checkDuplicateRecordNew($condition,'usermaster');
		
		if($duplicateCon == true)
		{
			$res['message']="Email already exist.";
			$res['status']=true;
		}
		else
		{
			$res['message']="";
			$res['status']=false;
		}
		echo json_encode($res);
		
	}
	
	public function duplicateMobileForEdit()
	{
		$mobile = $this->input->get("mobile");
		$code=$this->input->get("code");
		$duplicateCon = $this->GlobalModel->checkDuplicateRecordInUpdate('mobile',$mobile,$code,'usermaster');
		if($duplicateCon == true)
		{
			$res['message']="Mobile number already exist.";
			$res['status']=true;
		}
		else
		{
			$res['message']="";
			$res['status']=false;
		}
		echo json_encode($res);
		
	}
	
	public function duplicateEmailForEdit()
	{
		$email = $this->input->get("email");
		$code=$this->input->get("code");
		$duplicateCon = $this->GlobalModel->checkDuplicateRecordInUpdate('userEmail',$email,$code,'usermaster');
		if($duplicateCon == true)
		{
			$res['message']="Email already exist.";
			$res['status']=true;
		}
		else
		{
			$res['message']="";
			$res['status']=false;
		}
		echo json_encode($res);
		
	}
}
?>