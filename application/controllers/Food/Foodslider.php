<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Foodslider extends CI_Controller {
	var $session_key;
	public function __construct()
	{
		parent::__construct();
		$this->load->helper('form','url','html');
		$this->load->library('form_validation');
		$this->load->model('GlobalModel');
		$this->load->model('GlobalModel1');
		$this->session_key = $this->session->userdata('key'.SESS_KEY);
		if (!isset($this->session->userdata['logged_in' . $this->session_key]['code'])) {
			redirect('Admin/login', 'refresh');
		}
	}
	
	public function listRecords()
	{
		$data['error']=$this->session->flashdata('response'); 
		$this->load->view('dashboard/header');
		$this->load->view('dashboard/foodslider/list',$data);
		$this->load->view('dashboard/footer');
	}
	
	public function getSliderList()
	{
		$tables=array('foodslider');		
		$requiredColumns=array
		(
			array('code', 'caption', 'isActive','sliderPhoto') 
		);
		$conditions=array(
		);
		$extraConditionColumnNames=array(
		); 
		$extraConditions=array(
		);
		
		$Records = $this->GlobalModel1->make_datatables($tables,$requiredColumns,$conditions,$extraConditionColumnNames,$extraConditions);
		// print_r($Records->result());
		$offset = $this->input->post("start") ?? $this->input->get("start");
		$srno = (intval($offset) > 0 ? intval($offset) : 0) + 1;
		$data=array();
		foreach($Records->result() as $row) 
		{
			
			if($row->isActive_02 == "1")
			{
				$status = " <span class='label label-sm label-success'>Active</span>";
			}
			else
			{
				$status = " <span class='label label-sm label-warning'>Inactive</span>";
			}
			
			if($row->sliderPhoto_03!="")
			{
				$path = 'uploads/foodslider/'.$row->sliderPhoto_03;
				if(file_exists($path))
				{
					$path = base_url($path);
					$path = '<img style="width:50px;height:50px" src="'.$path.'">';
				}
				else 
				{
					$path = '<b>No Image</b>';
				}
			}
			else
			{
				$path = '<b>No Image</b>';
			}
			
			
			$actionHtml='<div class="btn-group">
					<button type="button" class="btn btn-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<i class="ti-settings"></i>
					</button>
					<div class="dropdown-menu animated slideInUp" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 35px, 0px);">
						<a class="dropdown-item edit" data-seq="'.$row->code_00.'" id="'.$row->code_00.'"><i class="ti-pencil-alt"></i> Edit</a>
						<a class="dropdown-item  mywarning" data-seq="'.$row->code_00.'" id="'.$row->code_00.'"><i class="ti-trash" href></i> Delete</a>
					</div>
				</div>';
			
			$data[] = array(
				$srno,
				$row->code_00,
				//$row->caption_01,
				$path,
				$status,
				$actionHtml
			);		
			
			$srno++;
		}
		$dataCount=$this->GlobalModel1->get_all_data($tables,$requiredColumns,$conditions,$extraConditionColumnNames,$extraConditions);
		$output = array( 
		"draw"                    =>     intval($this->input->post("draw") ?? $this->input->get("draw") ?? 0),  
		"recordsTotal"          =>      $dataCount,  
		"recordsFiltered"     =>     $dataCount,  
		"data"                    =>     $data  
		);
		echo json_encode($output);
	}	
	
	public function add()
    {
    	$this->load->view('dashboard/foodslider/add');
    }
	
	public function edit()
	{
		$code=$this->input->post('code');
        $data['query'] = $this->GlobalModel->selectDataById($code,'foodslider');    	 
    	$this->load->view('dashboard/foodslider/edit',$data);    	 
	}
	
	public function save()
	{
		
		$addID = $this->session->userdata['logged_in'.$this->session_key]['code'];
		$userRole = $this->session->userdata['logged_in'.$this->session_key]['role'];
		$userName = $this->session->userdata['logged_in'.$this->session_key]['username']; 
		$role = "";
		
		switch($userRole){
			case "ADM" : $role="Admin"; break;
			case "USR" : $role="User"; break; 
		}
		
		$ip=$_SERVER['REMOTE_ADDR'];
		$text = $role." ".$userName.' added new food slider from '.$ip; 
		
		$log_text = array(
			'code' => "demo",
			'addID'=>$addID,
			'logText' => $text
		);
		
		$caption= $this->input->post('caption');		
		 
		$uploadRootDir = 'uploads/';
		$uploadDir = 'uploads/foodslider';
		if(!file_exists($uploadDir)) mkdir($uploadDir,0755,false);
		
		if (!empty($_FILES['imagePath']['name'])) {
			
			$insData['caption'] = ucfirst(strtolower(trim($caption)));
			$data = array(
				'caption' => $caption,
				'addID'=>$addID,
				'addIP'=>$ip,
				'isActive' => trim($this->input->post("isActive")),
			);
			$code = $this->GlobalModel->addWithoutYear($data, 'foodslider', 'FSLI');			
			if($code!='false')
			{
				
				$tmpFile = $_FILES['imagePath']['tmp_name'];
				$ext = pathinfo($_FILES['imagePath']['name'], PATHINFO_EXTENSION);
				$name = $code . time() . '_.' . $ext;
				$filename = $uploadDir . '/' . $name;
				move_uploaded_file($tmpFile, $filename);
				if (file_exists($filename))
				{
					$subData = array('sliderPhoto' => $name);
					$filedoc = $this->GlobalModel->doEdit($subData, 'foodslider', $code);
					$response['status']="true";
					$response['message']="Slider added Successfully Updated.";
				} 
				else 
				{
					unlink($filename);
					$response['status']="false";
					$response['message']="Failed to upload the Image";
				} 
			}
			else
			{
				$response['status']='false';
				$response['message']="Failed To Add Menu Category";
			}	 
		}
		else
		{
			$response['status']= "false";
			$response['message']="Cannot upload empty image";
		}		
		echo json_encode($response);
	}
	
	public function update()
	{
		$addID = $this->session->userdata['logged_in'.$this->session_key]['code'];
		$userRole = $this->session->userdata['logged_in'.$this->session_key]['role'];
		$userName = $this->session->userdata['logged_in'.$this->session_key]['username']; 
		$role = "";
		
		switch($userRole){
			case "ADM" : $role="Admin"; break;
			case "USR" : $role="User"; break; 
		}
		
		$ip=$_SERVER['REMOTE_ADDR'];
		$text = $role." ".$userName.' updated food slider from '.$ip; 
		
		$log_text = array(
			'code' => "demo",
			'addID'=>$addID,
			'logText' => $text
		);
		
		$code= $this->input->post('code');	
		$isActive= $this->input->post('isActive');
		$uploadRootDir = 'uploads/';
		$uploadDir = 'uploads/foodslider';
		if(!file_exists($uploadDir)) mkdir($uploadDir,0755,false);
		
		$update=array('isActive'=>$isActive,'editID'=>$addID,'editIP'=>$ip);
		$result = $this->GlobalModel->doEdit($update, 'foodslider', $code);
		
		$filedoc = 'false';
		
		if (!empty($_FILES['imagePath']['name'])) {
			$tmpFile = $_FILES['imagePath']['tmp_name'];
			$ext = pathinfo($_FILES['imagePath']['name'], PATHINFO_EXTENSION);
			$name = $code . time() . '_.' . $ext;
			$filename = $uploadDir . '/' . $name;
			move_uploaded_file($tmpFile, $filename);
			if (file_exists($filename)) {
				$subData = array('sliderPhoto' => $name);
				$filedoc = $this->GlobalModel->doEdit($subData, 'foodslider', $code); 
			} else {
				unlink($filename); 
				$filedoc = 'false';
			}			 
		}
		
		if($result!='false' || $filedoc!='false')
		{
			$this->GlobalModel->activityAdd($log_text,'activitymaster','ACT'); 
			$response['status']="true";
			$response['message']="Food Slider Successfully Updated.";
		}
		else
		{
			$response['status']= "false";
			$response['message']="Failed to update the slider";
		}		
		echo json_encode($response);
	}
	
	public function delete()
    {
        $code = $this->input->post('code'); 
		//Activity Track Starts 
		$addID = $this->session->userdata['logged_in'.$this->session_key]['code'];
		$userRole = $this->session->userdata['logged_in'.$this->session_key]['role'];
		$userName = $this->session->userdata['logged_in'.$this->session_key]['username']; 
		$role = "";
		
		switch($userRole){
			case "ADM" : $role="Admin"; break;
			case "USR" : $role="User"; break;
		}
		
		$ip=$_SERVER['REMOTE_ADDR']; 
		$dataQ = $this->GlobalModel->selectDataByField('code',$code,'foodslider');  
		$categoryName=''; 
		
		foreach ($dataQ->result() as $row) 
		{	
			$categoryName = $row->sliderPhoto; 
		}
		
		
		$text = $role." ".$userName.' deleted Food Slider "'.$categoryName.'" from '.$ip; 
		
		$log_text = array(
			'code' => "demo",
			'addID'=>$addID,
			'logText' => $text
		);
		$this->GlobalModel->activityAdd($log_text,'activitymaster','ACT');  
		
		$data1=array(
			'deleteID' => $addID,
			'deleteIP' => $ip,
			'isActive' => 0,
			'isDelete' => 1
		);
		
		$data=array(
			'deleteID' => $addID,
			'deleteIP' => $ip
		);
		
		$resultData=$this->GlobalModel->doEdit($data,'foodslider',$code);
    
		echo $this->GlobalModel->delete($code,'foodslider');
    }
}	