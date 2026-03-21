<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Menucategory extends CI_Controller
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
		$this->load->view('dashboard/menucategory/list', $data);
		$this->load->view('dashboard/footer');
	}

	public function add()
	{
		// $this->load->view('dashboard/header');
		$this->load->view('dashboard/menucategory/add');
		// $this->load->view('dashboard/footer');	
	}

	public function edit()
	{
		$code = $this->input->post('code');
		$data['query'] = $this->GlobalModel->selectDataById($code, 'menucategory');
		// $this->load->view('dashboard/header');
		$this->load->view('dashboard/menucategory/edit', $data);
		// $this->load->view('dashboard/footer');
	}

	public function getMenuCategoryList()
	{
		$tableName = "menucategory";
		$search = $this->input->GET("search")['value'];
		$orderColumns = array("menucategory.*");
		$condition = array();
		$orderBy = array('menucategory' . '.id' => 'DESC');
		$joinType = array();
		$join = array();
		$groupByColumn = array();
		$limit = $this->input->GET("length");
		$offset = $this->input->GET("start");
		$extraCondition = " (menucategory.isDelete=0 OR menucategory.isDelete IS NULL)";
		$like = array("menucategory.menuCategoryName" => $search . "~both");
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
									<a class="dropdown-item edit" data-seq="' . $row->code . '" id="' . $row->code . '"><i class="ti-pencil-alt"></i> Edit</a>
									<a class="dropdown-item  mywarning" data-seq="' . $row->code . '" id="' . $row->code . '"><i class="ti-trash" href></i> Delete</a>
								</div>
							</div>';

				$data[] = array(
					$srno,
					$row->code,
					$row->menuCategoryName,
					$row->priority,
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
		$menuCategoryName = trim($this->input->post("menuCategoryName"));
		$priority = trim($this->input->post("priority"));
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
		$text = $role . " " . $userName . ' added new Menu Category "' . $menuCategoryName . '" from ' . $ip;

		$log_text = array(
			'code' => "demo",
			'addID' => $addID,
			'logText' => $text
		);

		//Activity Track Ends

		$result = $this->GlobalModel->checkDuplicateRecord('menuCategoryName', $menuCategoryName, 'menucategory');
		if ($result == true) {
			$data = array('error_message' => 'Duplicate Menu Category Name');
			$response['status'] = 'error';
			$response['message'] = 'Duplicate Menu Category Name';
		} else {
			$data = array(
				'menuCategoryName' => $menuCategoryName,
				'addID' => $addID,
				'addIP' => $ip,
				'priority'=>$priority,
				'isActive' => trim($this->input->post("isActive")),
			);
			$code = $this->GlobalModel->addWithoutYear($data, 'menucategory', 'MNCAT');
			if ($code != 'false') {
				$response['status'] = 'true';
				$response['message'] = "Menu Category Successfully Added.";
				$this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
			} else {
				$response['status'] = 'false';
				$response['message'] = "Failed To Add Menu Category";
			}
		}
		echo json_encode($response);
	}

	public function update()
	{
		$code =  $this->input->post('code');
		$menuCategoryName = trim($this->input->post("menuCategoryName"));
		$priority = trim($this->input->post("priority"));
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
		$text = $role . " " . $userName . ' uopdated Menu Category "' . $menuCategoryName . '" from ' . $ip;

		$log_text = array(
			'code' => "demo",
			'addID' => $addID,
			'logText' => $text
		);

		//Activity Track Ends 
		$result = $this->GlobalModel->checkDuplicateRecordNotEqualtoCode('menuCategoryName', $menuCategoryName, 'menucategory', $code);

		if ($result == true) {
			$data = array('error_message' => 'Duplicate Menu Category Name');
			$response['status'] = 'error';
			$response['message'] = 'Duplicate Menu Category Name';
		} else {
			$data = array(
				'menuCategoryName' => $menuCategoryName,
				'priority'=> $priority,
				'editID' => $addID,
				'editIP' => $ip,
				'isActive' => $isActive
			);
			$result = $this->GlobalModel->doEdit($data, 'menucategory', $code);
			if ($result != 'false') { 
				$response['status'] = 'true';
				$response['message'] = "Menu Category Successfully Updated.";
				$this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
			} else {
				$response['status'] = 'false';
				$response['message'] = "No change In Menu Category";
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
		$dataQ = $this->GlobalModel->selectDataByField('code', $code, 'menucategory');
		$categoryName = ''; 
		foreach ($dataQ->result() as $row) {
			$categoryName = $row->menuCategoryName;
		}
		$text = $role . " " . $userName . ' deleted Menu Category "' . $categoryName . '" from ' . $ip;
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
		$resultData = $this->GlobalModel->doEdit($data, 'menucategory', $code);
		echo $this->GlobalModel->delete($code, 'menucategory');
	}
}
?>