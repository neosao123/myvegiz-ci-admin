<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Jobtype extends CI_Controller {
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
    public function add()
{
	$this->load->view('dashboard/header');
	$this->load->view('dashboard/jobtype/add');
	$this->load->view('dashboard/footer');
}
// public function view()
 // {
	
    // $code = $this->uri->segment(code);
      // echo "$code";
    // $data['query'] = $this->GlobalModel->selectDataById($code,'jobtypemaster');
    // $this->load->view('dashboard/header');
    // $this->load->view('dashboard/jobtype/view',$data);
     // $this->load->view('dashboard/footer');
  // }

public function listRecords()
{        
   
   $data['error']=$this->session->flashdata('response'); 
   
    $this->load->view('dashboard/header');
    $this->load->view('dashboard/jobtype/list',$data);
    $this->load->view('dashboard/footer');
}
   public function getJobTypeList()
	{
		$tables=array('jobtypemaster');
		
		$requiredColumns=array
		(
			array('code', 'jobTypeName', 'jobTypeDescription', 'isActive')
			//array('entityName','code')
		);
		
		$conditions=array(
		);
		$extraConditionColumnNames=array(
		);
		
		$extraConditions=array(
		);
		
        $Records = $this->GlobalModel1->make_datatables($tables,$requiredColumns,$conditions,$extraConditionColumnNames,$extraConditions);
		//print_r($Records->result());
			$srno=$_GET['start']+1;
			$data=array();
		  foreach($Records->result() as $row) {
							
				 if($row->isActive_03 == "1")
				 {
				    $status = " <span class='label label-sm label-success'>Active</span>";
				 }
				 else
				 {
				   $status = " <span class='label label-sm label-warning'>Inactive</span>";
				 }
				 
				 
				 $actionHtml='<div class="btn-group">
                                                        <button type="button" class="btn btn-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <i class="ti-settings"></i>
                                                        </button>
                                                        <div class="dropdown-menu animated slideInUp" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 35px, 0px);">
                                                            <a class="dropdown-item  blue" data-toggle="modal" data-target="#responsive-modal" data-seq="'.$row->code_00.'"  href="'.$row->code_00.'"><i class="ti-eye"></i> Open</a>
                                                            <a class="dropdown-item" href="'.base_url().'index.php/Jobtype/edit/'.$row->code_00.'"><i class="ti-pencil-alt"></i> Edit</a>
                                                            <a class="dropdown-item  mywarning"  data-seq="'.$row->code_00.'" id="'.$row->code_00.'"><i class="ti-trash" href></i> Delete</a>
                                                            
                                                        </div>
                                                    </div>';
								 
               $data[] = array(
					$srno,
					$row->code_00,
                    $row->jobTypeName_01,
                    $row->jobTypeDescription_02,
					$status,
					$actionHtml
               );
			   
			  
			$srno++;
		  }
		   $dataCount=$this->GlobalModel1->get_all_data($tables,$requiredColumns,$conditions,$extraConditionColumnNames,$extraConditions);
			   $output = array( 
                "draw"                    =>     intval($_GET["draw"]),  
                "recordsTotal"            =>     $dataCount,  
                "recordsFiltered"         =>     $dataCount,  
                "data"                    =>     $data  
				);
          echo json_encode($output);
	}
      public function save()
	  {
            $jobTypeName= strtoupper($this->input->post("jobTypeName"));
			$jobTypeA=trim($this->input->post("jobTypeName"));
			
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
			$text = $role." ".$userName.' added new jobtype "'.$jobTypeA.'" from '.$ip; 
			
			$log_text = array(
							'code' => "demo",
							'addID'=>$addID,
							'logText' => $text
						);
			
			//Activity Track Ends
            
			$result = $this->GlobalModel->checkDuplicateRecord('jobTypeName',$jobTypeName,'jobtypemaster');
            if ($result !=FALSE) 
            {
            $data = array(
                'error_message' => ' Duplicate jobtype name'
                                 );
            $this->load->view('dashboard/header');
            $this->load->view('dashboard/jobtype/add', $data);
            $this->load->view('dashboard/footer');
            }



    else{
		
   		$this->form_validation->set_rules('jobTypeName', 'Job Type Name', 'required');
   						   if ($this->form_validation->run()== FALSE) 
   						{	 
   								$data['error_message'] ='* Fields are Required!';
								$this->load->view('dashboard/header');
								$this->load->view('dashboard/jobtype/add',$data);
								$this->load->view('dashboard/footer');
   								
						}
						else
						{
							
	$data = array(
                    'jobTypeName' => $jobTypeA,
                    'jobTypeDescription' => ucfirst(strtolower(trim($this->input->post("jobTypeDescription")))),
                    'addID'=>$addID,
					'addIP'=>$ip,
					'isActive' => $this->input->post("isActive")
                );
                   
             $result = $this->GlobalModel->addWithoutYear($data, 'jobtypemaster', 'JOBTP'); 
			 if($result!='false')
			{
				$response['status']=true;
				$response['message']="Job Type Successfully Added.";
				$this->GlobalModel->activityAdd($log_text,'activitymaster','ACT');
			}
			else
			{
				$response['status']=false;
				$response['message']="Failed To Add Job Type";
			}
			 // print_r($response);
			 $this->session->set_flashdata('response', json_encode($response));
             redirect(base_url() . 'index.php/jobtype/listRecords', 'refresh');
           }
     }
}
public function edit()
{
    $code = $this->uri->segment(3); 
   
    $data['query'] = $this->GlobalModel->edit('jobtypemaster',$code);
  
    $this->load->view('dashboard/header');
    $this->load->view('dashboard/jobtype/edit',$data);
     $this->load->view('dashboard/footer');
}
   
     public function update()
    {
            $code =  $this->input->post('code');
			$jobTypeNameA = trim($this->input->post("jobTypeName"));
			$jobTypeDescription= ucfirst(strtolower(trim($this->input->post("jobTypeDescription"))));
            $isActive= $this->input->post("isActive");
			
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
			$text = $role." ".$userName.' updated jobtype "'.$jobTypeNameA.'" from '.$ip; 
			
			$log_text = array(
							'code' => "demo",
							'addID'=> $addID,
							'logText'=> $text
						); 
			
			//Activity Track Ends
			
			$Records=$this->GlobalModel->selectDataByField('code',$code,'jobtypemaster');
	     	$dbJobTypeName=$Records->result()[0]->jobTypeName;

			    $this->form_validation->set_rules('jobTypeName', 'Job Type Name', 'required');
   						   if ($this->form_validation->run()== FALSE) 
   						{	 
   								$data['error_message'] ='* Fields are Required!';
								$data['query'] = $this->GlobalModel->edit('jobtypemaster',$code);
  
							$this->load->view('dashboard/header');
							$this->load->view('dashboard/jobtype/edit',$data);
							 $this->load->view('dashboard/footer');
   								
						}
						else
						{
							if($dbJobTypeName!=$jobTypeNameA)
							{ 
						     $result = $this->GlobalModel->checkDuplicateRecord('jobTypeName',$jobTypeNameA,'jobtypemaster');
								if ($result !=FALSE) 
								{
								$data = array(
									'error_message' => ' Duplicate jobtype name'
													 );
								 
									$data['query'] = $this->GlobalModel->edit('jobtypemaster',$code);
								  
									$this->load->view('dashboard/header');
									$this->load->view('dashboard/jobtype/edit',$data);
									 $this->load->view('dashboard/footer');
								}
								
								else
								{
									$this->subUpdateFunction($jobTypeNameA,$code,$jobTypeDescription,$isActive,$log_text,$addID,$ip);
								}
							}
							else
							{
							   $this->subUpdateFunction($jobTypeNameA,$code,$jobTypeDescription,$isActive,$log_text,$addID,$ip);	
							}

           }
	}
	
	    function subUpdateFunction($jobTypeNameA,$code,$jobTypeDescription,$isActive,$log_text,$addID,$ip)
   {
		$data = array(
			'jobTypeName' => $jobTypeNameA,
            'jobTypeDescription' =>$jobTypeDescription,
			'editID'=>$addID,
			'editIP'=>$ip,
			'isActive' => $isActive
		);
		 $result=$this->GlobalModel->doEdit($data,'jobtypemaster',$code);
		   if($result!='false')
		{
			$response['status']=true;
			$response['message']="Job Type Successfully Updated.";
			$this->GlobalModel->activityAdd($log_text,'activitymaster','ACT');
		}
		else
		{
			$response['status']=false;
			$response['message']="No change In Job Type";
		}
		  //print_r($result);
		  //print_r($response);
		 $this->session->set_flashdata('response', json_encode($response));
		redirect(base_url() . 'index.php/jobtype/listRecords', 'refresh');
	}
   
        public function delete() {
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
		$dataQ = $this->GlobalModel->selectDataByField('code',$code,'jobtypemaster');  
		$jobTypeName=''; 
		
		foreach ($dataQ->result() as $row) 
		{	
			$jobTypeName = $row->jobTypeName; 
		}
		
		$text = $role." ".$userName.' deleted jobtype "'.$jobTypeName.'" from '.$ip; 
		
		$log_text = array(
							'code' => "demo",
							'addID'=>$addID,
							'logText' => $text
						);
		
		$this->GlobalModel->activityAdd($log_text,'activitymaster','ACT'); 
		  
		//Activity Track Ends
       
		echo $this->GlobalModel->deleteForever($code,'jobtypemaster');
       // redirect(base_url() . 'index.php/jobtype/listRecords', 'refresh');
        }
		
		
	public function view()
	{
		$code = $this->input->get('code');
		
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
		
		//Activity Track Ends
        
		$tables=array('jobtypemaster');
		
		$requiredColumns=array
		(
			array('code', 'jobTypeName', 'jobTypeDescription', 'isActive')
			
		);
		
		$conditions=array(
		);
		$extraConditionColumnNames=array(
		array("code")
		);
		
		$extraConditions=array(
		array($code)
		);
		
        $Records = $this->GlobalModel1->make_datatables($tables,$requiredColumns,$conditions,$extraConditionColumnNames,$extraConditions);
		//print_r($Records->result());
		
		$modelHtml='<form>';
		$activeStatus="";
		
		foreach($Records->result() as $row)
		{
			 
			
			if($row->isActive_03 == "1")
            {
                $activeStatus='<span class="label label-sm label-success">Active</span>';
            }
			else
			{
				$activeStatus='<span class="label label-sm label-warning">Inactive</span>';
			}
												  
			$modelHtml.='<div class="form-row"><div class="col-md-6 mb-3"><label><b>Job Type Code:</b> </label>
						<input type="text" value="'.$row->code_00.'" class="form-control-line" readonly></div>
					<div class="col-md-6 mb-3"><label> <b>Job Type Name:</b> </label>
						<input type="text" class="form-control-line" value="'.$row->jobTypeName_01.'" readonly></div> </div>
						<div class="col-md-12 mb-3"><label><b>Job Type Description: </b></label>
						<input type="text" class="form-control-line" row="2" cols="50" readonly value="'.$row->jobTypeDescription_02.'" </div>
					<div class="form-group">'.$activeStatus .'</div>';
					
					//for activity
					
					$text = $role." ".$userName.' viewed jobtype "'.$row->jobTypeName_01.'" from '.$ip; 
					
					$log_text = array(
										'code' => "demo",
										'addID'=>$addID,
										'logText' => $text
									);
					
					$this->GlobalModel->activityAdd($log_text,'activitymaster','ACT');
					
					//Activity Track Ends
		}
		
		$modelHtml.='</form>';
		echo $modelHtml;
	}
    
    
}
?>