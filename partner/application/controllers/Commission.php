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
		$this->load->model('GlobalModel1');
		$this->session_key = $this->session->userdata('partner_key' . SESS_KEY_PARTNER);
		if (!isset($this->session->userdata['part_logged_in' . $this->session_key]['code'])) {
			redirect('login', 'refresh');
		}
	}

	public function listRecords()
	{
		$data['error'] = $this->session->flashdata('response');
		$data['query'] = $this->GlobalModel->selectData('usermaster');
		$data['vendor'] = $this->GlobalModel->selectQuery('vendor.*', 'vendor', array('vendor.isActive' => 1));
		$this->load->view('header');
		$this->load->view('vendorordercommission/list', $data);
		$this->load->view('footer');
	}

	public function getVendorCommissionList()
	{   
		$userCode = $this->session->userdata['part_logged_in' . $this->session_key]['code']; 
		$fromDate = $this->input->get('fromDate');
		$toDate = $this->input->get('toDate');
		$orderType = $this->input->GET('orderType'); 
		$tableName = array('vendorordercommission');
		//$orderColumns = array("view_deliverycommission.*");
		$orderColumns = array("vendorordercommission.*, vendor.firstName,vendor.entityName, vendor.lastName, vendor.code as userCode");		
		$condition = array('vendorordercommission.deliveryBoyCode' => $userCode);
		if ($fromDate != '') {
			$fromDate = date('Y-m-d', strtotime(str_replace('/', '-', $fromDate)));
			$toDate = date('Y-m-d', strtotime(str_replace('/', '-', $toDate)));
			$fromDate = $fromDate . " 00:00:00";
			$toDate = $toDate . " 23:59:59";
		}
		$orderBy = array('vendorordercommission' . '.id' => 'DESC');
		$joinType = array('vendor' => 'inner'); 
		$join = array('vendor' => 'vendorordercommission.deliveryBoyCode=vendor.code');
		$groupByColumn = array("");
		$limit = $this->input->GET("length");
		$offset = $this->input->GET("start");
		$extraCondition = "vendorordercommission.addDate between '".$fromDate."' And '".$toDate."'";       
		$like = array();
		$Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
		//echo $this->db->last_query();
		//exit(); 
		$srno = $_GET['start'] + 1;
		$data = array();
		$vendorAmount1=0;
		$html="";
		if ($Records) {
			foreach ($Records->result() as $row) {
				$vendorAmount1+=$row->vendorAmount;
				if($row->isPaid==1)
				{
					$html='<span class="label label-success">Paid</span>';	
				}
				else{
					$html='<span class="label label-warning">Not Paid</span>';		
				}
						
				$deliveryBoy = 	$row->entityName;
				$orderDate = date('d/m/Y h:i A', strtotime($row->addDate));
				$data[] = array($srno,$orderDate, $row->orderCode, $row->subTotal,$row->comissionPercentage.'(%)',$row->comissionAmount,$row->vendorAmount,$html); 
				$srno++;
			}
			$dataCount = sizeOf($this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, '', '', $groupByColumn, $extraCondition)->result());
		} else {
			$dataCount = 0;
		}		
		$output = array(
			"draw" => intval($_GET["draw"]),
			"recordsTotal" => $dataCount,
			"recordsFiltered" => $dataCount,
			"vendorAmount1"=>$vendorAmount1,
			"data" => $data
		);
		echo json_encode($output);
	}
	


	public function viewCurrentHistory()
	{
		$code = $this->input->GET('code');
		$array = array('userCode' => $code, 'isActive' => 1);
		$Records = $this->GlobalModel->selectQuery('commissiontemp.*', 'commissiontemp', $array);
		$data['commissionData'] = $Records;
		$data['userCode'] = $code;
		$this->load->view('dashboard/commission/unpaid', $data);
	}

	public function save()
	{
		$code = $this->input->post('code');
		$addID = $this->session->userdata['part_logged_in' . $this->session_key]['code'];
		$userRole = $this->session->userdata['part_logged_in' . $this->session_key]['role'];
		$userName = $this->session->userdata['part_logged_in' . $this->session_key]['username'];
		$role = "";
		$name = "";
		$com_code = md5(uniqid(rand()));
		$cart_code = md5(uniqid(rand()));
		$forgot = md5(uniqid(rand()));
		switch ($userRole) {
			case "ADM":
				$role = "Admin";
				break;
			case "USR":
				$role = "User";
				break;
		}
		$entryDate = date('Y-m-d h:i:s');
		$month =  date('m');
		$year =  date('Y');
		$ip = $_SERVER['REMOTE_ADDR'];
		$text = $role . " " . $userName . " paid commision for month " . $month . " & year " . $year . " from " . $ip;

		$log_text = array(
			'code' => "demo",
			'addID' => $addID,
			'logText' => $text
		);
		$array = array('userCode' => $code, 'isActive' => 1);
		$totalPoints = 0;
		$countOrders = 0;
		$data = $this->GlobalModel->getRecordsWithArray('*', 'commissiontemp', $array);
		if ($data != false) {
			foreach ($data->result() as $row) {
				$totalPoints += $row->points;
			}
			$countOrders = sizeOf($data->result());
			$dataToSave = array(
				'userCode' => $code,
				'addIP' => $ip,
				'entryDate' => $entryDate,
				'points' => $totalPoints,
				'month' => $month,
				'year' => $year,
				'addID' => $addID,
				'addDate' => $entryDate,
				'isActive' => 1
			);
			$result = $this->GlobalModel->addNew($dataToSave, 'commissionpayment', 'PAY');
			if ($result != false) {
				$this->GlobalModel->deleteForeverFromField('userCode', $code, 'commissiontemp');
				echo 'true';
			} else {
				echo 'false';
			}
		} else {
			echo 'false';
		}
	}

	public function showhistory()
	{
		$code = $this->input->GET('code');
		$tableName = array('usermaster');
		$orderColumns = array("employeemaster.firstName,employeemaster.lastName,usermaster.code as userCode");
		$condition = array('usermaster.code' => $code);
		$orderBy = array('usermaster' . '.id' => 'DESC');
		$joinType = array('employeemaster' => 'inner');
		$join = array('employeemaster' => 'employeemaster.code=usermaster.empCode');
		$groupByColumn = array();
		$limit = $this->input->GET("length");
		$offset = $this->input->GET("start");
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
}
?>