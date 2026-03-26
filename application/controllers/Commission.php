<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Commission extends CI_Controller
{
	var $privilege;
	var $session_key;

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('form', 'url', 'html');
		$this->load->library('form_validation');
		$this->load->model('GlobalModel');
		$this->load->model('Testing');
		$this->session_key = $this->session->userdata('key' . SESS_KEY);
		if (!isset($this->session->userdata['logged_in' . $this->session_key]['code'])) {
			redirect('Admin/login', 'refresh');
		}
	}

	public function listRecords()
	{
		$data['error'] = $this->session->flashdata('response');
		$data['query'] = $this->GlobalModel->selectData('usermaster');
		$data['employee'] = $this->GlobalModel->selectActiveData('usermaster');
		$this->load->view('dashboard/header');
		$this->load->view('dashboard/commission/list', $data);
		$this->load->view('dashboard/footer');
	}
	
	public function getDeliveryBoyCommissionsList(){
		
		$userCode = $this->input->post('deliveryboyCode') ?? $this->input->get('deliveryboyCode');
        $fromDate = $this->input->post('fromDate') ?? $this->input->get('fromDate');
		$toDate = $this->input->post('toDate') ?? $this->input->get('toDate');		
		$tableName = array('deliveryboyearncommission'); 
		$orderColumns = array("ifNull(sum(deliveryboyearncommission.commissionAmount),0) as commission,deliveryboyearncommission.orderCode, usermaster.name,deliveryboyearncommission.deliveryBoyCode");		
		$condition = array('usermaster.code' => $userCode,'deliveryboyearncommission.commissionType!='=>'penalty');
		$orderBy = array('usermaster.id' => 'DESC');
		$joinType = array('usermaster' => 'inner'); 
		$join = array('usermaster' => 'deliveryboyearncommission.deliveryBoyCode=usermaster.code');
		//$groupByColumn = array();                     
		$groupByColumn = array("deliveryboyearncommission.deliveryBoyCode");
		$limit = $this->input->post("length") ?? $this->input->get("length");
		$offset = $this->input->post("start") ?? $this->input->get("start");               
		
		$dateCondition = "";
		if ($fromDate != "") {
			$fromDate = DateTime::createFromFormat('d/m/Y', $fromDate)->format('Y-m-d');
			$toDate = DateTime::createFromFormat('d/m/Y', $toDate)->format('Y-m-d');
			$dateCondition = " AND deliveryboyearncommission.addDate BETWEEN '" . $fromDate . " 00:00:00' AND '" . $toDate . " 23:59:59'";
		}
		$extraCondition = " (usermaster.isDelete is NUll or usermaster.isDelete = 0)". $dateCondition;       
		$like = array();
		$Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
        //echo $this->db->last_query();  
		$srno = ($this->input->post('start') ?? $this->input->get('start')) + 1;
		$data = array();
		if ($Records) {
			foreach ($Records->result() as $row) { 
			   $paidCommission=0;
			   $remCommission=0;
			   $query=$this->db->query("select ifNull(sum(deliveryboyearncommission.commissionAmount),0) as paidcommission from deliveryboyearncommission where deliveryBoyCode='".$row->deliveryBoyCode."' and (addDate between '".$fromDate." 00:00:00' AND '".$toDate." 23:59:59') and commissionType !='penalty' and isPaid ='1'");
			   if($query){
				   foreach($query->result() as $res)
		           {
					   $paidCommission=number_format($res->paidcommission,2,'.','');
				   }
			   }
			   $remCommission=$row->commission-$paidCommission;
			   $actionHtml = '
					<div class="btn-group">
						<button type="button" class="btn btn-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<i class="ti-settings"></i>
						</button>
						<div class="dropdown-menu animated slideInUp" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 35px, 0px);">
							  <a class="dropdown-item" href="' . base_url() . 'Commission/details/' . $row->deliveryBoyCode . '"><i class="ti-eye mr-2"></i>View</a>';
						if($remCommission>0){	  
						 $actionHtml .= '<a class="dropdown-item blue" data-toggle="modal" data-target="#responsive-modal" data-seq="'.$row->deliveryBoyCode.'"  href><i class="ti-money" ></i> Pay</a>';
						}	 
						$actionHtml .= '</div>
					</div>';		
				$data[] = array($srno,$row->name,$row->commission,$paidCommission,number_format($remCommission,2,'.',''),$actionHtml); 
				$srno++;      
			}
			$dataCount = sizeOf($this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, '', '', $groupByColumn, $extraCondition)->result());
		} else {
			$dataCount = 0;
		}		
		$output = array(
			"draw" => intval($this->input->post("draw") ?? $this->input->get("draw")),
			"recordsTotal" => $dataCount,
			"recordsFiltered" => $dataCount,
			"data" => $data
		);
		echo json_encode($output);
		
	}
	
	public function details(){
		$data['code'] = $this->uri->segment(3);
		$this->load->view('dashboard/header');
		$this->load->view('dashboard/commission/commissionlist', $data);
		$this->load->view('dashboard/footer');
	}

	public function getDeliveryBoyCommissionList()
	{   
		$userCode = $this->input->post('deliveryboyCode') ?? $this->input->get('deliveryboyCode'); 
		$dateSearch = $this->input->post('date') ?? $this->input->get('date'); 
		$orderType = $this->input->post('orderType') ?? $this->input->get('orderType'); 
		$fromDate = $this->input->post('fromDate') ?? $this->input->get('fromDate');
		$toDate = $this->input->post('toDate') ?? $this->input->get('toDate');
		$tableName = array('deliveryboyearncommission');
		$orderColumns = array("ifNull(deliveryboyearncommission.commissionAmount,0) as commission,deliveryboyearncommission.orderType,ifNull(deliveryboyearncommission.orderAmount,0) as totalorderAmount,deliveryboyearncommission.orderCode, usermaster.name,deliveryboyearncommission.deliveryBoyCode,deliveryboyearncommission.isPaid");		
		$condition = array('usermaster.code' => $userCode,"deliveryboyearncommission.orderType"=>$orderType,'deliveryboyearncommission.commissionType!='=>'penalty');
		$orderBy = array('usermaster.id' => 'DESC');
		$joinType = array('usermaster' => 'inner'); 
		$join = array('usermaster' => 'deliveryboyearncommission.deliveryBoyCode=usermaster.code');
		$groupByColumn = array();                     
		//$groupByColumn = array("deliveryboyearncommission.deliveryBoyCode");
		$limit = $this->input->post("length") ?? $this->input->get("length");
		$offset = $this->input->post("start") ?? $this->input->get("start");               
		
		$dateCondition = "";
		if ($fromDate != "") {
			$fromDate = DateTime::createFromFormat('d/m/Y', $fromDate)->format('Y-m-d');
			$toDate = DateTime::createFromFormat('d/m/Y', $toDate)->format('Y-m-d');
			$dateCondition = " AND deliveryboyearncommission.addDate BETWEEN '" . $fromDate . " 00:00:00' AND '" . $toDate . " 23:59:59'";
		}
		$extraCondition = " (usermaster.isDelete is NUll or usermaster.isDelete = 0)". $dateCondition;       
		$like = array();
		$Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
       // echo $this->db->last_query();  
		$srno = ($this->input->post('start') ?? $this->input->get('start')) + 1;
		$data = array();
		if ($Records) {
			foreach ($Records->result() as $row) { 
			    if ($row->isPaid == 1) {
					$status = "<span class='label label-sm label-success'>Paid</span>";
				} else {
					$status = "<span class='label label-sm label-warning'>Unpaid</span>";
				}
				
				$recAmount = $row->totalorderAmount - $row->commission;
				
                $actionHtml='<a class="dropdown-item  blue" data-toggle="modal" data-target="#responsive-modal" data-ordertype="'.$row->orderType.'" data-order="'.$row->orderCode.'"  data-seq="'.$row->deliveryBoyCode.'"  href><i class="ti-eye"></i> Open</a>';	  								
				$data[] = array($srno,$row->name,$row->totalorderAmount,$recAmount,$row->commission,$status,$actionHtml); 
				$srno++;
			}
			$dataCount = sizeOf($this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, '', '', $groupByColumn, $extraCondition)->result());
		} else {
			$dataCount = 0;
		}		
		$output = array(
			"draw" => intval($this->input->post("draw") ?? $this->input->get("draw")),
			"recordsTotal" => $dataCount,
			"recordsFiltered" => $dataCount,
			"data" => $data
		);
		echo json_encode($output);
	}
	


	public function viewCurrentHistory()
	{
		$dbcode = $this->input->post('code') ?? $this->input->get('code');
		$fromDate = $this->input->post('fromDate') ?? $this->input->get('fromDate');
		$toDate = $this->input->post('toDate') ?? $this->input->get('toDate');
		$order = $this->input->post('order') ?? $this->input->get('order');
		$orderType = $this->input->post('orderType') ?? $this->input->get('orderType');
	
		//$date = date('Y-m-d',strtotime(str_replace('/','-',$dateSearch)));
		if ($fromDate != "") {
			$fromDate = DateTime::createFromFormat('d/m/Y', $fromDate)->format('Y-m-d');
			$toDate = DateTime::createFromFormat('d/m/Y', $toDate)->format('Y-m-d');
		}
		
		$query1=$this->db->query("select orderCode from deliveryboyearncommission where deliveryBoyCode='".$dbcode."' and (addDate between '".$fromDate." 00:00:00' AND '".$toDate." 23:59:59') and orderType='".$orderType."'");
		$orderCodeArr=[];
		//print_r($this->db->last_query());
		if($query1){
        foreach($query1->result() as $res)
		{
			array_push($orderCodeArr,$res->orderCode);
			
		}
		}
		//print_r(implode(',',$orderCodeArr));
		if($orderType=='vegetable')
		{
			$orderColumns = array('deliveryboyearncommission.*,ordermaster.*');
			$orderBy = array('ordermaster' . '.id' => 'DESC');
			$joinType = array('ordermaster' => 'inner',);  
			$join = array('ordermaster' => 'deliveryboyearncommission.orderCode=ordermaster.code');
			$extraCondition = "(ordermaster.code in('".implode( "', '",$orderCodeArr)."')) And deliveryboyearncommission.orderType='vegetable' ";  
		}else
		{
			$orderColumns = array('deliveryboyearncommission.*,vendorordermaster.grandTotal as totalPrice');
			$orderBy = array('vendorordermaster' . '.id' => 'DESC');
			$joinType = array('vendorordermaster' => 'inner',);  
			$join = array('vendorordermaster' => 'deliveryboyearncommission.orderCode=vendorordermaster.code');
			$extraCondition = "(vendorordermaster.code in('".implode( "', '",$orderCodeArr)."')) And deliveryboyearncommission.orderType='food' ";  
		}
		
		$groupByColumn = array();
		$like = array();
		$array = array('deliveryboyearncommission.deliveryBoyCode' => $dbcode,'deliveryboyearncommission.isActive' => 1);
		
		$Records = $this->GlobalModel->selectQuery($orderColumns,'deliveryboyearncommission', $array, $orderBy, $join, $joinType, $like, '', '', $groupByColumn, $extraCondition);
		//echo $this->db->last_query();
		$data['commissionData'] = $Records;
		$data['userCode'] = $dbcode;
	   // $data['dateSearch'] = $date;
		$this->load->view('dashboard/commission/unpaid', $data); 
	}

	public function save()
	{
		$dbcode = $this->input->post('code');
		$dateSearch = $this->input->post('dateSearch');
		
		$query1=$this->db->query("select orderCode from view_deliverycommission where deliveryBoyCode='".$dbcode."' and addDate='".$dateSearch."'");
		//$orderCodeArr=[];
		$result=false;
        foreach($query1->result() as $res)
		{
			//array_push($orderCodeArr,$res->orderCode);
			$result=$this->db->query("UPDATE deliveryboyearncommission SET isPaid=1, isActive=0,isDelete=1 Where deliveryBoyCode='".$dbcode."' And orderCode='".$res->orderCode."'");
		    
		}
            if ($result != false) {
				echo 'true';
			} else {
				echo 'false';
			}		
	}

	public function showhistory()
	{
		$code = $this->input->post('code') ?? $this->input->get('code');
		$tableName = array('usermaster');
		$orderColumns = array("employeemaster.firstName,employeemaster.lastName,usermaster.code as userCode");
		$condition = array('usermaster.code' => $code);
		$orderBy = array('usermaster' . '.id' => 'DESC');
		$joinType = array('employeemaster' => 'inner');
		$join = array('employeemaster' => 'employeemaster.code=usermaster.empCode');
		$groupByColumn = array();
		$limit = $this->input->post("length") ?? $this->input->get("length");
		$offset = $this->input->post("start") ?? $this->input->get("start");
		$extraCondition = " (usermaster.isDelete is NUll or usermaster.isDelete = 0)";
		$like = array();
		$resultar = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
		$data['userdata'] = $resultar;

		$array = array('userCode' => $code, 'isActive' => 1);
		$Records = $this->GlobalModel->getRecordsWithArray('*', 'commissionpayment', $array);
		$data['commissionData'] = $Records;
		$data['userCode'] = $code;
		$this->load->view('dashboard/commission/paid', $data);
	}
	
	public function viewUnpaid(){
		$dbcode = $this->input->post('code') ?? $this->input->get('code');
		$fromDate = $this->input->post('fromDate') ?? $this->input->get('fromDate');
		$toDate = $this->input->post('toDate') ?? $this->input->get('toDate');
		$tableName = array('deliveryboyearncommission'); 
		$orderColumns = array("ifNull(sum(deliveryboyearncommission.commissionAmount),0) as commission,deliveryboyearncommission.orderCode, usermaster.name,deliveryboyearncommission.deliveryBoyCode");		
		$condition = array('usermaster.code' => $dbcode,'deliveryboyearncommission.commissionType!='=>'penalty','deliveryboyearncommission.isPaid'=>'0');
		$orderBy = array('usermaster.id' => 'DESC');
		$joinType = array('usermaster' => 'inner'); 
		$join = array('usermaster' => 'deliveryboyearncommission.deliveryBoyCode=usermaster.code');                     
		$groupByColumn = array("deliveryboyearncommission.deliveryBoyCode");
		$dateCondition = "";
		if ($fromDate != "") {
			$fromDate = DateTime::createFromFormat('d/m/Y', $fromDate)->format('Y-m-d');
			$toDate = DateTime::createFromFormat('d/m/Y', $toDate)->format('Y-m-d');
			$dateCondition = " AND deliveryboyearncommission.addDate BETWEEN '" . $fromDate . " 00:00:00' AND '" . $toDate . " 23:59:59'";
		}
		$extraCondition = " (usermaster.isDelete is NUll or usermaster.isDelete = 0)". $dateCondition;       
		$like = array();
		$Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, array(), "", "", $groupByColumn, $extraCondition);
		
		if ($Records) {
			foreach ($Records->result() as $row) { 
			   $response["commission"]=$row->commission;
			}
		}else{
			$response["commission"]=0;
		}
		echo json_encode($response);
	}
	
	public function paidStatus(){
		$dbcode = $this->input->post('code') ?? $this->input->get('code');
		$fromDate = $this->input->post('fromDate') ?? $this->input->get('fromDate');
		$toDate = $this->input->post('toDate') ?? $this->input->get('toDate');
		$tableName = array('deliveryboyearncommission'); 
		$orderColumns = array("deliveryboyearncommission.code");		
		$condition = array('usermaster.code' => $dbcode,'deliveryboyearncommission.commissionType!='=>'penalty','deliveryboyearncommission.isPaid'=>'0');
		$orderBy = array('usermaster.id' => 'DESC');
		$joinType = array('usermaster' => 'inner'); 
		$join = array('usermaster' => 'deliveryboyearncommission.deliveryBoyCode=usermaster.code');                     
		$groupByColumn = array();
		$dateCondition = "";
		if ($fromDate != "") {
			$fromDate = DateTime::createFromFormat('d/m/Y', $fromDate)->format('Y-m-d');
			$toDate = DateTime::createFromFormat('d/m/Y', $toDate)->format('Y-m-d');
			$dateCondition = " AND deliveryboyearncommission.addDate BETWEEN '" . $fromDate . " 00:00:00' AND '" . $toDate . " 23:59:59'";
		}
		$extraCondition = " (usermaster.isDelete is NUll or usermaster.isDelete = 0)". $dateCondition;       
		$like = array();
		$Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, array(), "", "", $groupByColumn, $extraCondition);
	    if ($Records) {
			foreach ($Records->result() as $row) { 
			   $result=$this->GlobalModel->doEdit(['isPaid' => 1], 'deliveryboyearncommission', $row->code);
			   if($result==true){
			     $response["status"]=true;
			   }
			}
		}else{
			$response["status"]=false;
		}
		echo json_encode($response);
	}
}
?>