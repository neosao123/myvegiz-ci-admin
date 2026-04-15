<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Vendorordercommission extends CI_Controller
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
		$data['vendor'] = $this->GlobalModel->selectQuery('vendor.*', 'vendor', array('vendor.isActive' => 1));
		$this->load->view('dashboard/header');
		$this->load->view('dashboard/vendorordercommission/list', $data);
		$this->load->view('dashboard/footer');
	}

	public function listViewRecords()
	{
		$data['code'] = $this->uri->segment(3);
		$data['error'] = $this->session->flashdata('response');
		$data['query'] = $this->GlobalModel->selectData('usermaster');
		$data['vendor'] = $this->GlobalModel->selectQuery('vendor.*', 'vendor', array('vendor.isActive' => 1));
		$this->load->view('dashboard/header');
		$this->load->view('dashboard/vendorordercommission/view', $data);
		$this->load->view('dashboard/footer');
	}

	public function getVendorCommissionList()
	{
		$userCode = $this->input->post('vendorCode');
		$fromDate = $this->input->post('fromDate');
		$toDate = $this->input->post('toDate');
		$orderType = $this->input->post('orderType');
		$tableName = array('vendorordercommission');
		$orderColumns = array("vendorordercommission.deliveryBoyCode,sum(vendorordercommission.grandTotal) as grandTotal,sum(vendorordercommission.subTotal) as subTotal,sum(vendorordercommission.comissionPercentage) as comissionPercentage,sum(vendorordercommission.comissionAmount) as comissionAmount,sum(vendorordercommission.vendorAmount) as vendorAmount, vendor.firstName,vendor.entityName, vendor.lastName, vendor.code as userCode");
		$condition = array('vendorordercommission.deliveryBoyCode' => $userCode, "vendorordercommission.commissionType" => "regular");
		if ($fromDate != "" && $toDate != '') {
			$startDate = date('Y-m-d', strtotime(str_replace('/', '-', $fromDate)));
			$endDate = date('Y-m-d', strtotime(str_replace('/', '-', $toDate)));
		}
		else {
			$startDate = date('Y-m-d', strtotime(' - 7 days'));
			$endDate = date('Y-m-d');
		}
		$startDate = $startDate . " 00:00:00";
		$endDate = $endDate . " 23:59:59";
		$orderBy = array();
		$joinType = array('vendor' => 'inner');
		$join = array('vendor' => 'vendorordercommission.deliveryBoyCode=vendor.code');
		$groupByColumn = array("vendorordercommission.deliveryBoyCode");
		$limit = $this->input->post("length");
		$offset = $this->input->post("start");
		$extraCondition = "vendorordercommission.addDate between '" . $startDate . "' And '" . $endDate . "'";
		$like = array();
		$Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
		$srno = intval($this->input->post('start')) + 1;
		$data = array();
		$vendorAmount1 = 0;
		//print_r($Records->result());
		if ($Records) {
			foreach ($Records->result() as $row) {

				$paidCommission = 0;
				$remCommission = 0;
				$query = $this->db->query("select ifNull(sum(vendorordercommission.vendorAmount),0) as paidcommission from vendorordercommission where deliveryBoyCode='" . $row->deliveryBoyCode . "' and (addDate between '" . $startDate . "' AND '" . $endDate . "') and isPaid ='1'");
				if ($query) {
					foreach ($query->result() as $res) {
						$paidCommission = number_format($res->paidcommission, 2, '.', '');
					}
				}
				$remCommission = floatval($row->vendorAmount) - floatval($paidCommission);
				$actionHtml = '
				<div class="btn-group">
					<button type="button" class="btn btn-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<i class="ti-settings"></i>
					</button>
					<div class="dropdown-menu animated slideInUp" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 35px, 0px);">
						  <a class="dropdown-item" href="' . base_url() . 'Vendorordercommission/listViewRecords/' . $row->deliveryBoyCode . '"><i class="ti-eye mr-2"></i>View</a>';
				if ($remCommission > 0) {
					$actionHtml .= '<a class="dropdown-item blue" data-toggle="modal" data-target="#responsive-modal" data-vendor="' . $row->deliveryBoyCode . '"  href><i class="ti-money" ></i> Pay</a>';
				}
				$actionHtml .= '</div>  
				</div>';

				//$actionHtml='<a class="dropdown-item  blue" data-toggle="modal" data-target="#responsive-modal" data-vendor="'.$row->deliveryBoyCode.'" href><i class="ti-eye"></i> Open</a>';				
				$deliveryBoy = $row->entityName;
				$vendorAmount1 += floatval($row->vendorAmount);
				$data[] = array($srno, $deliveryBoy, $row->grandTotal, $row->subTotal, $row->comissionAmount, $row->vendorAmount, $paidCommission, $remCommission, $actionHtml);
				$srno++;
			}
			$dataCount = sizeOf($this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, '', '', $groupByColumn, $extraCondition)->result());
		}
		else {
			$dataCount = 0;
		}
		$output = array(
			"draw" => intval($this->input->post("draw")),
			"recordsTotal" => $dataCount,
			"recordsFiltered" => $dataCount,
			"vendorAmount1" => $vendorAmount1,
			"data" => $data
		);
		echo json_encode($output);
	}

	public function getVendorCommissionViewList()
	{
		$userCode = $this->input->post('vendorCode');
		$fromDate = $this->input->post('fromDate');
		$toDate = $this->input->post('toDate');
		$orderType = $this->input->post('orderType');
		$tableName = array('vendorordercommission');
		$orderColumns = array("vendorordercommission.isPaid,vendorordercommission.orderCode,vendorordercommission.deliveryBoyCode,vendorordercommission.grandTotal,vendorordercommission.subTotal,vendorordercommission.comissionPercentage,vendorordercommission.comissionAmount,vendorordercommission.vendorAmount, vendor.firstName,vendor.entityName, vendor.lastName, vendor.code as userCode");
		$condition = array('vendorordercommission.deliveryBoyCode' => $userCode, "vendorordercommission.commissionType" => "regular");
		if ($fromDate != "" && $toDate != '') {
			$startDate = date('Y-m-d', strtotime(str_replace('/', '-', $fromDate)));
			$endDate = date('Y-m-d', strtotime(str_replace('/', '-', $toDate)));
		}
		else {
			$startDate = date('Y-m-d', strtotime(' - 7 days'));
			$endDate = date('Y-m-d');
		}
		$startDate = $startDate . " 00:00:00";
		$endDate = $endDate . " 23:59:59";
		$orderBy = array();
		$joinType = array('vendor' => 'inner');
		$join = array('vendor' => 'vendorordercommission.deliveryBoyCode=vendor.code');
		$groupByColumn = array();
		$limit = $this->input->post("length");
		$offset = $this->input->post("start");
		$extraCondition = "vendorordercommission.addDate between '" . $startDate . "' And '" . $endDate . "'";
		$like = array();
		$Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
		$srno = intval($this->input->post('start')) + 1;
		$data = array();
		$vendorAmount1 = 0;
		//echo $this->db->last_query();
		if ($Records) {
			foreach ($Records->result() as $row) {
				if ($row->isPaid == 1) {
					$status = "<span class='label label-sm label-success'>Paid</span>";
				}
				else {
					$status = "<span class='label label-sm label-warning'>Unpaid</span>";
				}

				$actionHtml = '<a class="dropdown-item  blue" data-toggle="modal" data-target="#responsive-modal" data-vendor="' . $row->deliveryBoyCode . '" href><i class="ti-eye"></i> Open</a>';
				$deliveryBoy = $row->entityName;
				$vendorAmount1 += $row->vendorAmount;
				$data[] = array($srno, $row->orderCode, $row->grandTotal, $row->subTotal, $row->comissionAmount, $row->vendorAmount, $status, $actionHtml);
				$srno++;
			}
			$dataCount = sizeOf($this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, '', '', $groupByColumn, $extraCondition)->result());
		}
		else {
			$dataCount = 0;
		}
		$output = array(
			"draw" => intval($this->input->post("draw")),
			"recordsTotal" => $dataCount,
			"recordsFiltered" => $dataCount,
			"vendorAmount1" => $vendorAmount1,
			"data" => $data
		);
		echo json_encode($output);
	}



	public function viewCurrentHistory()
	{
		$vendorCode = $this->input->post('vendorCode');
		$fromDate = $this->input->post('fromDate');
		$toDate = $this->input->post('toDate');
		if ($fromDate != "" && $toDate != '') {
			$startDate = date('Y-m-d', strtotime(str_replace('/', '-', $fromDate)));
			$endDate = date('Y-m-d', strtotime(str_replace('/', '-', $toDate)));
		}
		else {
			$startDate = date('Y-m-d', strtotime(' - 7 days'));
			$endDate = date('Y-m-d');
		}
		$startDate = $startDate . " 00:00:00";
		$endDate = $endDate . " 23:59:59";
		$condition = array('vendorordercommission.deliveryBoyCode' => $vendorCode, 'vendorordercommission.isActive' => 1);
		$orderBy = array('vendorordercommission.id' => 'DESC');
		$extraCondition = " vendorordercommission.addDate between '" . $startDate . "' And '" . $endDate . "'";
		$Records = $this->GlobalModel->selectQuery('vendorordercommission.*', 'vendorordercommission', $condition, $orderBy, array(), array(), array(), '', '', '', $extraCondition);
		//echo $this->db->last_query();
		$data['commissionData'] = $Records;
		$data['vendorCode'] = $vendorCode;
		$data['fromDate'] = $fromDate;
		$data['toDate'] = $toDate;
		$this->load->view('dashboard/vendorordercommission/unpaid', $data);
	}


	public function viewUnpaid()
	{
		$vendorCode = $this->input->post('vendorCode');
		$fromDate = $this->input->post('fromDate');
		$toDate = $this->input->post('toDate');
		$tableName = array('vendorordercommission');
		$orderColumns = array("vendorordercommission.deliveryBoyCode,sum(vendorordercommission.vendorAmount) as vendorAmount, vendor.firstName,vendor.entityName, vendor.lastName, vendor.code as userCode");
		$condition = array('vendorordercommission.deliveryBoyCode' => $vendorCode, "vendorordercommission.commissionType" => "regular", "vendorordercommission.isPaid" => 0);
		if ($fromDate != "" && $toDate != '') {
			$startDate = date('Y-m-d', strtotime(str_replace('/', '-', $fromDate)));
			$endDate = date('Y-m-d', strtotime(str_replace('/', '-', $toDate)));
		}
		else {
			$startDate = date('Y-m-d', strtotime(' - 7 days'));
			$endDate = date('Y-m-d');
		}
		$startDate = $startDate . " 00:00:00";
		$endDate = $endDate . " 23:59:59";
		$orderBy = array();
		$joinType = array('vendor' => 'inner');
		$join = array('vendor' => 'vendorordercommission.deliveryBoyCode=vendor.code');
		$groupByColumn = array("vendorordercommission.deliveryBoyCode");
		$extraCondition = "vendorordercommission.addDate between '" . $startDate . "' And '" . $endDate . "'";
		$like = array();
		$Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, array(), "", "", $groupByColumn, $extraCondition);
		if ($Records) {
			foreach ($Records->result() as $row) {
				$response["commission"] = $row->vendorAmount;
			}
		}
		else {
			$response["commission"] = 0;
		}
		echo json_encode($response);
	}

	public function save()
	{
		$vendorCode = $this->input->post('vendorCode');
		$fromDate = $this->input->post('fromDate');
		$toDate = $this->input->post('toDate');
		if ($fromDate != "" && $toDate != '') {
			$startDate = date('Y-m-d', strtotime(str_replace('/', '-', $fromDate)));
			$endDate = date('Y-m-d', strtotime(str_replace('/', '-', $toDate)));
		}
		else {
			$startDate = date('Y-m-d', strtotime(' - 7 days'));
			$endDate = date('Y-m-d');
		}
		$startDate = $startDate . " 00:00:00";
		$endDate = $endDate . " 23:59:59";
		$result = false;
		$result = $this->db->query("UPDATE vendorordercommission SET isPaid=1 Where vendorordercommission.deliveryBoyCode='" . $vendorCode . "' and vendorordercommission.addDate between '" . $startDate . "' And '" . $endDate . "'");
		if ($result != false) {
			echo 'true';
		}
		else {
			echo 'false';
		}
	}

	public function paidStatus()
	{
		$vendorCode = $this->input->post('vendorCode');
		$fromDate = $this->input->post('fromDate');
		$toDate = $this->input->post('toDate');
		$tableName = array('vendorordercommission');
		$orderColumns = array("vendorordercommission.code,vendorordercommission.deliveryBoyCode");
		$condition = array('vendorordercommission.deliveryBoyCode' => $vendorCode, "vendorordercommission.commissionType" => "regular", "vendorordercommission.isPaid" => 0);
		if ($fromDate != "" && $toDate != '') {
			$startDate = date('Y-m-d', strtotime(str_replace('/', '-', $fromDate)));
			$endDate = date('Y-m-d', strtotime(str_replace('/', '-', $toDate)));
		}
		else {
			$startDate = date('Y-m-d', strtotime(' - 7 days'));
			$endDate = date('Y-m-d');
		}
		$startDate = $startDate . " 00:00:00";
		$endDate = $endDate . " 23:59:59";
		$orderBy = array();
		$joinType = array('vendor' => 'inner');
		$join = array('vendor' => 'vendorordercommission.deliveryBoyCode=vendor.code');
		$groupByColumn = array();
		$extraCondition = "vendorordercommission.addDate between '" . $startDate . "' And '" . $endDate . "'";
		$like = array();
		$Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, array(), "", "", $groupByColumn, $extraCondition);
		if ($Records) {
			foreach ($Records->result() as $row) {
				$result = $this->GlobalModel->doEdit(['isPaid' => 1], 'vendorordercommission', $row->code);
				if ($result == true) {
					$response["status"] = true;
				}
			}
		}
		else {
			$response["commission"] = 0;
		}
		echo json_encode($response);
	}
}
?>