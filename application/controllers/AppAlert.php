<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class AppAlert extends CI_Controller {
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
    	$this->load->view('dashboard/appalert/list',$data);
    	$this->load->view('dashboard/footer');
    }
	
    public function add()
    {
    	// $this->load->view('dashboard/header');
    	$this->load->view('dashboard/appalert/add');
    	// $this->load->view('dashboard/footer');	
    }
	
	public function edit()
    {
        $code=$this->input->post('code');
        $data['query'] = $this->GlobalModel->selectDataById($code,'appalert');
		
    	// $this->load->view('dashboard/header');
    	$this->load->view('dashboard/appalert/edit',$data);
    	// $this->load->view('dashboard/footer');
    }
	
	public function getAppAlertList()
	{
		$tableName="appalert";
		$orderColumns = array("appalert.*");
		$condition=array();
		$orderBy = array('appalert' . '.id' => 'DESC');
		$joinType=array('appalert'=>'inner');
		$join = array();
		$groupByColumn=array();
		$limit=$this->input->GET("length");
		$offset=$this->input->GET("start");
		$extraCondition=" appalert.isDelete=0 OR appalert.isDelete IS NULL";
		$like = array();
		$Records = $this->GlobalModel->selectQuery($orderColumns,$tableName,$condition,$orderBy,$join,$joinType,$like,$limit,$offset,$groupByColumn,$extraCondition);
		$srno=$_GET['start']+1;
		if($Records){
			foreach($Records->result() as $row) 
			{ 	
				$code=$row->code;		
							
				if($row->isActive == 1)
				{
					$status = "<span class='label mywarning1 label-sm label-success' data-seq='".$row->code."' >Active</span>";
							
				} else {
					$status = "<span class='label mywarning1 label-sm label-warning' data-seq='".$row->code."' >Inactive</span>";
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
						$row->title,
						$row->description,
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
		$title = trim($this->input->post("title"));
		$description = trim($this->input->post("description"));
		
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
		$text = $role." ".$userName.' added new App Alert"'.$title.'" from '.$ip;
		// $description = $role." ".$userName.' added new App Alert"'.$description.'" from '.$ip;		
		
		$log_text = array(
				'code' => "demo",
				'addID'=>$addID,
				'logText' => $text
			);

		//Activity Track Ends
		
		$result = $this->GlobalModel->checkDuplicateRecord('title',$title,'appalert');
		
		if ($result==true) 
		{
			
			$data = array('error_message' => 'Duplicate  App Alert');
			$response['status']='error';
			$response['message']='Duplicate App Alert Name';
		}
		else
		{
			    if(trim($this->input->post("isActive"))==1)
				{
					$result=$this->GlobalModel->selectData("appalert");
					
					foreach($result->result_array() as $row)
					{
						$this->db->set('isActive',0)
							 ->where('code',$row['code'])
							->update('appalert');
					//$this->GlobalModel->doEditAllisActive($udata,'appalert',$row['code']);
					}
				}
			
			
			$data = array(
				
				'title' => $title,
				'description' => $description,
				'addID'=>$addID,
				'addIP'=>$ip,
				'isActive' => trim($this->input->post("isActive")),
			);
						
			$code = $this->GlobalModel->addWithoutYear($data, 'appalert', 'ECAT');
			
			if($code!='false')
			{
				$response['status']='true';
				$response['message']="App alert Added.";
				$this->GlobalModel->activityAdd($log_text,'activitymaster','ACT');
				
				
				
			}
			else
			{
				$response['status']='false';
				$response['message']="Failed To Add App Alert";
			}
			// $this->session->set_flashdata('response', json_encode($response));		
			// redirect(base_url() . 'index.php/Vendor/listRecords', 'refresh');
		}
		echo json_encode($response);
    }
	
	public function update(){
		$code =  $this->input->post('code');
		$title = trim($this->input->post("title"));
		$description = trim($this->input->post("description"));
			
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
		$text = $role." ".$userName.' uopdated App Alert "'.$title.'" from '.$ip; 
		
		$log_text = array(
			'code' => "demo",
			'addID'=>$addID,
			'logText' => $text
		);
			
			//Activity Track Ends 
		$result = $this->GlobalModel->checkDuplicateRecordNotEqualtoCode('title',$title,'appalert',$code);
		
		if ($result==true) 
		{
			$data = array('error_message' => 'Duplicate Entity Category Name');
			$response['status']='error';
			$response['message']='Duplicate Entity Category Name';
		}
		else
		{	    if(trim($this->input->post("isActive"))==1)
				{
					$result=$this->GlobalModel->selectData("appalert");
					
					foreach($result->result_array() as $row)
					{
						$this->db->set('isActive',0)
							 ->where('code',$row['code'])
							->update('appalert');
					//$this->GlobalModel->doEditAllisActive($udata,'appalert',$row['code']);
					}
				}
	
			$data = array(
				
				'title' => $title,
				'description' => $description,
				'editID'=>$addID,
				'editIP'=>$ip,
				'isActive' => trim($this->input->post("isActive"))
			);
			  
			 
			$result = $this->GlobalModel->doEdit($data, 'appalert', $code); 
			
			if($result!='false')
			{
				$response['status']='true';
				$response['message']="App Alert Successfully Updated.";
				$this->GlobalModel->activityAdd($log_text,'activitymaster','ACT');
			}
			else
			{
				$response['status']='false';
				$response['message']="No change In App Alert";
			}
			
			// $this->session->set_flashdata('response', json_encode($response)); 
			// redirect(base_url() . 'index.php/Vendor/listRecords', 'refresh');
        }
		echo json_encode($response);
	}
	
	public function change_status(){
		 $code = $this->input->post('code');
		 $txt = $this->input->post('txt');
		 
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
		$text = $role." ".$userName.' uopdated App Alert "'.$title.'" from '.$ip; 
		
		$log_text = array(
			'code' => "demo",
			'addID'=>$addID,
			'logText' => $text
		);
		 
		 
		 
		        if(trim($this->input->post("txt"))=='Inactive')
				{
					$result=$this->GlobalModel->selectData("appalert");
					
					foreach($result->result_array() as $row)
					{
						$this->db->set('isActive',0)
							 ->where('code',$row['code'])
							->update('appalert');
					}
					$data = array(
				     'isActive' => 1
			        );
			  
			        $result = $this->GlobalModel->doEdit($data, 'appalert', $code); 
				}
				else
				{
					$data = array(
				     'isActive' => 0
			        );
			  
			        $result = $this->GlobalModel->doEdit($data, 'appalert', $code);
				}
				if($result!='false')
				{
					$response['status']='true';
					$response['message']="App Alert Successfully Updated.";
					$this->GlobalModel->activityAdd($log_text,'activitymaster','ACT');
				}
				else
				{
					$response['status']='false';
					$response['message']="No change In App Alert";
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
		$dataQ = $this->GlobalModel->selectDataByField('code',$code,'appalert');  
		$categoryName=''; 
		
		foreach ($dataQ->result() as $row) 
		{	
			$categoryName = $row->title; 
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
		  
		$resultData=$this->GlobalModel->doEdit($data,'appalert',$code);
    
		echo $this->GlobalModel->delete($code,'appalert');
    }
	
	public function activate()
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
		$dataQ = $this->GlobalModel->selectDataByField('code',$code,'appalert');  
		$categoryName=''; 
		
		foreach ($dataQ->result() as $row) 
		{	
			$categoryName = $row->title; 
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
		  
		$resultData=$this->GlobalModel->doEdit($data,'appalert',$code);
    
		echo $this->GlobalModel->delete($code,'appalert');
    }
	
	
}