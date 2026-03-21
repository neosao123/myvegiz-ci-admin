<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Activity extends CI_Controller
{
	var $session_key;
	public function __construct()
	{
		parent::__construct();
		$this->load->model('GlobalModel');
		$this->load->model('GlobalModel1');
		$this->session_key = $this->session->userdata('key' . SESS_KEY);
		if(!isset($this->session->userdata['logged_in' . $this->session_key]['code'])){
			redirect('Admin/login','refresh');
		}
	}

	public function getActivityList()
	{

		$tables = array('activitymaster');

		$requiredColumns = array(
			array('logText', 'date', 'addID')
		);
		$conditions = array();

		$addID = $this->input->GET("addID");
		$fromDate = $this->input->GET("fromDate");
		$toDate = $this->input->GET("toDate");
		$fromDate = $fromDate . " " . "00:00:00";
		$toDate = $toDate . " " . "23:59:59";

		$extraConditionColumnNames = array(
			array("addID")
		);

		$extraConditions = array(
			array($addID)
		);

		$extraDateConditionColumnNames = array(
			array("date")
		);

		$extraDateConditions = array(
			array($fromDate . '~' . $toDate)
		);

		$Records = $this->GlobalModel1->make_datatables($tables, $requiredColumns, $conditions, $extraConditionColumnNames, $extraConditions, $extraDateConditionColumnNames, $extraDateConditions);
		// print_r($Records->result());
		// exit();

		$srno = $_GET['start'] + 1;
		$data = array();
		foreach ($Records->result() as $row) {
			$data[] = array(
				$srno,
				$row->logText_00,
				$row->date_01
			);

			$srno++;
		}

		$dataCount = $this->GlobalModel1->get_all_data($tables, $requiredColumns, $conditions, $extraConditionColumnNames, $extraConditions, $extraDateConditionColumnNames, $extraDateConditions);
		$output = array(
			"draw"  => intval($_GET["draw"]),
			"recordsTotal"  => $dataCount,
			"recordsFiltered" => $dataCount,
			"data"  => $data
		);
		echo json_encode($output);
	}


	public function activityList()
	{
		$data['report'] = $this->GlobalModel->selectData('usermaster');
		$data['report'] = $this->GlobalModel->selectData('activitymaster');
		print_r($data['report']->result());
		exit();
		$this->load->view('dashboard/header');
		$this->load->view('dashboard/activityList/activityList', $data);
		$this->load->view('dashboard/footer');
	}
}
?>