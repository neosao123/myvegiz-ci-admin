<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Vendor extends CI_Controller
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
		$data['entitycategory'] = $this->GlobalModel->selectQuery('entitycategory.*', 'entitycategory', array('entitycategory.isActive' => 1));
		$data['vendor'] = $this->GlobalModel->selectQuery('vendor.*', 'vendor', array('vendor.isActive' => 1));
		$data['city'] = $this->GlobalModel->selectActiveData('citymaster');
		$data['error'] = $this->session->flashdata('response');
		$this->load->view('dashboard/header');
		$this->load->view('dashboard/vendor/list', $data);
		$this->load->view('dashboard/footer');
	}

	public function getAddressess()
	{
		$code = $this->input->get('cityCode');
		$menusubcategory = $this->GlobalModel->selectQuery('customaddressmaster.code,customaddressmaster.place', 'customaddressmaster', array("customaddressmaster.cityCode" => $code, 'customaddressmaster.isActive' => 1));
		$html = "";
		if ($menusubcategory) {
			$daResult = $menusubcategory->result_array();
			$html = '<option value="">Select Area</option>';
			foreach ($daResult as $r) {
				$html .= '<option value="' . $r['code'] . '">' . $r['place'] . '</option>';
			}
		}
		echo $html;
	}


	public function add()
	{
		$data['entitycategory'] = $this->GlobalModel->selectActiveData('entitycategory');
		$data['cuisines'] = $this->GlobalModel->selectActiveData('cuisinemaster');
		$data['city'] = $this->GlobalModel->selectActiveData('citymaster');
		$table_name = 'tagmaster';
		$orderColumns = array("tagmaster.*");
		$cond = array('tagmaster.isActive' => 1);
		$data['tags'] = $this->GlobalModel->selectQuery($orderColumns, $table_name, $cond);
		$this->load->view('dashboard/header');
		$this->load->view('dashboard/vendor/add', $data);
		$this->load->view('dashboard/footer');
	}

	public function edit()
	{
		$code = $this->uri->segment(3);
		$data['entitycategory'] = $this->GlobalModel->selectActiveData('entitycategory');
		$data['cuisines'] = $this->GlobalModel->selectActiveData('cuisinemaster');
		$data['city'] = $this->GlobalModel->selectActiveData('citymaster');
		$data['cuisineslines'] = $this->GlobalModel->selectQuery('vendorcuisinelineentries.*', "vendorcuisinelineentries", array("vendorcuisinelineentries.vendorCode" => $code));
		$data['query'] = $this->GlobalModel->selectDataById($code, 'vendor');
		$table_name = 'tagmaster';
		$orderColumns = array("tagmaster.*");
		$cond = array('tagmaster.isActive' => 1);
		$data['tags'] = $this->GlobalModel->selectQuery($orderColumns, $table_name, $cond);
		$cityCode = "";
		if ($data['query']) {
			$dbResult = $data['query']->result_array()[0];
			$cityCode = $dbResult['cityCode'];
		}
		$data['customaddress'] = false;
		if ($cityCode != "") {
			$data['customaddress'] = $this->GlobalModel->selectQuery('customaddressmaster.*', "customaddressmaster", array("customaddressmaster.cityCode" => $cityCode, "customaddressmaster.isActive" => 1));
		}
		$this->load->view('dashboard/header');
		$this->load->view('dashboard/vendor/edit', $data);
		$this->load->view('dashboard/footer');
	}

	public function getVendorList_old()
	{
		$tables = array('vendor');

		$requiredColumns = array(
				array('code', 'firstName', 'middleName', 'lastName', 'entityName', 'isActive', 'ownerContact', 'isServiceable')
		);
		$conditions = array();
		$extraConditionColumnNames = array();
		$extraConditions = array();
		$Records = $this->GlobalModel1->make_datatables($tables, $requiredColumns, $conditions, $extraConditionColumnNames, $extraConditions);
		// print_r($Records->result());
		$srno = $_GET['start'] + 1;
		$data = array();
		foreach ($Records->result() as $row) {

			if ($row->isActive_05 == "1") {
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
					<a class="dropdown-item" href="' . base_url() . 'Vendor/view/' . $row->code_00 . '"><i class="ti-eye"></i> Open</a>
					<a class="dropdown-item" href="' . base_url() . 'Vendor/edit/' . $row->code_00 . '"><i class="ti-pencil-alt"></i> Edit</a>
					
					<a class="dropdown-item  mywarning" data-seq="' . $row->code_00 . '" id="' . $row->code_00 . '"><i class="ti-trash" href></i> Delete</a>
				</div>
			</div>';

			$data[] = array(
				$srno,
				$row->code_00,
				$row->firstName_01 . ' ' . $row->middleName_02 . ' ' . $row->lastName_03,
				$row->entityName_04,
				$row->ownerContact_06,
				$row->isServiceable_07,
				$status,
				$actionHtml
			);

			$srno++;
		}
		$dataCount = $this->GlobalModel1->get_all_data($tables, $requiredColumns, $conditions, $extraConditionColumnNames, $extraConditions);
		$output = array(
			"draw" => intval($_GET["draw"]),
			"recordsTotal" => $dataCount,
			"recordsFiltered" => $dataCount,
			"data" => $data
		);
		echo json_encode($output);
	}

	public function getVendorList()
	{
		$code = $this->input->post('vendorCode');
		$ownerContact = $this->input->post('ownerContact');
		$entitycategoryCode = $this->input->post('entitycategoryCode');
		// $endDate=$this->input->post('endDate');

		$search = strtolower($this->input->post("search")['value'] ?? '');
		$tableName = "vendor";
		$orderColumns = array("vendor.*");
		$condition = array('vendor.code' => $code, 'vendor.ownerContact' => $ownerContact, 'vendor.entitycategoryCode' => $entitycategoryCode);
		// $condition=array();
		$orderBy = array('vendor' . '.id' => 'DESC');
		$joinType = array();
		$join = array();
		$groupByColumn = array();
		$limit = $this->input->post("length");
		$offset = $this->input->post("start");
		$extraCondition = "vendor.isDelete=0 OR vendor.isDelete IS NULL";
		// $like = array();
		$like = array('vendor.entityName' => $search . '~both', 'vendor.ownerContact' => $search . '~both', 'vendor.firstName' => $search . '~both', 'vendor.lastName' => $search . '~both', 'vendor.code' => $search . '~both');
		$Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
		$srno = intval($offset) + 1;
		if ($Records) {
			foreach ($Records->result() as $row) {
				if ($row->isActive == 1) {
					$status = "<span class='label label-sm label-success'>Active</span>";
				}
				else {
					$status = "<span class='label label-sm label-warning'>Inactive</span>";
				}

				$actionHtml = '<div class="btn-group">
								<button type="button" class="btn btn-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									<i class="ti-settings"></i>
								</button>
								<div class="dropdown-menu animated slideInUp" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 35px, 0px);">
									<a class="dropdown-item blue" data-toggle="modal" data-target="#responsive-modal" data-seq="' . $row->code . '" href="javascript:void(0)"><i class="ti-eye"></i> Open</a>
									<a class="dropdown-item" href="' . base_url() . 'Vendor/edit/' . $row->code . '"><i class="ti-pencil-alt"></i> Edit</a>
									<a class="dropdown-item" href="' . base_url() . 'Vendor/getVendorHours/' . $row->code . '"><i class="ti-time"></i> Hours</a>
									<a class="dropdown-item  mywarning" data-seq="' . $row->code . '" id="' . $row->code . '"><i class="ti-trash" href></i> Delete</a>
								</div>
							</div>';

				if ($row->manualIsServiceable == 1) {
					$toggle = '<input type="checkbox" class="toggle" data-size="mini" id="' . $row->code . '" checked>';
				}
				else {
					$toggle = '<input type="checkbox" class="toggle" data-size="mini" id="' . $row->code . '">';
				}
				if ($row->isPopular == 1) {
					$isPopular = "<span class='label label-sm label-success'>Yes</span>";
				}
				else {
					$isPopular = "<span class='label label-sm label-warning'>No</span>";
				}

				$data[] = array(
					$srno,
					$row->code,
					$row->firstName . ' ' . $row->middleName . ' ' . $row->lastName,
					$row->entityName,
					$row->ownerContact,
					$toggle,
					$status,
					$isPopular,
					$actionHtml
				);
				$srno++;
			}
			$dataCount = sizeof($this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, array(), '', '', '', $extraCondition)->result());
			$output = array(
				"draw" => intval($this->input->post("draw")),
				"recordsTotal" => $dataCount,
				"recordsFiltered" => $dataCount,
				"data" => $data
			);
			echo json_encode($output);
		}
		else {
			$dataCount = 0;
			$data = array();
			$output = array(
				"draw" => intval($this->input->post("draw")),
				"recordsTotal" => $dataCount,
				"recordsFiltered" => $dataCount,
				"data" => $data
			);
			echo json_encode($output);
		}
	}

	public function changeServiceable()
	{
		$code = $this->input->post('code');
		$flag = $this->input->post('flag');
		$data = array(
			'manualIsServiceable' => $flag,
			'isServiceable' => $flag,
		);
		$resultData = $this->GlobalModel->doEdit($data, 'vendor', $code);
		if ($resultData == 'true')
			echo true;
		else
			echo false;
	}

	public function save()
	{
		// $firstName= strtoupper($this->input->post("firstName"));
		// $lastName= strtoupper($this->input->post("lastName"));
		$entityName = trim($this->input->post("entityName"));
		$ownerContact = trim($this->input->post("ownerContact"));

		$gstApplicable = trim($this->input->post('gstApplicable'));
		$gstPercent = trim($this->input->post('gstPercent'));

		$bankDetails['beneficiaryName'] = trim($this->input->post('beneficiaryName'));
		$bankDetails['bankName'] = trim($this->input->post('bankName'));
		$bankDetails['accountNumber'] = trim($this->input->post('accountNumber'));
		$bankDetails['ifscCode'] = trim($this->input->post('ifscCode'));

		$bankDetails = json_encode($bankDetails);

		$cityCode = trim($this->input->post("cityCode"));
		$addressCode = trim($this->input->post("addressCode"));

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
		$text = $role . " " . $userName . ' added new Vendor "' . $entityName . '" from ' . $ip;

		$log_text = array(
			'code' => "demo",
			'addID' => $addID,
			'logText' => $text
		);

		//Activity Track Ends
		$isPopular = 0;
		$result = $this->GlobalModel->checkDuplicateRecord('entityName', $entityName, 'vendor');
		$result1 = $this->GlobalModel->checkDuplicateRecord('ownerContact', $ownerContact, 'vendor');
		if ($this->input->post("isPopular") == 1)
			$isPopular = 1;
		if ($result == true) {
			$data['entitycategory'] = $this->GlobalModel->selectActiveData('entitycategory');
			$data['cuisines'] = $this->GlobalModel->selectActiveData('cuisinemaster');
			$data['city'] = $this->GlobalModel->selectActiveData('citymaster');
			$table_name = 'tagmaster';
			$orderColumns = array("tagmaster.*");
			$cond = array('tagmaster.isActive' => 1);
			$data['tags'] = $this->GlobalModel->selectQuery($orderColumns, $table_name, $cond);
			$data = array('error_message' => 'Duplicate Entity Name');
			$this->load->view('dashboard/header');
			$this->load->view('dashboard/vendor/add', $data);
			$this->load->view('dashboard/footer');
		}
		else if ($result1 == true) {
			$data['entitycategory'] = $this->GlobalModel->selectActiveData('entitycategory');
			$data['cuisines'] = $this->GlobalModel->selectActiveData('cuisinemaster');
			$data['city'] = $this->GlobalModel->selectActiveData('citymaster');
			$table_name = 'tagmaster';
			$orderColumns = array("tagmaster.*");
			$cond = array('tagmaster.isActive' => 1);
			$data['tags'] = $this->GlobalModel->selectQuery($orderColumns, $table_name, $cond);
			$data = array('error_message' => 'Duplicate Owner Contact');
			$this->load->view('dashboard/header');
			$this->load->view('dashboard/vendor/add', $data);
			$this->load->view('dashboard/footer');
		}
		else {
			$this->form_validation->set_rules('firstName', 'First Name', 'trim|required');
			$this->form_validation->set_rules('lastName', 'last Name', 'trim|required');
			$this->form_validation->set_rules('entityName', 'Entity Name', 'trim|required');
			$this->form_validation->set_rules('address', 'address', 'trim|required');
			$this->form_validation->set_rules('fssaiNumber', 'fssaiNumber', 'trim|required');
			$this->form_validation->set_rules('latitude', 'latitude', 'trim|required');
			$this->form_validation->set_rules('longitude', 'longitude', 'trim|required');
			$this->form_validation->set_rules('ownerContact', 'ownerContact', 'trim|required');
			$this->form_validation->set_rules('cityCode', 'city', 'trim|required');
			$this->form_validation->set_rules('addressCode', 'address', 'trim|required');
			if ($this->form_validation->run() == FALSE) {
				$data['entitycategory'] = $this->GlobalModel->selectActiveData('entitycategory');
				$data['cuisines'] = $this->GlobalModel->selectActiveData('cuisinemaster');
				$data['city'] = $this->GlobalModel->selectActiveData('citymaster');
				$table_name = 'tagmaster';
				$orderColumns = array("tagmaster.*");
				$cond = array('tagmaster.isActive' => 1);
				$data['tags'] = $this->GlobalModel->selectQuery($orderColumns, $table_name, $cond);
				$data['error_message'] = '* Fields are Required!';
				$this->load->view('dashboard/header');
				$this->load->view('dashboard/vendor/add', $data);
				$this->load->view('dashboard/footer');
			}
			else {
				$password = md5('123456');
				$data = array(
					'entityName' => $entityName,
					'firstName' => trim($this->input->post("firstName")),
					'lastName' => trim($this->input->post("lastName")),
					'middleName' => trim($this->input->post("middleName")),
					'address' => trim($this->input->post("address")),
					'fssaiNumber' => trim($this->input->post("fssaiNumber")),
					'gstNumber' => trim($this->input->post("gstNumber")),
					'latitude' => trim($this->input->post("latitude")),
					'longitude' => trim($this->input->post("longitude")),
					'ownerContact' => $this->input->post("ownerContact"),
					'entitycategoryCode' => $this->input->post("entitycategoryCode"),
					'entityContact' => $this->input->post("entityContact"),
					'packagingType' => $this->input->post("packagingType"),
					'cartPackagingPrice' => $this->input->post("cartPackagingPrice"),
					'gstApplicable' => $gstApplicable,
					'gstPercent' => $gstPercent,
					'bankDetails' => $bankDetails,
					'cityCode' => $cityCode,
					'addressCode' => $addressCode,
					'password' => $password,
					'addID' => $addID,
					'addIP' => $ip,
					'isActive' => trim($this->input->post("isActive")),
					'manualIsServiceable' => trim($this->input->post("isServiceable")),
					'isPopular' => $isPopular,
					'isServiceable' => trim($this->input->post("isServiceable"))
				);
				if ($this->input->post("tagCode") == 1) {
					$data['tagCode'] = NULL;
				}
				else {
					$data['tagCode'] = $this->input->post("tagCode");
				}

				$code = $this->GlobalModel->addWithoutYear($data, 'vendor', 'VEND');
				$randomCode = $this->GlobalModel->randomCode(6);

				$dataUpdate['code'] = $randomCode;
				$result = $this->GlobalModel->doEdit($dataUpdate, 'vendor', $code);

				// For Vendor Image file upload 
				if ($code != 'false') {

					$cuisines = $this->input->post('cuisineCode');
					$addressData = array();
					for ($i = 0; $i < sizeof($cuisines); $i++) {
						// $addressData = array('vendorCode' => $code, 'cuisineCode' => $cuisines[$i], 'isActive' => $this->input->post("isActive"));
						$addressData = array('vendorCode' => $randomCode, 'cuisineCode' => $cuisines[$i], 'isActive' => 1);
						$addLineDataResult = $this->GlobalModel->addWithoutYear($addressData, 'vendorcuisinelineentries', 'VCLE');
						if ($addLineDataResult == 'true') {
							$addResultFlag = true;
						}
					}

					// if(! file_exists(FCPATH.'uploads/vendor/'.$code))
					if (!file_exists(FCPATH . 'uploads/vendor/' . $randomCode)) {
						// mkdir(FCPATH.'uploads/vendor/'.$code);
						mkdir(FCPATH . 'uploads/vendor/' . $randomCode);
					}

					$uploadRootDir = 'uploads/';
					// $uploadDir = 'uploads/vendor/'.$code;
					$uploadDir = 'uploads/vendor/' . $randomCode;


					$entityImage = "";
					if (!empty($_FILES['entityImage']['name'])) {
						$tmpFile = $_FILES['entityImage']['tmp_name'];
						$ext = pathinfo($_FILES['entityImage']['name'], PATHINFO_EXTENSION);
						// $filename = $uploadDir.'/'.$code.'-ENT.jpg';
						$filename = $uploadDir . '/' . $randomCode . '-ENT.jpg';
						move_uploaded_file($tmpFile, $filename);
						if (file_exists($filename)) {
							// $entityImage=$code.'-ENT.jpg';
							$entityImage = $randomCode . '-ENT.jpg';
						}
					}
					else {
						$dummyFile = 'file_not_found.png';
						$tmpFile = $uploadRootDir . $dummyFile;
						// $filename = $uploadDir.'/'.$code.'-ENT.jpg';
						$filename = $uploadDir . '/' . $randomCode . '-ENT.jpg';
						copy($tmpFile, $filename);
						if (file_exists($filename)) {
							// $entityImage=$code.'-ENT.jpg';
							$entityImage = $randomCode . '-ENT.jpg';
						}
					}

					$fssaiImage = "";
					if (!empty($_FILES['fssaiImage']['name'])) {
						$tmpFile = $_FILES['fssaiImage']['tmp_name'];
						$ext = pathinfo($_FILES['fssaiImage']['name'], PATHINFO_EXTENSION);
						// $filename = $uploadDir.'/'.$code.'-FSAI.jpg';
						$filename = $uploadDir . '/' . $randomCode . '-FSAI.jpg';
						move_uploaded_file($tmpFile, $filename);
						if (file_exists($filename)) {
							// $fssaiImage=$code.'-FSAI.jpg';
							$fssaiImage = $randomCode . '-FSAI.jpg';
						}
					}

					$gstImage = "";
					if (!empty($_FILES['gstImage']['name'])) {
						$tmpFile = $_FILES['gstImage']['tmp_name'];
						$ext = pathinfo($_FILES['gstImage']['name'], PATHINFO_EXTENSION);
						// $filename = $uploadDir.'/'.$code.'-GST.jpg';
						$filename = $uploadDir . '/' . $randomCode . '-GST.jpg';
						move_uploaded_file($tmpFile, $filename);
						if (file_exists($filename)) {
							// $gstImage=$code.'-GST.jpg';
							$gstImage = $randomCode . '-GST.jpg';
						}
					}

					$subData = array(
						'entityImage' => $entityImage,
						'fssaiImage' => $fssaiImage,
						'gstImage' => $gstImage
					);

					// $this->GlobalModel->doEdit($subData,'vendor',$code);
					$this->GlobalModel->doEdit($subData, 'vendor', $randomCode);
				}

				if ($code != 'false') {
					$response['status'] = true;
					$response['message'] = "Vendor Successfully Added.";
					$this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
				}
				else {
					$response['status'] = false;
					$response['message'] = "Failed To Add Vendor";
				}
				$this->session->set_flashdata('response', json_encode($response));
				redirect(base_url() . 'Vendor/listRecords', 'refresh');
			}
		}
	}


	public function update()
	{
		$code = $this->input->post('code');
		$entityName = trim($this->input->post("entityName"));
		$ownerContact = trim($this->input->post("ownerContact"));

		$gstApplicable = trim($this->input->post('gstApplicable'));
		$gstPercent = trim($this->input->post('gstPercent'));

		$gstApplicable = trim($this->input->post('gstApplicable'));
		$gstPercent = trim($this->input->post('gstPercent'));


		$bankDetails['beneficiaryName'] = trim($this->input->post('beneficiaryName'));
		$bankDetails['bankName'] = trim($this->input->post('bankName'));
		$bankDetails['accountNumber'] = trim($this->input->post('accountNumber'));
		$bankDetails['ifscCode'] = trim($this->input->post('ifscCode'));

		$bankDetails = json_encode($bankDetails);

		$cityCode = trim($this->input->post("cityCode"));
		$addressCode = trim($this->input->post("addressCode"));

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
		$text = $role . " " . $userName . ' uopdated Vendor "' . $entityName . '" from ' . $ip;

		$log_text = array(
			'code' => "demo",
			'addID' => $addID,
			'logText' => $text
		);

		//Activity Track Ends 
		$result = $this->GlobalModel->checkDuplicateRecordNotEqualtoCode('entityName', $entityName, 'vendor', $code);
		$result1 = $this->GlobalModel->checkDuplicateRecordNotEqualtoCode('ownerContact', $ownerContact, 'vendor', $code);
		$isPopular = 0;
		if ($this->input->post("isPopular") == 1)
			$isPopular = 1;
		if ($result == true) {
			$data['entitycategory'] = $this->GlobalModel->selectActiveData('entitycategory');
			$data = array('error_message' => 'Duplicate Entity name');
			$data['cuisines'] = $this->GlobalModel->selectActiveData('cuisinemaster');
			$data['city'] = $this->GlobalModel->selectActiveData('citymaster');
			$data['cuisineslines'] = $this->GlobalModel->selectQuery('vendorcuisinelineentries.*', "vendorcuisinelineentries", array("vendorcuisinelineentries.vendorCode" => $code));
			$data['query'] = $this->GlobalModel->selectDataById($code, 'vendor');
			$table_name = 'tagmaster';
			$orderColumns = array("tagmaster.*");
			$cond = array('tagmaster.isActive' => 1);
			$data['tags'] = $this->GlobalModel->selectQuery($orderColumns, $table_name, $cond);
			$cityCode = "";
			if ($data['query']) {
				$dbResult = $data['query']->result_array()[0];
				$cityCode = $dbResult['cityCode'];
			}
			$data['customaddress'] = false;
			if ($cityCode != "") {
				$data['customaddress'] = $this->GlobalModel->selectQuery('customaddressmaster.*', "customaddressmaster", array("customaddressmaster.cityCode" => $cityCode, "customaddressmaster.isActive" => 1));
			}
			$this->load->view('dashboard/header');
			$this->load->view('dashboard/vendor/edit', $data);
			$this->load->view('dashboard/footer');
		}
		else if ($result1 == true) {
			$data['entitycategory'] = $this->GlobalModel->selectActiveData('entitycategory');
			$data = array('error_message' => 'Duplicate Owner Contact');
			$data['cuisines'] = $this->GlobalModel->selectActiveData('cuisinemaster');
			$data['city'] = $this->GlobalModel->selectActiveData('citymaster');
			$data['cuisineslines'] = $this->GlobalModel->selectQuery('vendorcuisinelineentries.*', "vendorcuisinelineentries", array("vendorcuisinelineentries.vendorCode" => $code));
			$data['query'] = $this->GlobalModel->selectDataById($code, 'vendor');
			$table_name = 'tagmaster';
			$orderColumns = array("tagmaster.*");
			$cond = array('tagmaster.isActive' => 1);
			$data['tags'] = $this->GlobalModel->selectQuery($orderColumns, $table_name, $cond);
			$cityCode = "";
			if ($data['query']) {
				$dbResult = $data['query']->result_array()[0];
				$cityCode = $dbResult['cityCode'];
			}
			$data['customaddress'] = false;
			if ($cityCode != "") {
				$data['customaddress'] = $this->GlobalModel->selectQuery('customaddressmaster.*', "customaddressmaster", array("customaddressmaster.cityCode" => $cityCode, "customaddressmaster.isActive" => 1));
			}
			$this->load->view('dashboard/header');
			$this->load->view('dashboard/vendor/edit', $data);
			$this->load->view('dashboard/footer');
		}
		else {
			$this->form_validation->set_rules('firstName', 'First Name', 'trim|required');
			$this->form_validation->set_rules('lastName', 'last Name', 'trim|required');
			$this->form_validation->set_rules('entityName', 'Entity Name', 'trim|required');
			$this->form_validation->set_rules('address', 'address', 'trim|required');
			$this->form_validation->set_rules('fssaiNumber', 'fssaiNumber', 'trim|required');
			$this->form_validation->set_rules('latitude', 'latitude', 'trim|required');
			$this->form_validation->set_rules('longitude', 'longitude', 'trim|required');
			$this->form_validation->set_rules('ownerContact', 'ownerContact', 'trim|required');
			$this->form_validation->set_rules('cityCode', 'city', 'trim|required');
			$this->form_validation->set_rules('addressCode', 'address', 'trim|required');
			if ($this->form_validation->run() == FALSE) {
				$data['entitycategory'] = $this->GlobalModel->selectActiveData('entitycategory');
				$data['error_message'] = '* Fields are Required!';
				$data['cuisines'] = $this->GlobalModel->selectActiveData('cuisinemaster');
				$data['city'] = $this->GlobalModel->selectActiveData('citymaster');
				$data['cuisineslines'] = $this->GlobalModel->selectQuery('vendorcuisinelineentries.*', "vendorcuisinelineentries", array("vendorcuisinelineentries.vendorCode" => $code));
				$data['query'] = $this->GlobalModel->selectDataById($code, 'vendor');
				$cityCode = "";
				if ($data['query']) {
					$dbResult = $data['query']->result_array()[0];
					$cityCode = $dbResult['cityCode'];
				}
				$table_name = 'tagmaster';
				$orderColumns = array("tagmaster.*");
				$cond = array('tagmaster.isActive' => 1);
				$data['tags'] = $this->GlobalModel->selectQuery($orderColumns, $table_name, $cond);
				$data['customaddress'] = false;
				if ($cityCode != "") {
					$data['customaddress'] = $this->GlobalModel->selectQuery('customaddressmaster.*', "customaddressmaster", array("customaddressmaster.cityCode" => $cityCode, "customaddressmaster.isActive" => 1));
				}
				$this->load->view('dashboard/header');
				$this->load->view('dashboard/vendor/edit', $data);
				$this->load->view('dashboard/footer');
			}
			else {

				$data = array(
					'entityName' => $entityName,
					'firstName' => trim($this->input->post("firstName")),
					'lastName' => trim($this->input->post("lastName")),
					'middleName' => trim($this->input->post("middleName")),
					'address' => trim($this->input->post("address")),
					'fssaiNumber' => trim($this->input->post("fssaiNumber")),
					'gstNumber' => trim($this->input->post("gstNumber")),
					'latitude' => trim($this->input->post("latitude")),
					'longitude' => trim($this->input->post("longitude")),
					'ownerContact' => $this->input->post("ownerContact"),
					'entitycategoryCode' => $this->input->post("entitycategoryCode"),
					'entityContact' => $this->input->post("entityContact"),
					'packagingType' => $this->input->post("packagingType"),
					'cartPackagingPrice' => $this->input->post("cartPackagingPrice"),
					'gstApplicable' => $gstApplicable,
					'gstPercent' => $gstPercent,
					'bankDetails' => $bankDetails,
					'cityCode' => $cityCode,
					'addressCode' => $addressCode,
					'editID' => $addID,
					'editIP' => $ip,
					'isActive' => trim($this->input->post("isActive")),
					'manualIsServiceable' => trim($this->input->post("isServiceable")),
					'isPopular' => $isPopular,
				);
				if ($this->input->post("tagCode") == 1) {
					$data['tagCode'] = NULL;
				}
				else {
					$data['tagCode'] = $this->input->post("tagCode");
				}

				$result = $this->GlobalModel->doEdit($data, 'vendor', $code);

				//multiple address area assign code
				$userAddrLine = $this->GlobalModel->deleteForeverFromField('vendorCode', $code, 'vendorcuisinelineentries');
				$cuisines = $this->input->post('cuisineCode');
				$addressData = array();
				$addResultFlag = false;
				$adi = 0;
				if (!empty($cuisines)) {
					for ($i = 0; $i < sizeOf($cuisines); $i++) {
						$addressData = array('vendorCode' => $code, 'cuisineCode' => $cuisines[$i]);
						$addLineDataResult = $this->GlobalModel->addWithoutYear($addressData, 'vendorcuisinelineentries', 'VCLE');
						if ($addLineDataResult != 'false') {
							$adi++;
						}
					}
				}
				if ($adi > 0) {
					$addResultFlag = true;
				}


				$uploadRootDir = 'uploads/';
				$uploadDir = 'uploads/vendor/' . $code;
				$resultFlag = "0";

				if (!file_exists(FCPATH . 'uploads/vendor/' . $code)) {
					mkdir(FCPATH . 'uploads/vendor/' . $code);
				}

				$entityImageOld = $this->input->post('entityImageOld');
				$entityImage = '';
				// For Bank Passbook file upload 
				if (!empty($_FILES['entityImage']['name'])) {
					if ($entityImageOld != "") {
						unlink('uploads/vendor/' . $code . '/' . $entityImageOld);
					}
					$tmpFile = $_FILES['entityImage']['tmp_name'];
					$ext = pathinfo($_FILES['entityImage']['name'], PATHINFO_EXTENSION);
					// $filename = $uploadDir.'/'.$code.'-ENT.jpg';
					$tnm = $code . '-ENT' . time() . '.jpg';
					$filename = $uploadDir . '/' . $tnm;
					move_uploaded_file($tmpFile, $filename);
					if (file_exists($filename)) {
						// $entityImage=$code.'-ENT'.time().'.jpg';
						$entityImage = $tnm;
					}
				}
				else {
					$entityImage = $entityImageOld;
				}

				$fssaiImageOld = $this->input->post('fssaiImageOld');
				$fssaiImage = "";
				if (!empty($_FILES['fssaiImage']['name'])) {
					if ($fssaiImageOld != "") {
						unlink('uploads/vendor/' . $code . '/' . $fssaiImageOld);
					}
					$tmpFile = $_FILES['fssaiImage']['tmp_name'];
					$ext = pathinfo($_FILES['fssaiImage']['name'], PATHINFO_EXTENSION);
					// $filename = $uploadDir.'/'.$code.'-FSAI.jpg';
					$tnm = $code . '-FSAI' . time() . '.jpg';
					$filename = $uploadDir . '/' . $tnm;
					move_uploaded_file($tmpFile, $filename);
					if (file_exists($filename)) {
						// $fssaiImage=$code.'-FSAI.jpg';
						$fssaiImage = $tnm;
					}
				}
				else {
					$fssaiImage = $fssaiImageOld;
				}

				$gstImageOld = $this->input->post('gstImageOld');
				$gstImage = "";
				if (!empty($_FILES['gstImage']['name'])) {
					if ($gstImageOld != "") {
						unlink('uploads/vendor/' . $code . '/' . $gstImageOld);
					}
					$tmpFile = $_FILES['gstImage']['tmp_name'];
					$ext = pathinfo($_FILES['gstImage']['name'], PATHINFO_EXTENSION);
					// $filename = $uploadDir.'/'.$code.'-GST.jpg';
					$tnm = $code . '-GST' . time() . '.jpg';
					$filename = $uploadDir . '/' . $tnm;
					move_uploaded_file($tmpFile, $filename);
					if (file_exists($filename)) {
						// $gstImage=$code.'-GST.jpg';
						$gstImage = $tnm;
					}
				}
				else {
					$gstImage = $gstImageOld;
				}

				$subData = array(
					'entityImage' => $entityImage,
					'fssaiImage' => $fssaiImage,
					'gstImage' => $gstImage,
				);

				$this->GlobalModel->doEdit($subData, 'vendor', $code);
				$resultFlag = "1";

				if ($result != 'false' || $resultFlag == '1' || $addResultFlag == true) {
					$response['status'] = true;
					$response['message'] = "Vendor Successfully Updated.";
					$this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
				}
				else {
					$response['status'] = false;
					$response['message'] = "No change In Vendor";
				}

				$this->session->set_flashdata('response', json_encode($response));
				redirect(base_url() . 'Vendor/listRecords', 'refresh');
			}
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
		$dataQ = $this->GlobalModel->selectDataByField('code', $code, 'vendor');
		$categoryName = '';

		foreach ($dataQ->result() as $row) {
			$categoryName = $row->entityName;
		}

		$text = $role . " " . $userName . ' deleted vendor "' . $categoryName . '" from ' . $ip;

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

		$this->db->query("delete from vendorcuisinelineentries where vendorCode='" . $code . "'");

		$resultData = $this->GlobalModel->doEdit($data, 'vendor', $code);

		echo $this->GlobalModel->delete($code, 'vendor');
	}

	public function view()
	{
		$code = $this->uri->segment(3);
		if (!$code)
			$code = $this->input->post('code');
		$orderColumns = "vendor.*,citymaster.cityName,customaddressmaster.place";
		$condition = array("vendor.code" => $code);
		$join = array("citymaster" => "vendor.cityCode=citymaster.code", "customaddressmaster" => "vendor.addressCode=customaddressmaster.code");
		$joinType = array("citymaster" => "left", "customaddressmaster" => "left");
		$data['query'] = $this->GlobalModel->selectQuery($orderColumns, 'vendor', $condition, array(), $join, $joinType);
		$entitycategoryCode = $data['query']->result_array()[0]['entitycategoryCode'];
		$table_name = 'tagmaster';
		$orderColumns = array("tagmaster.*");
		$cond = array('tagmaster.isActive' => 1);
		$data['tags'] = $this->GlobalModel->selectQuery($orderColumns, $table_name, $cond);
		$data['entitycategory'] = $this->GlobalModel->selectDataById($entitycategoryCode, 'entitycategory');
		$data['cuisineslines'] = $this->GlobalModel->selectQuery('vendorcuisinelineentries.cuisineCode,cuisinemaster.cuisineName', "vendorcuisinelineentries", array("vendorcuisinelineentries.vendorCode" => $code), array(), array("cuisinemaster" => "cuisinemaster.code=vendorcuisinelineentries.cuisineCode"), array("cuisinemaster" => 'inner'));
		$this->load->view('dashboard/header');
		$this->load->view('dashboard/vendor/view', $data);
		$this->load->view('dashboard/footer');
	}

	public function deleteImage()
	{
		$imgNm = $this->input->post('value');
		$code = $this->input->post('code');
		$type = $this->input->post('type');

		if ($type == 'entityImage') {
			$data = array(
				'entityImage' => '',
			);
		}
		else if ($type == 'gstImage') {
			$data = array(
				'gstImage' => '',
			);
		}
		else if ($type == 'fssaiImage') {
			$data = array(
				'fssaiImage' => '',
			);
		}
		if (!empty($data)) {
			unlink('uploads/vendor/' . $code . '/' . $imgNm);
			echo $resultData = $this->GlobalModel->doEdit($data, 'vendor', $code);
		}
		else {
			echo 'false';
		}

	// echo  $deleteData=$this->GlobalModel->deleteForever($code,'productphotos');
	}

	public function getVendorHours()
	{
		$code = $this->uri->segment(3);
		$condition['vendor.code'] = $code;
		$data['vendor'] = $this->GlobalModel->selectQuery("vendor.*", "vendor", $condition);
		$condition1['vendorhours.vendorCode'] = $code;
		$data['vendorhours'] = $this->GlobalModel->selectQuery("vendorhours.*", "vendorhours", $condition1);
		$this->load->view('dashboard/header');
		$this->load->view('dashboard/vendor/vendorhours', $data);
		$this->load->view('dashboard/footer');
	}

	public function saveHours()
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
		$vendorCode = trim($this->input->post("vendorCode"));
		$weekDay = trim($this->input->post("weekDay"));
		$fromTime = trim($this->input->post("fromTime"));
		$toTime = trim($this->input->post("toTime"));
		$fromTime = date('H:i:s', strtotime($fromTime));
		$toTime = date('H:i:s', strtotime($toTime));

		$ip = $_SERVER['REMOTE_ADDR'];
		$text = $role . " " . $userName . ' added new Open/Accept hours time for "' . $weekDay . '" from ' . $ip;
		$log_text = array(
			'code' => "demo",
			'addID' => $addID,
			'logText' => $text
		);
		$checkTime = $this->db->query("select vendorhours.id from vendorhours where '" . $fromTime . "'>fromTime and '" . $fromTime . "'<toTime and weekday='" . $weekDay . "' and vendorCode='" . $vendorCode . "'");
		if ($checkTime->num_rows() > 0) {
		}
		else {
			$checkTime = $this->db->query("select vendorhours.id from vendorhours where '" . $toTime . "'>fromTime and '" . $toTime . "'<toTime and weekday='" . $weekDay . "' and vendorCode='" . $vendorCode . "'");
		}
		if ($checkTime->num_rows() > 0) {
			$response['status'] = false;
			$response['message'] = 'From time and to time should not be overlapped';
		}
		else {
			$data = array(
				'vendorCode' => $vendorCode,
				'weekDay' => $weekDay,
				'fromTime' => $fromTime,
				'toTime' => $toTime,
				'addID' => $addID,
				'addIP' => $ip,
				'isActive' => 1,
			);
			$code = $this->GlobalModel->addWithoutYear($data, 'vendorhours', 'VHLE');
			if ($code != 'false') {
				$this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
				$response['status'] = true;
				$response['lineCode'] = $code;
				$response['message'] = 'Restaurant Hours Added Successfully';
			}
			else {
				$response['status'] = false;
				$response['message'] = 'Failed to add Restaurant Hours';
			}
		}
		echo json_encode($response);
	}


	public function updateRestaurantHour()
	{
		$code = $this->input->post('lineCode');
		$vendorCode = $this->input->post('vendorCode');
		$weekDay = $this->input->post('weekDay');
		$updateTo = $this->input->post('updateTo');
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
		$invalid = 0;
		$ip = $_SERVER['REMOTE_ADDR'];
		if ($updateTo == "to") {
			$toTime = trim($this->input->post("time"));
			$toTime = date('H:i:s', strtotime($toTime));
			$checkTime = $this->db->query("select vendorhours.id from vendorhours where '" . $toTime . "'>fromTime and '" . $toTime . "'<toTime and weekday='" . $weekDay . "' and vendorCode='" . $vendorCode . "' and code!='" . $code . "'");
			//echo $this->db->last_query();
			if ($checkTime->num_rows() > 0) {
				$invalid = 1;
			}
			else {
				$data = array(
					'toTime' => $toTime,
					'editID' => $addID,
					'editIP' => $ip,
					'isActive' => 1,
				);
			}
		}
		else {
			$fromTime = trim($this->input->post("time"));
			$fromTime = date('H:i:s', strtotime($fromTime));
			$checkTime = $this->db->query("select vendorhours.id from vendorhours where '" . $fromTime . "'>fromTime and '" . $fromTime . "'<toTime and weekday='" . $weekDay . "' and vendorCode='" . $vendorCode . "' and code!='" . $code . "'");
			//echo $this->db->last_query();
			if ($checkTime->num_rows() > 0) {
				$invalid = 1;
			}
			else {
				$data = array(
					'fromTime' => $fromTime,
					'editID' => $addID,
					'editIP' => $ip,
					'isActive' => 1,
				);
			}
		}
		if ($invalid == 1) {
			$response['status'] = false;
			$response['message'] = 'From time and to time should not be overlapped';
		}
		else {
			$code = $this->GlobalModel->doEdit($data, 'vendorhours', $code);
			if ($code != 'false') {
				$response['status'] = true;
				$response['message'] = 'Restaurant Hour updated successfully';
			}
			else {
				$response['status'] = false;
				$response['message'] = 'Failed to update Restaurant Hour';
			}
		}
		echo json_encode($response);
	}

	public function deleteHourLine()
	{
		$code = $this->input->post('lineCode');

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
		$dataQ = $this->GlobalModel->selectDataByField('code', $code, 'vendorhours');
		$categoryName = '';

		foreach ($dataQ->result() as $row) {
			$categoryName = $row->code . ', day => ' . $row->weekDay . ' time => From - ' . $row->fromTime . ' To -' . $row->toTime;
		}

		$text = $role . " " . $userName . ' deleted vendorhours "' . $categoryName . '" from ' . $ip;

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

		$result = $this->db->query("delete from vendorhours where code='" . $code . "'");
		if ($result) {
			$res['status'] = true;
		}
		else {
			$res['status'] = false;
		}
		echo json_encode($res);
	}

	public function checkOverlappingTimings()
	{
		$time = $this->input->post('time') . ':00';
		$weekDay = $this->input->post('weekDay');
		$code = $this->input->post('code');
		$vendorCode = $this->input->post('vendorCode');
		$checkTime = $this->db->selectQuery("select vendorhours.id from vendorhours where ('" . $time . "' between fromTime and toTime) and weekday='" . $weekDay . "' and vendorCode='" . $vendorCode . "' and code!='" . $code . "'");
		if ($checkTime) {
			$response['status'] = true;
		}
		else {
			$response['status'] = false;
		}
		echo json_encode($res);
	}


}
?>