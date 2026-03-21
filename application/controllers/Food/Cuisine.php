<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cuisine extends CI_Controller {
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
		$this->load->view('dashboard/cuisine/list',$data);
		$this->load->view('dashboard/footer');
	}
	
	public function getCuisineList()
	{
		$tables=array('cuisinemaster');		
		$requiredColumns=array
		(
			array('code', 'cuisineName', 'isActive','cuisinePhoto') 
		);
		$conditions=array(
		);
		$extraConditionColumnNames=array(
		); 
		$extraConditions=array(
		);
		
		$Records = $this->GlobalModel1->make_datatables($tables,$requiredColumns,$conditions,$extraConditionColumnNames,$extraConditions);
		// print_r($Records->result());
		$srno=$_GET['start']+1;
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
			
			if($row->cuisinePhoto_03!="")
			{
				$path = 'uploads/cuisinemaster/'.$row->cuisinePhoto_03;
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
				$row->cuisineName_01,
				$path,
				$status,
				$actionHtml
			);		
			
			$srno++;
		}
		$dataCount=$this->GlobalModel1->get_all_data($tables,$requiredColumns,$conditions,$extraConditionColumnNames,$extraConditions);
		$output = array( 
		"draw"                    =>     intval($_GET["draw"]),  
		"recordsTotal"          =>      $dataCount,  
		"recordsFiltered"     =>     $dataCount,  
		"data"                    =>     $data  
		);
		echo json_encode($output);
	}	
	
	public function add()
    {
    	$this->load->view('dashboard/cuisine/add');
    }
	
	public function edit()
	{
		$code=$this->input->post('code');
        $data['query'] = $this->GlobalModel->selectDataById($code,'cuisinemaster');    	 
    	$this->load->view('dashboard/cuisine/edit',$data);    	 
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
		$text = $role." ".$userName.' added new Cuisine from '.$ip; 
		$log_text = array(
			'code' => "demo",
			'addID'=>$addID,
			'logText' => $text
		);
		$cuisineName= $this->input->post('cuisineName');		
		$result = $this->GlobalModel->checkDuplicateRecord('cuisineName',ucfirst(strtolower(trim($cuisineName))),'cuisinemaster');
		if ($result==true) {
			$response['status']='error';
			$response['message']='Duplicate Cuisine Name';
		}else{
			$uploadRootDir = 'uploads/';
			$uploadDir = 'uploads/cuisinemaster';
			if(!file_exists($uploadDir)) mkdir($uploadDir,0755,false);
	        if (!empty($_FILES['imagePath']['name'])) {
			    $insData['cuisineName'] = ucfirst(strtolower(trim($cuisineName)));
				$data = array(
					'cuisineName' => $cuisineName,
					'addID'=>$addID,
					'addIP'=>$ip,
					'isActive' => trim($this->input->post("isActive")),
				);
				$code = $this->GlobalModel->addWithoutYear($data, 'cuisinemaster', 'CUIS');			
			    if($code!='false'){
					$tmpFile = $_FILES['imagePath']['tmp_name'];
					$ext = pathinfo($_FILES['imagePath']['name'], PATHINFO_EXTENSION);
					$name = $code . time() . '_.' . $ext;
					$filename = $uploadDir . '/' . $name;
					move_uploaded_file($tmpFile, $filename);
					if (file_exists($filename))
					{
						$subData = array('cuisinePhoto' => $name);
						$filedoc = $this->GlobalModel->doEdit($subData, 'cuisinemaster', $code);
						$response['status']="true";
						$response['message']="Slider added Successfully Updated.";
					} 
					else 
					{
						unlink($filename);
						$response['status']="false";
						$response['message']="Failed to upload the Image";
					} 
				}else{
					$response['status']='false';
					$response['message']="Failed To Add Menu Category";
				}	 
		    }else{
				$response['status']= "false";
				$response['message']="Cannot upload empty image";
			}
		}			
		echo json_encode($response);
	}
	
	
	public function update()
	{
		$code= $this->input->post('code');
		$cuisineName= $this->input->post('cuisineName');
		$isActive= $this->input->post('isActive');
		$uploadRootDir = 'uploads/';
		$uploadDir = 'uploads/cuisinemaster';
		$result = $this->GlobalModel->checkDuplicateRecordNotEqualtoCode('LOWER(cuisineName)',strtolower($cuisineName),'cuisinemaster',$code);
		if ($result==true) {
			$response['status']='error';
			$response['message']='Duplicate Cuisine Name';
		}else{
			if(!file_exists($uploadDir)) mkdir($uploadDir,0755,false);
			$updata = array('cuisineName' => $cuisineName,'isActive'=>$isActive);
			$result = $this->GlobalModel->doEdit($updata, 'cuisinemaster', $code);
			$filedoc = 'false';
			if (!empty($_FILES['imagePath']['name'])) {
				$tmpFile = $_FILES['imagePath']['tmp_name'];
				$ext = pathinfo($_FILES['imagePath']['name'], PATHINFO_EXTENSION);
				$name = $code . time() . '_.' . $ext;
				$filename = $uploadDir . '/' . $name;
				move_uploaded_file($tmpFile, $filename);
				if (file_exists($filename)) {
					$subData = array('cuisinePhoto' => $name,'isActive'=>$isActive);
					$filedoc = $this->GlobalModel->doEdit($subData, 'cuisinemaster', $code); 
				} else {
					unlink($filename);
					$filedoc = 'false';
				}			 
			}
			if($result!='false' || $filedoc!='false'){
				$response['status']="true";
				$response['message']="Cuisine Successfully Updated.";
			}else{
				$response['status']="false";
				$response['message']="Failed to update the cuisine";
			}
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
		$dataQ = $this->GlobalModel->selectDataByField('code',$code,'cuisinemaster');  
		$categoryName=''; 
		
		foreach ($dataQ->result() as $row) 
		{	
			$categoryName = $row->cuisineName; 
		}
		
		
		$text = $role." ".$userName.' deleted Cuisine "'.$categoryName.'" from '.$ip; 
		
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
		
		$resultData=$this->GlobalModel->doEdit($data,'cuisinemaster',$code);
    
		echo $this->GlobalModel->delete($code,'cuisinemaster');
    }
}	