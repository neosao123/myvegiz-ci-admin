<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Subunit extends CI_Controller {
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
    	$data['query'] = $this->GlobalModel->selectDataExcludeDelete('subunit');
		// print_r($data['query']->result());
		// exit();
    	$this->load->view('dashboard/header');
    	$this->load->view('dashboard/subunit/list',$data);
    	$this->load->view('dashboard/footer');
    	
    }
    public function add()
    {
		$table_name = 'uommaster';
		$orderColumns = array("uommaster.*");
		$cond = array('uommaster' . '.isDelete' => 0, 'uommaster' . '.isActive' => 1);
		$data['uom'] = $this->GlobalModel->selectQuery($orderColumns, $table_name, $cond);
		
    	$this->load->view('dashboard/header');
    	$this->load->view('dashboard/subunit/add',$data);
    	$this->load->view('dashboard/footer');
    	
    }

    public function edit()
    {
        $code=$this->uri->segment(3);		
		$table_name = 'uommaster';
		$orderColumns = array("uommaster.*");
		$cond = array('uommaster' . '.isDelete' => 0, 'uommaster' . '.isActive' => 1);
		$data['uom'] = $this->GlobalModel->selectQuery($orderColumns, $table_name, $cond);
		$data['query'] = $this->GlobalModel->selectDataById($code, 'subunit');
    	$this->load->view('dashboard/header');
    	$this->load->view('dashboard/subunit/edit',$data);
    	$this->load->view('dashboard/footer');
    	
    }

    // public function view()
    // {
        // $code=$this->uri->segment(3);
        // $data['query'] = $this->GlobalModel->selectDataById($code,'uommaster');
    	// $this->load->view('dashboard/header');
    	// $this->load->view('dashboard/Uom/view',$data);
    	// $this->load->view('dashboard/footer');
    	
    // }
	public function getSubunitList()
	{
		$tableName = 'subunit';
		$orderColumns = array("subunit.*,uommaster.uomName");
		$search = ($this->input->post("search") ?? $this->input->get("search"))['value'];
		$condition = array();
		$orderBy = array('subunit.id'=>'desc');
		$joinType = array('uommaster' => 'inner');
		$join = array('uommaster' => 'uommaster.code=subunit.uomCode');
		$groupByColumn = array();
		$limit = $this->input->post("length") ?? $this->input->get("length");
		$offset = $this->input->post("start") ?? $this->input->get("start");
		$srno = $offset + 1;
		$like = array();
		$extraCondition=" (subunit.isDelete=0 or subunit.isDelete is null)";
		$like = array("uommaster.uomName" => $search . "~both","subunit.subunitName" => $search . "~both","subunit.subunitSName" => $search . "~both");
		$data=array();
		$dataCount = 0;
		$Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
		$srno=$_GET['start']+1;
		$data=array();
		if($Records){
			foreach($Records->result() as $row) {
				if($row->isActive == "1"){
				    $status = " <span class='label label-sm label-success'>Active</span>";
				}else{
				   $status = " <span class='label label-sm label-warning'>Inactive</span>";
				}
				 $actionHtml='<div class="btn-group">
					<button type="button" class="btn btn-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<i class="ti-settings"></i>
					</button>
					<div class="dropdown-menu animated slideInUp" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 35px, 0px);">
						<a class="dropdown-item  blue" data-toggle="modal" data-target="#responsive-modal" data-seq="'.$row->code.'" href><i class="ti-eye"></i> Open</a>
						<a class="dropdown-item" href="'.base_url().'Subunit/edit/'.$row->code.'"><i class="ti-pencil-alt"></i> Edit</a>
						<a class="dropdown-item mywarning " data-seq="'.$row->code.'" id="'.$row->code.'"><i class="ti-trash" ></i> Delete</a>
					</div>
				</div>';
								 
               $data[] = array(
					$srno,
					$row->code,
					$row->uomName,
                    $row->subunitName,
                    $row->subunitSName,
					$status,
					$actionHtml
               );
			  $srno++;
            }
            $dataCount = sizeof($this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, "", "", $groupByColumn, $extraCondition)->result());
		}
        $output = array("draw" => intval($this->input->post("draw") ?? $_GET["draw"] ?? 0), "recordsTotal" => $dataCount, "recordsFiltered" => $dataCount, "data" => $data);
        echo json_encode($output);
	}
     public function save()
    { 
	   $subunitSName= strtoupper($this->input->post("subunitSName"));
	   
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
		$text = $role." ".$userName.' added new subunit "'.$subunitSName.'" from '.$ip; 
		
		$log_text = array(
						'code' => "demo",
						'addID'=>$addID,
						'logText' => $text
					);
		
		//Activity Track Ends
		
		$result = $this->GlobalModel->checkDuplicateRecord('subunitSName',$subunitSName,'subunit');
	   
		  if ($result!=FALSE) 
		  {
		  $data = array(
			  'error_message' => 'Duplicate Subunit Short Name'
							   );
		  $this->load->view('dashboard/header');
		  $this->load->view('dashboard/subunit/add', $data);
		  $this->load->view('dashboard/footer');
		  }
		  else
		  {
				 $this->form_validation->set_rules('uomCode', 'UOM Name', 'required');
				 $this->form_validation->set_rules('subunitName', 'Subunit Name', 'required');
				 $this->form_validation->set_rules('subunitSName', 'Subunit Short Name', 'required');
				
				   if ($this->form_validation->run()== FALSE) 
				{	 
				
						$data['error_message'] ='* Fields are Required!';
						$table_name = 'uommaster';
						$orderColumns = array("uommaster.*");
						$cond = array('uommaster' . '.isDelete' => 0, 'uommaster' . '.isActive' => 1);
						$data['uom'] = $this->GlobalModel->selectQuery($orderColumns, $table_name, $cond);
						$this->load->view('dashboard/header');
						$this->load->view('dashboard/subunit/add',$data);
						$this->load->view('dashboard/footer');
				}
					
				 else
				 {
				$data =array(
				'uomCode'=>trim($this->input->post('uomCode')),
				'subunitName'=>trim($this->input->post('subunitName')),
				'subunitSName'=>strtoupper($this->input->post('subunitSName')),
				'subunitDescription'=>trim($this->input->post('subunitDescription')),
				'addID'=>$addID,
				'addIP'=>$ip,
				'isActive' => trim($this->input->post("isActive"))
	  );

	   $result = $this->GlobalModel->addWithoutYear($data,'subunit','SUOM');
		if($result!='false')
	{
		$response['status']=true;
		$response['message']="Subunit Successfully Added.";
		$this->GlobalModel->activityAdd($log_text,'activitymaster','ACT');
		
	}
	else
	{
		$response['status']=false;
		$response['message']="Failed To Add Subunit";
	}
	 // print_r($response);
	 $this->session->set_flashdata('response', json_encode($response));
	  redirect(base_url().'Subunit/listRecords');
	}
}
    }
    public function update()
    {
        $code =  $this->input->post('code');
		$subunitName = trim($this->input->post('subunitName'));
		
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
		$text = $role." ".$userName.' updated Subunit "'.$subunitName.'" from '.$ip; 
		$log_text = array(
						'code' => "demo",
						'addID'=>$addID,
						'logText' => $text
					);
					
		//Activity Track Ends 
		                  $this->form_validation->set_rules('uomCode', 'UOM Name', 'required');
						 $this->form_validation->set_rules('subunitName', 'Subunit Name', 'required');
						 $this->form_validation->set_rules('subunitSName', 'Subunit Short Name', 'required');
						
						   if ($this->form_validation->run()== FALSE) 
						{	 
						
								$data['error_message'] ='* Fields are Required!';
								$table_name = 'uommaster';
								$orderColumns = array("uommaster.*");
								$cond = array('uommaster' . '.isDelete' => 0, 'uommaster' . '.isActive' => 1);
								$data['uom'] = $this->GlobalModel->selectQuery($orderColumns, $table_name, $cond);
								$data['query'] = $this->GlobalModel->selectDataById($code,'subunit');
								  $this->load->view('dashboard/header');
								  $this->load->view('dashboard/subunit/edit',$data);
								  $this->load->view('dashboard/footer');
						}
							
						 else
						 {
		 
							$data =array(
							    'uomCode'=>$this->input->post('uomCode'),
								'subunitName'=>trim($this->input->post('subunitName')),
								'subunitSName'=>trim($this->input->post('subunitSName')),
								'subunitDescription'=>trim($this->input->post('subunitDescription')),
								'editID'=>$addID,
								'editIP'=>$ip,
								'isActive' => trim($this->input->post("isActive"))
							);

           $result = $this->GlobalModel->doEdit($data,'subunit',$code);
		   if($result!='false')
			{
				$response['status']=true;
				$response['message']="Subunit Successfully Updated.";
				$this->GlobalModel->activityAdd($log_text,'activitymaster','ACT');
			}
			else
			{
				$response['status']=false;
				$response['message']="No change In Subunit";
			}
			  //print_r($result);
			  //print_r($response);
			 $this->session->set_flashdata('response', json_encode($response));
        redirect(base_url().'subunit/listRecords');
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
			$dataQ = $this->GlobalModel->selectDataByField('code',$code,'subunit');  
			$uomName='';
			
			foreach ($dataQ->result() as $row) 
			{	
				$uomName = $row->uomName; 
			}
			
			$text = $role." ".$userName.' deleted Subunit "'.$uomName.'" from '.$ip; 
			
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
		  
			$resultData=$this->GlobalModel->doEdit($data,'subunit',$code);
			
			//Activity Track Ends
			
            echo $this->GlobalModel->delete($code,'subunit');
            
           // redirect(base_url() . 'index.php/uom/listrecords', 'refresh');
         } 
	public function view()
	{
		 $code = $this->input->post('code') ?? $this->input->get('code');
		
			$addID = $this->session->userdata['logged_in'.$this->session_key]['code'];
			$userRole = $this->session->userdata['logged_in'.$this->session_key]['role'];
			$userName = $this->session->userdata['logged_in'.$this->session_key]['username']; 
			$role = "";
			
			switch($userRole){
					case "ADM" : $role="Admin"; break;
					case "USR" : $role="User"; break;
				}
		
		$ip=$_SERVER['REMOTE_ADDR'];
		$table_name = 'subunit';
        $orderColumns = array("subunit.*,uommaster.uomName");
        $cond=array('subunit' . ".code" => $code);
        $like= array();
        $orderBy = array();
        $joinType = array('uommaster' => 'inner');
		$join = array('uommaster' => 'uommaster.code=subunit.uomCode');

        $Records=$this->GlobalModel->selectQuery($orderColumns,$table_name,$cond,$orderBy,$join,$joinType,$like);
       
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
												  
			$modelHtml.='<div class="form-row"><div class="col-md-3 mb-3"><label><b>Code:</b> </label>
						<input type="text" value="'.$row->code.'" class="form-control-line"  readonly></div>
					 <div class="col-md-5 mb-3"><label><b> Unit Name:</b> </label>
						<input type="text" class="form-control-line" value="'.$row->uomName.'"  readonly></div> 
					<div class="col-md-5 mb-3"><label><b> Subunit Name:</b> </label>
						<input type="text" class="form-control-line" value="'.$row->subunitName.'"  readonly></div> 
					<div class="col-md-4 mb-3"><label><b>Subunit Short Name:</b> </label>
						<input type="text" class="form-control-line" value="'.$row->subunitSName.'"  readonly></div></div>     
					<div class="col-md-12 mb-3"><label><b>Subunit Description:</b> </label>
						<input type="text" class="form-control-line" row="2" cols="50" readonly value="'.$row->subunitDescription.'"> </div>
					<div class="form-group">'.$activeStatus .'</div>';
					
					//for activity
					
					$text = $role." ".$userName.' viewed Subunit "'.$row->subunitName.'" from '.$ip; 
					
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
    
  public function test(){
	  
		 
	  $data['query'] = $this->GlobalModel->selectDataExcludeDelete('uommaster');
	  print_r($data['query']->result());
	 
  }

}
?>