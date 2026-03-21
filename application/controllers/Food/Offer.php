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
		$this->session_key = $this->session->userdata('key' . SESS_KEY);
		if (!isset($this->session->userdata['logged_in' . $this->session_key]['code'])) {
			redirect('Admin/login', 'refresh');
		}
	}

	public function listRecords()
	{
		$addID = $this->session->userdata['logged_in' . $this->session_key]['code'];
		$data['vendors'] = $this->GlobalModel->selectQuery('vendor.code,vendor.entityName', 'vendor',array(),array('vendor' . '.entityName' => 'ASC'),array(),array(),array(),'','',array(),'');
		$data['coupan'] = $this->GlobalModel->selectQuery('vendoroffer.*', 'vendoroffer');
		$data['offertype'] = $this->GlobalModel->selectQuery('vendoroffer.*', 'vendoroffer');
		$data['discount'] = $this->GlobalModel->selectQuery('vendoroffer.*', 'vendoroffer');
		$data['error'] = $this->session->flashdata('response');
		$this->load->view('dashboard/header');
		$this->load->view('dashboard/vendoroffer/list', $data);
		$this->load->view('dashboard/footer');
	}

 

	public function edit()
	{
		$addID = $this->session->userdata['logged_in' . $this->session_key]['code'];
		$code = $this->uri->segment('4');
		$join = array("vendor"=>"vendoroffer.vendorCode=vendor.code");
		$joinType = array("vendor"=>"inner");
		$data['query'] = $this->GlobalModel->selectQuery('vendoroffer.*,vendor.entityName', 'vendoroffer', array("vendoroffer.code" => $code),array(),$join,$joinType); 
		$this->load->view('dashboard/header');
		$this->load->view('dashboard/vendoroffer/edit', $data);
		$this->load->view('dashboard/footer');
	}

	public function getOfferList()
	{
		$addID = $this->session->userdata['logged_in' . $this->session_key]['code'];
		$coupanCode = $this->input->get('coupanCode');
		$offerType = $this->input->get('offerType');
		$discountCode = $this->input->get('discountCode');
		$vendorCode = $this->input->get('vendorCode'); 
		$tableName = "vendoroffer";
		$orderColumns = array("vendoroffer.*,vendor.entityName");
		$condition = array("vendoroffer.vendorCode" => $vendorCode,"vendoroffer.code" => $coupanCode, "vendoroffer.offerType" => $offerType, 'vendoroffer.discount' => $discountCode);
		$orderBy = array('vendoroffer' . '.id' => 'DESC');
		$join = array("vendor"=>"vendoroffer.vendorCode=vendor.code");
		$joinType = array("vendor"=>"inner");
		$groupByColumn = array();
		$limit = $this->input->GET("length");
		$offset = $this->input->GET("start");  
		$extraCondition = " (vendoroffer.isDelete=0 OR vendoroffer.isDelete IS NULL)";
		$like = array();
		$Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
		$r = $this->db->last_query();
		//echo $r;
		//exit();
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
                
				if ($row->offerType == 'cap') {
                    $offerType = 'Per';
                } else {
                    $offerType = $row->offerType;
                }
				$actionHtml = '<div class="btn-group">
					<button type="button" class="btn btn-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<i class="ti-settings"></i>
					</button>
					<div class="dropdown-menu animated slideInUp" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 35px, 0px);">
						<a class="dropdown-item  blue" data-toggle="modal" data-target="#responsive-modal" data-seq="' . $row->code . '" href><i class="ti-eye"></i> Open</a>
						<a class="dropdown-item" href="' . base_url('Food/Offer/edit/' . $row->code) . '"><i class="ti-pencil-alt"></i> Edit</a> 
					</div>
				</div>';
				 
				if($row->isAdminApproved==1){
					$approved = "<span class='label label-sm label-success'>Yes</span>";
				} else{
					$approved = "<span class='label label-sm label-danger'>No</span>";
				}
				
				if ($row->offerType == 'flat') {
                    $discount = $row->flatAmount . ' ₹';
                } else {
                    $discount = $row->discount . ' %';
                }
 
				$data[] = array(
					$srno,
					$row->code,
					$row->entityName,
					$row->coupanCode,
					ucfirst($offerType),
					$discount,
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
			$this->load->view('dashboard/header');
			$this->load->view('dashboard/vendoroffer/edit', $data);
			$this->load->view('dashboard/footer');
		} else {
		     
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
				$data['query'] = $this->GlobalModel->selectQuery('vendoroffer.*', 'vendoroffer', array("vendoroffer.code" => $code, "vendoroffer.vendorCode" => $addID));
				$this->load->view('dashboard/header');
				$this->load->view('dashboard/vendoroffer/edit', $data);
				$this->load->view('dashboard/footer');
			} else { 
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
					'isAdminApproved' =>trim($this->input->post("isAdminApproved"))
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
				redirect(base_url() . 'Food/Offer/listRecords', 'refresh');
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
							<div class="col-md-6 mb-3"><label> <b>Coupon Code :</b></label>
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
							<div>' . $row->termsAndConditions . '</div>
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
 
}