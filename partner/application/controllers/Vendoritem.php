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
		$this->session_key = $this->session->userdata('partner_key' . SESS_KEY_PARTNER);
		if (!isset($this->session->userdata['part_logged_in' .  $this->session_key]['code'])) {
			redirect('Login', 'refresh');
		}
	}

	public function listRecords()
	{
		$addID = $this->session->userdata['part_logged_in' . $this->session_key]['code'];
		$data['error'] = $this->session->flashdata('response');
		$data['menucategory'] = $this->GlobalModel->selectQuery('menucategory.*', 'menucategory', array('menucategory.isActive' => 1));
		$data['menuitem'] = $this->GlobalModel->selectQuery('vendoritemmaster.*', 'vendoritemmaster', array('vendoritemmaster.isActive' => 1,'vendoritemmaster.vendorCode'=>$addID));
		$this->load->view('header');
		$this->load->view('vendoritem/list', $data);
		$this->load->view('footer');
	}

	public function add()
	{
		$data['menucategory'] = $this->GlobalModel->selectActiveData('menucategory');
		$this->load->view('header');
		$this->load->view('vendoritem/add', $data);
		$this->load->view('footer');
	}

	public function edit()
	{
		$addID = $this->session->userdata['part_logged_in' . $this->session_key]['code'];
		$code = $this->uri->segment('3');
		$data['query'] = $this->GlobalModel->selectQuery('vendoritemmaster.*', 'vendoritemmaster', array("vendoritemmaster.code" => $code, "vendoritemmaster.vendorCode" => $addID));
		$menuCode = $data['query']->result_array()[0]['menuCategoryCode'];
		$data['menucategory'] = $this->GlobalModel->selectActiveData('menucategory');
		$data['menusubcategory'] = $this->GlobalModel->selectQuery('menusubcategory.code,menusubcategory.menuSubCategoryName', 'menusubcategory', array("menusubcategory.menuCategoryCode" => $menuCode, "menusubcategory.isActive" => 1));
		$this->load->view('header');
		$this->load->view('vendoritem/edit', $data);
		$this->load->view('footer');
	}

	public function getVendorItemList()
	{
		$addID = $this->session->userdata['part_logged_in' . $this->session_key]['code'];
		$menucategoryCode = $this->input->get('menucategoryCode');
		$itemCode = $this->input->get('itemCode');
        $approvedStatus=$this->input->get('approvedStatus');
		$tableName = "vendoritemmaster";
		$orderColumns = array("vendoritemmaster.*,vendor.entityName,menucategory.menuCategoryName");
		$condition = array('vendoritemmaster.isActive' => 1, "vendoritemmaster.vendorCode" => $addID, "vendoritemmaster.menuCategoryCode" => $menucategoryCode, "vendoritemmaster.code" => $itemCode,"vendoritemmaster.isAdminApproved"=>$approvedStatus);
		$orderBy = array('vendoritemmaster' . '.id' => 'DESC');
		$joinType = array("vendor" => "inner", "menucategory" => 'inner', "vendoritemmaster" => 'inner');
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
				if ($row->itemActiveStatus == 1) {
					$itemActiveStatus = "<span class='label label-sm label-success'>Active</span>";
				} else {
					$itemActiveStatus = "<span class='label label-sm label-danger'>Inactive</span>";
				}

				if ($row->isActive == 1) {
					$status = "<span class='label label-sm label-success'>Active</span>";
				} else {
					$status = "<span class='label label-sm label-warning'>Inactive</span>";
				}

				if ($row->isAdminApproved == 1) {
					$isAdminApproved = '<span class="label label-sm label-success">Yes</span>';
				} else {
					$isAdminApproved = "<span class='label label-sm label-danger'>No</span>";
				}
				   $actionHtml = '<div class="btn-group">
					<button type="button" class="btn btn-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<i class="ti-settings"></i>
					</button>';
				   $actionHtml .='<div class="dropdown-menu animated slideInUp" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 35px, 0px);">
						<a class="dropdown-item" href="' . base_url() . 'Vendoritem/view/' . $row->code . '"><i class="ti-eye"></i> Open</a>';
					if ($row->isAdminApproved == 1) {
					$actionHtml .='<a class="dropdown-item" href="' . base_url() . 'Vendoritem/edit/' . $row->code . '"><i class="ti-pencil-alt"></i> Edit</a>';
					}
					$actionHtml .='<a class="dropdown-item" href="' . base_url() . 'Vendoritem/customizeaddon/' . $row->code . '"><i class="ti-eye"></i> Add Ons</a>
						<a class="dropdown-item  mywarning" data-seq="' . $row->code . '" id="' . $row->code . '"><i class="ti-trash" href></i> Delete</a>
					</div>
				</div>';
				// $activeAction = ' <div class="custom-control custom-checkbox"> 
				// <input type="checkbox" value="1" class="custom-control-input actionStatus" '.$checked.' data-id="'.$row->code.'" id="itm'.$row->code.'">
				// <label class="custom-control-label" for="itm'.$row->code.'"> '.$itemActiveStatus.' </label>
				// </div>';

				$data[] = array(
					$srno,
					$row->code,
					$row->itemName,
					// $row->entityName,
					$row->menuCategoryName,
					// $activeAction,
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
		$addID = $this->session->userdata['part_logged_in' . $this->session_key]['code'];
		$userRole = $this->session->userdata['part_logged_in' . $this->session_key]['role'];
		$userName = $this->session->userdata['part_logged_in' . $this->session_key]['username'];
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
		$this->form_validation->set_rules('itemDescription', 'Item Description', 'trim|required');
		$this->form_validation->set_rules('cuisineType', 'Cuisine Type', 'trim|required');
		$this->form_validation->set_rules('menuCategoryCode', 'Menu Category', 'trim|required');
		$this->form_validation->set_rules('salePrice', 'salePrice', 'trim|required');
		if ($this->form_validation->run() == FALSE) {
			$data['menucategory'] = $this->GlobalModel->selectActiveData('menucategory');
			$data['error_message'] = '* Fields are Required!';
			$this->load->view('header');
			$this->load->view('vendoritem/add', $data);
			$this->load->view('footer');
		} else {
			$vendorCode = $addID;
			$maxOrderQty = trim($this->input->post("maxOrderQty"));
			if ($maxOrderQty == 0 || $maxOrderQty == "") $maxOrderQty = 1;
			
			$conditionArray["vendoritemmaster.vendorCode"]=$vendorCode;
			$conditionArray["vendoritemmaster.itemName"]=$itemName;
			$result = $this->GlobalModel->checkDuplicateRecordNew('vendoritemmaster',$conditionArray);
			if($result==true) 
			{ 
				$data['menucategory']=$this->GlobalModel->selectActiveData('menucategory');
				$data = array('error_message' => 'Duplicate Item Name');
				$this->load->view('header');
				$this->load->view('vendoritem/add', $data);
				$this->load->view('footer');
			}
			else
			{
				$data = array(
					'itemName' => $itemName,
					'itemDescription' => trim($this->input->post("itemDescription")),
					'vendorCode' => $addID,
					'menuCategoryCode' => trim($this->input->post("menuCategoryCode")),
					'menuSubCategoryCode' => trim($this->input->post("menuSubCategoryCode")),
					'salePrice' => trim($this->input->post("salePrice")),
					'cuisineType' => trim($this->input->post("cuisineType")),
					'itemActiveStatus' => trim($this->input->post("itemActiveStatus")),
					'itemPackagingPrice' => trim($this->input->post("itemPackagingPrice")),
					'maxOrderQty' => $maxOrderQty,
					'isAdminApproved' => 0,
					'addID' => $addID,
					'addIP' => $ip,
					'isActive' => trim($this->input->post("isActive")),
				);
				$code = $this->GlobalModel->addWithoutYear($data, 'vendoritemmaster', 'VITM');
				// For Vendor Item Image file upload 
				if ($code != 'false') {
					if (!file_exists(FCPATH . 'uploads/' . $vendorCode)) {
						mkdir(FCPATH . 'uploads/' . $vendorCode);
					}

					if (!file_exists(FCPATH . 'uploads/' . $vendorCode . '/vendoritem')) {
						mkdir(FCPATH . 'uploads/' . $vendorCode . '/vendoritem');
					}

					$uploadRootDir = 'uploads/';
					$uploadDir = 'uploads/' . $vendorCode . '/vendoritem';

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
				redirect(base_url() . 'Vendoritem/listRecords', 'refresh');
			}
		}
	}

	public function update()
	{
		// print_r($_FILES);
		// exit;
		$code = trim($this->input->post("code"));
		$itemName = trim($this->input->post("itemName"));
		$itemName = ucwords(strtolower($itemName));
		//Activity Track Starts
		$addID = $this->session->userdata['part_logged_in' . $this->session_key]['code'];
		$userRole = $this->session->userdata['part_logged_in' . $this->session_key]['role'];
		$userName = $this->session->userdata['part_logged_in' . $this->session_key]['username'];
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
		$this->form_validation->set_rules('itemDescription', 'Item Description', 'trim|required');
		$this->form_validation->set_rules('cuisineType', 'Cuisine Type', 'trim|required');
		$this->form_validation->set_rules('menuCategoryCode', 'Menu Category', 'trim|required');
		$this->form_validation->set_rules('salePrice', 'salePrice', 'trim|required');
		if ($this->form_validation->run() == FALSE) {
			$data['error_message'] = '* Fields are Required!';
			$data['query'] = $this->GlobalModel->selectQuery('vendoritemmaster.*', 'vendoritemmaster', array("vendoritemmaster.code" => $code, "vendoritemmaster.vendorCode" => $addID));
			$menuCode = $data['query']->result_array()[0]['menuCategoryCode'];
			$data['menucategory'] = $this->GlobalModel->selectActiveData('menucategory');
			$data['menusubcategory'] = $this->GlobalModel->selectQuery('menusubcategory.code,menusubcategory.menuSubCategoryName', 'menusubcategory', array("menusubcategory.menuCategoryCode" => $menuCode, "menusubcategory.isActive" => 1));
			$this->load->view('header');
			$this->load->view('vendoritem/edit', $data);
			$this->load->view('footer');
		} else {
			$vendorCode = $addID;
			
			$conditionArray["vendoritemmaster.vendorCode"]=$vendorCode;
			$conditionArray["vendoritemmaster.itemName"]=$itemName;
			$result = $this->db->query("SELECT * FROM `vendoritemmaster` WHERE LOWER(itemName)='" . strtolower($itemName) . "' AND vendorCode = '" . $vendorCode. "' AND code != '" . $code. "' AND (`isDelete` IS NULL OR `isDelete`='0')");
			//$result=$this->GlobalModel->checkDuplicateRecordInUpdate('itemName',$itemName,$code,'vendoritemmaster');
			//echo $this->db->last_query();
			//exit();
			//$result = $this->GlobalModel->checkDuplicateRecordNew($conditionArray,'vendoritemmaster');
			if($result->num_rows()>0) 
			{ 
				$data['query'] = $this->GlobalModel->selectQuery('vendoritemmaster.*', 'vendoritemmaster', array("vendoritemmaster.code" => $code, "vendoritemmaster.vendorCode" => $addID));
				$menuCode = $data['query']->result_array()[0]['menuCategoryCode'];
				$data['menucategory'] = $this->GlobalModel->selectActiveData('menucategory');
				$data['menusubcategory'] = $this->GlobalModel->selectQuery('menusubcategory.code,menusubcategory.menuSubCategoryName', 'menusubcategory', array("menusubcategory.menuCategoryCode" => $menuCode, "menusubcategory.isActive" => 1));
				//$data = array('error_message' => 'Duplicate Item Name');
				$data['error_message'] = 'Duplicate Item Name';
				$this->load->view('header');
				$this->load->view('vendoritem/edit', $data);
				$this->load->view('footer');
			} else {
				// For Vendor Item Image file upload 
				$oldPhoto = "";
				$currentSalePrice = $this->input->post('salePrice');
				$oldSalePrice = 0;
				$RecordPhoto  = $this->GlobalModel->selectQuery("vendoritemmaster.itemPhoto,vendoritemmaster.salePrice", "vendoritemmaster", array("vendoritemmaster.code" => $code));
				if ($RecordPhoto) {
					$photoData = $RecordPhoto->result_array()[0];
					$oldSalePrice = $photoData['salePrice'];
					if ($photoData['itemPhoto'] != "") {
						$oldPhoto = 'uploads/' . $vendorCode . '/vendoritem/' . $photoData['itemPhoto'];
					}
				}
				$photoResult = "false";
				$entityImage = "";
				if (!empty($_FILES['itemImage']['name'])) {
					if (!file_exists(FCPATH . 'uploads/' . $vendorCode)) {
						mkdir(FCPATH . 'uploads/' . $vendorCode);
					}

					if (!file_exists(FCPATH . 'uploads/' . $vendorCode . '/vendoritem')) {
						mkdir(FCPATH . 'uploads/' . $vendorCode . '/vendoritem');
					}

					$uploadRootDir = 'uploads/';
					$uploadDir = 'uploads/' . $vendorCode . '/vendoritem';

					if ($oldPhoto != "") {
						if (!file_exists($oldPhoto)) unlink($oldPhoto);
					}

					sleep(2);

					$tmpFile = $_FILES['itemImage']['tmp_name'];
					$ext = pathinfo($_FILES['itemImage']['name'], PATHINFO_EXTENSION);
					$tnm = $code . '-VITMPhoto' . time() . '.jpg';
					// $filename = $uploadDir.'/'.$code.'-VITMPhoto.jpg';
					$filename = $uploadDir . '/' . $tnm;

					move_uploaded_file($tmpFile, $filename);
					if (file_exists($filename)) {
						// $entityImage=$code.'-VITMPhoto.jpg';
						$entityImage = $tnm;
					}

					$subData = array(
						'itemPhoto' => $entityImage,
					);

					$photoResult = $this->GlobalModel->doEdit($subData, 'vendoritemmaster', $code);
				}

				$maxOrderQty = trim($this->input->post("maxOrderQty"));
				if ($maxOrderQty == 0 || $maxOrderQty == "") $maxOrderQty = 1;

				$data = array(
					'itemName' => $itemName,
					'itemDescription' => trim($this->input->post("itemDescription")),
					'vendorCode' => $addID,
					'menuCategoryCode' => trim($this->input->post("menuCategoryCode")),
					'menuSubCategoryCode' => trim($this->input->post("menuSubCategoryCode")),
					'salePrice' => trim($this->input->post("salePrice")),
					'cuisineType' => trim($this->input->post("cuisineType")),
					'itemActiveStatus' => trim($this->input->post("itemActiveStatus")),
					'itemPackagingPrice' => trim($this->input->post("itemPackagingPrice")),
					'maxOrderQty' => $maxOrderQty,
					'editID' => $addID,
					'editIP' => $ip,
					'isActive' => trim($this->input->post("isActive")),
				);
				if ($oldSalePrice != $currentSalePrice || $entityImage != "") {
					$data['isAdminApproved'] = 0;
				}

				$result = $this->GlobalModel->doEdit($data, 'vendoritemmaster', $code);

				if ($code != 'false' || $photoResult != "false") {
					$response['status'] = true;
					$response['message'] = "Vendor Item Successfully Updated.";
					$this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
				} else {
					$response['status'] = false;
					$response['message'] = "Failed To Update Vendor Item";
				}
				$this->session->set_flashdata('response', json_encode($response));
				redirect(base_url() . 'Vendoritem/listRecords', 'refresh');
			}
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
		$code = $this->uri->segment('3');
		$tableName = "vendoritemmaster";
		$orderColumns = array("vendoritemmaster.*,vendor.entityName,menucategory.menuCategoryName,menusubcategory.menuSubCategoryName");
		$condition = array('vendoritemmaster.code' => $code);
		$orderBy = array('vendoritemmaster' . '.id' => 'DESC');
		$joinType = array("vendor" => "inner", "menucategory" => "inner", "menusubcategory" => "left");
		$join = array("vendor" => "vendoritemmaster.vendorCode=vendor.code", "menucategory" => "vendoritemmaster.menuCategoryCode=menucategory.code", "menusubcategory" => "vendoritemmaster.menuSubCategoryCode=menusubcategory.code");
		$data['query'] = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType);
		$this->load->view('header');
		$this->load->view('vendoritem/view', $data);
		$this->load->view('footer');
	}

	public function delete()
	{
		$code = $this->input->post('code');

		//Activity Track Starts

		$addID = $this->session->userdata['part_logged_in' . $this->session_key]['code'];
		$userRole = $this->session->userdata['part_logged_in' . $this->session_key]['role'];
		$userName = $this->session->userdata['part_logged_in' . $this->session_key]['username'];
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

		$text = $role . " " . $userName . ' delete requested by Vendor ".$addID." for Item "' . $categoryName . '" from ' . $ip;

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

		$resultData = $this->GlobalModel->doEdit($data, 'vendoritemmaster', $code);
		if ($resultData == 'true') echo true;
		else echo false;
	}

	public function updateItemStatus()
	{
		$code = $this->input->post('code');
		$activeStatus  = $this->input->post('activeStatus');
		//Activity Track Starts

		$addID = $this->session->userdata['part_logged_in' . $this->session_key]['code'];
		$userRole = $this->session->userdata['part_logged_in' . $this->session_key]['role'];
		$userName = $this->session->userdata['part_logged_in' . $this->session_key]['username'];
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

		if ($activeStatus == 1) $setActive = 'active';

		else  $setActive = 'in-active';

		$text = $role . " " . $userName . ' item status was changed to ".$setActive." by Vendor ".$addID." for Item "' . $categoryName . '" from ' . $ip;

		$log_text = array(
			'code' => "demo",
			'addID' => $addID,
			'logText' => $text
		);
		$this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');

		$data = array(
			'editID' => $addID,
			'editIP' => $ip,
			'itemActiveStatus' => $activeStatus
		);

		$resultData = $this->GlobalModel->doEdit($data, 'vendoritemmaster', $code);
		if ($resultData == 'true') echo true;
		else echo false;
	}

	public function customizeaddon()
	{
		$code = $this->uri->segment('3');
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

		$this->load->view('header');
		$this->load->view('vendoritem/addons', $data);
		$this->load->view('footer');
	}

	public function addAddonCategory()
	{
		$addID = $this->session->userdata['part_logged_in' . $this->session_key]['code'];
		$userRole = $this->session->userdata['part_logged_in' . $this->session_key]['role'];
		$userName = $this->session->userdata['part_logged_in' . $this->session_key]['username'];
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
		$result = $this->GlobalModel->checkDuplicateRecord('categoryTitle', $categoryTitle, 'customizedcategory');
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
				'isActive' => trim($this->input->post("isActive")),
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
		$addID = $this->session->userdata['part_logged_in' . $this->session_key]['code'];
		$userRole = $this->session->userdata['part_logged_in' . $this->session_key]['role'];
		$userName = $this->session->userdata['part_logged_in' . $this->session_key]['username'];
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

		$text = $role . " " . $userName . ' added updated customized catefory  "' . $categoryTitle . '" from ' . $ip;

		$log_text = array(
			'code' => "demo",
			'addID' => $addID,
			'logText' => $text
		);
		//Activity Track Ends  
		$result = $this->GlobalModel->checkDuplicateRecordNotEqualtoCode('categoryTitle', $categoryTitle, 'customizedcategory', $customizedCategoryCode);
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
				'isActive' => trim($this->input->post("isActive")),
			);
			$resultUpdate = $this->GlobalModel->doEdit($data, 'customizedcategory', $customizedCategoryCode);
			if ($resultUpdate != 'false') {
				$this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
				$response['status']  = 'true';
				$response['code']  = $customizedCategoryCode;
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
		$addID = $this->session->userdata['part_logged_in' . $this->session_key]['code'];
		$userRole = $this->session->userdata['part_logged_in' . $this->session_key]['role'];
		$userName = $this->session->userdata['part_logged_in' . $this->session_key]['username'];
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
		$price = $price!=""?$price:0;
		$ip = $_SERVER['REMOTE_ADDR'];
		$text = $role . " " . $userName . ' added new customized catefory  "' . $subTitle . '" from ' . $ip;
		$log_text = array(
			'code' => "demo",
			'addID' => $addID,
			'logText' => $text
		);
		//Activity Track Ends
		$result = $this->GlobalModel->checkDuplicateRecord('subCategoryTitle', $subTitle, 'customizedcategorylineentries');
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
				'isActive' => trim($this->input->post("isActive")),
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

	

	public function deleteAddonLine()
	{
		$code = $this->input->post('code');
		//Activity Track Starts
		$addID = $this->session->userdata['part_logged_in' . $this->session_key]['code'];
		$userRole = $this->session->userdata['part_logged_in' . $this->session_key]['role'];
		$userName = $this->session->userdata['part_logged_in' . $this->session_key]['username'];
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

	public function deleteImage()
	{
		$imgNm = $this->input->post('value');
		$code = $this->input->post('code');
		$codeItem = $this->input->post('codeItem');

		$data = array(
			'itemPhoto' => '',
		);
		if (!empty($data)) {
			unlink('uploads/' . $code . '/vendoritem/' . $imgNm);
			echo $resultData = $this->GlobalModel->doEdit($data, 'vendoritemmaster', $codeItem);
		} else {
			echo 'false';
		}
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
}
?>