<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Currency extends CI_Controller
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
		$data['error'] = $this->session->flashdata('response');
		$this->load->view('dashboard/header');
		$this->load->view('dashboard/currency/list', $data);
		$this->load->view('dashboard/footer');
	}
	public function add()
	{
		$this->load->view('dashboard/header');
		$this->load->view('dashboard/currency/add');
		$this->load->view('dashboard/footer');
	}

	public function edit()
	{
		$code = $this->uri->segment(3);
		$data['query'] = $this->GlobalModel->selectDataById($code, 'currencymaster');
		$this->load->view('dashboard/header');
		$this->load->view('dashboard/currency/edit', $data);
		$this->load->view('dashboard/footer');
	}

	// public function view()
	// {
	// $code=$this->uri->segment(3);
	// $data['query'] = $this->GlobalModel->selectDataById($code,'currencymaster');
	// $this->load->view('dashboard/header');
	// $this->load->view('dashboard/currency/view',$data);
	// $this->load->view('dashboard/footer');

	// }

	public function getCurrencyList()
	{
		$tables = array('currencymaster');

		$requiredColumns = array(
				array('code', 'currencyName', 'currencySName', 'currencyDescription', 'isActive')

		);
		$conditions = array();
		$extraConditionColumnNames = array();

		$extraConditions = array();

		$Records = $this->GlobalModel1->make_datatables($tables, $requiredColumns, $conditions, $extraConditionColumnNames, $extraConditions);
		//print_r($Records->result());
		$offset = $this->input->post("start") ?? $this->input->get("start");
		$srno = (intval($offset) > 0 ? intval($offset) : 0) + 1;
		$data = array();
		foreach ($Records->result() as $row) {

			if ($row->isActive_04 == "1") {
				$status = " <span class='label label-sm label-success'>Active</span>";
			}
			else {
				$status = " <span class='label label-sm label-warning'>Inactive</span>";
			}


			$actionHtml = '<div class="btn-group">
                                                        <button type="button" class="btn btn-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <i class="ti-settings"></i>
                                                        </button>
                                                        <div class="dropdown-menu animated slideInUp" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 35px, 0px);">
                                                            <a class="dropdown-item  blue" data-toggle="modal" data-target="#responsive-modal" data-seq="' . $row->code_00 . '" href><i class="ti-eye"></i> Open</a>
                                                            <a class="dropdown-item" href="' . base_url() . 'Currency/edit/' . $row->code_00 . '"><i class="ti-pencil-alt"></i> Edit</a>
                                                            <a class="dropdown-item  mywarning" data-seq="' . $row->code_00 . '" id="' . $row->code_00 . '"><i class="ti-trash" href></i> Delete</a>
                                                            
                                                        </div>
                                                    </div>';

			$data[] = array(
				$srno,
				$row->code_00,
				$row->currencyName_01,
				$row->currencySName_02,
				$row->currencyDescription_03,
				$status,
				$actionHtml
			);


			$srno++;
		}
		$dataCount = $this->GlobalModel1->get_all_data($tables, $requiredColumns, $conditions, $extraConditionColumnNames, $extraConditions);
		$output = array(
			"draw" => intval($this->input->post("draw") ?? $this->input->get("draw") ?? 0),
			"recordsTotal" => $dataCount,
			"recordsFiltered" => $dataCount,
			"data" => $data
		);
		echo json_encode($output);
	}

	public function save()
	{

		$currencySName = strtoupper($this->input->post("currencySName"));

		//Activity Track Starts
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
		$text = $role . " " . $userName . ' added new currancy "' . $currencySName . '" from ' . $ip;

		$log_text = array(
			'code' => "demo",
			'addID' => $addID,
			'logText' => $text
		);

		//Activity Track Ends

		$result = $this->GlobalModel->checkDuplicateRecord('currencySName', $currencySName, 'currencymaster');
		if ($result != FALSE) {
			$data = array(
				'error_message' => 'Duplicate currancy short name'
			);
			$this->load->view('dashboard/header');
			$this->load->view('dashboard/currency/add', $data);
			$this->load->view('dashboard/footer');
		}
		else {

			$this->form_validation->set_rules('currencyName', 'Currency Name', 'required');
			$this->form_validation->set_rules('currencySName', 'Currency Short Name', 'required');

			if ($this->form_validation->run() == FALSE) {

				$data['error_message'] = '* Fields are Required!';
				$this->load->view('dashboard/header');
				$this->load->view('dashboard/currency/add', $data);
				$this->load->view('dashboard/footer');
			}
			else {
				$data = array(
					'currencyName' => trim($this->input->post("currencyName")),
					'currencySName' => strtoupper(trim($this->input->post("currencySName"))),
					'currencyDescription' => trim($this->input->post("currencyDescription")),
					'addID' => $addID,
					'addIP' => $ip,
					'isActive' => trim($this->input->post("isActive"))
				);

				$result = $this->GlobalModel->addWithoutYear($data, 'currencymaster', 'CUR');
				if ($result != 'false') {
					$response['status'] = true;
					$response['message'] = "Currency Successfully Added.";
					$this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
				}
				else {
					$response['status'] = false;
					$response['message'] = "Failed To Add Currency";
				}
				$this->session->set_flashdata('response', json_encode($response));
				redirect(base_url() . 'currency/listRecords', 'refresh');
			}
		}
	}
	public function update()
	{

		$currencyName = trim($this->input->post("currencyName"));
		$code = $this->input->post('code');

		//Activity Track Starts

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
		$text = $role . " " . $userName . ' updated Currency "' . $currencyName . '" from ' . $ip;

		$log_text = array(
			'code' => "demo",
			'addID' => $addID,
			'logText' => $text
		);

		//Activity Track Ends 

		$this->form_validation->set_rules('currencyName', 'Currency Name', 'required');
		$this->form_validation->set_rules('currencySName', 'Currency Short Name', 'required');

		if ($this->form_validation->run() == FALSE) {

			$data['error_message'] = '* Fields are Required!';
			$this->load->view('dashboard/header');
			$this->load->view('dashboard/currency/add', $data);
			$this->load->view('dashboard/footer');
		}
		else {

			$data = array(
				'currencyName' => $currencyName,
				'currencySName' => strtoupper(trim($this->input->post("currencySName"))),
				'currencyDescription' => trim($this->input->post("currencyDescription")),
				'editID' => $addID,
				'editIP' => $ip,
				'isActive' => trim($this->input->post("isActive"))
			);

			$result = $this->GlobalModel->doEdit($data, 'currencymaster', $code);
			if ($result != 'false') {
				$response['status'] = true;
				$response['message'] = "Currency Successfully Updated.";
				$this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
			}
			else {
				$response['status'] = false;
				$response['message'] = "No change In Currency";
			}
			$this->session->set_flashdata('response', json_encode($response));

			redirect(base_url() . 'currency/listRecords', 'refresh');
		}
	}

	public function delete()
	{
		$code = $this->input->post('code');

		//Activity Track Starts

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
		$dataQ = $this->GlobalModel->selectDataByField('code', $code, 'currencymaster');
		$currencyName = '';

		foreach ($dataQ->result() as $row) {
			$currencyName = $row->currencyName;
		}

		$text = $role . " " . $userName . ' deleted currency "' . $currencyName . '" from ' . $ip;

		$log_text = array(
			'code' => "demo",
			'addID' => $addID,
			'logText' => $text
		);
		$this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');

		$data = array(
			'deleteID' => $addID,
			'deleteIP' => $ip
		);

		$resultData = $this->GlobalModel->doEdit($data, 'currencymaster', $code);

		echo $this->GlobalModel->delete($code, 'currencymaster');

	//redirect(base_url() . 'index.php/currency/listrecords', 'refresh');
	}
	public function view()
	{
		$code = $this->input->get('code');

		//Activity Track Starts

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

		$tables = array('currencymaster');

		$requiredColumns = array(
				array('code', 'currencyName', 'currencySName', 'currencyDescription', 'isActive')

		);
		$conditions = array();
		$extraConditionColumnNames = array(
				array("code")
		);

		$extraConditions = array(
				array($code)
		);

		$Records = $this->GlobalModel1->make_datatables($tables, $requiredColumns, $conditions, $extraConditionColumnNames, $extraConditions);
		// print_r($Records->result());

		$modelHtml = '<form>';
		$activeStatus = "";

		foreach ($Records->result() as $row) {



			if ($row->isActive_04 == "1") {
				$activeStatus = '<span class="label label-sm label-success">Active</span>';
			}
			else {
				$activeStatus = '<span class="label label-sm label-warning">Inactive</span>';
			}

			$modelHtml .= '<div class="form-row"><div class="col-md-3 mb-3"><label> <b> Code:</b> </label>
						<input type="text" value="' . $row->code_00 . '" class="form-control-line"  readonly></div>
					<div class="col-md-5 mb-3"><label> <b>Currency Name:</b> </label>
						<input type="text" class="form-control-line" value="' . $row->currencyName_01 . '"  readonly></div>
						<div class="col-md-4 mb-3"><label> <b>Currency Short Name:</b> </label>
						<input type="text" class="form-control-line" value="' . $row->currencySName_02 . '"  readonly></div> </div>
						<div class="col-md-12 mb-3"><label><b>Currency Description:</b> </label>
						<input type="text" class="form-control-line" row="2" cols="50" readonly value="' . $row->currencyDescription_03 . '"> </div>
					<div class="form-group">' . $activeStatus . '</div>';

			//for activity

			$text = $role . " " . $userName . ' viewed currency "' . $row->currencyName_01 . '" from ' . $ip;

			$log_text = array(
				'code' => "demo",
				'addID' => $addID,
				'logText' => $text
			);

			$this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');

		//Activity Track Ends
		}

		$modelHtml .= '</form>';
		echo $modelHtml;
	}
}
?>