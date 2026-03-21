<?php

defined('BASEPATH') or exit('No direct script access allowed');

class MenuSubcategory extends CI_Controller
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
		$this->load->view('dashboard/menusubcategory/list', $data);
		$this->load->view('dashboard/footer');
	}

	public function add()
	{
		$data['query1'] = $this->GlobalModel->selectQuery('menucategory.*', 'menucategory', array("menucategory.isActive" => 1),array("menucategory.menuCategoryName"=>"ASC"));
		$this->load->view('dashboard/menusubcategory/add', $data);
	}

	public function edit()
	{
		$code = $this->input->post('code');
		$data['query'] = $this->GlobalModel->selectDataById($code, 'menusubcategory');
		$data['query1'] = $this->GlobalModel->selectQuery('menucategory.*', 'menucategory', array("menucategory.isActive" => 1),array("menucategory.menuCategoryName"=>"ASC"));
		$this->load->view('dashboard/menusubcategory/edit', $data);
	}

	public function getMenuSubCategoryList()
	{
		$tableName = "menusubcategory";
		$search = $this->input->GET("search")['value'];
		$orderColumns = array("menusubcategory.*,menucategory.menuCategoryName");
		$condition = array('menucategory.isActive' => 1);
		$orderBy = array('menusubcategory' . '.id' => 'ASC');
		$joinType = array('menucategory' => 'inner');
		$join = array('menucategory' => 'menucategory.code=menusubcategory.menuCategoryCode');
		$groupByColumn = array();
		$limit = $this->input->GET("length");
		$offset = $this->input->GET("start");
		$extraCondition = " (menusubcategory.isDelete=0 OR menusubcategory.isDelete IS NULL)";
		$like = array("menusubcategory.code" => $search . "~both","menusubcategory.menuSubCategoryName" => $search . "~both","menucategory.menuCategoryName" => $search . "~both");
		$Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
		$srno = $_GET['start'] + 1;
		$dataCount = 0;
		$data = array();
		$query = $this->db->last_query();
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
									<a class="dropdown-item edit" data-seq="' . $row->code . '" id="' . $row->code . '"><i class="ti-pencil-alt"></i> Edit</a>
									<a class="dropdown-item  mywarning" data-seq="' . $row->code . '" id="' . $row->code . '"><i class="ti-trash" href></i> Delete</a>
								</div>
							</div>';

				$data[] = array(
					$srno,
					$row->code,
					$row->menuCategoryName,
					$row->menuSubCategoryName,
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
			"query"			  => 	 $query
		);
		echo json_encode($output);
	}

	public function save()
	{
		$menuSubCategoryName = trim($this->input->post("menuSubCategoryName"));
		$menuCategoryCode = trim($this->input->post("menuCategoryCode"));
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
		$text = $role . " " . $userName . ' added new Sub-menu Category "' . $menuSubCategoryName . '" from ' . $ip; 
		$log_text = array(
			'code' => "demo",
			'addID' => $addID,
			'logText' => $text
		); 
		//Activity Track Ends 
		$result = $this->GlobalModel->checkDuplicateRecord('menuSubCategoryName', $menuSubCategoryName, 'menusubcategory'); 
		if ($result == true) {
			$data = array('error_message' => 'Duplicate Sub-menu Category Name');
			$response['status'] = 'error';
			$response['message'] = 'Duplicate Sub-menu Category Name';
		} else {
			$data = array(
				'menuCategoryCode' => $menuCategoryCode,
				'menuSubCategoryName' => $menuSubCategoryName,
				'addID' => $addID,
				'addIP' => $ip,
				'isActive' => trim($this->input->post("isActive")),
			);

			$code = $this->GlobalModel->addWithoutYear($data, 'menusubcategory', 'SBCAT');

			if ($code != 'false') {
				$response['status'] = 'true';
				$response['message'] = "Sub-menu Category Successfully Added.";
				$this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
			} else {
				$response['status'] = 'false';
				$response['message'] = "Failed To Add Sub-menu Category";
			}
		}
		echo json_encode($response);
	}

	public function update()
	{
		$code =  $this->input->post('code');
		$menuSubCategoryName = trim($this->input->post("menuSubCategoryName"));
		$menuCategoryCode = trim($this->input->post("menuCategoryCode"));
		$isActive = trim($this->input->post("isActive"));
		if($isActive!=1){
			$isActive = 0;
		} else {
			$isActive = 1;
		}
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
		$text = $role . " " . $userName . ' uopdated Sub-menu Category "' . $menuSubCategoryName . '" from ' . $ip;

		$log_text = array(
			'code' => "demo",
			'addID' => $addID,
			'logText' => $text
		);

		//Activity Track Ends 
		$result = $this->GlobalModel->checkDuplicateRecordNotEqualtoCode('menuSubCategoryName', $menuSubCategoryName, 'menusubcategory', $code);

		if ($result == true) {
			$data = array('error_message' => 'Duplicate Sub-menu Category Name');
			$response['status'] = 'error';
			$response['message'] = 'Duplicate Sub-menu Category Name';
		} else {
			$data = array(
				'menuCategoryCode' => $menuCategoryCode,
				'menuSubCategoryName' => $menuSubCategoryName,
				'editID' => $addID,
				'editIP' => $ip,
				'isActive' =>$isActive
			);
			$result = $this->GlobalModel->doEdit($data, 'menusubcategory', $code);
			if ($result != 'false') { 
				$response['status'] = 'true';
				$response['message'] = "Sub-menu Category Successfully Updated.";
				$this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
			} else {
				$response['status'] = 'false';
				$response['message'] = "No change In Sub-menu Category";
			}
		}
		echo json_encode($response);
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
		$dataQ = $this->GlobalModel->selectDataByField('code', $code, 'menusubcategory');
		$categoryName = '';
		foreach ($dataQ->result() as $row) {
			$categoryName = $row->menuSubCategoryName;
		}
		$text = $role . " " . $userName . ' deleted Sub-menu Category "' . $categoryName . '" from ' . $ip;
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
		$resultData = $this->GlobalModel->doEdit($data, 'menusubcategory', $code); 
		echo $this->GlobalModel->delete($code, 'menusubcategory');
	}
}
?>