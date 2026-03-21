<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class SalaryGrade extends CI_Controller {
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
  $this->load->view('dashboard/salaryGrade/add');
  $this->load->view('dashboard/footer');
}
// public function view()
 // {   
    // $code = $this->uri->segment(3); 
    // $data['query'] = $this->GlobalModel->selectDataById($code,'salarygrademaster');
    // $this->load->view('dashboard/header');
    // $this->load->view('dashboard/salaryGrade/view',$data);
     // $this->load->view('dashboard/footer');
  // }

public function listRecords()
{
     $data['error']=$this->session->flashdata('response');     
   // $data['query'] = $this->GlobalModel->selectDataExcludeDelete('salarygrademaster'); 
   
    $this->load->view('dashboard/header');
    $this->load->view('dashboard/salaryGrade/list',$data);
    $this->load->view('dashboard/footer');
}
public function getSalaryGradeList()
	{
		$tables=array('salarygrademaster');
		
		$requiredColumns=array
		(
			array('code', 'salaryGradeName', 'salaryGradeSName', 'salaryGradeDescription', 'isActive')
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
							
				 if($row->isActive_04 == "1")
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
                                                            <a class="dropdown-item" href="'.base_url().'index.php/SalaryGrade/edit/'.$row->code_00.'"><i class="ti-pencil-alt"></i> Edit</a>
                                                            <a class="dropdown-item  mywarning"  data-seq="'.$row->code_00.'" id="'.$row->code_00.'"><i class="ti-trash" href></i> Delete</a>
                                                            
                                                        </div>
                                                    </div>';
								 
               $data[] = array(
					$srno,
					$row->code_00,
                    $row->salaryGradeName_01,
                    $row->salaryGradeSName_02,
					$row->salaryGradeDescription_03,
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

public function save() 
{

             $salaryGradeSName= strtoupper($this->input->post("salaryGradeSName"));
			 $salaryGradeNameA = trim($this->input->post("salaryGradeName"));
			 
             $result = $this->GlobalModel->checkDuplicateRecord('salaryGradeSName',$salaryGradeSName,'salarygrademaster');
            if ($result !=FALSE) 
            {
            $data = array(
                'error_message' => ' Duplicate salarygrade short name'
                                 );
            $this->load->view('dashboard/header');
            $this->load->view('dashboard/salarygrade/add', $data);
            $this->load->view('dashboard/footer');
            }

            else
			{
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
				$text = $role." ".$userName.' added new salary grade "'.$salaryGradeNameA.'" from '.$ip; 
				
				$log_text = array(
								'code' => "demo",
								'addID'=>$addID,
								'logText' => $text
							);
				
				//Activity Track Ends	
                   
		$this->form_validation->set_rules('salaryGradeName', 'Salary Grade Name', 'required');
   		$this->form_validation->set_rules('salaryGradeSName','Salary Grade Short Name', 'required');
   						   if ($this->form_validation->run()== FALSE) 
   						{	 
   								$data['error_message'] ='* Fields are Required!';
								 $this->load->view('dashboard/header');
								  $this->load->view('dashboard/salaryGrade/add',$data);
								  $this->load->view('dashboard/footer');
   								
						}							
                      else
					  {
						  
				$data = array(
								'salaryGradeName' => $salaryGradeNameA,
								'salaryGradeSName' => strtoupper(trim($this->input->post("salaryGradeSName"))),
								'salaryGradeDescription' => trim($this->input->post("salaryGradeDescription")),
								'addID'=>$addID,
								'addIP'=>$ip,
								'isActive' => $this->input->post("isActive")
							);
	  
					   
				 $result = $this->GlobalModel->addWithoutYear($data, 'salarygrademaster', 'SALAG');
				if($result!='false')
				{
					$response['status']=true;
					$response['message']="Salary Grade Successfully Added.";
					$this->GlobalModel->activityAdd($log_text,'activitymaster','ACT');
				}
				else
				{
					$response['status']=false;
					$response['message']="Failed To Add Job Type";
				}
				 // print_r($response);
				 $this->session->set_flashdata('response', json_encode($response));			 
				 redirect(base_url() . 'index.php/salaryGrade/listRecords', 'refresh');
            }
    }
}
public function edit()
{
    $code = $this->uri->segment(3); 
    
    
    $data['query'] = $this->GlobalModel->edit('salarygrademaster',$code);
   
    $this->load->view('dashboard/header');
    $this->load->view('dashboard/salaryGrade/edit',$data);
     $this->load->view('dashboard/footer');
}
   
     public function update()
    {
		$code =  $this->input->post('code');
        $salaryGradeName = trim($this->input->post("salaryGradeName")); 
		
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
		$text = $role." ".$userName.' updated salary grade "'.$salaryGradeName.'" from '.$ip; 
		
		$log_text = array(
						'code' => "demo",
						'addID'=>$addID,
						'logText' => $text
					); 
		
		//Activity Track Ends
		
		$this->form_validation->set_rules('salaryGradeName', 'Salary Grade Name', 'required');
   		$this->form_validation->set_rules('salaryGradeSName','Salary Grade Short Name', 'required');
   						   if ($this->form_validation->run()== FALSE) 
   						{	 
   								$data['error_message'] ='* Fields are Required!';
								$data['query'] = $this->GlobalModel->edit('salarygrademaster',$code);
   
								$this->load->view('dashboard/header');
								$this->load->view('dashboard/salaryGrade/edit',$data);
								 $this->load->view('dashboard/footer');
						}							
                      else
					  {
            
            $data = array(
                    'salaryGradeName' => $salaryGradeName,
                    'salaryGradeSName' => trim(strtoupper($this->input->post("salaryGradeSName"))),
                    'salaryGradeDescription' =>trim($this->input->post("salaryGradeDescription")),
                    'editID'=>$addID,
					'editIP'=>$ip,
					'isActive' => $this->input->post("isActive")
                );
           // print_r($data);
                //Array binded data, table name and intial of code 
				$result=$this->GlobalModel->doEdit($data,'salarygrademaster',$code);
                if($result!='false')
			{
				$response['status']=true;
				$response['message']="Salary Grade Successfully Updated.";
				$this->GlobalModel->activityAdd($log_text,'activitymaster','ACT');
				
			}
			else
			{
				$response['status']=false;
				$response['message']="No change In Salary Grade";
			}
			   //print_r($result);
			  //print_r($response);
			 $this->session->set_flashdata('response', json_encode($response));
             redirect(base_url() . 'index.php/salaryGrade/listRecords', 'refresh');

           }
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
		$dataQ = $this->GlobalModel->selectDataByField('code',$code,'salarygrademaster');  
		$salaryGradeName=''; 
		
		foreach ($dataQ->result() as $row) 
		{	
			$salaryGradeName = $row->salaryGradeName; 
		}
		
		$text = $role." ".$userName.' deleted salary grade "'.$salaryGradeName.'" from '.$ip; 
		  
		//Activity Track Ends 
		
        echo $this->GlobalModel->deleteForever($code,'salarygrademaster');
        // redirect(base_url() . 'index.php/salaryGrade/listRecords', 'refresh');
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
		
        $tables=array('salarygrademaster');
		
		$requiredColumns=array
		(
			array('code', 'salaryGradeName', 'salaryGradeSName', 'salaryGradeDescription', 'isActive')
			
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
		
		foreach($Records->result() as $row)
		{
			
			 
			$activeStatus="";
			if($row->isActive_04 == "1")
            {
                $activeStatus='<span class="label label-sm label-success">Active</span>';
            }
			else
			{
				$activeStatus='<span class="label label-sm label-warning">Inactive</span>';
			}
												  
			$modelHtml.='<div class="form-row"><div class="col-md-4 mb-3"><label> <b>Code:</b> </label>
						<input type="text" value="'.$row->code_00.'" class="form-control-line"  readonly></div>
					   <div class="col-md-4 mb-3"><label><b> SalaryGrade Name:</b> </label>
						<input type="text" class="form-control-line" value="'.$row->salaryGradeName_01.'"  readonly></div> 
						<div class="col-md-4 mb-3"><label><b> SalaryGrade SName: </b></label>
						<input type="text" class="form-control-line" value="'.$row->salaryGradeSName_02.'"  readonly></div> </div>
						<div class="col-md-12 mb-3"><label><b>SalaryGrade Description:</b> </label>
						<input type="text" class="form-control-line" row="2" cols="50" readonly value="'.$row->salaryGradeDescription_03.'"> </div>
					   <div class="form-group">'.$activeStatus .'</div>';
					   
					   //for activity
						
						$text = $role." ".$userName.' viewed salary grade "'.$row->salaryGradeName_01.'" from '.$ip; 
						
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