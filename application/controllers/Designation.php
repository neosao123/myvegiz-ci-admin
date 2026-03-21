<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Designation extends CI_Controller {
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
    	//$data['query'] = $this->GlobalModel->selectData('designationmaster'); 
		
    	$this->load->view('dashboard/header');
    	$this->load->view('dashboard/designation/list',$data);
    	$this->load->view('dashboard/footer');
    	
    }
    public function add()
    {
       
    	$this->load->view('dashboard/header');
    	$this->load->view('dashboard/designation/add');
    	$this->load->view('dashboard/footer');
    	
    }

    public function edit()
    {
        $code=$this->uri->segment(3);
        $data['query'] = $this->GlobalModel->selectDataById($code,'designationmaster');
    	$this->load->view('dashboard/header');
    	$this->load->view('dashboard/designation/edit',$data);
    	$this->load->view('dashboard/footer');
    	
    }

    // public function view()
    // {
        // $code=$this->uri->segment(3);
        // $data['query'] = $this->GlobalModel->selectDataById($code,'designationmaster');
    	// $this->load->view('dashboard/header');
    	// $this->load->view('dashboard/designation/view',$data);
    	// $this->load->view('dashboard/footer');
    	
    // }
	public function getdesignationname()
	{
		$designationName = $this->input->get('designationName');
		$dataDesignationName = $this->GlobalModel->similarResultFind('designationmaster','designationName',$designationName);
		
	    $designation = '';
		foreach( $dataDesignationName->result() as $designation)
		{   

			$designation.='<option value= "' . $designation->designationName . '">';
		}
		echo $designation;
	} 
	public function getDesignationList()
	{
		$tables=array('designationmaster');
		
		$requiredColumns=array
		(
			array('code', 'designationName', 'designationSName', 'designationDescription', 'isActive')
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
                                                            <a class="dropdown-item" href="'.base_url().'index.php/Designation/edit/'.$row->code_00.'"><i class="ti-pencil-alt"></i> Edit</a>
                                                            <a class="dropdown-item  mywarning" data-seq="'.$row->code_00.'" id="'.$row->code_00.'"><i class="ti-trash" href></i> Delete</a>
                                                            
                                                        </div>
                                                    </div>';
								 
               $data[] = array(
					$srno,
					$row->code_00,
                    $row->designationName_01,
                    $row->designationSName_02,
					$row->designationDescription_03,
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
                $designationSName= strtoupper($this->input->post("designationSName"));
				$designationNameA=trim($this->input->post('designationName'));
				
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
				$text = $role." ".$userName.' added new designation "'.$designationNameA.'" from '.$ip; 
				
				$log_text = array(
								'code' => "demo",
								'addID'=>$addID,
								'logText' => $text
							);
				
				//Activity Track Ends
             
			 $result = $this->GlobalModel->checkDuplicateRecord('designationSName',$designationSName,'designationmaster');
            if ($result !=FALSE) 
            {
            $data = array(
                'error_message' => ' Duplicate designation short name'
                                 );
            $this->load->view('dashboard/header');
            $this->load->view('dashboard/designation/add', $data);
            $this->load->view('dashboard/footer');
            }

            else
            {
				$this->form_validation->set_rules('designationName', 'Designation Name', 'required');
		        $this->form_validation->set_rules('designationSName', 'Designation Short Name', 'required');
   						   if ($this->form_validation->run()== FALSE) 
   						{	 
   								$data['error_message'] ='* Fields are Required!';
								
								$this->load->view('dashboard/header');
								$this->load->view('dashboard/designation/add',$data);
								$this->load->view('dashboard/footer');
   								
						}
						else
						{
							
              $data =array(
                  'designationName'=>$designationNameA,
                  'designationSName'=>strtoupper($this->input->post('designationSName')),
                  'designationDescription'=>trim($this->input->post('designationDescription')),
                  'addID'=>$addID,
				  'addIP'=>$ip,
				  'isActive' => trim($this->input->post("isActive"))
              );

             $result = $this->GlobalModel->addWithoutYear($data,'designationmaster','DES');
			 if($result!='false')
			{
				$response['status']=true;
				$response['message']="Designation Successfully Added.";
				$this->GlobalModel->activityAdd($log_text,'activitymaster','ACT');
			}
			else
			{
				$response['status']=false;
				$response['message']="Failed To Add Designation";
			}
			print_r($response);
			$this->session->set_flashdata('response', json_encode($response));
        redirect(base_url().'index.php/designation/listRecords');
          }

     }
}            
			  public function update()
         {
            $code =  $this->input->post('code'); 
            $designationNameA=trim($this->input->post('designationName'));
			
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
			$text = $role." ".$userName.' updated designation "'.$designationNameA.'" from '.$ip; 
			
			$log_text = array(
							'code' => "demo",
							'addID'=>$addID,
							'logText' => $text
						); 
			
			//Activity Track Ends
			
			$this->form_validation->set_rules('designationName', 'Designation Name', 'required');
		        $this->form_validation->set_rules('designationSName', 'Designation Short Name', 'required');
   						   if ($this->form_validation->run()== FALSE) 
   						{	 
   								$data['error_message'] ='* Fields are Required!';
								 $data['query'] = $this->GlobalModel->selectDataById($code,'designationmaster');
									$this->load->view('dashboard/header');
									$this->load->view('dashboard/designation/edit',$data);
									$this->load->view('dashboard/footer');
   								
						}
						else
						{
             $data =array(
                  'designationName'=> $designationNameA,
                  'designationSName'=>strtoupper($this->input->post("designationSName")),
                  'designationDescription'=>trim($this->input->post('designationDescription')),
                  'editID'=>$addID,
				  'editIP'=>$ip,
				  'isActive' => trim($this->input->post("isActive"))
        );
          
            
         $result = $this->GlobalModel->doEdit($data,'designationmaster',$code);
		    if($result!='false')
			{
				$response['status']=true;
				$response['message']="Designation Successfully Updated.";
				$this->GlobalModel->activityAdd($log_text,'activitymaster','ACT');
			}
			else
			{
				$response['status']=false;
				$response['message']="No change In Designation";
			}
			$this->session->set_flashdata('response', json_encode($response));

          redirect(base_url().'index.php/designation/listRecords');
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
		$dataQ = $this->GlobalModel->selectDataByField('code',$code,'designationmaster');  
		$designationName=''; 
		
		foreach ($dataQ->result() as $row) 
		{	
			$designationName = $row->designationName; 
		}
		
		$text = $role." ".$userName.' deleted designation "'.$designationName.'" from '.$ip; 
		
		$log_text = array(
							'code' => "demo",
							'addID'=>$addID,
							'logText' => $text
						);
		
		$this->GlobalModel->activityAdd($log_text,'activitymaster','ACT');
		 
		//Activity Track Ends 
		
        echo $result = $this->GlobalModel->deleteForever($code,'designationmaster');
        //redirect(base_url() . 'index.php/designation/listRecords', 'refresh');
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
		
        $tables=array('designationmaster');
		
		$requiredColumns=array
		(
			array('code', 'designationName', 'designationSName', 'designationDescription', 'isActive')
			
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
			
			
			if($row->isActive_04 == "1")
            {
                $activeStatus='<span class="label label-sm label-success">Active</span>';
            }
			else
			{
				$activeStatus='<span class="label label-sm label-warning">Inactive</span>';
			}
												  
			$modelHtml.='<div class="form-row"><div class="col-md-4 mb-3"><label><b> Code:</b> </label>
						<input type="text" value="'.$row->code_00.'" class="form-control-line"  readonly></div>
					<div class="col-md-5 mb-3"><label> <b>Designation Name:</b> </label>
						<input type="text" class="form-control-line" value="'.$row->designationName_01.'"  readonly></div> 
					<div class="col-md-3 mb-3"><label><b> Short Name: </b></label>
						<input type="text" class="form-control-line" value="'.$row->designationSName_02.'"  readonly></div></div>     
					<div class="col-md-12 mb-3"><label><b>designation Description:</b> </label>
						<input type="text" class="form-control-line" row="2" cols="50" readonly value="'.$row->designationDescription_03.'"> </div>
					<div class="form-group">'.$activeStatus .'</div>';
					
					//for activity
					
					$text = $role." ".$userName.' viewed designation "'.$row->designationName_01.'" from '.$ip; 
					
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