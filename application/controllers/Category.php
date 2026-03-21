<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Category extends CI_Controller
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
		if(!isset($this->session->userdata['logged_in' . $this->session_key]['code'])){
			redirect('Admin/login','refresh');
		}
	}

	public function listRecords()
	{
		$data['error'] = $this->session->flashdata('response');
		$this->load->view('dashboard/header');
		$this->load->view('dashboard/category/list', $data);
		$this->load->view('dashboard/footer');
	}
	public function add()
	{
		$this->load->view('dashboard/header');
		$this->load->view('dashboard/category/add');
		$this->load->view('dashboard/footer');
	}

	public function edit()
	{
		$code = $this->uri->segment(3);

		$data['query'] = $this->GlobalModel->selectDataById($code, 'categorymaster');
		$this->load->view('dashboard/header');
		$this->load->view('dashboard/category/edit', $data);
		$this->load->view('dashboard/footer');
	}
	

	public function getCategoryList()
	{
		$tableName = "categorymaster";
		$search = $this->input->GET("search")['value'];
		$orderColumns = array("categorymaster.*,maincategorymaster.mainCategoryName");
		$condition = array('categorymaster.mainCategoryCode' => 'MCAT_1');
		$orderBy = array('categorymaster' . '.id' => 'DESC');
		$joinType = array('maincategorymaster' => 'inner');
		$join = array('maincategorymaster' => 'maincategorymaster.code=categorymaster.mainCategoryCode');
		$groupByColumn = array();
		$limit = $this->input->GET("length");
		$offset = $this->input->GET("start");
		$extraCondition = " categorymaster.isDelete=0 OR categorymaster.isDelete IS NULL";
		$like = array("categorymaster.categoryName" => $search . "~both","categorymaster.categorySName" => $search . "~both","maincategorymaster.mainCategoryName" => $search . "~both");
		$Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
		$srno = $_GET['start'] + 1;
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
									<a class="dropdown-item" href="' . base_url() . 'index.php/Category/edit/' . $row->code . '"><i class="ti-pencil-alt"></i> Edit</a>
									<a class="dropdown-item  mywarning" data-seq="' . $row->code . '" id="' . $row->code . '"><i class="ti-trash" href></i> Delete</a>
								</div>
							</div>';

				$data[] = array(
					$srno,
					$row->code,
					$row->mainCategoryName,
					$row->categoryName,
					$row->categorySName,
					$status,
					$actionHtml
				);
				$srno++;
			}
			$dataCount = sizeof($this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, array(), '', '', '', $extraCondition)->result());
			$output = array(
				"draw"			  =>     intval($_GET["draw"]),
				"recordsTotal"    =>      $dataCount,
				"recordsFiltered" =>     $dataCount,
				"data"            =>     $data
			);
			echo json_encode($output);
		} else {
			$dataCount = 0;
			$data = array();
			$output = array(
				"draw"			  =>     intval($_GET["draw"]),
				"recordsTotal"    =>     $dataCount,
				"recordsFiltered" =>     $dataCount,
				"data"            =>     $data
			);
			echo json_encode($output);
		}
	}


	public function save()
	{

		$categorySName = strtoupper($this->input->post("categorySName"));
		$categoryName = trim($this->input->post("categoryName"));

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
		$text = $role . " " . $userName . ' added new category "' . $categoryName . '" from ' . $ip;

		$log_text = array(
			'code' => "demo",
			'addID' => $addID,
			'logText' => $text
		);

		//Activity Track Ends

		// $result = $this->GlobalModel->checkDuplicateRecord('categorySName',$categorySName,'categorymaster');

		$condition2 = array('categorySName' => $categorySName, 'mainCategoryCode' => 'MCAT_1');
		$result = $this->GlobalModel->checkDuplicateRecordNew($condition2, 'categorymaster');

		if ($result == true) {
			$data = array(
				'error_message' => 'Duplicate Category short name'
			);
			$this->load->view('dashboard/header');
			$this->load->view('dashboard/category/add', $data);
			$this->load->view('dashboard/footer');
		} else {

			$this->form_validation->set_rules('categoryName', 'Category Name', 'required');
			$this->form_validation->set_rules('categorySName', 'Category Short Name', 'required');

			if ($this->form_validation->run() == FALSE) {

				$data['error_message'] = '* Fields are Required!';
				$this->load->view('dashboard/header');
				$this->load->view('dashboard/c/adategoryd', $data);
				$this->load->view('dashboard/footer');
			} else {
				$data = array(
					'mainCategoryCode' => 'MCAT_1',
					'categoryName' => $categoryName,
					'categorySName' => strtoupper(trim(str_replace(' ', '', $this->input->post("categorySName")))),
					'addID' => $addID,
					'addIP' => $ip,
					'isActive' => trim($this->input->post("isActive"))
				);

				$code = $this->GlobalModel->addWithoutYear($data, 'categorymaster', 'PCAT');
				// For Category Image file upload 
				if ($code != 'false') {
					if (!file_exists(FCPATH . 'uploads/category/' . $code)) {
						mkdir(FCPATH . 'uploads/category/' . $code);
					}

					$uploadRootDir = 'uploads/';
					$uploadDir = 'uploads/category/' . $code;


					$categoryImage = "";

					if (!empty($_FILES['categoryImage']['name'])) {
						$tmpFile = $_FILES['categoryImage']['tmp_name'];
						$ext = pathinfo($_FILES['categoryImage']['name'], PATHINFO_EXTENSION);
						$filename = $uploadDir . '/' . $code . '-CAT.jpg';
						move_uploaded_file($tmpFile, $filename);
						if (file_exists($filename)) {
							$categoryImage = $code . '-CAT.jpg';
						}
					} else {
						$dummyFile = 'file_not_found.png';
						$tmpFile = $uploadRootDir . $dummyFile;
						$filename = $uploadDir . '/' . $code . '-CAT.jpg';
						copy($tmpFile, $filename);
						if (file_exists($filename)) {

							$categoryImage = $code . '-CAT.jpg';
						}
					}
					$subData = array(
						'categoryImage' => $categoryImage,
					);
					//print_r($subData);
					$this->GlobalModel->doEdit($subData, 'categorymaster', $code);
				}

				if ($code != 'false') {
					$response['status'] = true;
					$response['message'] = "Category Successfully Added.";
					$this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
				} else {
					$response['status'] = false;
					$response['message'] = "Failed To Add Category";
				}
				$this->session->set_flashdata('response', json_encode($response));
				redirect(base_url() . 'Category/listRecords', 'refresh');
			}
		}
	}

	public function update()
	{

		$categoryName = trim($this->input->post("categoryName"));
		$categorySName = trim($this->input->post("categorySName"));
		$code =  $this->input->post('code');

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
		$text = $role . " " . $userName . ' updated Category "' . $categoryName . '" from ' . $ip;

		$log_text = array(
			'code' => "demo",
			'addID' => $addID,
			'logText' => $text
		);

		//Activity Track Ends 
		// $result = $this->GlobalModel->checkDuplicateRecordNotEqualtoCode('categorySName',$categorySName,'categorymaster',$code);
		$condition2 = array('categorySName' => $categorySName, 'mainCategoryCode' => 'MCAT_1', 'code!=' => $code);
		$result = $this->GlobalModel->checkDuplicateRecordNew($condition2, 'categorymaster');
		if ($result == true) {
			$data = array('error_message' => 'Duplicate Category short name');
			/* $this->load->view('dashboard/header');
				$this->load->view('dashboard/category/add', $data);
				$this->load->view('dashboard/footer'); */

			$data['query'] = $this->GlobalModel->selectDataById($code, 'categorymaster');
			$this->load->view('dashboard/header');
			$this->load->view('dashboard/category/edit', $data);
			$this->load->view('dashboard/footer');
		} else {


			$this->form_validation->set_rules('categoryName', 'Category Name', 'required');
			$this->form_validation->set_rules('categorySName', 'Category Short Name', 'required');

			if ($this->form_validation->run() == FALSE) {

				$data['error_message'] = '* Fields are Required!';
				$this->load->view('dashboard/header');
				$this->load->view('dashboard/category/add', $data);
				$this->load->view('dashboard/footer');
			} else {

				$data = array(
					'mainCategoryCode' => 'MCAT_1',
					'categoryName' => $categoryName,
					'categorySName' => strtoupper(trim(str_replace(' ', '', $this->input->post("categorySName")))),
					//'currencyDescription' => trim($this->input->post("currencyDescription")),
					'editID' => $addID,
					'editIP' => $ip,
					'isActive' => trim($this->input->post("isActive"))
				);


				$result = $this->GlobalModel->doEdit($data, 'categorymaster', $code);
				$shortcode = strtoupper(trim(str_replace(' ', '', $this->input->post("categorySName"))));
				if (trim($this->input->post("isActive")) != 1) {
					$update = array('isActive' => 0, 'editID' => $addID, 'editIP' => $ip);
				} else {
					$update = array('isActive' => 1, 'editID' => $addID, 'editIP' => $ip);
				}
				$sa = $this->GlobalModel->doEditWithField($update, 'productmaster', 'productCategory', $shortcode);

				$uploadRootDir = 'uploads/';
				$uploadDir = 'uploads/category/' . $code;
				$resultFlag = "0";

				if (!file_exists(FCPATH . 'uploads/category/' . $code)) {
					mkdir(FCPATH . 'uploads/category/' . $code);
				}


				// For Bank Passbook file upload 
				if (!empty($_FILES['categoryImage']['name'])) {
					$tmpFile = $_FILES['categoryImage']['tmp_name'];
					$ext = pathinfo($_FILES['categoryImage']['name'], PATHINFO_EXTENSION);
					$filename = $uploadDir . '/' . $code . '-CAT.jpg';
					move_uploaded_file($tmpFile, $filename);

					if (file_exists($filename)) {
						$categoryImage = $code . '-CAT.jpg';
					}
					//Add to Database
					$subPassData = array(
						'categoryImage' => $categoryImage,

					);
					$this->GlobalModel->doEdit($subPassData, 'categorymaster', $code);
					$resultFlag = "1";
				}

				if ($result != 'false' || $resultFlag == '1') {
					$response['status'] = true;
					$response['message'] = "Category Successfully Updated.";
					$this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
				} else {
					$response['status'] = false;
					$response['message'] = "No change In Category";
				}
				$this->session->set_flashdata('response', json_encode($response));

				redirect(base_url() . 'index.php/Category/listRecords', 'refresh');
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
		$dataQ = $this->GlobalModel->selectDataByField('code', $code, 'categorymaster');
		$categoryName = '';

		foreach ($dataQ->result() as $row) {
			$categoryName = $row->categoryName;
		}

		$productResult = $this->GlobalModel->selectDataByField('productCategory', $code, 'productmaster');
		foreach ($productResult->result() as $prdct) {
			$productCode = $prdct->code;
			$dataStock = $this->GlobalModel->selectDataByField('productCode', $code, 'stockinfo');
			$stockId = '';

			foreach ($dataStock->result() as $row) {
				$stockId = $row->id;
				$dataDeleteStock  = array(
					'isDelete'	=>	'1'
				);
				$this->GlobalModel->doEditWithField($dataDeleteStock, 'stockinfo', 'id', $stockId);
			}
		}

		$dataProduct = $this->GlobalModel->deleteWithField('productCategory', $code, 'productmaster');


		$text = $role . " " . $userName . ' deleted category "' . $categoryName . '" from ' . $ip;

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

		$resultData = $this->GlobalModel->doEdit($data, 'categorymaster', $code);

		echo $this->GlobalModel->delete($code, 'categorymaster');

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

		$tables = array('categorymaster');

		$requiredColumns = array(
			array('code', 'categoryName', 'categorySName', 'categoryImage', 'isActive')

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
			} else {
				$activeStatus = '<span class="label label-sm label-warning">Inactive</span>';
			}

			$modelHtml .= '<div class="form-row"><div class="col-md-3 mb-3"><label> <b> Code:</b> </label>
						<input type="text" value="' . $row->code_00 . '" class="form-control-line"  readonly></div>
					<div class="col-md-5 mb-3"><label> <b>Category Name:</b> </label>
						<input type="text" class="form-control-line" value="' . $row->categoryName_01 . '"  readonly></div>
						<div class="col-md-4 mb-3"><label> <b>Category Short Name:</b> </label>
						<input type="text" class="form-control-line" value="' . $row->categorySName_02 . '"  readonly></div> </div>
						
					<div class="form-group">' . $activeStatus . '</div>';

			//for activity

			$text = $role . " " . $userName . ' viewed currency "' . $row->categoryName_01 . '" from ' . $ip;

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