<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Kolkata');
class Settings extends CI_Controller {
    var $privilege;
    public function __construct() {
        parent::__construct();
		$this->load->helper('form','url','html');
      	$this->load->library('form_validation');
        $this->load->model('GlobalModel'); 
		$this->load->model('GlobalModel1');
		
		$this->session_key = $this->session->userdata('key' . SESS_KEY);
        if (!isset($this->session->userdata['logged_in' . $this->session_key]['code'])) {
            redirect('Admin/login', 'refresh');
        }
	}
	 
	public function updateMaintenanceOn() {
		$data['settingValue'] = $this->input->post('settingValue');
		$data['messageTitle'] = $this->input->post('messageTitle');
		$data['messageDescription'] = $this->input->post('messageDescription');
		$code = 'SET_1';
		$update = $this->GlobalModel->doEdit($data,"settings",$code);
		if($update!='false'){
			echo true;
		} else {
			echo false;
		}
	}
	public function updateMaintenanceOff() {
		$data['settingValue'] =  $this->input->get('settingValue');;
		$code = 'SET_1';
		$update = $this->GlobalModel->doEdit($data,"settings",$code);
		if($update!='false'){
			echo true;
		} else {
			echo false;
		}
	}
	public function getMaintenanceMode()
	{		
		$resultData = $this->GlobalModel->selectQuery('settings.*','settings',array('settings.code'=>'SET_1'));
		$maintenance_mode['settingValue'] = $resultData->result_array()[0]['settingValue'];
		$maintenance_mode['messageTitle'] = $resultData->result_array()[0]['messageTitle'];
		$maintenance_mode['messageDescription'] = $resultData->result_array()[0]['messageDescription'];
		echo json_encode($maintenance_mode);
	}
	
	public function listRecords()
    {
		$data['error']=$this->session->flashdata('response');
    	//$data['query'] = $this->GlobalModel->selectData('designationmaster'); 
		
    	$this->load->view('dashboard/header');
    	$this->load->view('dashboard/settings/list',$data);
    	$this->load->view('dashboard/footer');
    	
    }
	
	public function getSettingList(){
		$tableName = array('settings'); 
		$orderColumns = array("code,settingName,settingValue,messageTitle,messageDescription,isActive");		
		$condition = array('settings.isActive=1');
		$orderBy = array();
		$joinType = array(); 
		$join = array();                  
		$groupByColumn = array();
		$limit = $this->input->GET("length");
		$offset = $this->input->GET("start");   
        $like = array();
		$extraCondition="";
        $Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);		
		$srno = $_GET['start'] + 1;
		$data = array();
		if ($Records) {
			 foreach($Records->result() as $row) {
				 $actionHtml='<div class="btn-group">
                          <button type="button" class="btn btn-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                           <i class="ti-settings"></i>
                          </button>
                           <div class="dropdown-menu animated slideInUp" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 35px, 0px);">
                            <a class="dropdown-item  blue" data-toggle="modal" data-target="#responsive-modal" data-seq="'.$row->code.'"  href="'.$row->code.'"><i class="ti-eye"></i> Open</a>
                            <a class="dropdown-item" href="'.base_url().'Settings/edit/'.$row->code.'"><i class="ti-pencil-alt"></i> Edit</a>
                              </div>
                           </div>';
								 
				   $data[] = array(
						$srno,
						$row->code,
						$row->settingName,
						$row->settingValue,
						$row->messageTitle,
						$row->messageDescription,
						
						$actionHtml
				   );				   
				  
				$srno++;
			 }
			 $dataCount = sizeOf($this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, '', '', $groupByColumn, $extraCondition)->result());
		}else {
			$dataCount = 0;
		}
        $output = array(
			"draw" => intval($_GET["draw"]),
			"recordsTotal" => $dataCount,
			"recordsFiltered" => $dataCount,
			"data" => $data
		);
		echo json_encode($output);		
	}
	public function getSettingListold()
	{
		$tables=array('settings');
		
		$requiredColumns=array
		(
			array()
			//array('entityName','code')
		);
		
		$conditions=array('settings.isActive=1');
		$extraConditionColumnNames=array(
		);
		
		$extraConditions=array(
		);
		
        $Records = $this->GlobalModel1->make_datatables1($tables,$requiredColumns,$conditions,$extraConditionColumnNames,$extraConditions);
		//echo $this->db->last_query();
		//print_r($Records->result());
			$srno=$_GET['start']+1;
			$data=array();
		  foreach($Records->result() as $row) {
							
				 if($row->isActive_05 == "1")
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
                            <a class="dropdown-item" href="'.base_url().'Settings/edit/'.$row->code_00.'"><i class="ti-pencil-alt"></i> Edit</a>
                              </div>
                           </div>';
								 
               $data[] = array(
					$srno,
					$row->code_00,
                    $row->settingName_01,
                    $row->settingValue_02,
					$row->messageTitle_03,
					$row->messageDescription_04,
					
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
	
	
	 public function view()
	{
		$code = $this->input->get('code');
		
		//Activity Track Starts
		
		//Activity Track Starts
        $statusFlag = 0;
        $addID = $this->session->userdata['logged_in' . $this->session_key]['code'];
        $userRole = $this->session->userdata['logged_in' . $this->session_key]['role'];
        $userName = $this->session->userdata['logged_in' . $this->session_key]['username'];
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
	    $tableName = array('settings'); 
		$orderColumns = array("code,settingName,settingValue,messageTitle,messageDescription,isActive");		
		$condition = array('settings.code' => $code);
        $Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition);		
		//echo $this->db->last_query();
		$modelHtml='<form>';
		$activeStatus="";
		foreach($Records->result() as $row)
		{
			 
			if($row->isActive == "1")  
            {
                $activeStatus='<span class="label label-sm label-success">Active</span>';
            }
			else
			{
				$activeStatus='<span class="label label-sm label-warning">Inactive</span>';
			}
												  
			$modelHtml.='<div class="form-row"><div class="col-md-4 mb-3"><label><b>Setting Code:</b> </label>
						<input type="text" value="'.$row->code.'" class="form-control-line"  readonly></div>
						<div class="col-md-8 mb-3"><label><b>Setting Name:</b> </label>
						<input type="text" class="form-control-line" value="'.$row->settingName.'"  readonly></div> </div>	
						<div class="col-md-12 mb-3"><label><b>Setting Value:</b> </label>
						<input type="text" class="form-control-line" row="2" cols="50" readonly value="'.$row->settingValue.'"</div></div>
						<div class="col-md-12 mb-3"><label><b>Message Title:</b> </label>
						<input type="text" class="form-control-line" row="2" cols="50" readonly value="'.$row->messageTitle.'"</div></div>
						<div class="col-md-12 mb-3"><label><b>Message Description:</b> </label>
						<input type="text" class="form-control-line" row="2" cols="50" readonly value="'.$row->messageDescription.'"</div></div>
					     ';
					 
					//for activity
					
					$text = $role." ".$userName.' viewed settings "'.$row->settingName.'" from '.$ip; 
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
	
	
	  public function viewold()
	{
		$code = $this->input->get('code');
		
		//Activity Track Starts
		
		//Activity Track Starts
        $statusFlag = 0;
        $addID = $this->session->userdata['logged_in' . $this->session_key]['code'];
        $userRole = $this->session->userdata['logged_in' . $this->session_key]['role'];
        $userName = $this->session->userdata['logged_in' . $this->session_key]['username'];
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
       
	   // $data = $this->GlobalModel->selectDataById($code,'departmentmaster');
	$tables=array('settings');
		
		$requiredColumns=array
		(
			array('code', 'settingName', 'settingValue', 'messageTitle', 'messageDescription','isActive'),
		
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
			 
			 
			
			if($row->isActive_05 == "1")
            {
                $activeStatus='<span class="label label-sm label-success">Active</span>';
            }
			else
			{
				$activeStatus='<span class="label label-sm label-warning">Inactive</span>';
			}
												  
			$modelHtml.='<div class="form-row"><div class="col-md-4 mb-3"><label><b>Setting Code:</b> </label>
						<input type="text" value="'.$row->code_00.'" class="form-control-line"  readonly></div>
						<div class="col-md-8 mb-3"><label><b>Setting Name:</b> </label>
						<input type="text" class="form-control-line" value="'.$row->settingName_01.'"  readonly></div> </div>	
						<div class="col-md-12 mb-3"><label><b>Setting Value:</b> </label>
						<input type="text" class="form-control-line" row="2" cols="50" readonly value="'.$row->settingValue_02.'"</div></div>
						<div class="col-md-12 mb-3"><label><b>Message Title:</b> </label>
						<input type="text" class="form-control-line" row="2" cols="50" readonly value="'.$row->messageTitle_03.'"</div></div>
						<div class="col-md-12 mb-3"><label><b>Message Description:</b> </label>
						<input type="text" class="form-control-line" row="2" cols="50" readonly value="'.$row->messageDescription_04.'"</div></div>
					     ';
					 
					//for activity
					
					$text = $role." ".$userName.' viewed settings "'.$row->settingName_01.'" from '.$ip; 
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
    
	  public function edit($code = NULL) {
		$res = $this->GlobalModel->edit('settings', $code);
		
	    $data['query'] = $this->GlobalModel->edit('settings', $code);
		
		
        $this->load->view('dashboard/header');
        $this->load->view('dashboard/settings/edit', $data);
        $this->load->view('dashboard/footer');
    }
	
	public function update() {
        $code = $this->input->post("code");
        $settingName = $this->input->post("settingName"); 
        $settingValue = $this->input->post("settingValue");
        $messageTitle = $this->input->post("messageTitle");
        $messageDescription = $this->input->post("messageDescription");
		
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
        $text = $role . " " . $userA . ' updated Settings "' . $settingName . '" from ' . $ip;
        $log_text = array('code' => "demo", 'addID' => $addID, 'logText' => $text);
        //Activity Track Ends
        $this->form_validation->set_rules('settingValue', 'Setting Value', 'required');
        
		
        if ($this->form_validation->run() == FALSE) {
            $data['error_message'] = '* Fields are Required!';
           
		    $data['query'] = $this->GlobalModel->edit('settings', $code);
            $this->load->view('dashboard/header');
            $this->load->view('dashboard/usermaster/edit', $data);
            $this->load->view('dashboard/footer');
        } else {
            
            $data = array('settingValue' => trim($this->input->post("settingValue")), 'messageTitle'=>$this->input->post('messageTitle'),'messageDescription' => $this->input->post('messageDescription'),'editID' => $addID, 'editIP' => $ip);
            $result = $this->GlobalModel->doEdit($data, 'settings', $code);            
            
            if ($result != 'false') {
                $response['status'] = true;
                $response['message'] = "Setting Successfully Updated.";
                $this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
            } else {
                $response['status'] = false;
                $response['message'] = "No change In Setting";
            }
            $this->session->set_flashdata('response', json_encode($response));
        } //else conditions
        redirect(base_url('Settings/listRecords'), 'refresh');
    }
   
   
	
}