<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Couponoffer extends CI_Controller
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
	}

	public function listRecords()
	{
		$addID = $this->session->userdata['logged_in' . $this->session_key]['code'];
		$data['coupan'] = $this->GlobalModel->selectQuery('vegitableoroffer.*', 'vegitableoroffer');
		$data['offertype'] = $this->GlobalModel->selectQuery('vegitableoroffer.*', 'vegitableoroffer');
		$data['discount'] = $this->GlobalModel->selectQuery('vegitableoroffer.*', 'vegitableoroffer');
		$data['error'] = $this->session->flashdata('response');
		$this->load->view('dashboard/header');
		$this->load->view('dashboard/couponoffer/list', $data);
		$this->load->view('dashboard/footer');
	}

	public function add()
	{
		$this->load->view('dashboard/header');
		$this->load->view('dashboard/couponoffer/add', $data);
		$this->load->view('dashboard/footer');
	}

	public function edit()
	{
		$addID = $this->session->userdata['logged_in' . $this->session_key]['code'];
		$code = $this->uri->segment('3');
		$data['query'] = $this->GlobalModel->selectQuery('vegitableoroffer.*', 'vegitableoroffer', array("vegitableoroffer.code" => $code));
		$this->load->view('dashboard/header');
		$this->load->view('dashboard/couponoffer/edit', $data);
		$this->load->view('dashboard/footer');
	}

	public function getOfferList()
	{
		$addID = $this->session->userdata['logged_in' . $this->session_key]['code'];
		$coupanCode = $this->input->post('coupanCode') ?? $this->input->get('coupanCode');
		$offerType = $this->input->post('offerType') ?? $this->input->get('offerType');
		$discountCode = $this->input->post('discountCode') ?? $this->input->get('discountCode');
		$fromDate = $this->input->post('fromDate') ?? $this->input->get('fromDate');
		$toDate = $this->input->post('toDate') ?? $this->input->get('toDate');

		$tableName = "vegitableoroffer";
		$orderColumns = array("vegitableoroffer.*");
		$condition = array('vegitableoroffer.code' => $coupanCode, 'vegitableoroffer.offerType' => $offerType, 'vegitableoroffer.discount' => $discountCode);
		$orderBy = array('vegitableoroffer' . '.id' => 'DESC');
		$joinType = array();
		$join = array();
		$groupByColumn = array();
		$limit = $this->input->post("length") ?? $this->input->get("length");
		$offset = $this->input->post("start") ?? $this->input->get("start");
		$dateCondition = "";
		if ($fromDate != "") {
			$fromDate = DateTime::createFromFormat('d/m/Y', $fromDate)->format('Y-m-d');
			$toDate = DateTime::createFromFormat('d/m/Y', $toDate)->format('Y-m-d');
			$dateCondition = " AND vegitableoroffer.editDate BETWEEN '" . $fromDate . " 01:00:01' AND '" . $toDate . " 12:59:59'";
		}
		$extraCondition = " (vegitableoroffer.isDelete=0 OR vegitableoroffer.isDelete IS NULL)";
		$like = array();
		$Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
		$r = $this->db->last_query();
		$srno = (intval($offset) > 0 ? intval($offset) : 0) + 1;
		$dataCount = 0;
		$data = array();
		if ($Records) {
			foreach ($Records->result() as $row) {
				$code = $row->code;
				if ($row->offerType == 'cap') {
					$offerType = 'Per';
				}
				else {
					$offerType = $row->offerType;
				}
				if ($row->isActive == 1) {
					$status = "<span class='label label-sm label-success'>Active</span>";
				}
				else {
					$status = "<span class='label label-sm label-warning'>Inactive</span>";
				}
				if ($row->offerType == 'flat') {
					$discount = $row->flatAmount . ' ₹';
				}
				else {
					$discount = $row->discount . ' %';
				}
				$actionHtml = '<div class="btn-group">
					<button type="button" class="btn btn-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<i class="ti-settings"></i>
					</button>
					<div class="dropdown-menu animated slideInUp" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 35px, 0px);">
						<a class="dropdown-item  blue" data-toggle="modal" data-target="#responsive-modal" data-seq="' . $row->code . '" href><i class="ti-eye"></i> Open</a>
						<a class="dropdown-item" href="' . base_url('Couponoffer/edit/' . $row->code) . '"><i class="ti-pencil-alt"></i> Edit</a>
						<a class="dropdown-item  mywarning" data-seq="' . $row->code . '" id="' . $row->code . '"><i class="ti-trash" href></i> Delete</a>
					</div>
				</div>';
				$data[] = array(
					$srno,
					$row->code,
					$row->coupanCode,
					ucfirst($offerType),
					$discount,
					$row->minimumAmount,
					$status,
					$actionHtml
				);
				$srno++;
			}
			$dataCount = sizeof($this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, '', '', $groupByColumn, $extraCondition)->result());
		}
		$output = array(
			"draw" => intval($this->input->post("draw") ?? $this->input->get("draw") ?? 0),
			"recordsTotal" => $dataCount,
			"recordsFiltered" => $dataCount,
			"data" => $data,
			'r' => $r
		);
		echo json_encode($output);
	}

	public function save()
	{
		$startDate = DateTime::createFromFormat('d/m/Y', $this->input->post("startDate"))->format('Y-m-d');
		$endDate = DateTime::createFromFormat('d/m/Y', $this->input->post("endDate"))->format('Y-m-d');

		$coupanCode = trim($this->input->post("coupanCode"));
		$offerType = trim($this->input->post("offerType"));

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
		$text = $role . " " . $userName . ' added new vegitable Offer of Coupan Code "' . $coupanCode . '" from ' . $ip;

		$log_text = array(
			'code' => "demo",
			'addID' => $addID,
			'logText' => $text
		);

		//Activity Track Ends

		$result = $this->GlobalModel->checkDuplicateRecord('coupanCode', $coupanCode, 'vegitableoroffer');

		if ($result == true) {
			$data = array('error_message' => 'Duplicate Coupan Code');
			$this->load->view('dashboard/header');
			$this->load->view('dashboard/couponoffer/add', $data);
			$this->load->view('dashboard/footer');
		}
		else {
			$this->form_validation->set_rules('coupanCode', 'coupanCode', 'trim|required');
			$this->form_validation->set_rules('offerType', 'offerType', 'trim|required');
			//$this->form_validation->set_rules('discount', 'discount', 'trim|required');
			$this->form_validation->set_rules('minimumAmount', 'minimumAmount', 'trim|required');
			$this->form_validation->set_rules('perUserLimit', 'perUserLimit', 'trim|required');
			$this->form_validation->set_rules('startDate', 'startDate', 'trim|required');
			$this->form_validation->set_rules('endDate', 'endDate', 'trim|required');

			if ($offerType == "cap") {
				$this->form_validation->set_rules('discount', 'discount', 'trim|required');
				$this->form_validation->set_rules('capLimit', 'capLimit', 'trim|required');
			}
			if ($offerType == "flat") {
				$this->form_validation->set_rules('flatAmount', 'flatAmount', 'trim|required');
			}
			if ($this->form_validation->run() == FALSE) {
				$data['error_message'] = '* Fields are Required!';
				$this->load->view('dashboard/header');
				$this->load->view('dashboard/couponoffer/add', $data);
				$this->load->view('dashboard/footer');
			}
			else {
				$vendorCode = $addID;
				$data = array(

					'coupanCode' => $coupanCode,
					'offerType' => $offerType,
					'minimumAmount' => trim($this->input->post("minimumAmount")),
					'perUserLimit' => trim($this->input->post("perUserLimit")),
					'startDate' => $startDate,
					'endDate' => $endDate,
					'termsAndConditions' => trim($this->input->post("termsAndConditions")),
					'addID' => $addID,
					'addIP' => $ip,
					'isActive' => trim($this->input->post("isActive")),

				);
				if ($offerType == 'cap') {
					$data['capLimit'] = trim($this->input->post("capLimit"));
					$data['discount'] = trim($this->input->post("discount"));
					$data['flatAmount'] = 0;
				}
				if ($offerType == 'flat') {
					$data['capLimit'] = 0;
					$data['discount'] = trim($this->input->post("flatAmount"));
					$data['flatAmount'] = trim($this->input->post("flatAmount"));
				}
				$code = $this->GlobalModel->addWithoutYear($data, 'vegitableoroffer', 'VCOP');

				if ($code != 'false') {
					$response['status'] = true;
					$response['message'] = "Vegitable Offer Successfully Added.";
					$this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
				}
				else {
					$response['status'] = false;
					$response['message'] = "Failed To Add Offer";
				}
				$this->session->set_flashdata('response', json_encode($response));
				redirect(base_url() . 'couponoffer/listRecords', 'refresh');
			}
		}
	}

	public function update()
	{
		$fromDateFormatS = str_replace('/', '-', $this->input->post("startDate"));
		$startDate = date('Y-m-d', strtotime($fromDateFormatS));

		$fromDateFormat = str_replace('/', '-', $this->input->post("endDate"));
		$endDate = date('Y-m-d', strtotime($fromDateFormat));

		$code = trim($this->input->post("code"));
		$coupanCode = trim($this->input->post("coupanCode"));
		$offerType = trim($this->input->post("offerType"));
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
		$text = $role . " " . $userName . ' updated vegitable offer of "' . $coupanCode . '" of code ' . $code . ' from ' . $ip;

		$log_text = array(
			'code' => "demo",
			'addID' => $addID,
			'logText' => $text
		);

		//Activity Track Ends

		$result = $this->GlobalModel->checkDuplicateRecordNotEqualtoCode('coupanCode', $coupanCode, 'vegitableoroffer', $code);

		if ($result == true) {
			$data = array('error_message' => 'Duplicate Coupan Code');
			$data['query'] = $this->GlobalModel->selectQuery('vegitableoroffer.*', 'vegitableoroffer', array("vegitableoroffer.code" => $code));
			$this->load->view('dashboard/header');
			$this->load->view('dashboard/couponoffer/edit', $data);
			$this->load->view('dashboard/footer');
		}
		else {
			$this->form_validation->set_rules('coupanCode', 'coupanCode', 'trim|required');
			$this->form_validation->set_rules('offerType', 'offerType', 'trim|required');
			//$this->form_validation->set_rules('discount', 'discount', 'trim|required');
			$this->form_validation->set_rules('minimumAmount', 'minimumAmount', 'trim|required');
			$this->form_validation->set_rules('perUserLimit', 'perUserLimit', 'trim|required');
			$this->form_validation->set_rules('startDate', 'startDate', 'trim|required');
			$this->form_validation->set_rules('endDate', 'endDate', 'trim|required');

			if ($offerType == "cap") {
				$this->form_validation->set_rules('discount', 'discount', 'trim|required');
				$this->form_validation->set_rules('capLimit', 'capLimit', 'trim|required');
			}
			if ($offerType == "flat") {
				$this->form_validation->set_rules('flatAmount', 'flatAmount', 'trim|required');
			}

			if ($this->form_validation->run() == FALSE) {
				$data['error_message'] = '* Fields are Required!';
				$data['query'] = $this->GlobalModel->selectQuery('vegitableoroffer.*', 'vegitableoroffer', array("vegitableoroffer.code" => $code));
				$this->load->view('dashboard/header');
				$this->load->view('dashboard/couponoffer/edit', $data);
				$this->load->view('dashboard/footer');
			}
			else {
				$data = array(
					'coupanCode' => $coupanCode,
					'offerType' => $offerType,
					'minimumAmount' => trim($this->input->post("minimumAmount")),
					'perUserLimit' => trim($this->input->post("perUserLimit")),
					'startDate' => $startDate,
					'endDate' => $endDate,
					'termsAndConditions' => trim($this->input->post("termsAndConditions")),
					'editID' => $addID,
					'editIP' => $ip,
					'isActive' => trim($this->input->post("isActive")),

				);
				if ($offerType == 'cap') {
					$data['capLimit'] = trim($this->input->post("capLimit"));
					$data['discount'] = trim($this->input->post("discount"));
					$data['flatAmount'] = 0;
				}
				if ($offerType == 'flat') {
					$data['capLimit'] = 0;
					$data['discount'] = trim($this->input->post("flatAmount"));
					$data['flatAmount'] = trim($this->input->post("flatAmount"));
				}
				$result = $this->GlobalModel->doEdit($data, 'vegitableoroffer', $code);

				if ($result != 'false') {
					$response['status'] = true;
					$response['message'] = "Vegitable Offer Successfully Updated.";
					$this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
				}
				else {
					$response['status'] = false;
					$response['message'] = "Failed To Update Offer";
				}
				$this->session->set_flashdata('response', json_encode($response));
				redirect(base_url() . 'couponoffer/listRecords', 'refresh');
			}
		}
	}

	public function view()
	{
		// $code = $this->input->get('code');		
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
		//Activity Track Ends

		$tableName = "vegitableoroffer";
		$orderColumns = array("vegitableoroffer.*");
		$condition = array("vegitableoroffer.code" => $code);
		$Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition);
		$modelHtml = '<form>';
		$activeStatus = "";
		$photoData = "";
		$benefitsData = "";
		foreach ($Records->result() as $row) {
			if ($row->isActive == "1") {
				$activeStatus = '<span class="label label-sm label-success">Active</span>';
			}
			else {
				$activeStatus = '<span class="label label-sm label-warning">Inactive</span>';
			}

			$modelHtml .= '<div class="form-row">
							<div class="col-md-6 mb-3"><label> <b>Coupan Code :</b></label>
								<input type="text" value="' . $row->coupanCode . '" class="form-control-line"  readonly>
							</div>
							<div class="col-md-6 mb-3"><label> <b>Offer Type :</b> </label>
								<input type="text" class="form-control-line" value="' . ucfirst($row->offerType) . '"  readonly>
							</div> 
						</div>
						 
						<div class="form-row">
							<div class="col-md-4 mb-3"><label> <b>Minimum Amount : </b></label>
								<input type="text" value="' . $row->minimumAmount . '" class="form-control-line"  readonly>
							</div>';
			if ($row->offerType == "cap") {
				$modelHtml .= '<div class="col-md-4 mb-3"><label> <b>Discount (%):</b> </label>
												<input type="text" class="form-control-line" value="' . $row->discount . '"  readonly>
											</div> 
											<div class="col-md-4 mb-3"><label> <b>Cap Limit :</b> </label>
												<input type="text" class="form-control-line" value="' . $row->capLimit . '"  readonly>
											</div>';
			}
			if ($row->offerType == "flat") {
				$modelHtml .= '<div class="col-md-4 mb-3"><label> <b>Flat Amount:</b> </label>
												<input type="text" class="form-control-line" value="' . $row->flatAmount . '"  readonly>
											</div>';
			}
			$modelHtml .= '</div>
						
						<div class="form-row">
							<div class="col-md-4 mb-3"><label> <b>Per User Limit :</b></label>
								<input type="text" value="' . $row->perUserLimit . '" class="form-control-line"  readonly>
							</div>
							<div class="col-md-4 mb-3"><label> <b>Start Date :</b> </label>
								<input type="text" class="form-control-line" value="' . date('d-m-Y', strtotime($row->startDate)) . '"  readonly>
							</div> 
							<div class="col-md-4 mb-3"><label> <b>End Date :</b> </label>
								<input type="text" class="form-control-line" value="' . date('d-m-Y', strtotime($row->endDate)) . '"  readonly>
							</div> 
						</div>
						<div class="form-row">
							<div class="col-md-12 mb-3"><label> <b>Terms And Conditions :</b></label>
							<p>' . $row->termsAndConditions . '</p>
							</div>
						</div>';

			$text = $role . " " . $userName . ' viewed Vegitable Offer "' . $row->coupanCode . '" from ' . $ip;

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
		$dataQ = $this->GlobalModel->selectDataByField('code', $code, 'vegitableoroffer');
		$categoryName = '';

		foreach ($dataQ->result() as $row) {
			$categoryName = $row->coupanCode;
		}

		$text = $role . " " . $userName . ' delete Vegitable Offer Coupan Code "' . $categoryName . '" from ' . $ip;

		$log_text = array(
			'code' => "demo",
			'addID' => $addID,
			'logText' => $text
		);
		$this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');

		$data = array(
			'editID' => $addID,
			'editIP' => $ip,
		);
		$resultData = $this->GlobalModel->delete($code, 'vegitableoroffer');
		if ($resultData == 'true')
			echo true;
		else
			echo false;
	}
}