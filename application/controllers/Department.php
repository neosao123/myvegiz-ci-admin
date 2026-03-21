<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Department extends CI_Controller {
  public function __construct()
    {
        parent::__construct();
		 $this->load->helper('form','url','html');
      	 $this->load->library('form_validation');
        $this->load->model('GlobalModel');
		 $this->load->model('GlobalModel1');
		 $session_key = $this->session->userdata('key'.SESS_KEY);
		 if (!isset($this->session->userdata['logged_in' . $this->session_key]['code'])) {
			redirect('Admin/login', 'refresh');
		}
    }
    public function add()
{
  $this->load->view('dashboard/header');
  $this->load->view('dashboard/department/add');
  $this->load->view('dashboard/footer');
}
// public function view()
 // {
   
    // $code = $this->uri->segment(3); 
    
    // $data['entities'] = $this->GlobalModel->selectDataExcludeDelete('entities');
     // $data['office'] = $this->GlobalModel->selectDataExcludeDelete('officemaster');
    // $data['query'] = $this->GlobalModel->selectDataById($code,'departmentmaster');
    // $this->load->view('dashboard/header');
    // $this->load->view('dashboard/department/view',$data);
     // $this->load->view('dashboard/footer');
  // }

public function listRecords()
{
    $data['error']=$this->session->flashdata('response');
	  
    $this->load->view('dashboard/header');
    $this->load->view('dashboard/department/list',$data);
    $this->load->view('dashboard/footer');
}
public function getDepartmentList()
	{
		$tables=array('departmentmaster');
		
		$requiredColumns=array
		(
			array('code','departmentName','departmentDescription','isActive'),
			
		);
		
		$conditions=array(
			
		);
		$extraConditionColumnNames=array(
		);
		
		$extraConditions=array(
		);
		
         $Records = $this->GlobalModel1->make_datatables($tables,$requiredColumns,$conditions,$extraConditionColumnNames,$extraConditions);
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
                                                            <a class="dropdown-item" href="'.base_url().'index.php/Department/edit/'.$row->code_00.'"><i class="ti-pencil-alt"></i> Edit</a>
                                                            <a class="dropdown-item  mywarning" data-seq="'.$row->code_00.'" id="'.$row->code_00.'"><i class="ti-trash" href></i> Delete</a>
                                                            
                                                        </div>
                                                    </div>';
								 
               $data[] = array(
			    
					$srno,
					$row->code_00,
					$row->departmentName_01,
                    $row->departmentDescription_02,
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
    public function save() {  
	
	$departmentName=strtoupper($this->input->post("departmentName"));
	$departmentA=trim($this->input->post("departmentName"));
		
		//Activity Track Starts
		
		$addID = $this->session->userdata['logged_in'.$session_key]['code'];
		$userRole = $this->session->userdata['logged_in'.$session_key]['role'];
		$userName = $this->session->userdata['logged_in'.$session_key]['username']; 
		$role = "";
		
		switch($userRole){
				case "ADM" : $role="Admin"; break;
				case "USR" : $role="User"; break;
			}
		
		$ip=$_SERVER['REMOTE_ADDR'];
		$text = $role." ".$userName.' added new department "'.$departmentA.'" from '.$ip; 
		
		$log_text = array(
						'code' => "demo",
						'addID'=>$addID,
						'logText' => $text
					);
		
		//Activity Track Ends
            
			$result = $this->GlobalModel->checkDuplicateRecord('departmentName',$departmentA,'departmentmaster');
            if ($result !=FALSE) 
            {
            $data = array(
			  'error_message' => 'Duplicate Department Name'
                           );
						   
		
			$data['query'] = $this->GlobalModel->selectDataExcludeDelete('departmentmaster');
            $this->load->view('dashboard/header');
            $this->load->view('dashboard/department/add',$data);
            $this->load->view('dashboard/footer');
            }
			else
            {
               $this->form_validation->set_rules('departmentName', 'Department Name', 'required');
   						   if ($this->form_validation->run()== FALSE) 
   						{	 	
   								$data['error_message'] ='* Fields are Required!';
								 $this->load->view('dashboard/header');
								  $this->load->view('dashboard/department/add',$data);
								  $this->load->view('dashboard/footer');
   								
						}
						else
						{
                          $data = array(
                    'departmentName' => $departmentA,
                    'departmentDescription' => ucfirst(strtolower(trim($this->input->post("departmentDescription")))),
                    'addID'=>$addID,
					'addIP'=>$ip,
					'isActive' => $this->input->post("isActive")
					
                );

                 
              $result = $this->GlobalModel->addWithoutYear($data, 'departmentmaster','DEPRT'); 
			  if($result!='false')
			{
				$response['status']=true;
				$response['message']="Department Successfully Added.";
				$this->GlobalModel->activityAdd($log_text,'activitymaster','ACT');
			}
			else
			{
				$response['status']=false;
				$response['message']="Failed To Add Department";
			}
			$this->session->set_flashdata('response', json_encode($response));
             redirect(base_url() . 'index.php/department/listRecords', 'refresh');
           }
	 }
}
    public function update()
    {
		$code =  $this->input->post('code');
		$departmentA=trim($this->input->post("departmentName"));
		$departmentDescrioption=ucfirst(strtolower(trim($this->input->post("departmentDescription"))));
		$isActive=$this->input->post("isActive");
		
		//Activity Track Starts
		
		$addID = $this->session->userdata['logged_in'.$session_key]['code'];
		$userRole = $this->session->userdata['logged_in'.$session_key]['role'];
		$userName = $this->session->userdata['logged_in'.$session_key]['username']; 
		$role = "";
		
		switch($userRole){
				case "ADM" : $role="Admin"; break;
				case "USR" : $role="User"; break;
		}
		
		$ip=$_SERVER['REMOTE_ADDR'];
		$text = $role." ".$userName.' updated department "'.$departmentA.'" from '.$ip; 
		
		$log_text = array(
			'code' => "demo",
			'addID'=>$addID,
			'logText' => $text
		); 
		
		//Activity Track Ends
		
		$Records=$this->GlobalModel->selectDataByField('code',$code,'departmentmaster');
		$dbDepartmentName=$Records->result()[0]->departmentName;
		
		$this->form_validation->set_rules('departmentName', 'Department Name', 'required');
		if ($this->form_validation->run()== FALSE) 
		{	 	
			$data['error_message'] ='* Fields are Required!';
										  
			$data['query'] = $this->GlobalModel->edit('departmentmaster',$code);
		   
			$this->load->view('dashboard/header');
			$this->load->view('dashboard/department/edit',$data);
			 $this->load->view('dashboard/footer');
				
		}
		else
		{
			
			if($dbDepartmentName!=$departmentA)
			{
				 $result = $this->GlobalModel->checkDuplicateRecord('departmentName',$departmentA,'departmentmaster');
				 
				if ($result !=FALSE) 
				{
					$data = array
					(
						'error_message' => 'Duplicate Department Name'
					);
			
					$data['query'] = $this->GlobalModel->edit('departmentmaster',$code);
					   
					$this->load->view('dashboard/header');
					$this->load->view('dashboard/department/edit',$data);
					$this->load->view('dashboard/footer');
				}
	
				else
				{
					$this->subUpdateFunction($departmentA,$code,$departmentDescrioption,$isActive,$log_text,$addID,$ip);
				}
			}
			else
			{
				$this->subUpdateFunction($departmentA,$code,$departmentDescrioption,$isActive,$log_text,$addID,$ip);
			}
		}
	}
   
   function subUpdateFunction($departmentA,$code,$departmentDescrioption,$isActive,$log_text,$addID,$ip)
   {
		$data = array(
			'departmentName' => $departmentA,
			'departmentDescription' => $departmentDescrioption,
			'editID'=>$addID,
			'editIP'=>$ip,
			'isActive' => $isActive
		);
		
		$result=$this->GlobalModel->doEdit($data,'departmentmaster',$code);
	   
		if($result!='false')
		{
			$response['status']=true;
			$response['message']="Department Successfully Updated.";
			$this->GlobalModel->activityAdd($log_text,'activitymaster','ACT');
		}
		else
		{
			$response['status']=false;
			$response['message']="No change In Department";
		}
		 // print_r($response);
		 $this->session->set_flashdata('response', json_encode($response));
		 redirect(base_url() . 'index.php/department/listRecords', 'refresh');
	}
   
   
   
public function edit()
{
    $code = $this->uri->segment(3); 
    
    
    $data['query'] = $this->GlobalModel->edit('departmentmaster',$code);
   
    $this->load->view('dashboard/header');
    $this->load->view('dashboard/department/edit',$data);
     $this->load->view('dashboard/footer');
}
   
         
  public function delete() 
	{
        $code=$this->input->post('code');
		
		//Activity Track Starts
		
		$addID = $this->session->userdata['logged_in'.$session_key]['code'];
		$userRole = $this->session->userdata['logged_in'.$session_key]['role'];
		$userName = $this->session->userdata['logged_in'.$session_key]['username']; 
		$role = "";
		
		switch($userRole){
				case "ADM" : $role="Admin"; break;
				case "USR" : $role="User"; break;
			}
		
		$ip=$_SERVER['REMOTE_ADDR']; 
		$dataQ = $this->GlobalModel->selectDataByField('code',$code,'departmentmaster');  
		$departmentName=''; 
		
		foreach ($dataQ->result() as $row) 
		{	
			$departmentName = $row->departmentName; 
		}
		
		$text = $role." ".$userName.' deleted department "'.$departmentName.'" from '.$ip; 
		
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
		  
		$resultData=$this->GlobalModel->doEdit($data,'departmentmaster',$code);
		
		//Activity Track Ends
       
	   echo $this->GlobalModel->delete($code,'departmentmaster');
       // redirect(base_url() . 'index.php/department/listRecords', 'refresh');
    }
  public function view()
	{
		$code = $this->input->get('code');
		
		//Activity Track Starts
		
		$addID = $this->session->userdata['logged_in'.$session_key]['code'];
		$userRole = $this->session->userdata['logged_in'.$session_key]['role'];
		$userName = $this->session->userdata['logged_in'.$session_key]['username']; 
		$role = "";
		
		switch($userRole){
				case "ADM" : $role="Admin"; break;
				case "USR" : $role="User"; break; 
			}
		
		$ip=$_SERVER['REMOTE_ADDR']; 
		
		//Activity Track Ends
       
	   // $data = $this->GlobalModel->selectDataById($code,'departmentmaster');
	$tables=array('departmentmaster');
		
		$requiredColumns=array
		(
			array('code','departmentName','departmentDescription','isActive'),
		
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
												  
			$modelHtml.='<div class="form-row"><div class="col-md-4 mb-3"><label><b>Department Code:</b> </label>
						<input type="text" value="'.$row->code_00.'" class="form-control-line"  readonly></div>
						<div class="col-md-8 mb-3"><label><b>Department Name:</b> </label>
						<input type="text" class="form-control-line" value="'.$row->departmentName_01.'"  readonly></div> </div>	
						<div class="col-md-12 mb-3"><label><b>Department Description:</b> </label>
						<input type="text" class="form-control-line" row="2" cols="50" readonly value="'.$row->departmentDescription_02.'"</div></div>
					<div class="form-group">'.$activeStatus .'</div>';
					
					//for activity
					
					$text = $role." ".$userName.' viewed department "'.$row->departmentName_01.'" from '.$ip; 
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