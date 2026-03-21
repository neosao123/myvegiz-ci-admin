<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Subcategory extends CI_Controller
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
		$this->load->view('dashboard/subcategory/list', $data);
		$this->load->view('dashboard/footer');
	}
	public function add()
	{
		$table_name = 'categorymaster';
		$orderColumns = array("categorymaster.*");
		$cond = array('categorymaster' . '.isDelete' => 0, 'categorymaster' . '.isActive' => 1, 'categorymaster.mainCategoryCode' => 'MCAT_1');
		$data['category'] = $this->GlobalModel->selectQuery($orderColumns, $table_name, $cond);
		$this->load->view('dashboard/header');
		$this->load->view('dashboard/subcategory/add',$data);
		$this->load->view('dashboard/footer');
	}

	public function edit()
	{
		$code = $this->uri->segment(3);
		$table_name = 'categorymaster';
		$orderColumns = array("categorymaster.*");
		$cond = array('categorymaster' . '.isDelete' => 0, 'categorymaster' . '.isActive' => 1, 'categorymaster.mainCategoryCode' => 'MCAT_1');
		$data['category'] = $this->GlobalModel->selectQuery($orderColumns, $table_name, $cond);
		$data['query'] = $this->GlobalModel->selectDataById($code, 'subcategorymaster');
		$this->load->view('dashboard/header');
		$this->load->view('dashboard/subcategory/edit', $data);
		$this->load->view('dashboard/footer');
	}
		public function getsubcategoryList()
	{
		$tableName = "subcategorymaster";
		$search = $this->input->GET("search")['value'];
		$orderColumns = array("subcategorymaster.*,categorymaster.categoryName");
		$condition = array('categorymaster.mainCategoryCode' => 'MCAT_1');
		$orderBy = array('subcategorymaster' . '.id' => 'DESC');
		$joinType = array('categorymaster' => 'inner');
		$join = array('categorymaster' => 'categorymaster.code=subcategorymaster.categoryCode');
		$groupByColumn = array();
		$limit = $this->input->GET("length");
		$offset = $this->input->GET("start");
		$extraCondition = " subcategorymaster.isDelete=0 OR subcategorymaster.isDelete IS NULL";
		$like = array("subcategorymaster.subcategoryName" => $search . "~both","categorymaster.categoryName" => $search . "~both");
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
									<a class="dropdown-item" href="' . base_url() . 'subcategory/edit/' . $row->code . '"><i class="ti-pencil-alt"></i> Edit</a>
									<a class="dropdown-item  mywarning" data-seq="' . $row->code . '" id="' . $row->code . '"><i class="ti-trash" href></i> Delete</a>
								</div>
							</div>';

				$data[] = array(
					$srno,
					$row->code,
					$row->categoryName,
					$row->subcategoryName,
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
		$categoryCode = trim($this->input->post("categoryCode"));
		$subcategoryName = trim($this->input->post("subcategoryName"));
		//Activity Track Starts
		$addID = $this->session->userdata['logged_in' . $this->session_key]['code'];
		$userRole = $this->session->userdata['logged_in' . $this->session_key]['role'];
		$userName = $this->session->userdata['logged_in' . $this->session_key]['username'];
		$role = "";
		$isActive=0;
		if(trim($this->input->post("isActive"))!=''){
			$isActive = trim($this->input->post("isActive"));
		}
		switch ($userRole) {
			case "ADM":
				$role = "Admin";
				break;
			case "USR":
				$role = "User";
				break;
		}
		$ip = $_SERVER['REMOTE_ADDR'];
		$text = $role . " " . $userName . ' added new subcategory "' . $subcategoryName . '" from ' . $ip;
		$log_text = array(
			'code' => "demo",
			'addID' => $addID,
			'logText' => $text
		);
		$result = $this->db->query("SELECT * FROM subcategorymaster WHERE LOWER(subcategoryName)='".$subcategoryName."' AND categoryCode='".$categoryCode."' and (`isDelete` IS NULL OR `isDelete`='0')");
		if ($result->num_rows() > 0) {
			$data['error_message'] = 'Duplicate subcategory name';
			
			$table_name = 'categorymaster';
			$orderColumns = array("categorymaster.*");
			$cond = array('categorymaster' . '.isDelete' => 0, 'categorymaster' . '.isActive' => 1, 'categorymaster.mainCategoryCode' => 'MCAT_1');
			$data['category'] = $this->GlobalModel->selectQuery($orderColumns, $table_name, $cond);
			$this->load->view('dashboard/header');
			$this->load->view('dashboard/subcategory/add', $data);
			$this->load->view('dashboard/footer');
		} else {
			$this->form_validation->set_rules('categoryCode', 'Category Name', 'required');
			$this->form_validation->set_rules('subcategoryName', 'subcategory Name', 'required');
			if ($this->form_validation->run() == FALSE) {
				$data['error_message'] = '* Fields are Required!';
				$table_name = 'categorymaster';
				$orderColumns = array("categorymaster.*");
				$cond = array('categorymaster' . '.isDelete' => 0, 'categorymaster' . '.isActive' => 1, 'categorymaster.mainCategoryCode' => 'MCAT_1');
				$data['category'] = $this->GlobalModel->selectQuery($orderColumns, $table_name, $cond);
				$this->load->view('dashboard/header');
				$this->load->view('dashboard/subcategory/add', $data);
				$this->load->view('dashboard/footer');
			} else {
				$data = array(
					'categoryCode' => $categoryCode,
					'subcategoryName' => $subcategoryName,
					'addID' => $addID,
					'addIP' => $ip,
					'isActive' => $isActive
				);

				$code = $this->GlobalModel->addWithoutYear($data, 'subcategorymaster', 'PSCAT');
				if ($code != 'false') {
					$response['status'] = true;
					$response['message'] = "Subcategory Added Successfully";
					$this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
				} else {
					$response['status'] = false;
					$response['message'] = "Failed To Add Subcategory";
				}
				$this->session->set_flashdata('response', json_encode($response));
				redirect(base_url() . 'Subcategory/listRecords', 'refresh');
			}
		}
	}

	public function update()
	{
		$subcategoryName = trim($this->input->post("subcategoryName"));
		$categoryCode = trim($this->input->post("categoryCode"));
		$code =  $this->input->post('code');
		$addID = $this->session->userdata['logged_in' . $this->session_key]['code'];
		$userRole = $this->session->userdata['logged_in' . $this->session_key]['role'];
		$userName = $this->session->userdata['logged_in' . $this->session_key]['username'];
		$role = "";
		$isActive=0;
		if(trim($this->input->post("isActive"))!=''){
			$isActive = trim($this->input->post("isActive"));
		}
		switch ($userRole) {
			case "ADM":
				$role = "Admin";
				break;
			case "USR":
				$role = "User";
				break;
		}

		$ip = $_SERVER['REMOTE_ADDR'];
		$text = $role . " " . $userName . ' updated subcategory "' . $subcategoryName . '" from ' . $ip;
		$log_text = array(
			'code' => "demo",
			'addID' => $addID,
			'logText' => $text
		);
		$result = $this->db->query("SELECT * FROM subcategorymaster WHERE LOWER(subcategoryName)='".$subcategoryName."' AND categoryCode='".$categoryCode."' AND code!= '" . $code . "' and (`isDelete` IS NULL OR `isDelete`='0')");
		if ($result->num_rows() > 0) {
			$data = array('error_message' => 'Duplicate Subcategory');
			$table_name = 'categorymaster';
			$orderColumns = array("categorymaster.*");
			$cond = array('categorymaster' . '.isDelete' => 0, 'categorymaster' . '.isActive' => 1, 'categorymaster.mainCategoryCode' => 'MCAT_1');
			$data['category'] = $this->GlobalModel->selectQuery($orderColumns, $table_name, $cond);
			$data['query'] = $this->GlobalModel->selectDataById($code, 'subcategorymaster');
			$this->load->view('dashboard/header');
			$this->load->view('dashboard/subcategory/edit', $data);
			$this->load->view('dashboard/footer');
		} else {
			$this->form_validation->set_rules('subcategoryName', 'subcategory Name', 'required');
			$this->form_validation->set_rules('categoryCode', 'Category Name', 'required');
			if ($this->form_validation->run() == FALSE) {
				$data['error_message'] = '* Fields are Required!';
				$table_name = 'categorymaster';
				$orderColumns = array("categorymaster.*");
				$cond = array('categorymaster' . '.isDelete' => 0, 'categorymaster' . '.isActive' => 1, 'categorymaster.mainCategoryCode' => 'MCAT_1');
				$data['category'] = $this->GlobalModel->selectQuery($orderColumns, $table_name, $cond);
				$data['query'] = $this->GlobalModel->selectDataById($code, 'subcategorymaster');
				$this->load->view('dashboard/header');
				$this->load->view('dashboard/subcategory/edit', $data);
				$this->load->view('dashboard/footer');
			} else {
				$data = array(
					'categoryCode' => $categoryCode,
					'subcategoryName' => $subcategoryName,
					'editID' => $addID,
					'editIP' => $ip,
					'isActive' => $isActive
				);
				$result = $this->GlobalModel->doEdit($data, 'subcategorymaster', $code);
				if ($result != 'false') {
					$response['status'] = true;
					$response['message'] = "Subcategory Successfully Updated.";
					$this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
				} else {
					$response['status'] = false;
					$response['message'] = "No change In Subcategory";
				}
				$this->session->set_flashdata('response', json_encode($response));
				redirect(base_url() . 'Subcategory/listRecords', 'refresh');
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
		$dataQ = $this->GlobalModel->selectDataByField('code', $code, 'subcategorymaster');
		$subcategoryName = '';
		foreach ($dataQ->result() as $row) {
			$subcategoryName = $row->subcategoryName;
		}
		$text = $role . " " . $userName . ' deleted subcategory "' . $subcategoryName . '" from ' . $ip;
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
		$resultData = $this->GlobalModel->doEdit($data, 'subcategorymaster', $code);
		echo $this->GlobalModel->delete($code, 'subcategorymaster');
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
		$tableName = "subcategorymaster";
		$orderColumns = array("subcategorymaster.*,categorymaster.categoryName");
		$condition = array('subcategorymaster.code'=>$code, 'categorymaster.mainCategoryCode' => 'MCAT_1');
		$orderBy = array('subcategorymaster' . '.id' => 'DESC');
		$joinType = array('categorymaster' => 'inner');
		$join = array('categorymaster' => 'categorymaster.code=subcategorymaster.categoryCode');
		$groupByColumn = array();
		$limit = $this->input->GET("length");
		$offset = $this->input->GET("start");
		$extraCondition = " subcategorymaster.isDelete=0 OR subcategorymaster.isDelete IS NULL";
		$like = array();
		$Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
		$modelHtml = '<form>';
		$activeStatus = "";
		foreach ($Records->result() as $row) {
			if ($row->isActive == "1") {
				$activeStatus = '<span class="label label-sm label-success">Active</span>';
			} else {
				$activeStatus = '<span class="label label-sm label-warning">Inactive</span>';
			}
			$modelHtml .= '<div class="form-row">
				<div class="col-md-3 mb-3"><label> <b> Code:</b> </label>
					<input type="text" value="' . $row->code . '" class="form-control-line"  readonly>
				</div>
				<div class="col-md-5 mb-3"><label> <b>Category Name:</b> </label>
					<input type="text" class="form-control-line" value="' . $row->categoryName . '"  readonly>
				</div>
				<div class="col-md-4 mb-3"><label> <b>Subcategory Name:</b> </label>
						<input type="text" class="form-control-line" value="' . $row->subcategoryName . '"  readonly>
				</div> 
			</div>
			<div class="form-group">' . $activeStatus . '</div>';
			$text = $role . " " . $userName . ' viewed subcategory "' . $row->subcategoryName . '" from ' . $ip;
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