<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Vendoritem extends CI_Controller
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
		$data['vendor'] = $this->GlobalModel->selectQuery('vendor.*', 'vendor', array('vendor.isActive' => 1));
		$data['menucategory'] = $this->GlobalModel->selectQuery('menucategory.*', 'menucategory', array('menucategory.isActive' => 1));
		$data['itemstatus'] = $this->GlobalModel->selectQuery('vendoritemmaster.*', 'vendoritemmaster');
		//$data['itemactivestatus'] = $this->GlobalModel->selectQuery('vendoritemmaster.*','vendoritemmaster',array('vendoritemmaster.isActive'=>1));
		$data['menuitem'] = $this->GlobalModel->selectQuery('vendoritemmaster.*', 'vendoritemmaster', array('vendoritemmaster.isActive' => 1));
		$data['itemtype'] = $this->GlobalModel->selectQuery('vendoritemmaster.*', 'vendoritemmaster', array('vendoritemmaster.isActive' => 1));
		$data['fromDate'] = $this->GlobalModel->selectQuery('vendoritemmaster.*', 'vendoritemmaster', array('vendoritemmaster.isActive' => 1));
		$data['toDate'] = $this->GlobalModel->selectQuery('vendoritemmaster.*', 'vendoritemmaster', array('vendoritemmaster.isActive' => 1));
		$data['error'] = $this->session->flashdata('response');
		$this->load->view('dashboard/header');
		$this->load->view('dashboard/vendoritem/list', $data);
		$this->load->view('dashboard/footer');
	}

	public function add()
	{
		$data['vendormaster'] = $this->GlobalModel->selectActiveData('vendor');
		$data['menucategory'] = $this->GlobalModel->selectActiveData('menucategory');
		$this->load->view('dashboard/header');
		$this->load->view('dashboard/vendoritem/add', $data);
		$this->load->view('dashboard/footer');
	}

	public function edit()
	{
		$code = $this->uri->segment('4');
		$data['query'] = $this->GlobalModel->selectDataById($code, 'vendoritemmaster');
		$menuCode = $data['query']->result_array()[0]['menuCategoryCode'];
		$data['vendormaster'] = $this->GlobalModel->selectActiveData('vendor');
		$data['menucategory'] = $this->GlobalModel->selectActiveData('menucategory');
		$data['menusubcategory'] = $this->GlobalModel->selectQuery('menusubcategory.code,menusubcategory.menuSubCategoryName', 'menusubcategory', array("menusubcategory.menuCategoryCode" => $menuCode, "menusubcategory.isActive" => 1));
		$this->load->view('dashboard/header');
		$this->load->view('dashboard/vendoritem/edit', $data);
		$this->load->view('dashboard/footer');
	}

	public function getVendorItemList()
	{
		$menucategoryCode = $this->input->get('menucategoryCode');
		$itemCode = $this->input->get('itemCode');
		$startDate = $this->input->get('startDate');
		$endDate = $this->input->get('endDate');
		$vendorCode = $this->input->get('vendorCode');
		$itemtype = $this->input->get('itemtype');
		$itemstatus = $this->input->get('itemstatus');
		//$itemactivestatus=$this->input->get('itemactivestatus');
		// $fromDate=$this->input->get('fromDate');
		// $toDate=$this->input->get('toDate');

		$tableName = "vendoritemmaster";
		$orderColumns = array("vendoritemmaster.*,vendor.entityName,menucategory.menuCategoryName");
		$condition = array(
			'menucategory.isActive' => 1, 
			'vendoritemmaster.isActive' => 1,
			"vendoritemmaster.menuCategoryCode" => $menucategoryCode, 
			"vendoritemmaster.isActive" => $itemstatus, 
			"vendoritemmaster.vendorCode" => $vendorCode, 
			"vendoritemmaster.code" => $itemCode, 
			"vendoritemmaster.cuisineType" => $itemtype
		);
		$orderBy = array('vendoritemmaster' . '.id' => 'DESC');
		$joinType = array("vendor" => 'inner', "menucategory" => 'inner', "vendoritemmaster" => 'inner');
		$join = array("vendor" => "vendoritemmaster.vendorCode=vendor.code", "menucategory" => "vendoritemmaster.menuCategoryCode=menucategory.code");
		$groupByColumn = array();
		$limit = $this->input->GET("length");
		$offset = $this->input->GET("start");
		$extraCondition = " (vendoritemmaster.isDelete=0 OR vendoritemmaster.isDelete IS NULL)";
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
				if ($row->itemActiveStatus == 1) {
					$itemActiveStatus = "<span class='label label-sm label-success'>Active</span>";
				} else {
					$itemActiveStatus = "<span class='label label-sm label-danger'>Inactive</span>";
				}
				if ($row->isAdminApproved == 1) {
					$isAdminApproved = "<span class='label label-sm label-success'>Yes</span>";
				} else {
					$isAdminApproved = "<span class='label label-sm label-danger'>No</span>";
				}

				$actionHtml = '<div class="btn-group">
					<button type="button" class="btn btn-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<i class="ti-settings"></i>
					</button>
					<div class="dropdown-menu animated slideInUp" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 35px, 0px);">
						<a class="dropdown-item" href="' . base_url() . 'Food/Vendoritem/view/' . $row->code . '"><i class="ti-eye"></i> Open</a>
						<a class="dropdown-item" href="' . base_url() . 'Food/Vendoritem/edit/' . $row->code . '"><i class="ti-pencil-alt"></i> Edit</a>
						<a class="dropdown-item" href="' . base_url() . 'Food/Vendoritem/customizeaddon/' . $row->code . '"><i class="ti-eye"></i> Add Ons</a>
						<a class="dropdown-item  mywarning" data-seq="' . $row->code . '" id="' . $row->code . '"><i class="ti-trash" href></i> Delete</a>
					</div>
				</div>';
				/*  $dateCondition = "";
        if ($fromDate != "")
        {
            $fromDate = DateTime::createFromFormat('d/m/Y', $fromDate)->format('Y-m-d');
            $toDate = DateTime::createFromFormat('d/m/Y', $toDate)->format('Y-m-d');
            $dateCondition = "'" . $fromDate . " 01:00:01' AND '" . $toDate . " 12:59:59'";
        }
			$dateCondition;	*/
				$data[] = array(
					$srno,
					$row->code,
					$row->itemName,
					$row->entityName,
					$row->menuCategoryName,
					$status,
					$itemActiveStatus,
					$isAdminApproved,
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
		$itemName = trim($this->input->post("itemName"));
		$itemName = ucwords(strtolower($itemName));
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
		$text = $role . " " . $userName . ' added new Vendor item "' . $itemName . '" from ' . $ip;

		$log_text = array(
			'code' => "demo",
			'addID' => $addID,
			'logText' => $text
		);
		//Activity Track Ends

	
		$this->form_validation->set_rules('itemName', 'Item Name', 'trim|required');
		//$this->form_validation->set_rules('itemDescription', 'Item Description', 'trim|required');
		$this->form_validation->set_rules('cuisineType', 'Cuisine Type', 'trim|required');
		$this->form_validation->set_rules('menuCategoryCode', 'Menu Category', 'trim|required');
		$this->form_validation->set_rules('vendorCode', 'Vendor', 'trim|required');
		$this->form_validation->set_rules('salePrice', 'salePrice', 'trim|required');
		if ($this->form_validation->run() == FALSE) {
			$data['vendormaster'] = $this->GlobalModel->selectActiveData('vendor');
			$data['menucategory'] = $this->GlobalModel->selectActiveData('menucategory');
			$data['error_message'] = '* Fields are Required!';
			$this->load->view('dashboard/header');
			$this->load->view('dashboard/vendoritem/add', $data);
			$this->load->view('dashboard/footer');
		} else {
			$vendorCode = trim($this->input->post("vendorCode"));
			$maxOrderQty = trim($this->input->post("maxOrderQty"));
			if ($maxOrderQty == 0 || $maxOrderQty == "") $maxOrderQty = 1;
			//check same item exists with   
			/*
			$conditionArray["vendoritemmaster.vendorCode"]=$vendorCode;
			$conditionArray["vendoritemmaster.itemName"]=$itemName;
			$result = $this->GlobalModel->checkDuplicateRecordNew($conditionArray,'vendoritemmaster');
			if($result==true) 
			{
				$data['vendormaster']=$this->GlobalModel->selectActiveData('vendor');    
				$data['menucategory']=$this->GlobalModel->selectActiveData('menucategory');
				$data = array('error_message' => 'Duplicate Item Name');
				$this->load->view('dashboard/header');
				$this->load->view('dashboard/vendoritem/add', $data);
				$this->load->view('dashboard/footer');
			}
			else
			{
			*/				
				$data = array(
					'itemName' => $itemName,
					'itemDescription' => trim($this->input->post("itemDescription")),
					'vendorCode' => trim($this->input->post("vendorCode")),
					'menuCategoryCode' => trim($this->input->post("menuCategoryCode")),
					'menuSubCategoryCode' => trim($this->input->post("menuSubCategoryCode")),
					'salePrice' => trim($this->input->post("salePrice")),
					'cuisineType' => trim($this->input->post("cuisineType")),
					'itemActiveStatus' => trim($this->input->post("itemActiveStatus")),
					'isAdminApproved' => trim($this->input->post("isAdminApproved")),
					'itemPackagingPrice' => trim($this->input->post("itemPackagingPrice")),
					'maxOrderQty' => $maxOrderQty,
					'addID' => $addID,
					'addIP' => $ip,
					'isActive' => trim($this->input->post("isActive")),
				);
				$code = $this->GlobalModel->addWithoutYear($data, 'vendoritemmaster', 'VITM');
				// For Vendor Item Image file upload 
				if ($code != 'false') {
					if (!file_exists(FCPATH . 'partner/uploads/' . $vendorCode)) {
						mkdir(FCPATH . 'partner/uploads/' . $vendorCode);
					}
					if (!file_exists(FCPATH . 'partner/uploads/' . $vendorCode . '/vendoritem')) {
						mkdir(FCPATH . 'partner/uploads/' . $vendorCode . '/vendoritem');
					}
					$uploadRootDir = 'uploads/';
					$uploadDir = 'partner/uploads/' . $vendorCode . '/vendoritem';
					$entityImage = "";
					if (!empty($_FILES['itemImage']['name'])) {
						$tmpFile = $_FILES['itemImage']['tmp_name'];
						$ext = pathinfo($_FILES['itemImage']['name'], PATHINFO_EXTENSION);
						$filename = $uploadDir . '/' . $code . '-VITMPhoto.jpg';
						move_uploaded_file($tmpFile, $filename);
						if (file_exists($filename)) {
							$entityImage = $code . '-VITMPhoto.jpg';
						}
					}
					// else
					// {                    
					// $dummyFile='file_not_found.png';
					// $tmpFile = $uploadRootDir.$dummyFile;
					// $filename = $uploadDir.'/'.$code.'-VITMPhoto.jpg';
					// copy($tmpFile, $filename);
					// if(file_exists($filename))
					// {
					// $entityImage=$code.'-VITMPhoto.jpg';
					// }
					// }  
					$subData = array(
						'itemPhoto' => $entityImage,
					);
					$this->GlobalModel->doEdit($subData, 'vendoritemmaster', $code);
				}
				if ($code != 'false') {
					$response['status'] = true;
					$response['message'] = "Vendor Item Successfully Added.";
					$this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
				} else {
					$response['status'] = false;
					$response['message'] = "Failed To Add Vendor Item";
				}
				$this->session->set_flashdata('response', json_encode($response));
				redirect(base_url() . 'Food/Vendoritem/listRecords', 'refresh');
			/*
			}
			*/
		}
	}

	public function update()
	{
		$code = trim($this->input->post("code"));
		$itemName = trim($this->input->post("itemName"));
		$itemName = ucwords(strtolower($itemName));
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
		$text = $role . " " . $userName . ' updated vendor item "' . $itemName . '" of code ' . $code . ' from ' . $ip;

		$log_text = array(
			'code' => "demo",
			'addID' => $addID,
			'logText' => $text
		);

		//Activity Track Ends 
		$this->form_validation->set_rules('itemName', 'Item Name', 'trim|required');
		//$this->form_validation->set_rules('itemDescription', 'Item Description', 'trim|required');
		$this->form_validation->set_rules('cuisineType', 'Cuisine Type', 'trim|required');
		$this->form_validation->set_rules('menuCategoryCode', 'Menu Category', 'trim|required');
		$this->form_validation->set_rules('vendorCode', 'Vendor', 'trim|required');
		$this->form_validation->set_rules('salePrice', 'salePrice', 'trim|required');
		if ($this->form_validation->run() == FALSE) {
			$data['error_message'] = '* Fields are Required!';
			$data['query'] = $this->GlobalModel->selectDataById($code, 'vendoritemmaster');
			$menuCode = $data['query']->result_array()[0]['menuCategoryCode'];
			$data['vendormaster'] = $this->GlobalModel->selectActiveData('vendor');
			$data['menucategory'] = $this->GlobalModel->selectActiveData('menucategory');
			$data['menusubcategory'] = $this->GlobalModel->selectQuery('menusubcategory.code,menusubcategory.menuSubCategoryName', 'menusubcategory', array("menusubcategory.menuCategoryCode" => $menuCode, "menusubcategory.isActive" => 1));
			$this->load->view('dashboard/header');
			$this->load->view('dashboard/vendoritem/edit', $data);
			$this->load->view('dashboard/footer');
		} else {
			$vendorCode = trim($this->input->post("vendorCode"));
			$maxOrderQty = trim($this->input->post("maxOrderQty"));
			if ($maxOrderQty == 0 || $maxOrderQty == "") $maxOrderQty = 1;
			//check same item name exists for selected vendor
			
			/*
			$conditionArray["vendoritemmaster.vendorCode"]=$vendorCode;
			$conditionArray["vendoritemmaster.itemName"]=$itemName;
			$result = $this->GlobalModel->checkDuplicateRecordNew($conditionArray,'vendoritemmaster');
			if ($result==true) 
			{
				$data = array('error_message' => 'Duplicate Item Name');
				$data['query'] = $this->GlobalModel->selectDataById($code,'vendoritemmaster');
				$menuCode = $data['query']->result_array()[0]['menuCategoryCode'];
				$data['vendormaster']=$this->GlobalModel->selectActiveData('vendor');    
				$data['menucategory']=$this->GlobalModel->selectActiveData('menucategory');
				$data['menusubcategory']= $this->GlobalModel->selectQuery('menusubcategory.code,menusubcategory.menuSubCategoryName','menusubcategory',array("menusubcategory.menuCategoryCode"=>$menuCode,"menusubcategory.isActive"=>1));
				$this->load->view('dashboard/header');
				$this->load->view('dashboard/vendoritem/edit',$data);
				$this->load->view('dashboard/footer'); 
			}
			else
			{ 
			*/
				$data = array(
					'itemName' => $itemName,
					'itemDescription' => trim($this->input->post("itemDescription")),
					'vendorCode' => trim($this->input->post("vendorCode")),
					'menuCategoryCode' => trim($this->input->post("menuCategoryCode")),
					'menuSubCategoryCode' => trim($this->input->post("menuSubCategoryCode")),
					'salePrice' => trim($this->input->post("salePrice")),
					'cuisineType' => trim($this->input->post("cuisineType")),
					'itemActiveStatus' => trim($this->input->post("itemActiveStatus")),
					'isAdminApproved' => trim($this->input->post("isAdminApproved")),
					'itemPackagingPrice' => trim($this->input->post("itemPackagingPrice")),
					'maxOrderQty' => $maxOrderQty,
					'editID' => $addID,
					'editIP' => $ip,
					'isActive' => trim($this->input->post("isActive")),
				);
				$result = $this->GlobalModel->doEdit($data, 'vendoritemmaster', $code);
				// For Vendor Item Image file upload 
				$oldPhoto = "";
				$RecordPhoto  = $this->GlobalModel->selectQuery("vendoritemmaster.itemPhoto", "vendoritemmaster", array("vendoritemmaster.code" => $code));
				if ($RecordPhoto) {
					$photoData = $RecordPhoto->result_array()[0];
					if ($photoData['itemPhoto'] != "")	$oldPhoto = 'partner/uploads/' . $vendorCode . '/vendoritem/' . $photoData['itemPhoto'];
				}
				$photoResult = "false";
				$entityImage = "";
				if (!empty($_FILES['itemImage']['name'])) {
					if (!file_exists(FCPATH . 'partner/uploads/' . $vendorCode)) {
						mkdir(FCPATH . 'partner/uploads/' . $vendorCode);
					}

					if (!file_exists(FCPATH . 'partner/uploads/' . $vendorCode . '/vendoritem')) {
						mkdir(FCPATH . 'partner/uploads/' . $vendorCode . '/vendoritem');
					}

					$uploadRootDir = 'uploads/';
					$uploadDir = 'partner/uploads/' . $vendorCode . '/vendoritem';

					if ($oldPhoto != "") {
						if (file_exists($oldPhoto)) unlink($oldPhoto);
					}
					sleep(2);
					$tmpFile = $_FILES['itemImage']['tmp_name'];
					$ext = pathinfo($_FILES['itemImage']['name'], PATHINFO_EXTENSION);
					$filename = $uploadDir . '/' . $code . '-VITMPhoto.jpg';
					move_uploaded_file($tmpFile, $filename);
					if (file_exists($filename)) {
						$entityImage = $code . '-VITMPhoto.jpg';
						$subData = array(
							'itemPhoto' => $entityImage,
						);
						$photoResult = $this->GlobalModel->doEdit($subData, 'vendoritemmaster', $code);
					}
				}

				if ($code != 'false' || $photoResult != "false") {
					$response['status'] = true;
					$response['message'] = "Vendor Item Successfully Updated.";
					$this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
				} else {
					$response['status'] = false;
					$response['message'] = "Failed To Update Vendor Item";
				}
				$this->session->set_flashdata('response', json_encode($response));
				redirect(base_url() . 'Food/Vendoritem/listRecords', 'refresh');
			/*
			}
			*/
		}
	}

	public function getsubCategoryItems()
	{
		$code = $this->input->get('menuCategoryCode');
		$menusubcategory = $this->GlobalModel->selectQuery('menusubcategory.code,menusubcategory.menuSubCategoryName', 'menusubcategory', array("menusubcategory.menuCategoryCode" => $code, 'menusubcategory.isActive' => 1));
		$html = "";
		if ($menusubcategory) {
			$daResult = $menusubcategory->result_array();
			$html = '<option value="">Select Sub Category</option>';
			foreach ($daResult as $r) {
				$html .= '<option value="' . $r['code'] . '">' . $r['menuSubCategoryName'] . '</option>';
			}
		}
		echo $html;
	}

	public function view()
	{
		$code = $this->uri->segment('4');
		$tableName = "vendoritemmaster";
		$orderColumns = array("vendoritemmaster.*,vendor.entityName,menucategory.menuCategoryName,menusubcategory.menuSubCategoryName");
		$condition = array('vendoritemmaster.code' => $code);
		$orderBy = array('vendoritemmaster' . '.id' => 'DESC');
		$joinType = array("vendor" => "inner", "menucategory" => "inner", "menusubcategory" => "left");
		$join = array("vendor" => "vendoritemmaster.vendorCode=vendor.code", "menucategory" => "vendoritemmaster.menuCategoryCode=menucategory.code", "menusubcategory" => "vendoritemmaster.menuSubCategoryCode=menusubcategory.code");
		$data['query'] = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType);
		$this->load->view('dashboard/header');
		$this->load->view('dashboard/vendoritem/view', $data);
		$this->load->view('dashboard/footer');
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
		$dataQ = $this->GlobalModel->selectDataByField('code', $code, 'vendoritemmaster');
		$categoryName = '';

		foreach ($dataQ->result() as $row) {
			$categoryName = $row->itemName;
		}

		$text = $role . " " . $userName . ' deleted Vendor Item "' . $categoryName . '" from ' . $ip;

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

		$resultData = $this->GlobalModel->doEdit($data, 'vendoritemmaster', $code);

		echo $this->GlobalModel->delete($code, 'vendoritemmaster');
	}

	public function customizeaddon()
	{
		$code = $this->uri->segment('4');
		$tableName = "vendoritemmaster";
		$orderColumns = array("vendoritemmaster.*,vendor.entityName");
		$condition = array('vendoritemmaster.code' => $code);
		$orderBy = array('vendoritemmaster' . '.id' => 'DESC');
		$joinType = array("vendor" => "inner");
		$join = array("vendor" => "vendoritemmaster.vendorCode=vendor.code");
		$data['query'] = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType);

		$table1 = "customizedcategory";
		$orderColumns1 = "customizedcategory.*";
		$condition1 = array("customizedcategory.vendorItemCode" => $code);
		$orderBy1 = array("customizedcategory.id" => "ASC");
		$data['categories'] = $this->GlobalModel->selectQuery($orderColumns1, $table1, $condition1, $orderBy1);

		$table2 = "customizedcategorylineentries";
		$orderColumns2 = "customizedcategorylineentries.*";
		$condition2 = array("customizedcategory.vendorItemCode" => $code);
		$orderBy2 = array("customizedcategorylineentries.id" => "ASC");
		$join2 = array("customizedcategory" => "customizedcategorylineentries.customizedCategoryCode=customizedcategory.code");
		$joinType2 = array("customizedcategory" => 'inner');
		$data['categoriesline'] = $this->GlobalModel->selectQuery($orderColumns2, $table2, $condition2, $orderBy2, $join2, $joinType2);

		$this->load->view('dashboard/header');
		$this->load->view('dashboard/vendoritem/addons', $data);
		$this->load->view('dashboard/footer');
	}

	public function addAddonCategory()
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

		$vendorItemCode = trim($this->input->post("vendorItemCode"));
		$categoryTitle = trim($this->input->post("categoryTitle"));
		$categoryType = trim($this->input->post("categoryType"));
		$isCateEnabled = trim($this->input->post("isCateEnabled"));

		$ip = $_SERVER['REMOTE_ADDR'];
		$text = $role . " " . $userName . ' added new customized catefory  "' . $categoryTitle . '" from ' . $ip;

		$log_text = array(
			'code' => "demo",
			'addID' => $addID,
			'logText' => $text
		);
		//Activity Track Ends
		$where = array("vendorItemCode"=>$vendorItemCode,"categoryTitle"=>$categoryTitle);
		$result = $this->GlobalModel->checkDuplicateRecordNew($where, 'customizedcategory');
		if ($result == true) {
			$response['status']  = 'exists';
			$response['message'] = 'Duplicate Category Exists';
		} else {
			$data = array(
				'vendorItemCode' => $vendorItemCode,
				'categoryTitle' => $categoryTitle,
				'categoryType' => $categoryType,
				'isEnabled' => $isCateEnabled,
				'addID' => $addID,
				'addIP' => $ip,
				'isActive' => $this->input->post("isActive"),
			);
			$code = $this->GlobalModel->addWithoutYear($data, 'customizedcategory', 'ITCC');
			if ($code != 'false') {
				$this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
				$response['status']  = 'true';
				$response['code']  = $code;
				$response['message'] = 'Category Added Successfully';
			} else {
				$response['status']  = 'failed';
				$response['message'] = 'Failed to add category';
			}
		}
		echo json_encode($response);
	}

	public function updateAddonCategory()
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

		$vendorItemCode = trim($this->input->post("vendorItemCode"));
		$categoryTitle = trim($this->input->post("categoryTitle"));
		$categoryType = trim($this->input->post("categoryType"));
		$isCateEnabled = trim($this->input->post("isCateEnabled"));
		$customizedCategoryCode = trim($this->input->post("customizedCategoryCode"));
		$ip = $_SERVER['REMOTE_ADDR'];

		$text = $role . " " . $userName . ' added updated customized category  "' . $categoryTitle . '" from ' . $ip;

		$log_text = array(
			'code' => "demo",
			'addID' => $addID,
			'logText' => $text
		);
		//Activity Track Ends
		$where = array("vendorItemCode"=>$vendorItemCode,"categoryTitle"=>$categoryTitle,'code!='=>$customizedCategoryCode);
		$result = $this->GlobalModel->checkDuplicateRecordNew($where, 'customizedcategory'); 
		if ($result == true) {
			$response['status']  = 'exists';
			$response['message'] = 'Duplicate Category Exists';
		} else {
			$data = array(
				'vendorItemCode' => $vendorItemCode,
				'categoryTitle' => $categoryTitle,
				'categoryType' => $categoryType,
				'isEnabled' => $isCateEnabled,
				'editID' => $addID,
				'editIP' => $ip,
				'isActive' => $this->input->post("isActive"),
			);
			$resultUpdate = $this->GlobalModel->doEdit($data, 'customizedcategory', $customizedCategoryCode);
			if ($resultUpdate != 'false') {
				$this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
				$response['status']  = 'true';
				$response['code']  = $customizedCategoryCode;
				$response['updatedtitle']  = $categoryTitle;
				$response['message'] = 'Category updated successfully';
			} else {
				$response['status']  = 'failed';
				$response['code']  = $customizedCategoryCode;
				$response['message'] = 'Failed to update category';
			}
		}
		echo json_encode($response);
	}

	public function addAddonLine()
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

		$cateCode = trim($this->input->post("cateCode"));
		$subTitle = trim($this->input->post("subTitle"));
		$price = trim($this->input->post("price"));
		$price = $price!="" ? $price : 0;
		$ip = $_SERVER['REMOTE_ADDR'];
		$text = $role . " " . $userName . ' added new customized catefory  "' . $subTitle . '" from ' . $ip;

		$log_text = array(
			'code' => "demo",
			'addID' => $addID,
			'logText' => $text
		);
		//Activity Track Ends
		$where = array("customizedCategoryCode"=>$cateCode,"subCategoryTitle"=>$subTitle);
		$result = $this->GlobalModel->checkDuplicateRecordNew($where, 'customizedcategorylineentries'); 
		if ($result == true) {
			$response['status']  = 'exists';
			$response['message'] = 'Duplicate Sub Category Exists';
		} else {
			$data = array(
				'customizedCategoryCode' => $cateCode,
				'subCategoryTitle' => $subTitle,
				'price' => $price,
				'isEnabled' => trim($this->input->post("isCateEnabled")),
				'addID' => $addID,
				'addIP' => $ip,
				'isActive' =>$this->input->post("isActive"),
			);
			$code = $this->GlobalModel->addWithoutYear($data, 'customizedcategorylineentries', 'CCLN');
			if ($code != 'false') {
				$this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
				$response['status']  = 'true';
				$response['code']  = $code;
				$response['message'] = 'Sub Category Added Successfully';
			} else {
				$response['status']  = 'failed';
				$response['message'] = 'Failed to add Sub Category';
			}
		}
		echo json_encode($response);
	}

	public function deleteAddonCategory()
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
		$dataQ = $this->GlobalModel->selectDataByField('code', $code, 'customizedcategory');
		$categoryName = '';

		foreach ($dataQ->result() as $row) {
			$categoryName = $row->categoryTitle;
		}

		$text = $role . " " . $userName . ' category delete by Vendor "' . $addID . '" of "' . $categoryName . '" from ' . $ip;

		$log_text = array(
			'code' => "demo",
			'addID' => $addID,
			'logText' => $text
		);
		$this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');

		$data = array(
			'editID' => $addID,
			'editIP' => $ip,
			'isDeleteRequested' => 1
		);

		$resultDataLine = $this->GlobalModel->deleteForeverFromField('customizedCategoryCode', $code, 'customizedcategorylineentries');

		$resultData = $this->GlobalModel->deleteForever($code, 'customizedcategory');
		if ($resultData == 'true') echo true;
		else echo false;
	}


	public function deleteAddonLine()
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
		$dataQ = $this->GlobalModel->selectDataByField('code', $code, 'customizedcategorylineentries');
		$categoryName = '';

		foreach ($dataQ->result() as $row) {
			$categoryName = $row->subCategoryTitle;
		}

		$text = $role . " " . $userName . ' addon delete by Vendor ".$addID." of "' . $categoryName . '" from ' . $ip;

		$log_text = array(
			'code' => "demo",
			'addID' => $addID,
			'logText' => $text
		);
		$this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');

		$data = array(
			'editID' => $addID,
			'editIP' => $ip,
			'isDeleteRequested' => 1
		);

		$resultData = $this->GlobalModel->deleteForever($code, 'customizedcategorylineentries');
		if ($resultData == 'true') echo true;
		else echo false;
	}

	public function getAddonCategoryData()
	{
		$code = $this->input->post('code');
		$data = $this->GlobalModel->selectQuery('customizedcategory.*', 'customizedcategory', array("customizedcategory.code" => $code));
		if ($data) {
			// $r['status'] = true;
			// $r['data'] = json_encode($data->result_array()[0]);
			$dataRes = json_encode($data->result_array()[0]);
		} else {
			// $r['status'] = false;
			$dataRes = "";
		}
		// echo json_encode($r);
		echo $dataRes;
	}
}
?>