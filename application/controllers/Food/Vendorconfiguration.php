<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Vendorconfiguration extends CI_Controller
{
	var $session_key;
	public function __construct()
	{
		parent::__construct();
		$this->load->helper('form', 'url', 'html');
		$this->load->library('form_validation');
		$this->load->model('GlobalModel');
		$this->load->model('GlobalModel1');
		$this->session_key = $this->session->userdata('key' . SESS_KEY);
		if (!isset($this->session->userdata['logged_in' . $this->session_key]['code'])) {
			redirect('Admin/login', 'refresh');
		}
	}

	public function listRecords()
	{
		$data['vendorconfiguration'] = $this->GlobalModel->selectQuery('vendorconfiguration.*', 'vendorconfiguration', array('vendorconfiguration.isActive' => 1));
		$data['vendorcommissionslabs'] = $this->GlobalModel->selectQuery('vendorcommissionslabs.*', 'vendorcommissionslabs', array('vendorcommissionslabs.isActive' => 1));
		$data['error'] = $this->session->flashdata('response');
		$this->load->view('dashboard/header');
		$this->load->view('dashboard/vendorconfiguration/list', $data);
		$this->load->view('dashboard/footer');
	}

	public function update_configuration()
	{
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

		$code = trim($this->input->post("configCode"));
		$maxCODAmount = trim($this->input->post("maxCODAmount"));
		$defaultVendorCommission = trim($this->input->post("defaultVendorCommission"));
		$shippingCharges = trim($this->input->post("shippingCharges"));
		$shippingChargesUpto = trim($this->input->post("shippingChargesUpto"));

		$ip = $_SERVER['REMOTE_ADDR'];

		$text = $role . " " . $userName . ' updated vendorconfiguration to mincod => ' . $maxCODAmount . ' && defaultVendorCommission =>' . $defaultVendorCommission . ' from ' . $ip;

		$log_text = array(
			'code' => "demo",
			'addID' => $addID,
			'logText' => $text
		);
		//Activity Track Ends  

		$data = array(
			'maxCODAmount' => $maxCODAmount,
			'defaultVendorCommission' => $defaultVendorCommission,
			'shippingCharges' => $shippingCharges,
			'shippingChargesUpto' => $shippingChargesUpto,
			'addID' => $addID,
			'addIP' => $ip,
			'editID' => $addID,
			'editIP' => $ip,
			'isActive' => 1,
		);
		$resultUpdate = $this->GlobalModel->doEdit($data, 'vendorconfiguration', $code);
		if ($resultUpdate != 'false') {
			$this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
			$response['status'] = true;
			$response['code'] = $code;
			$response['message'] = 'Vendor configuration updated successfully';
		}
		else {
			$response['status'] = false;
			$response['message'] = 'Failed to update vendor configuration';
		}

		echo json_encode($response);
	}

	public function save_commissionslab()
	{
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

		$text = $role . " " . $userName . ' updated vendorcommissionslabs  from ' . $ip;

		$log_text = array(
			'code' => "demo",
			'addID' => $addID,
			'logText' => $text
		);

		$userAddrLine = $this->db->query('truncate table vendorcommissionslabs');
		$amountFrom = $this->input->post('amountFrom');
		$amountTo = $this->input->post('amountTo');
		$commissionRate = $this->input->post('commissionRate');

		$addressData = array();
		$addResultFlag = false;
		$adi = false;
		if (!empty($amountFrom)) {
			for ($i = 0; $i < sizeOf($amountFrom); $i++) {
				if ($amountFrom[$i] != "" && $amountTo[$i] != "" && $commissionRate[$i] != "") {
					$addressData = array(
						'amountFrom' => $amountFrom[$i],
						'amountTo' => $amountTo[$i],
						'commissionRate' => $commissionRate[$i],
						'isActive' => 1,
						'addID' => $addID,
						'addIP' => $ip
					);
					$addLineDataResult = $this->GlobalModel->addWithoutYear($addressData, 'vendorcommissionslabs', 'VCLE');
					if ($addLineDataResult != 'false') {
						$adi = true;
					}
					else {
						$adi = false;
					}
				}
			}
			if ($adi) {
				$result['status'] = true;
				$result['message'] = "Commission slabs added successfully";
			}
			else {
				$userAddrLine = $this->db->query('truncate table vendorcommissionslabs');
				$result['status'] = false;
				$result['message'] = "Failed to add commission slabs";
			}
		}
		else {
			$result['status'] = false;
			$result['message'] = "Cannnot add empty commission slabs";
		}
		echo json_encode($result);
	}

	public function delete_commision_slab_line()
	{
		$code = $this->input->post('code');
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
		$text = $role . " " . $userName . ' commission slab deleted by "' . $addID . '" of "' . $code . '" from ' . $ip;

		$log_text = array(
			'code' => "demo",
			'addID' => $addID,
			'logText' => $text
		);
		$this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
		$resultData = $this->GlobalModel->deleteForever($code, 'vendorcommissionslabs');
		if ($resultData == 'true')
			echo true;
		else
			echo false;
	}
}