<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Offer extends CI_Controller
{
	var $session_key;
	public function __construct()
	{
		parent::__construct();
		$this->load->helper('form', 'url', 'html');
		$this->load->library('form_validation');
		$this->load->model('GlobalModel');
		$this->load->model('GlobalModel1');
		$this->session_key = $this->session->userdata('key' . SESS_KEY_PARTNER);
	}

	public function listRecords()
	{
		$addID = $this->session->userdata['logged_in' . $this->session_key]['code'];
		$data['coupan'] = $this->GlobalModel->selectQuery('vendoroffer.*', 'vendoroffer');
		$data['offertype'] = $this->GlobalModel->selectQuery('vendoroffer.*', 'vendoroffer');
		$data['discount'] = $this->GlobalModel->selectQuery('vendoroffer.*', 'vendoroffer');
		$data['error'] = $this->session->flashdata('response');
		$this->load->view('header');
		$this->load->view('offer/list', $data);
		$this->load->view('footer');
	}

	public function add()
	{
		$this->load->view('header');
		$this->load->view('offer/add', $data);
		$this->load->view('footer');
	}

	public function edit()
	{
		$addID = $this->session->userdata['logged_in' . $this->session_key]['code'];
		$code = $this->uri->segment('3');
		$data['query'] = $this->GlobalModel->selectQuery('vendoroffer.*', 'vendoroffer', array("vendoroffer.code" => $code, "vendoroffer.vendorCode" => $addID));
		$this->load->view('header');
		$this->load->view('offer/edit', $data);
		$this->load->view('footer');
	}

	public function getOfferList()
	{
		$addID = $this->session->userdata['logged_in' . $this->session_key]['code'];
		$coupanCode = $this->input->get('coupanCode');
		$offerType = $this->input->get('offerType');
		$discountCode = $this->input->get('discountCode');
		$fromDate = $this->input->get('fromDate');
		$toDate = $this->input->get('toDate');

		$tableName = "vendoroffer";
		$orderColumns = array("vendoroffer.*");
		$condition = array('vendoroffer.isActive' => 1, 'vendoroffer.vendorCode' => $addID, 'vendoroffer.code' => $coupanCode, 'vendoroffer.offerType' => $offerType, 'vendoroffer.code' => $discountCode, 'vendoroffer.addDate' => $fromDate, 'vendoroffer.addDate' => $toDate);
		$orderBy = array('vendoroffer' . '.id' => 'DESC');
		$joinType = array();
		$join = array();
		$groupByColumn = array();
		$limit = $this->input->GET("length");
		$offset = $this->input->GET("start");
		$dateCondition = "";
		if ($fromDate != "") {
			$fromDate = DateTime::createFromFormat('d/m/Y', $fromDate)->format('Y-m-d');
			$toDate = DateTime::createFromFormat('d/m/Y', $toDate)->format('Y-m-d');
			$dateCondition = " AND vendoroffer.editDate BETWEEN '" . $fromDate . " 01:00:01' AND '" . $toDate . " 12:59:59'";
		}
		$extraCondition = " (vendoroffer.isDelete=0 OR vendoroffer.isDelete IS NULL)";
		$like = array();
		$Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
		$r = $this->db->last_query();
		$srno = $_GET['start'] + 1;
		$dataCount = 0;
		$data = array();
		if ($Records) {
			foreach ($Records->result() as $row) {
				$code = $row->code;
				if ($row->isActive == 1) {
					$status = "<span class='label label-sm label-success'>Active</span>";
				} else {
					$status = "<span class='label label-sm label-warning'>Inactive</span>";
				}

				$actionHtml = '<div class="btn-group">
					<button type="button" class="btn btn-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<i class="ti-settings"></i>
					</button>
					<div class="dropdown-menu animated slideInUp" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 35px, 0px);">
						<a class="dropdown-item  blue" data-toggle="modal" data-target="#responsive-modal" data-seq="' . $row->code . '" href><i class="ti-eye"></i> Open</a>
						<a class="dropdown-item" href="' . base_url('Offer/edit/' . $row->code) . '"><i class="ti-pencil-alt"></i> Edit</a>
						<a class="dropdown-item  mywarning" data-seq="' . $row->code . '" id="' . $row->code . '"><i class="ti-trash" href></i> Delete</a>
					</div>
				</div>';
				
				
				if($row->isAdminApproved==1){
					$approved = "<span class='label label-sm label-success'>Yes</span>";
				} else{
					$approved = "<span class='label label-sm label-success'>No</span>";
				}


				$data[] = array(
					$srno,
					$row->code,
					$row->coupanCode,
					ucfirst($row->offerType),
					$row->discount,
					$row->minimumAmount,
					$approved,
					$status,
					$actionHtml
				);
				$srno++;
			}
			$dataCount = sizeof($this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, '', '', $groupByColumn, $extraCondition)->result());
		}
		$output = array(
			"draw"			  =>     intval($_GET["draw"]),
			"recordsTotal"    =>     $dataCount,
			"recordsFiltered" =>     $dataCount,
			"data"            =>     $data,
			'r'				  => 	 $r
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
		$text = $role . " " . $userName . ' added new Offer of Coupan Code "' . $coupanCode . '" from ' . $ip;

		$log_text = array(
			'code' => "demo",
			'addID' => $addID,
			'logText' => $text
		);

		//Activity Track Ends

		$result = $this->GlobalModel->checkDuplicateRecord('coupanCode', $coupanCode, 'vendoroffer');

		if ($result == true) {
			$data = array('error_message' => 'Duplicate Coupan Code');
			$this->load->view('header');
			$this->load->view('offer/add', $data);
			$this->load->view('footer');
		} else {
			$this->form_validation->set_rules('coupanCode', 'coupanCode', 'trim|required');
			$this->form_validation->set_rules('offerType', 'offerType', 'trim|required');
			$this->form_validation->set_rules('discount', 'discount', 'trim|required');
			$this->form_validation->set_rules('minimumAmount', 'minimumAmount', 'trim|required');
			$this->form_validation->set_rules('perUserLimit', 'perUserLimit', 'trim|required');
			$this->form_validation->set_rules('startDate', 'startDate', 'trim|required');
			$this->form_validation->set_rules('endDate', 'endDate', 'trim|required');

			if ($offerType == "cap") {
				$this->form_validation->set_rules('capLimit', 'capLimit', 'trim|required');
			}
			if ($this->form_validation->run() == FALSE) {
				$data['error_message'] = '* Fields are Required!';
				$this->load->view('header');
				$this->load->view('offer/add', $data);
				$this->load->view('footer');
			} else {
				$vendorCode = $addID;
				$data = array(
					'vendorCode' => $addID,
					'coupanCode' => $coupanCode,
					'offerType' => $offerType,
					'discount' => trim($this->input->post("discount")),
					'minimumAmount' => trim($this->input->post("minimumAmount")),
					'perUserLimit' => trim($this->input->post("perUserLimit")),
					'startDate' => $startDate,
					'endDate' => $endDate,
					'capLimit' => trim($this->input->post("capLimit")),
					'termsAndConditions' => trim($this->input->post("termsAndConditions")),
					'addID' => $addID,
					'addIP' => $ip,
					'isActive' => trim($this->input->post("isActive")),
					'isAdminApproved' =>0
				);
				$code = $this->GlobalModel->addWithoutYear($data, 'vendoroffer', 'VOFF');

				if ($code != 'false') {
					$response['status'] = true;
					$response['message'] = "Offer Successfully Added.";
					$this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
				} else {
					$response['status'] = false;
					$response['message'] = "Failed To Add Offer";
				}
				$this->session->set_flashdata('response', json_encode($response));
				redirect(base_url() . 'Offer/listRecords', 'refresh');
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
		$text = $role . " " . $userName . ' updated offer of "' . $coupanCode . '" of code ' . $code . ' from ' . $ip;

		$log_text = array(
			'code' => "demo",
			'addID' => $addID,
			'logText' => $text
		);

		//Activity Track Ends

		$result = $this->GlobalModel->checkDuplicateRecordNotEqualtoCode('coupanCode', $coupanCode, 'vendoroffer', $code);

		if ($result == true) {
			$data = array('error_message' => 'Duplicate Coupan Code');
			$data['query'] = $this->GlobalModel->selectQuery('vendoroffer.*', 'vendoroffer', array("vendoroffer.code" => $code, "vendoroffer.vendorCode" => $addID));
			$this->load->view('header');
			$this->load->view('offer/edit', $data);
			$this->load->view('footer');
		} else {
			$this->form_validation->set_rules('coupanCode', 'coupanCode', 'trim|required');
			$this->form_validation->set_rules('offerType', 'offerType', 'trim|required');
			$this->form_validation->set_rules('discount', 'discount', 'trim|required');
			$this->form_validation->set_rules('minimumAmount', 'minimumAmount', 'trim|required');
			$this->form_validation->set_rules('perUserLimit', 'perUserLimit', 'trim|required');
			$this->form_validation->set_rules('startDate', 'startDate', 'trim|required');
			$this->form_validation->set_rules('endDate', 'endDate', 'trim|required');

			if ($offerType == "cap") {
				$this->form_validation->set_rules('capLimit', 'capLimit', 'trim|required');
			}

			if ($this->form_validation->run() == FALSE) {
				$data['error_message'] = '* Fields are Required!';
				$data['query'] = $this->GlobalModel->selectQuery('vendoroffer.*', 'vendoroffer', array("vendoroffer.code" => $code, "vendoroffer.vendorCode" => $addID));
				$this->load->view('header');
				$this->load->view('offer/edit', $data);
				$this->load->view('footer');
			} else {
				$vendorCode = $addID;

				$data = array(
					'coupanCode' => $coupanCode,
					'offerType' => $offerType,
					'discount' => trim($this->input->post("discount")),
					'minimumAmount' => trim($this->input->post("minimumAmount")),
					'perUserLimit' => trim($this->input->post("perUserLimit")),
					'startDate' => $startDate,
					'endDate' => $endDate,
					'capLimit' => trim($this->input->post("capLimit")),
					'termsAndConditions' => trim($this->input->post("termsAndConditions")),
					'editID' => $addID,
					'editIP' => $ip,
					'isActive' => trim($this->input->post("isActive")),
					'isAdminApproved' =>0
				);

				$result = $this->GlobalModel->doEdit($data, 'vendoroffer', $code);

				if ($result != 'false') {
					$response['status'] = true;
					$response['message'] = "Offer Successfully Updated.";
					$this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
				} else {
					$response['status'] = false;
					$response['message'] = "Failed To Update Offer";
				}
				$this->session->set_flashdata('response', json_encode($response));
				redirect(base_url() . 'Offer/listRecords', 'refresh');
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

		$tableName = "vendoroffer";
		$orderColumns = array("vendoroffer.*");
		$condition = array("vendoroffer.code" => $code);
		$Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition);
		$modelHtml = '<form>';
		$activeStatus = "";
		$photoData = "";
		$benefitsData = "";
		foreach ($Records->result() as $row) {
			if ($row->isActive == "1") {
				$activeStatus = '<span class="label label-sm label-success">Active</span>';
			} else {
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
							<div class="col-md-4 mb-3"><label> <b>Discount (%):</b> </label>
								<input type="text" class="form-control-line" value="' . $row->discount . '"  readonly>
							</div> 
							<div class="col-md-4 mb-3"><label> <b>Minimum Amount : </b></label>
								<input type="text" value="' . $row->minimumAmount . '" class="form-control-line"  readonly>
							</div>';
			if ($row->offerType == "cap") {
				$modelHtml .= '<div class="col-md-4 mb-3"><label> <b>Cap Limit :</b> </label>
								<input type="text" class="form-control-line" value="' . $row->capLimit . '"  readonly>
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
							<textarea class="form-control-line">"' . $row->termsAndConditions . '"</textarea>
							</div>
						</div>';

			$text = $role . " " . $userName . ' viewed Offer "' . $row->coupanCode . '" from ' . $ip;

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
		$dataQ = $this->GlobalModel->selectDataByField('code', $code, 'vendoroffer');
		$categoryName = '';

		foreach ($dataQ->result() as $row) {
			$categoryName = $row->coupanCode;
		}

		$text = $role . " " . $userName . ' delete Offer Coupan Code "' . $categoryName . '" from ' . $ip;

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
		$resultData = $this->GlobalModel->delete($code, 'vendoroffer');
		if ($resultData == 'true') echo true;
		else echo false;
	}
}