<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Vendorcommission extends CI_Controller
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
		$data['vendor'] = $this->GlobalModel->selectQuery('vendor.*', 'vendor', array('vendor.isActive' => 1));
		$this->load->view('dashboard/header');
		$this->load->view('dashboard/commission/vendorComList', $data);
		$this->load->view('dashboard/footer');
	}


	public function updateVendorPaymentFlag()
	{
		$vendorCode = $this->input->post('vendorCode');
		$paymentDate = $this->input->post('paymentDate');
		$dateFrom = date('Y-m-d', strtotime(str_replace('/', '-', $paymentDate)));
		$dateTo = date('Y-m-d', strtotime(str_replace('/', '-', $paymentDate)));

		$result = $this->db->query("update vendorordermaster set vendorAmountPaid=1,vendorPaymentDate='" . date('Y-m-d H:i:s') . "' where vendorCode='" . $vendorCode . "' and (addDate between '" . $dateFrom . "  00:00:00' and '" . $dateTo . " 23:59:59')");
		if ($result == true) {
			echo true;
		}
		else {
			echo false;
		}
	}

	public function getVendorCommissionList()
	{

		$vendorCode = $this->input->post('vendorCode') ?? $this->input->get('vendorCode');
		$dateSearch = $this->input->post('date') ?? $this->input->get('date');

		$tableName = array('vendorordermaster');

		$orderColumns = array("vendor.code as vendorCode,vendor.entityName,ROUND(ifNull( sum( vendorordermaster.grandTotal ), 0 )) AS totalorderAmount,ROUND(ifNull( sum( vendorordermaster.grandTotal *( 15 / 100 )), 0 )) AS deliverAmount,ROUND((ifNull( sum( vendorordermaster.grandTotal ), 0 ) - ifNull( sum( vendorordermaster.grandTotal *( 15 / 100 )), 0 ))) AS vendorAmount");
		$condition = array("vendorordermaster.orderStatus" => 'DEL', "vendorordermaster.vendorAmountPaid" => 0);
		if ($vendorCode) {
			$condition["vendorordermaster.vendorCode"] = $vendorCode;
		}
		if ($dateSearch != "") {
			$date = date('Y-m-d', strtotime(str_replace('/', '-', $dateSearch)));
			$date = date('Y-m-d', strtotime($date . ' -1 days'));
			$where = " ( vendorordermaster.addDate between '" . $date . " 00:00:00' and '" . $date . " 23:59:59')";
		}
		else {
			$date = date('Y-m-d');
			$date = date('Y-m-d', strtotime($date . ' -1 days'));
			$where = " ( vendorordermaster.addDate between '" . $date . " 00:00:00' and '" . $date . " 23:59:59')";
		}
		$orderBy = array('vendorordermaster' . '.id' => 'DESC');
		$joinType = array('vendor' => 'inner');
		$join = array('vendor' => 'vendor.code=vendorordermaster.vendorCode');
		$groupByColumn = array("vendorordermaster.vendorCode");
		$limit = $this->input->post("length") ?? $this->input->get("length");
		$offset = $this->input->post("start") ?? $this->input->get("start");
		$extraCondition = " (vendorordermaster.isDelete is NUll or vendorordermaster.isDelete = 0) and " . $where;
		$like = array();
		$Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
		//echo $this->db->last_query();
		$srno = ($this->input->post('start') ?? $this->input->get('start')) + 1;
		$data = array();
		if ($Records) {
			foreach ($Records->result() as $row) {
				$actionHtml = '<div class="btn-group">  <button type="button" class="btn btn-success paybtn" data-id="' . $row->vendorCode . '">Pay</button></div>';

				$data[] = array($srno, $row->entityName, $row->totalorderAmount, $row->deliverAmount, $row->vendorAmount, $actionHtml);
				$srno++;
			}
			$dataCount = sizeOf($this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, '', '', $groupByColumn, $extraCondition)->result());
		}
		else {
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
		$code = $this->input->post('code') ?? $this->input->get('code');
		$array = array('userCode' => $code, 'isActive' => 1);
		$Records = $this->GlobalModel->selectQuery('commissiontemp.*', 'commissiontemp', $array);
		$data['commissionData'] = $Records;
		$data['userCode'] = $code;
		$this->load->view('dashboard/commission/unpaid', $data);
	}

	public function save()
	{
		$code = $this->input->post('code');
		$addID = $this->session->userdata['logged_in' . $this->session_key]['code'];
		$userRole = $this->session->userdata['logged_in' . $this->session_key]['role'];
		$userName = $this->session->userdata['logged_in' . $this->session_key]['username'];
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
		$month = date('m');
		$year = date('Y');
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
			}
			else {
				echo 'false';
			}
		}
		else {
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
}
?>