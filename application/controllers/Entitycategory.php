<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Entitycategory extends CI_Controller {
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
    	$this->load->view('dashboard/entitycategory/list',$data);
    	$this->load->view('dashboard/footer');
    }
	
    public function add()
    {
    	// $this->load->view('dashboard/header');
    	$this->load->view('dashboard/entitycategory/add');
    	// $this->load->view('dashboard/footer');	
    }
	
	public function edit()
    {
        $code=$this->input->post('code');
        $data['query'] = $this->GlobalModel->selectDataById($code,'entitycategory');
    	// $this->load->view('dashboard/header');
    	$this->load->view('dashboard/entitycategory/edit',$data);
    	// $this->load->view('dashboard/footer');
    }
	
	public function getEntityCategoryList()
	{
		$search = $this->input->GET("search")['value'];
		$tableName="entitycategory";
		$orderColumns = array("entitycategory.*,maincategorymaster.mainCategoryName");
		$condition=array();
		$orderBy = array('entitycategory' . '.id' => 'DESC');
		$joinType=array('maincategorymaster'=>'inner');
		$join = array('maincategorymaster'=>'maincategorymaster.code=entitycategory.mainCategoryCode');
		$groupByColumn=array();
		$limit=$this->input->GET("length");
		$offset=$this->input->GET("start");
		$extraCondition=" entitycategory.isDelete=0 OR entitycategory.isDelete IS NULL";
		$like = array("maincategorymaster.mainCategoryName" => $search . "~both","entitycategory.entityCategoryName" => $search . "~both");
		$Records = $this->GlobalModel->selectQuery($orderColumns,$tableName,$condition,$orderBy,$join,$joinType,$like,$limit,$offset,$groupByColumn,$extraCondition);
		$srno=$_GET['start']+1;
		if($Records){
			foreach($Records->result() as $row) 
			{ 	
				$code=$row->code;		
							
				if($row->isActive == 1)
				{
					$status = "<span class='label label-sm label-success'>Active</span>";
				} else {
					$status = "<span class='label label-sm label-warning'>Inactive</span>";
				}
				
				$actionHtml='<div class="btn-group">
								<button type="button" class="btn btn-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									<i class="ti-settings"></i>
								</button>
								<div class="dropdown-menu animated slideInUp" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 35px, 0px);">
									<a class="dropdown-item edit" data-seq="'.$row->code.'" id="'.$row->code.'"><i class="ti-pencil-alt"></i> Edit</a>
									<a class="dropdown-item  mywarning" data-seq="'.$row->code.'" id="'.$row->code.'"><i class="ti-trash" href></i> Delete</a>
								</div>
							</div>';
				
					$data[] = array(
						$srno,
						$row->code,
						$row->mainCategoryName,
						$row->entityCategoryName,
						 $status,
						$actionHtml
					); 
					$srno++;
			} 
			$dataCount=sizeof($this->GlobalModel->selectQuery($orderColumns,$tableName,$condition,$orderBy,$join,$joinType,array(),'','','',$extraCondition)->result());
			$output = array( 
				"draw"			  =>     intval($_GET["draw"]),  
				"recordsTotal"    =>      $dataCount,  
				"recordsFiltered" =>     $dataCount,  
				"data"            =>     $data  
			);
			echo json_encode($output);
		} else{
			$dataCount=0;
			$data =array();
			$output = array( 
				"draw"			  =>     intval($_GET["draw"]),  
				"recordsTotal"    =>     $dataCount,  
				"recordsFiltered" =>     $dataCount,  
				"data"            =>     $data  
			);
			echo json_encode($output);
		}
	}
	
	public function save()
    {
		$entityCategoryName = trim($this->input->post("entityCategoryName"));
			
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
		$text = $role." ".$userName.' added new Entity Category "'.$entityCategoryName.'" from '.$ip; 
		
		$log_text = array(
				'code' => "demo",
				'addID'=>$addID,
				'logText' => $text
			);

		//Activity Track Ends
		
		$result = $this->GlobalModel->checkDuplicateRecord('entityCategoryName',$entityCategoryName,'entitycategory');
		
		if ($result==true) 
		{
			$data = array('error_message' => 'Duplicate Entity Category Name');
			$response['status']='error';
			$response['message']='Duplicate Entity Category Name';
		}
		else
		{
			$data = array(
				'mainCategoryCode' => 'MCAT_3',
				'entityCategoryName' => $entityCategoryName,
				'addID'=>$addID,
				'addIP'=>$ip,
				'isActive' => trim($this->input->post("isActive")),
			);
						
			$code = $this->GlobalModel->addWithoutYear($data, 'entitycategory', 'ECAT');
			
			if($code!='false')
			{
				$response['status']='true';
				$response['message']="Entity Category Successfully Added.";
				$this->GlobalModel->activityAdd($log_text,'activitymaster','ACT');
			}
			else
			{
				$response['status']='false';
				$response['message']="Failed To Add Entity Category";
			}
			// $this->session->set_flashdata('response', json_encode($response));		
			// redirect(base_url() . 'index.php/Vendor/listRecords', 'refresh');
		}
		echo json_encode($response);
    }
	
	public function update(){
		$code =  $this->input->post('code');
		$entityCategoryName = trim($this->input->post("entityCategoryName"));
			
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
		$text = $role." ".$userName.' uopdated Entity Category "'.$entityCategoryName.'" from '.$ip; 
		
		$log_text = array(
			'code' => "demo",
			'addID'=>$addID,
			'logText' => $text
		);
			
			//Activity Track Ends 
		$result = $this->GlobalModel->checkDuplicateRecordNotEqualtoCode('entityCategoryName',$entityCategoryName,'entitycategory',$code);
		
		if ($result==true) 
		{
			$data = array('error_message' => 'Duplicate Entity Category Name');
			$response['status']='error';
			$response['message']='Duplicate Entity Category Name';
		}
		else
		{	
			$data = array(
				'mainCategoryCode' => 'MCAT_3',
				'entityCategoryName' => $entityCategoryName,
				'editID'=>$addID,
				'editIP'=>$ip,
				'isActive' => trim($this->input->post("isActive"))
			);
			  
			 
			$result = $this->GlobalModel->doEdit($data, 'entitycategory', $code); 
			
			if($result!='false')
			{
				$response['status']='true';
				$response['message']="Entity Category Successfully Updated.";
				$this->GlobalModel->activityAdd($log_text,'activitymaster','ACT');
			}
			else
			{
				$response['status']='false';
				$response['message']="No change In Entity Category";
			}
			
			// $this->session->set_flashdata('response', json_encode($response)); 
			// redirect(base_url() . 'index.php/Vendor/listRecords', 'refresh');
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
		$dataQ = $this->GlobalModel->selectDataByField('code',$code,'entitycategory');  
		$categoryName=''; 
		
		foreach ($dataQ->result() as $row) 
		{	
			$categoryName = $row->entityCategoryName; 
		}
		
		$text = $role." ".$userName.' deleted Entity Category "'.$categoryName.'" from '.$ip; 
		
		$log_text = array(
			'code' => "demo",
			'addID'=>$addID,
			'logText' => $text
		);
		$this->GlobalModel->activityAdd($log_text,'activitymaster','ACT');  
		
		$data=array(
			'deleteID' => $addID,
			'deleteIP' => $ip
		);
		  
		$resultData=$this->GlobalModel->doEdit($data,'entitycategory',$code);
    
		echo $this->GlobalModel->delete($code,'entitycategory');
    }
	
}