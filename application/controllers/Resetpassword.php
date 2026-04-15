<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Resetpassword extends CI_Controller { 
	var $session_key;
    public function __construct() {
			parent::__construct();
			$this->load->helper('form', 'url', 'html');
			$this->load->library('form_validation');
			$this->load->model('GlobalModel'); 
			$this->load->library('sendemail');
			$this->load->library('passwordlib');
			$this->load->library('notificationlibv_3');
			$this->session_key = $this->session->userdata('key'.SESS_KEY);
			if (!isset($this->session->userdata['logged_in' . $this->session_key]['code'])) {
				redirect('Admin/login', 'refresh');
			}
    }                  

    public function listRecords_new() 
	{
		$data['city'] = $this->GlobalModel->selectActiveData('citymaster');  
		$this->load->view('dashboard/header');
		$this->load->view('dashboard/resetpassword/list',$data);
		$this->load->view('dashboard/footer');
	}
	
    public function deResetPassword()
    {
        $urlToken = $this->uri->segment(3);
        $urlToken = str_replace('%20','',$urlToken);  
        $Records = $this->GlobalModel->selectQuery('usermaster.*','usermaster',array('usermaster.resetToken'=>$urlToken));
        if($Records) { 
            $token1 = $this->passwordlib->base64url_decode($urlToken); 
            $code = substr($token1, strpos($token1, "/") + 1);    
            $data['code'] = $code;
            $data['token'] = $urlToken;
            $this->load->view('delivery/resetPassword',$data);
        } else {
            $this->session->set_flashdata('message', 'Password Reset Link is Expired Please Reset Password Again to Continue');
            redirect("Welcome");
        }
    }
    
    public function cuRestPassword(){
    $urlToken = $this->uri->segment(3);
    $Records = $this->GlobalModel->selectQuery('clientmaster.*','clientmaster',array('clientmaster.resetToken'=>$urlToken));
    if($Records){
      $token1 = $this->passwordlib->base64url_decode($urlToken);
      $code = substr($token1, strpos($token1, "/") + 1);    
      $data['code'] = $code;
      $data['token'] = $urlToken;
      $this->load->view('customer/resetPassword',$data);
    }else{
      $this->session->set_flashdata('message', 'Password Reset Link is Expired Please Reset Password Again to Continue');
      redirect("Welcome");
    }
  }
  
    public function resetDeliveryBoyPassword(){
        $code = $this->input->post('code'); 
        $password = $this->input->post('password');
        $confirmPassword = $this->input->post('confirmPassword');
        $this->form_validation->set_rules('password', 'Password', 'required');
        $this->form_validation->set_rules('confirmPassword', 'Confrim Password', 'required'); 
        if ($this->form_validation->run() == FALSE) {   
            $res['status'] = false;
            $res['message'] = "* Fields are Required!";
        } else {
            if($password==$confirmPassword){
                $password = md5(trim($password));
                $data['password']= $password;
                $data['editID'] = $code;
                $data['editDate'] = date('Y-m-d H:i:s');
                $data['editIP'] = $_SERVER['REMOTE_ADDR'];
                $data['resetToken'] = null;
                $result = $this->GlobalModel->doEdit($data,"usermaster",$code);
                if($result=='true'){
                    $res['status'] = true;
                    $res['message'] = "Password reset was successful! Please login using the mobile application";  
                } else {
                    $res['status'] = false;
                    $res['message'] = "Failed to reset your passwords...";  
                }
            } else { 
                $res['status'] = false;
                $res['message'] = "Passwords Donot match..";
            }
        }
        echo json_encode($res);
    }
  
    public function resetClientPassword(){
        $code = $this->input->post('code'); 
        $password = $this->input->post('password');
        $confirmPassword = $this->input->post('confirmPassword');
        $this->form_validation->set_rules('password', 'Password', 'required');
        $this->form_validation->set_rules('confirmPassword', 'Confrim Password', 'required'); 
        if ($this->form_validation->run() == FALSE) {   
            $res['status'] = false;
            $res['message'] = "* Fields are Required!";
        } else {
            if($password==$confirmPassword){
                $password = md5(trim($password));
                $data['password']= $password;
                $data['editID'] = $code;
                $data['editDate'] = date('Y-m-d H:i:s');
                $data['editIP'] = $_SERVER['REMOTE_ADDR'];
                $data['resetToken'] = null;
                $result = $this->GlobalModel->doEdit($data,"clientmaster",$code);
                if($result=='true'){
                    $res['status'] = true;
                    $res['message'] = "Password reset was successful! Please login using the mobile application";  
                } else {
                    $res['status'] = false;
                    $res['message'] = "Failed to reset your passwords...";  
                }
            } else { 
                $res['status'] = false;
                $res['message'] = "Passwords Donot match..";
            }
        }
        echo json_encode($res);
    }
  
  
     public function listRecords() 
	{
		$data['city'] = $this->GlobalModel->selectActiveData('citymaster');  
		$this->load->view('dashboard/header');
		$this->load->view('dashboard/resetpassword/list',$data);
		$this->load->view('dashboard/footer');
	}
	  
	function getrestpasswordList() {
		$tableName = "resetpassword";
		$cityCode = $this->input->post('cityCode') ?? $this->input->get('cityCode');
		$orderColumns = array("clientmaster.code,resetpassword.userCode,resetpassword.id,clientmaster.mobile,clientmaster.emailId,clientmaster.name,citymaster.cityName");
		$orderBy = array('resetpassword' . '.id' => 'DESC');
		$search = ($this->input->post("search")['value'] ?? $this->input->get("search")['value']);
		$condition = array('citymaster.isActive'=>1,'clientmaster.cityCode'=>$cityCode);
		$joinType = array('clientmaster' =>'inner','citymaster'=>'left');
		$join = array('clientmaster'=>'clientmaster.code=resetpassword.userCode','citymaster'=>'citymaster.code=clientmaster.cityCode');
		$groupByColumn = array();
		$limit = $this->input->post("length") ?? $this->input->get("length");
		$offset = $this->input->post("start") ?? $this->input->get("start");
		$extraCondition = "";
		$like = array('clientmaster.emailId' => $search.'~both','clientmaster.mobile' => $search.'~both','clientmaster.code' => $search.'~both','clientmaster.name'=>$search.'~both');
		$Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
		//print_r($this->db->last_query());
		$srno = $offset + 1;
		$data = array();
		if($Records){
			foreach ($Records->result() as $row) {
				$actionHtml = '<div class="hidden-sm hidden-xs action-buttons">
					<a class="btn btn-primary mywarning dfd-top-right cd-popup-trigger resetpswd text-white" id="' . $row->code . '" title="Delete">
					Reset <i class="ace-icon fa fa-refresh bigger-130 text-white"></i></a></div>';
				$data[] = array(
					$srno,
					$row->userCode,
					$row->cityName,
					$row->name,
					$row->emailId,
					$row->mobile,
					$actionHtml
				);
				$srno++;
			}
			$dataCount = sizeof($this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType)->result());
			$output = array(
				"draw" => intval($this->input->post("draw") ?? $this->input->get("draw")),
				"recordsTotal" => $dataCount,
				"recordsFiltered" => $dataCount,
				"data" => $data
			);
		} else {
			$dataCount = 0;
			$output = array(
				"draw" => intval($this->input->post("draw") ?? $this->input->get("draw")),
				"recordsTotal" => $dataCount,
				"recordsFiltered" => $dataCount,
				"data" => $data
			);
		}
		echo json_encode($output);
	}
	
	function reset()
	{
		$code = $this->input->post('code');
		$password =  md5('123456'); 
		$data = array('password' => $password);
		$tableName = "clientmaster";
		$res = $this->GlobalModel->doEditWithField($data, $tableName, 'code', $code);
		
		$random = rand(0, 999);
		$dataNoti = array("title" => 'Password Reset Successful', "message" => 'Your password has been rest successful.', "order_id" => "", "random_id" => $random, 'type' => '');
		$checkdevices = $this->GlobalModel->selectDataByField('code', $code, 'clientmaster');
		$DeviceIdsArr[] = $checkdevices->result() [0]->firebaseId;
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
		$notify = $this->notificationlibv_3->pushNotification($dataArr, $notification);
		
		$this->GlobalModel->deleteForeverFromField('userCode',$code,'resetpassword');
		$msg = 'Password is reset successfully';
		echo json_encode($msg);
	}
	
	public function listDeliveryRecords() 
	{
		$data['city'] = $this->GlobalModel->selectActiveData('citymaster');
		$this->load->view('dashboard/header');
		$this->load->view('dashboard/resetpassword/deliveryboylist',$data);
		$this->load->view('dashboard/footer');
	}
	
	function getDeliveryBoyPasswordList() {
		$cityCode = $this->input->post('cityCode') ?? $this->input->get('cityCode');
		$tableName = "resetpassword";
		$orderColumns = array("resetpassword.userCode,resetpassword.id,usermaster.*,citymaster.cityName");
		$orderBy = array('resetpassword' . '.id' => 'DESC');
		$search = ($this->input->post("search")['value'] ?? $this->input->get("search")['value']);
		$condition = array('usermaster.role'=>'DLB','citymaster.isActive'=>1,'usermaster.cityCode'=>$cityCode);
		$joinType = array('usermaster' =>'inner','citymaster'=>'left');
		$join = array('usermaster'=>'usermaster.code=resetpassword.userCode','citymaster'=>'citymaster.code=usermaster.cityCode');
		$groupByColumn = array();
		$limit = $this->input->post("length") ?? $this->input->get("length");
		$offset = $this->input->post("start") ?? $this->input->get("start");
		$extraCondition = "";
		$like = array('usermaster.username' => $search.'~both','usermaster.userEmail' => $search.'~both','usermaster.code' => $search.'~both','usermaster.empCode'=>$search.'~both');
		$Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
		//print_r($this->db->last_query());
		if($Records)
		{
			$srno = $offset + 1;
			$data = array();
			foreach ($Records->result() as $row) {
				$actionHtml = '<div class="hidden-sm hidden-xs action-buttons">
					<a class="btn btn-primary mywarning dfd-top-right cd-popup-trigger resetpswd text-white" id="' . $row->code . '" title="Delete">
					Reset <i class="ace-icon fa fa-refresh bigger-130 text-white"></i></a></div>';
				$data[] = array(
					$srno,
					$row->userCode,
					$row->cityName,
					$row->username,
					$row->userEmail,
					$actionHtml
				);
				$srno++;
			}
			$dataCount = sizeof($this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType)->result());
			$output = array(
				"draw" => intval($this->input->post("draw") ?? $this->input->get("draw")),
				"recordsTotal" => $dataCount,
				"recordsFiltered" => $dataCount,
				"data" => $data
			);
			echo json_encode($output);
		}
		else
		{
			$output = array(
				"draw" => intval($this->input->post("draw") ?? $this->input->get("draw")),
				"recordsTotal" => 0,
				"recordsFiltered" => 0,
				"data" => '',
			);				
			echo json_encode($output);
		}
	}
	
	function resetDeliveryPwd()
	{
		$code = $this->input->post('code');
		$password =  md5('123456'); 
		$data = array('password' => $password);
		$tableName = "usermaster";
		$res = $this->GlobalModel->doEditWithField($data, $tableName, 'code', $code);
		
		$random = rand(0, 999);
		$dataNoti = array("title" => 'Password Reset Successful', "message" => 'Your password has been rest successful.', "order_id" => "", "random_id" => $random, 'type' => '');
		$checkdevices = $this->GlobalModel->selectDataByField('code', $code, 'usermaster');
		$DeviceIdsArr[] = $checkdevices->result()[0]->firebase_id;
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
		$notify = $this->notificationlibv_3->pushNotification($dataArr, $notification);
		
		$this->GlobalModel->deleteForeverFromField('userCode',$code,'resetpassword');
		$msg = 'Password was reset successfully';
		echo json_encode($msg);
	}
  
}