<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class DeliveryChargesSlots extends CI_Controller {
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
    	$this->load->view('dashboard/deliverySlots/list',$data);
    	$this->load->view('dashboard/footer');
    	
    }
    public function add()
    {
    	$this->load->view('dashboard/header');
    	$this->load->view('dashboard/deliverySlots/add');
    	$this->load->view('dashboard/footer');
    	
    }

    public function edit()
    {
        $code=$this->uri->segment(3);
        $data['query']=$this->GlobalModel->selectQuery('deliveryChargesSlots.*','deliveryChargesSlots',array('deliveryChargesSlots.code' => $code));
    	$this->load->view('dashboard/header');
    	$this->load->view('dashboard/deliverySlots/edit',$data);
    	$this->load->view('dashboard/footer');
    	
    }
	public function getSlotList()
	{
		$tableName="deliveryChargesSlots";
		$search = $this->input->GET("search")['value'];
		$orderColumns = array("deliveryChargesSlots.*");
		$condition=array('deliveryChargesSlots.code!='=>'DSLT_1');
		$orderBy = array('deliveryChargesSlots.id' => 'DESC');
		$joinType=array();
		$join = array();
		$groupByColumn=array();
		$limit=$this->input->GET("length");
		$offset=$this->input->GET("start");
		$extraCondition=" deliveryChargesSlots.isDelete=0 OR deliveryChargesSlots.isDelete IS NULL";
		$like = array("deliveryChargesSlots.slotTitle" => $search . "~both","deliveryChargesSlots.startTime" => $search . "~both","deliveryChargesSlots.endTime" => $search . "~both","deliveryChargesSlots.deliveryCharge" => $search . "~both");
		$Records = $this->GlobalModel->selectQuery($orderColumns,$tableName,$condition,$orderBy,$join,$joinType,$like,$limit,$offset,$groupByColumn,$extraCondition);
		$srno=$_GET['start']+1;
		$dataCount=0;
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
						 <a class="dropdown-item" href="'.base_url().'DeliveryChargesSlots/edit/'.$row->code.'"><i class="ti-pencil-alt"></i> Edit</a>
						 <a class="dropdown-item mywarning " data-seq="'.$row->code.'" id="'.$row->code.'"><i class="ti-trash" ></i> Delete</a>
					 </div>
				 </div>';
									 
				   $data[] = array(
						$srno,
						$row->code,
						$row->slotTitle,
						date('h:i A',strtotime($row->startTime)),
						date('h:i A',strtotime($row->endTime)),
						$row->deliveryCharge,
						$status,
						$actionHtml
				   ); 
				$srno++;
			  }
		}
		    $dataCount1=$this->GlobalModel->selectQuery($orderColumns,$tableName,$condition,$orderBy,$join,$joinType,array(),'','','',$extraCondition);
			if ($dataCount1) {
				$dataCount = sizeOf($dataCount1->result());
			} else {
				$dataCount = 0;
			}
			//$dataCount=sizeof($this->GlobalModel->selectQuery($orderColumns,$tableName,$condition,$orderBy,$join,$joinType,array(),'','','',$extraCondition)->result());
				   $output = array( 
					"draw"                    =>     intval($_GET["draw"]),  
					"recordsTotal"          =>      $dataCount,  
					"recordsFiltered"     =>     $dataCount,  
					"data"                    =>     $data  
					);
          echo json_encode($output);
	}
    
	public function save(){ 
        $slotTitle= $this->input->post("slotTitle");
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
		$text = $role." ".$userName.' added new Delivery Slots "'.$slotTitle.'" from '.$ip; 
		$log_text = array(
			'code' => "demo",
			'addID'=>$addID,
			'logText' => $text
		);
		//Activity Track Ends
		$result = $this->GlobalModel->checkDuplicateRecord('LOWER(slotTitle)',strtolower($slotTitle),'deliveryChargesSlots');
		if ($result!=FALSE) {
            $data = array('error_message' => 'Duplicate Slot Name');
            $this->load->view('dashboard/header');
            $this->load->view('dashboard/deliverySlots/add', $data);
            $this->load->view('dashboard/footer');
        }else{
			$this->form_validation->set_rules('slotTitle', 'Slot Title', 'required');
			$this->form_validation->set_rules('startTime', 'Start Time', 'required');
			$this->form_validation->set_rules('endTime', 'End Time', 'required');
			$this->form_validation->set_rules('deliveryCharge','Delivery Charge', 'required');
			if ($this->form_validation->run()== FALSE) {	 
				$data['error_message'] ='* Fields are Required!';
				$this->load->view('dashboard/header');
				$this->load->view('dashboard/deliverySlots/add',$data);
				$this->load->view('dashboard/footer');
			}else{
				$data =array(
					'slotTitle'=>trim($this->input->post('slotTitle')),
                    'startTime'=>strtoupper($this->input->post('startTime')),
                    'endTime'=>trim($this->input->post('endTime')),
                    'deliveryCharge'=>trim($this->input->post('deliveryCharge')),
                    'isActive' => trim($this->input->post("isActive")),
					'addID'=>$addID,
					'addIP'=>$ip,
                );
				$result = $this->GlobalModel->addWithoutYear($data,'deliveryChargesSlots','DSLT');
			    if($result!='false'){
					$response['status']=true;
					$response['message']="Slot Added Successfully";
					$this->GlobalModel->activityAdd($log_text,'activitymaster','ACT');
				}else{
					$response['status']=false;
					$response['message']="Failed To Add Slot";
				}
				$this->session->set_flashdata('response', json_encode($response));
				redirect(base_url().'DeliveryChargesSlots/listRecords');
            }
		}
    }
    public function update()
    {
        $code =  $this->input->post('code');
		$slotTitle= $this->input->post("slotTitle");
		$addID = $this->session->userdata['logged_in'.$this->session_key]['code'];
		$userRole = $this->session->userdata['logged_in'.$this->session_key]['role'];
		$userName = $this->session->userdata['logged_in'.$this->session_key]['username']; 
		$role = "";
		switch($userRole){
				case "ADM" : $role="Admin"; break;
				case "USR" : $role="User"; break; 
			}
		$ip=$_SERVER['REMOTE_ADDR'];
		$text = $role." ".$userName.' updated Delivery Slots "'.$slotTitle.'" from '.$ip; 
		$log_text = array(
			'code' => "demo",
			'addID'=>$addID,
			'logText' => $text
		);
		//Activity Track Ends 
		$this->form_validation->set_rules('slotTitle', 'Slot Title', 'required');
		$this->form_validation->set_rules('startTime', 'Start Time', 'required');
		$this->form_validation->set_rules('endTime', 'End Time', 'required');
		$this->form_validation->set_rules('deliveryCharge','Delivery Charge', 'required');
		if ($this->form_validation->run()== FALSE){	 
			$data['error_message'] ='* Fields are Required!';
			$data['query'] = $this->GlobalModel->selectDataById($code,'deliveryChargesSlots');
			$this->load->view('dashboard/header');
			$this->load->view('dashboard/deliverySlots/edit',$data);
			$this->load->view('dashboard/footer');
		}else{
			$data =array(
				'slotTitle'=>trim($this->input->post('slotTitle')),
                'startTime'=>strtoupper($this->input->post('startTime')),
                'endTime'=>trim($this->input->post('endTime')),
                'deliveryCharge'=>trim($this->input->post('deliveryCharge')),
                'isActive' => trim($this->input->post("isActive")),
				'addID'=>$addID,
				'addIP'=>$ip,
            );
			$result = $this->GlobalModel->doEdit($data,'deliveryChargesSlots',$code);
			if($result!='false'){
				$response['status']=true;
				$response['message']="Slot Updated  Successfully ";
				$this->GlobalModel->activityAdd($log_text,'activitymaster','ACT');
			}else{
				$response['status']=false;
				$response['message']="No change In Slot";
			}
			$this->session->set_flashdata('response', json_encode($response));
			redirect(base_url().'DeliveryChargesSlots/listRecords');
	   }
    }
    public function delete(){
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
		$dataQ = $this->GlobalModel->selectDataByField('code',$code,'deliveryChargesSlots');  
		$slotTitle='';
		foreach ($dataQ->result() as $row) {	
				$slotTitle = $row->slotTitle; 
		}
		$text = $role." ".$userName.' deleted Delivery Slots "'.$slotTitle.'" from '.$ip; 
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
		$resultData=$this->GlobalModel->doEdit($data,'deliveryChargesSlots',$code);
		//Activity Track Ends
	    echo $this->GlobalModel->delete($code,'deliveryChargesSlots');
    } 
	public function view(){
		$code = $this->input->get('code');
		$addID = $this->session->userdata['logged_in'.$this->session_key]['code'];
	    $userRole = $this->session->userdata['logged_in'.$this->session_key]['role'];
	    $userName = $this->session->userdata['logged_in'.$this->session_key]['username']; 
	    $role = "";
		switch($userRole){
		    case "ADM" : $role="Admin"; break;
			case "USR" : $role="User"; break;
		}
		$ip=$_SERVER['REMOTE_ADDR'];
		$table_name = 'deliveryChargesSlots';
        $orderColumns = array("deliveryChargesSlots.*");
        $cond=array('deliveryChargesSlots.code'=> $code);
        $like= array();
        $orderBy = array();
        $joinType=array();
        $join = array();
        $Records=$this->GlobalModel->selectQuery($orderColumns,$table_name,$cond,$orderBy,$join,$joinType,$like);
		$modelHtml='<form>';
		$activeStatus="";
		foreach($Records->result() as $row){
			if($row->isActive == "1"){
                $activeStatus='<span class="label label-sm label-success">Active</span>';
            }else{
				$activeStatus='<span class="label label-sm label-warning">Inactive</span>';
			}								  
			$modelHtml.='<div class="form-row">
					<div class="col-md-3 mb-3"><label><b>Code:</b> </label>
						<input type="text" value="'.$row->code.'" class="form-control-line"  readonly>
					</div>
					<div class="col-md-5 mb-3"><label><b> Slot Title:</b> </label>
						<input type="text" class="form-control-line" value="'.$row->slotTitle.'"  readonly>
					</div> 
					<div class="col-md-4 mb-3"><label><b>Start Time:</b> </label>
						<input type="text" class="form-control-line" value="'.$row->startTime.'"  readonly>
					</div>
				</div>  
				<div class="form-row">
					<div class="col-md-12 mb-3"><label><b>End Time:</b> </label>
						<input type="text" class="form-control-line" readonly value="'.$row->endTime.'"> 
					</div>
					<div class="col-md-12 mb-3"><label><b>Delivery Charge:</b> </label>
						<input type="text" class="form-control-line" readonly value="'.$row->deliveryCharge.'"> 
					</div>
					<div class="col-md-12 mb-3">'.$activeStatus .'</div>
				</div>';
			     //for activity
				$text = $role." ".$userName.' viewed delivery slots "'.$row->slotTitle.'" from '.$ip; 
				$log_text = array(
					'code' => "demo",
					'addID'=>$addID,
					'logText' => $text
				);
				$this->GlobalModel->activityAdd($log_text,'activitymaster','ACT');
			}
			$modelHtml.='</form>';
			echo $modelHtml;
	}
	
	public function checkOverlappingSlots(){
		$inputTime = date('h:i:s',strtotime($this->input->post("inputTime")));
		$checkSlot = $this->db->query("select id from deliveryChargesSlots where isActive=1 and '".$inputTime."' between startTime and endTime");
		//echo $this->db->last_query();
		if($checkSlot!=false){
			if($checkSlot->num_rows()>0){
				echo 1;
			}
		}
	}
}
?>