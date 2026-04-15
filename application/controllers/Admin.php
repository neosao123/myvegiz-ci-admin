<?php
defined('BASEPATH') or exit('No direct script access allowed');
//include APPPATH . 'third_party/Realtime/bin/server.php';
class Admin extends CI_Controller
{
	var $session_key;
	public function __construct()
	{
		parent::__construct();
		$this->load->model('GlobalModel');
		$this->load->model('ApiModel');
		$this->load->library('sendsms');
	}

	public function index()
	{
		$this->session_key = $this->session->userdata('key' . SESS_KEY);
		if (isset($this->session->userdata['logged_in' . $this->session_key]['code'])) {
			$code = ($this->session->userdata['logged_in' . $this->session_key]['code']);
			//active area	
			$data['activePlace'] = $this->GlobalModel->getCountOfPerticularValue('customaddressmaster', 'isService', '1');

			//pending and cancelled order count
			$data['pendingRes'] = $this->GlobalModel->getCountOfValueWithDate('ordermaster', 'orderStatus', 'PND');
			$data['qry'] = $this->db->last_query();
			$data['cancelledRes'] = $this->GlobalModel->getCountOfValueWithDate('ordermaster', 'orderStatus', 'CAN');

			//placed and delivered and rejected order count
			$data['placeOrder'] = $this->GlobalModel->getCountOfAdmAction('ordermaster', 'orderStatus', 'PLC', 'placedTime');
			$data['deliverdOrder'] = $this->GlobalModel->getCountOfAdmAction('ordermaster', 'orderStatus', 'DEL', 'deliveredTime');
			$data['rejectOrder'] = $this->GlobalModel->getCountOfAdmAction('ordermaster', 'orderStatus', 'RJT', 'rejectedTime');

			//recent inward count
			$data['recentInward'] = $this->GlobalModel->getCountWthField('inwardentries', 'code');

			//reset password user count
			$data['resetPwd'] = $this->GlobalModel->getTableRecordCount('resetpassword');

			//Today's sales and purchase amount count 
			$data['saleAmt'] = $this->GlobalModel->getCountWithAmount('ordermaster', 'totalPrice', 'deliveredTime');
			$data['purchaseAmt'] = $this->GlobalModel->getCountWithAmount('inwardentries', 'total', 'inwardDate');

			//valueable coustmer count
			$data['customer'] = $this->GlobalModel->getCountOfPerticularValue('clientmaster', 'isDelete', 0);
			$data['oreder'] = $this->GlobalModel->selectData('ordermaster'); // 			print_r($data);
// 			exit();

			$orderColumns = array("count(resetpassword.id) pCount,usermaster.code,usermaster.role");
			$cond = array("resetpassword.isActive" => 1, "usermaster.role" => "DLB");
			$orderBy = array('resetpassword' . ".id" => 'ASC');
			$join = array("usermaster" => "usermaster.code = resetpassword.userCode");
			$joinType = array("usermaster" => "inner");
			$like = array();
			$limit = "";
			$offset = "";
			$groupByColumn = array();
			$extraCondition = "";

			$p_result = $this->GlobalModel->selectQuery($orderColumns, 'resetpassword', $cond, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
			if ($p_result) {
				$data["dlbReset"] = $p_result->result_array()[0]["pCount"];
			}
			else {
				$data["dlbReset"] = 0;
			}


			$orderColumns1 = array("count(resetpassword.id) puCount,clientmaster.code,clientmaster.isDelete");
			$cond1 = array("resetpassword.isActive" => 1, "clientmaster.isDelete" => "0");
			$orderBy1 = array('resetpassword' . ".id" => 'ASC');
			$join1 = array("clientmaster" => "clientmaster.code = resetpassword.userCode");
			$joinType1 = array("clientmaster" => "inner");
			$like1 = array();
			$limit1 = "";
			$offset1 = "";
			$groupByColumn1 = array();
			$extraCondition1 = "";

			$c_result = $this->GlobalModel->selectQuery($orderColumns1, 'resetpassword', $cond1, $orderBy1, $join1, $joinType1, $like1, $limit1, $offset1, $groupByColumn1, $extraCondition1);
			if ($c_result) {
				$data["usrReset"] = $c_result->result_array()[0]["puCount"];
			}
			else {
				$data["usrReset"] = 0;
			}



			$this->load->view('dashboard/header');
			$this->load->view('dashboard/dashboard', $data);
			$this->load->view('dashboard/footer');
		}
		else {
			$data['error_message'] = $this->session->flashdata('error_message');
			$data['logout_message'] = $this->session->flashdata('logout_message');
			$this->load->view('dashboard/login', $data);
		}
	}

	public function testRealtime()
	{
		$this->session_key = $this->session->userdata('key' . SESS_KEY);
		if (isset($this->session->userdata['logged_in' . $this->session_key]['code'])) {
			$data['username'] = ($this->session->userdata['logged_in' . $this->session_key]['username']);
			$data['code'] = ($this->session->userdata['logged_in' . $this->session_key]['code']);
			$this->load->view('dashboard/header');
			$this->load->view('dashboard/realtime', $data);
			$this->load->view('dashboard/footer');
		}
		else {
			$data['error_message'] = $this->session->flashdata('error_message');
			$data['logout_message'] = $this->session->flashdata('logout_message');
			$this->load->view('dashboard/login', $data);
		}
	}

	public function login()
	{
		$data['error_message'] = $this->session->flashdata('error_message');
		$data['logout_message'] = $this->session->flashdata('logout_message');
		$this->load->view('dashboard/login', $data);
	}

	public function welcome()
	{
		$this->session_key = $this->session->userdata('key' . SESS_KEY);
		//$this->load->view('dashboard/tempheader');
		$this->load->view('dashboard/header');
		$this->load->view('dashboard/welcome');
		$this->load->view('dashboard/footer');
	}

	public function tempHeader()
	{
		$this->load->view('dashboard/tempheader');
		$this->load->view('dashboard/footer');
	}

	public function db_backup()
	{
		$this->load->helper('url');
		$this->load->helper('file');
		$this->load->helper('download');
		$this->load->library('zip');
		$this->load->dbutil();
		$db_format = array('format' => 'zip', 'filename' => 'wf_eveggies.sql');
		$backup = & $this->dbutil->backup($db_format);
		$dbname = 'backup-on-' . date('Y-m-d') . '.zip';
		$save = 'assets/db_backup/' . $dbname;
		write_file($save, $backup);
		force_download($dbname, $backup);
	}

	//dashboard inward list
	public function getInwardList()
	{
		$offset = $this->input->post('start') ?? $this->input->get('start');
		$limit = 5;
		$tableName = 'inwardentries';
		$orderColumns = array("inwardentries.*");
		$cond = array();
		$orderBy = array('inwardentries' . ".id" => 'DESC');
		$joinType = array();
		$join = array();
		$groupByColumn = array();

		$currentDate = date("Y-m-d");
		$extraCondition = "inwardentries.inwardDate BETWEEN '" . $currentDate . "' AND '" . $currentDate . " 23:59:59.999' AND inwardentries.isDelete is Null or inwardentries.isDelete=0";

		$Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $cond, $orderBy, $join, $joinType, array(), $limit, $offset, $groupByColumn, $extraCondition);

		if ($Records) {
			$totalRecords = sizeof($Records->result());
			$dataArray = array();
			$dataCountArray = array();
			for ($i = 0; $i < $totalRecords; $i++) {

				$inwardCode = $Records->result()[$i]->code;
				$offset1 = $this->input->post('start') ?? $this->input->get('start');
				$limit1 = 1;
				$tableName1 = 'inwardlineentries';
				$orderColumns1 = array("inwardlineentries.*,storagemaster.storageName,storagemaster.storageSName");
				$cond1 = array('inwardlineentries.inwardCode' => $inwardCode);
				$orderBy1 = array('inwardlineentries' . ".id" => 'DESC');
				$joinType1 = array('storagemaster' => 'inner');
				$join1 = array('storagemaster' => 'storagemaster.storageSName = inwardlineentries.storageCode');
				$groupByColumn1 = array();
				$extraCondition1 = array();
				$lineRecords = $this->GlobalModel->selectQuery($orderColumns1, $tableName1, $cond1, $orderBy1, $join1, $joinType1, array(), $limit1, $offset1, $groupByColumn1, $extraCondition1);
				$srno = (intval($offset1) > 0 ? intval($offset1) : 0) + 1;
				$data = array();
				$dataCount = sizeof($lineRecords->result());
				array_push($dataCountArray, $dataCount);

				foreach ($lineRecords->result() as $lineRow) {

					$data = array(
						$srno,
						$lineRow->productCode,
						$lineRow->productName,
						$lineRow->productQuantity,
						$lineRow->storageName
					);
					array_push($dataArray, $data);
					$srno++;
				}
			} //new for loop
			$totalCount = array_sum($dataCountArray);
			// $dataCount = sizeof($this->GlobalModel->selectQuery($orderColumns1,$tableName1,$cond1,$orderBy1,$join1,$joinType1,array(),$limit1,$offset1,$groupByColumn1, $extraCondition1)->result_array());
			$output = array(
				"draw" => intval($this->input->post("draw") ?? $this->input->get("draw") ?? 0),
				"recordsTotal" => $totalCount,
				"recordsFiltered" => $totalCount,
				"data" => $dataArray
			);
			echo json_encode($output);
		}
		else {

			$output = array(
				"draw" => intval($this->input->post("draw") ?: 1),
				"recordsTotal" => 0,
				"recordsFiltered" => 0,
				"data" => '',
			);

			echo json_encode($output);
		}
	}


	public function getAllProductData()
	{

		$product = $this->input->get('current_prod');
		$dataPro = $this->db->query("SELECT `productName`,`code` FROM `productmaster` WHERE isDelete IS NULL OR isDelete=0 AND `productName` LIKE '" . $product . "%' LIMIT 10");

		$place = '';
		foreach ($dataPro->result() as $pin) {

			$place .= '<option value="' . $pin->code . '">' . $pin->productName . '</option>';
		}
		echo $place;
	}

	public function getProductList()
	{
		$productCode = $this->input->post('productCode') ?? $this->input->get('productCode');
		$currentDate = date("Y-m-d");

		$offset = $this->input->post('start') ?? $this->input->get('start');
		$limit = 1;
		$tableName = 'productmaster';
		$orderColumns = array("productmaster.code,productmaster.productName,productmaster.productCategory,inwardlineentries.inwardCode,inwardlineentries.productCode,inwardlineentries.storageCode,inwardlineentries.addDate,storagemaster.storageName,storagemaster.storageSName,inwardentries.code,inwardentries.inwardDate");
		$cond = array('productmaster.code' => $productCode);
		$orderBy = array('productmaster' . ".id" => 'DESC');
		$joinType = array('inwardlineentries' => 'left', 'storagemaster' => 'left', 'inwardentries' => 'inner');
		$join = array('inwardlineentries' => 'productmaster.code = inwardlineentries.productCode', 'inwardentries' => 'inwardentries.code = inwardlineentries.inwardCode', 'storagemaster' => 'inwardlineentries.storageCode = storagemaster.storageSName');
		$groupByColumn = array();

		$extraCondition = "inwardentries.inwardDate BETWEEN '" . $currentDate . "' AND '" . $currentDate . " 23:59:59.999'";

		$Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $cond, $orderBy, $join, $joinType, array(), $limit, $offset, $groupByColumn, $extraCondition);
		if ($Records) {
			$srno = (intval($offset) > 0 ? intval($offset) : 0) + 1;
			$data = array();

			foreach ($Records->result() as $row) {


				$currentDate = date("Y-m-d");
				$query = $this->db->query("SELECT orderCode,productCode  FROM `orderlineentries` WHERE `productCode`='" . $row->productCode . "' AND `addDate` BETWEEN '" . $currentDate . "' AND '" . $currentDate . " 23:59:59.999' ");
				$size = sizeof($query->result());
				$count = 1;
				$orderCodes = "";


				foreach ($query->result() as $orderLine) {
					$comma = "";
					if ($size != $count) {
						$comma = ",";
					}
					$orderCodes .= "'" . $orderLine->orderCode . "'" . $comma;
					$count++;
				}

				$totalAmount = $this->db->query("SELECT sum(`totalPrice`) as total,orderStatus FROM `ordermaster` WHERE code IN(" . $orderCodes . ") AND `orderStatus` = 'DEL' AND `deliveredTime` BETWEEN '" . $currentDate . "' AND '" . $currentDate . " 23:59:59.999' "); //01:00:01
				// print_r($totalAmount->result());
				if ($totalAmount != false) {
					foreach ($totalAmount->result() as $amt) {
						$amount = $amt->total;
					}
				}
				else {
					$amount = 0;
				}
				$data[] = array(
					$srno,
					$row->productCode,
					$row->productName,
					$row->storageName,
					$amount
				);
				$srno++;
			}

			$dataCount = sizeof($this->GlobalModel->selectQuery($orderColumns, $tableName, $cond, $orderBy, $join, $joinType, array(), $limit, $offset, $groupByColumn, $extraCondition)->result_array());
			$output = array(
				"draw" => intval($this->input->post("draw") ?? $this->input->get("draw") ?? 0),
				"recordsTotal" => $dataCount,
				"recordsFiltered" => $dataCount,
				"data" => $data
			);
			echo json_encode($output);
		}
		else {
			$output = array(
				"draw" => intval($this->input->post("draw") ?? $this->input->get("draw") ?? 0),
				"recordsTotal" => 0,
				"recordsFiltered" => 0,
				"data" => '',
			);

			echo json_encode($output);
		}
	}

	public function getOrderCounts()
	{
		$today = date('Y-m-d');
		$orderAssignedDelvBoyVege = $orderAssignedDelvBoyFood = $absentDelvBoysVege = $absentDelvBoysFood = $totalDelvBoysFood = $totalDelvBoysVege = $presentDelBoysFood = $presentDelBoysVege = $totalDelvBoys = $rejectedOrders = $deliveredOrders = $releasedOrders = $cancelledOrders = $pickedOrders = $placedOrders = $pendingOrders = $totalOrders = 0;
		$joinType = $join = $like = $orderBy = array();
		$limit = $offset = $extraCondition = "";
		//$condition = array("ordermaster.isActive" => 1,"ordermaster.paymentStatus"=>"PID");
		$condition = array("ordermaster.isActive" => 1);
		$groupBy = array();
		$extraCondition = "(`ordermaster`.`orderStatus` NOT IN ('RJT', 'CAN')) AND (`ordermaster`.`addDate` BETWEEN '" . $today . " 00:00:00' AND '" . $today . " 23:59:59')";
		$allOrders = $this->GlobalModel->selectQuery("COUNT(DISTINCT `ordermaster`.`code`) as cnt", "ordermaster", $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupBy, $extraCondition);
		//echo $this->db->last_query();

		if ($allOrders) {
			$totalOrders = $allOrders->row()->cnt;
		}
		$condition = array("vendorordermaster.isActive" => 1);
		$groupByV = array();
		$extraConditionV = "(`vendorordermaster`.`orderStatus` NOT IN ('RJT', 'CAN')) AND (`vendorordermaster`.`addDate` BETWEEN '" . $today . " 00:00:00' AND '" . $today . " 23:59:59')";
		$allOrders = $this->GlobalModel->selectQuery("COUNT(DISTINCT `vendorordermaster`.`code`) as cnt", "vendorordermaster", $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByV, $extraConditionV);
		//echo $this->db->last_query();

		if ($allOrders) {
			$totalOrders += $allOrders->row()->cnt;
		}
		//echo $totalOrders;
// 		$condition1['bookorderstatuslineentries.statusLine'] = 'PND';
// 		$groupBy1 = array();
// 		$extraCondition1 = "";//" (bookorderstatuslineentries.addDate between '" . $today . " 00:00:00' and '" . $today . " 23:59:59')";
// 		$allPendingOrders = $this->GlobalModel->selectQuery("count(distinct bookorderstatuslineentries.orderCode) as cnt", "bookorderstatuslineentries", $condition1, $orderBy, $join, $joinType, $like, $limit, $offset, $groupBy1, $extraCondition1);
// 		if ($allPendingOrders) {
// 			$pendingOrders = $allPendingOrders->result()[0]->cnt;
// 		}

		$condition['vendorordermaster.orderStatus'] = 'PND';
		//$condition['vendorordermaster.paymentStatus'] = 'PID';
		$groupBy1 = array();
		$extraCondition1 = "";
		//$extraCondition1 = " (vendorordermaster.addDate between '" . $today . " 00:00:00' and '" . $today . " 23:59:59')";
		$allPendingOrders = $this->GlobalModel->selectQuery("count(distinct vendorordermaster.code) as cnt", "vendorordermaster", $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupBy1, $extraCondition1);
		if ($allPendingOrders) {
			$pendingOrders = $allPendingOrders->result()[0]->cnt;
		}

		$condition1['ordermaster.orderStatus'] = 'PND';
		//$condition1['ordermaster.paymentStatus'] = 'PID';
		$condition1['ordermaster.isActive'] = 1;
		$groupBy1 = array();
		$extraCondition1 = "";
		//$extraCondition1 = "(ordermaster.addDate between '" . $today . " 00:00:00' and '" . $today . " 23:59:59')";
		$allPendingOrders = $this->GlobalModel->selectQuery("count(distinct ordermaster.code) as cnt", "ordermaster", $condition1, $orderBy, $join, $joinType, $like, $limit, $offset, $groupBy1, $extraCondition1);
		//echo $this->db->last_query();
		if ($allPendingOrders) {
			$pendingOrders += $allPendingOrders->result()[0]->cnt;
		}

		//$condition2['ordermaster.orderStatus'] = 'PLC';
		//$condition2['ordermaster.paymentStatus'] = 'PID';
		$groupBy2 = array();
		$extraCondition2 = "(`ordermaster`.`orderStatus` NOT IN ('PND','RJT', 'CAN','DEL','REL','RCH','PUP')) and (ordermaster.editDate between '" . $today . " 00:00:00' and '" . $today . " 23:59:59')";
		$allOrders = $this->GlobalModel->selectQuery("count(distinct ordermaster.code) as cnt", "ordermaster", array(), $orderBy, $join, $joinType, $like, $limit, $offset, $groupBy2, $extraCondition2);
		if ($allOrders) {
			$placedOrders = $allOrders->result()[0]->cnt;
		}

		//$condition2_1['vendorordermaster.orderStatus'] = 'PLC';
		//$condition2_1['vendorordermaster.paymentStatus'] = 'PID';
		$groupBy2_1 = array();
		$extraCondition2_1 = " (`vendorordermaster`.`orderStatus` NOT IN ('PND','RJT', 'CAN','DEL','REL','RCH','PUP')) and (vendorordermaster.editDate between '" . $today . " 00:00:00' and '" . $today . " 23:59:59')";
		$allOrders = $this->GlobalModel->selectQuery("count(distinct vendorordermaster.code) as cnt", "vendorordermaster", array(), $groupBy2_1, $join, $joinType, $like, $limit, $offset, $groupBy2_1, $extraCondition2_1);
		if ($allOrders) {
			$placedOrders += $allOrders->result()[0]->cnt;
		}

		$condition3['ordermaster.orderStatus'] = 'PUP';
		$condition3['ordermaster.isActive'] = '1';
		$groupBy3 = array();
		$extraCondition3 = " (ordermaster.addDate between '" . $today . " 00:00:00' and '" . $today . " 23:59:59')";
		$allOrders = $this->GlobalModel->selectQuery("count(distinct ordermaster.code) as cnt", "ordermaster", $condition3, $groupBy3, $join, $joinType, $like, $limit, $offset, $groupBy3, $extraCondition3);
		if ($allOrders) {
			$pickedOrders = $allOrders->result()[0]->cnt;
		}


		$condition3_1['vendorordermaster.orderStatus'] = 'PUP';
		$condition3_1['vendorordermaster.isActive'] = '1';
		$groupBy3_1 = array();
		$extraCondition3_1 = " (vendorordermaster.addDate between '" . $today . " 00:00:00' and '" . $today . " 23:59:59')";
		$allOrders = $this->GlobalModel->selectQuery("count(distinct vendorordermaster.code) as cnt", "vendorordermaster", $condition3_1, $groupBy3_1, $join, $joinType, $like, $limit, $offset, $groupBy3_1, $extraCondition3_1);
		if ($allOrders) {
			$pickedOrders += $allOrders->result()[0]->cnt;
		}


		$condition4['bookorderstatuslineentries.statusLine'] = 'RJT';
		$groupBy4 = array();
		$extraCondition4 = " (bookorderstatuslineentries.addDate between '" . $today . " 00:00:00' and '" . $today . " 23:59:59')";
		$allOrders = $this->GlobalModel->selectQuery("count(distinct bookorderstatuslineentries.orderCode) as cnt", "bookorderstatuslineentries", $condition4, $orderBy, $join, $joinType, $like, $limit, $offset, $groupBy4, $extraCondition4);
		if ($allOrders) {
			$rejectedOrders = $allOrders->result()[0]->cnt;
		}


		$condition5['ordermaster.orderStatus'] = 'DEL';
		$condition5['ordermaster.isActive'] = 1;
		$groupBy5 = array();
		$extraCondition5 = " (ordermaster.editDate between '" . $today . " 00:00:00' and '" . $today . " 23:59:59')";
		$delOrders = $this->GlobalModel->selectQuery("count(distinct ordermaster.code) as cnt", "ordermaster", $condition5, $orderBy, $join, $joinType, $like, $limit, $offset, $groupBy5, $extraCondition5);
		if ($delOrders) {
			$deliveredOrders = $delOrders->result()[0]->cnt;
		}

		$condition19['vendorordermaster.orderStatus'] = "DEL";
		$condition19['vendorordermaster.isActive'] = 1;
		$extraCondition19 = " (vendorordermaster.editDate between '" . $today . " 00:00:00' and '" . $today . " 23:59:59')";
		$delRestoOrders = $this->GlobalModel->selectQuery("count(distinct ordermaster.code) as cnt", "ordermaster", $condition5, $orderBy, $join, $joinType, $like, $limit, $offset, array(), $extraCondition19);
		if ($delRestoOrders) {
			$deliveredOrders += $delRestoOrders->result()[0]->cnt;
		}

		$condition6['deliveryboystatuslines.orderStatus'] = 'REL';
		$groupBy6 = array();
		$joinType6 = array('vendorordermaster' => "inner");
		$join6 = array('vendorordermaster' => 'vendorordermaster.code=deliveryboystatuslines.orderCode');
		$extraCondition6 = " (vendorordermaster.deliveryBoyCode='' or  vendorordermaster.deliveryBoyCode IS NULL) and (deliveryboystatuslines.addDate between '" . $today . " 00:00:00' and '" . $today . " 23:59:59')";
		$allOrders = $this->GlobalModel->selectQuery("count(deliveryboystatuslines.orderCode) as cnt", "deliveryboystatuslines", $condition6, $orderBy, $join6, $joinType6, $like, $limit, $offset, $groupBy6, $extraCondition6);
		//echo $this->db->last_query();
		if ($allOrders) {
			$releasedOrders = $allOrders->result()[0]->cnt;
		}
		$conditiongroc['deliveryboystatuslines.orderStatus'] = 'REL';
		$groupBygroc = array();
		$joinTypegroc = array('ordermaster' => "inner");
		$joingroc = array('ordermaster' => 'ordermaster.code=deliveryboystatuslines.orderCode');
		$extraConditiongroc = " (ordermaster.deliveryBoyCode='' or ordermaster.deliveryBoyCode is null) and (deliveryboystatuslines.addDate between '" . $today . " 00:00:00' and '" . $today . " 23:59:59')";
		$allOrdersgroc = $this->GlobalModel->selectQuery("count(deliveryboystatuslines.orderCode) as cnt", "deliveryboystatuslines", $conditiongroc, $orderBy, $joingroc, $joinTypegroc, $like, $limit, $offset, $groupBygroc, $extraConditiongroc);
		///echo $this->db->last_query();
		if ($allOrdersgroc) {
			$releasedOrders += $allOrdersgroc->result()[0]->cnt;
		}


		$condition7['bookorderstatuslineentries.statusLine'] = 'CAN';
		$groupBy7 = array();
		$extraCondition7 = " (bookorderstatuslineentries.addDate between '" . $today . " 00:00:00' and '" . $today . " 23:59:59')";
		$allOrders = $this->GlobalModel->selectQuery("count(distinct bookorderstatuslineentries.orderCode) as cnt", "bookorderstatuslineentries", $condition7, $orderBy, $join, $joinType, $like, $limit, $offset, $groupBy7, $extraCondition7);
		if ($allOrders) {
			$cancelledOrders = $allOrders->result()[0]->cnt;
		}

		//echo $this->db->last_query();
		//exit();  

		$condition8['usermaster.isActive'] = '1';
		$condition8['deliveryBoyActiveOrder.isActive'] = '1';
		$condition8['usermaster.role'] = 'DLB';
		$groupBy8 = array();
		$join8['deliveryBoyActiveOrder'] = "deliveryBoyActiveOrder.deliveryBoyCode=usermaster.code";
		$joinType8['deliveryBoyActiveOrder'] = 'inner';
		$delBoys = $this->GlobalModel->selectQuery(" (select Count(usermaster.code) from usermaster INNER JOIN `deliveryBoyActiveOrder` ON `deliveryBoyActiveOrder`.`deliveryBoyCode`=`usermaster`.`code` where deliveryType='food' and usermaster.isActive=1 and `deliveryBoyActiveOrder`.`isActive` = 1 and usermaster.role='DLB') as foodCnt, (select Count(usermaster.code) from usermaster INNER JOIN `deliveryBoyActiveOrder` ON `deliveryBoyActiveOrder`.`deliveryBoyCode`=`usermaster`.`code` where deliveryType='slot' and usermaster.isActive=1 and `deliveryBoyActiveOrder`.`isActive` = 1 and  usermaster.role='DLB') as vegeCnt", "usermaster", $condition8, $orderBy, $join8, $joinType8, $like, 1, $offset, $groupBy8, "");
		//echo $this->db->last_query();
		if ($delBoys) {
			$totalDelvBoysFood = $delBoys->result()[0]->foodCnt;
			$totalDelvBoysVege = $delBoys->result()[0]->vegeCnt;
		}

		$condition9['deliveryBoyActiveOrder.loginStatus'] = '1';
		$condition9['deliveryBoyActiveOrder.isActive'] = '1';
		$condition9['usermaster.isActive'] = '1';
		$condition9['usermaster.deliveryType'] = 'food';
		$condition9['usermaster.role'] = 'DLB';
		$join9['usermaster'] = "deliveryBoyActiveOrder.deliveryBoyCode=usermaster.code";
		$joinType9['usermaster'] = 'inner';
		$groupBy9 = array();
		$delBoys = $this->GlobalModel->selectQuery("count(distinct deliveryBoyActiveOrder.deliveryBoyCode) as cnt", "deliveryBoyActiveOrder", $condition9, $orderBy, $join9, $joinType9, $like, $limit, $offset, $groupBy9, "");
		//echo $this->db->last_query();

		if ($delBoys) {
			$presentDelBoysFood = $delBoys->result()[0]->cnt;
		}

		$condition14['deliveryBoyActiveOrder.loginStatus'] = '1';
		$condition14['deliveryBoyActiveOrder.isActive'] = '1';
		$condition14['usermaster.deliveryType'] = 'slot';
		$condition14['usermaster.isActive'] = '1';
		$condition14['usermaster.role'] = 'DLB';
		$join14['usermaster'] = "deliveryBoyActiveOrder.deliveryBoyCode=usermaster.code";
		$joinType14['usermaster'] = 'inner';
		$groupBy14 = array();
		$delBoys = $this->GlobalModel->selectQuery("count(distinct deliveryBoyActiveOrder.deliveryBoyCode) as cnt", "deliveryBoyActiveOrder", $condition14, $orderBy, $join14, $joinType14, $like, $limit, $offset, $groupBy14, "");
		//echo $this->db->last_query();
		if ($delBoys) {
			$presentDelBoysVege = $delBoys->result()[0]->cnt;
		}

		$condition10['deliveryBoyActiveOrder.loginStatus'] = '0';
		$condition10['deliveryBoyActiveOrder.isActive'] = '1';
		$condition10['usermaster.deliveryType'] = 'food';
		$condition10['usermaster.role'] = 'DLB';
		$condition10['usermaster.isActive'] = '1';
		$join10['usermaster'] = "deliveryBoyActiveOrder.deliveryBoyCode=usermaster.code";
		$joinType10['usermaster'] = 'inner';
		$groupBy10 = array();
		$delBoys = $this->GlobalModel->selectQuery("count(distinct deliveryBoyActiveOrder.deliveryBoyCode) as cnt", "deliveryBoyActiveOrder", $condition10, $orderBy, $join10, $joinType10, $like, $limit, $offset, $groupBy10, "");
		//echo $this->db->last_query();
		//exit();
		if ($delBoys) {
			$absentDelvBoysFood = $delBoys->result()[0]->cnt;
		}
		$condition13['deliveryBoyActiveOrder.loginStatus'] = '0';
		$condition13['deliveryBoyActiveOrder.isActive'] = '1';
		$condition13['usermaster.deliveryType'] = 'slot';
		$condition13['usermaster.role'] = 'DLB';
		$condition13['usermaster.isActive'] = '1';
		$join13['usermaster'] = "deliveryBoyActiveOrder.deliveryBoyCode=usermaster.code";
		$joinType13['usermaster'] = 'inner';
		$groupBy13 = array();
		$delBoys = $this->GlobalModel->selectQuery("count(distinct deliveryBoyActiveOrder.deliveryBoyCode) as cnt", "deliveryBoyActiveOrder", $condition13, $orderBy, $join13, $joinType13, $like, $limit, $offset, $groupBy13, "");
		//echo $this->db->last_query();
		//exit();
		if ($delBoys) {
			$absentDelvBoysVege = $delBoys->result()[0]->cnt;
		}
		$condition11['deliveryBoyActiveOrder.orderCount'] = '1';
		$condition11['deliveryBoyActiveOrder.loginStatus'] = '1';
		$condition11['deliveryBoyActiveOrder.isActive'] = '1';
		$condition11['usermaster.deliveryType'] = 'food';
		$condition11['usermaster.role'] = 'DLB';
		$condition11['usermaster.isActive'] = '1';
		$join11['usermaster'] = "deliveryBoyActiveOrder.deliveryBoyCode=usermaster.code";
		$joinType11['usermaster'] = 'inner';
		$groupBy11 = array();
		$delBoys = $this->GlobalModel->selectQuery("count(distinct deliveryBoyActiveOrder.deliveryBoyCode) as cnt", "deliveryBoyActiveOrder", $condition11, $orderBy, $join11, $joinType11, $like, $limit, $offset, $groupBy11, "");
		//echo $this->db->last_query();
		if ($delBoys) {
			$orderAssignedDelvBoyFood = $delBoys->result()[0]->cnt;
		}
		$condition12['deliveryBoyActiveOrder.orderCount'] = '1';
		$condition12['deliveryBoyActiveOrder.loginStatus'] = '1';
		$condition12['deliveryBoyActiveOrder.isActive'] = '1';
		$condition12['usermaster.deliveryType'] = 'slot';
		$condition12['usermaster.role'] = 'DLB';
		$condition12['usermaster.isActive'] = '1';
		$join12['usermaster'] = "deliveryBoyActiveOrder.deliveryBoyCode=usermaster.code";
		$joinType12['usermaster'] = 'inner';
		$groupBy12 = array();
		$delBoys = $this->GlobalModel->selectQuery("count(distinct deliveryBoyActiveOrder.deliveryBoyCode) as cnt", "deliveryBoyActiveOrder", $condition12, $orderBy, $join12, $joinType12, $like, $limit, $offset, $groupBy12, "");
		if ($delBoys) {
			$orderAssignedDelvBoyVege = $delBoys->result()[0]->cnt;
		}
		$res['cancelledOrders'] = $cancelledOrders;
		$res['releasedOrders'] = $releasedOrders;
		$res['deliveredOrders'] = $deliveredOrders;
		$res['rejectedOrders'] = $rejectedOrders;
		$res['pickedOrders'] = $pickedOrders;
		$res['placedOrders'] = $placedOrders;
		$res['pendingOrders'] = $pendingOrders;
		$res['totalOrders'] = $totalOrders;
		$res['totalDelvBoysFood'] = $totalDelvBoysFood;
		$res['totalDelvBoysVege'] = $totalDelvBoysVege;
		$res['presentDelBoysFood'] = $presentDelBoysFood;
		$res['presentDelBoysVege'] = $presentDelBoysVege;
		$res['absentDelvBoysFood'] = $absentDelvBoysFood;
		$res['absentDelvBoysVege'] = $absentDelvBoysVege;
		$res['orderAssignedDelvBoyFood'] = $orderAssignedDelvBoyFood;
		$res['orderAssignedDelvBoyVege'] = $orderAssignedDelvBoyVege;
		echo json_encode($res);
	}

	public function getVegeGroceryReleasedOrders()
	{
		$today = date('Y-m-d');
		$total = 0;
		$tableName = 'deliveryboystatuslines';
		$whereConditionArray = array('deliveryboystatuslines.orderStatus' => 'REL');
		$orderColumnsArray = array('deliveryboystatuslines.deliveryBoyCode,deliveryboystatuslines.orderCode,deliveryboystatuslines.reason,deliveryboystatuslines.addDate,clientmaster.name,ordermaster.totalPrice');
		$orderBy = array('deliveryboystatuslines.id' => 'DESC');
		$joinType = array('ordermaster' => "inner", 'clientmaster' => 'inner');
		$join = array('ordermaster' => 'ordermaster.code=deliveryboystatuslines.orderCode', 'clientmaster' => 'clientmaster.code=ordermaster.clientCode');
		$like = array();
		$limit = $this->input->GET("length");
		$offset = $this->input->GET("start");
		$groupByColumn = array();
		$extraCondition = " (ordermaster.deliveryBoyCode='' or ordermaster.deliveryBoyCode is null) and deliveryboystatuslines.addDate BETWEEN '" . $today . " 00:00:01' and '" . $today . " 23:59:59'";
		$Records = $this->GlobalModel->selectQuery($orderColumnsArray, $tableName, $whereConditionArray, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
		$srno = $offset + 1;
		$data = array();
		$dataCount = 0;
		//echo $this->db->last_query();
		if ($Records) {
			foreach ($Records->result() as $row) {
				$dlbName = '';
				$dlbCode = $row->deliveryBoyCode;
				if ($dlbCode != '') {
					$tableNameDLB = "usermaster";
					$orderColumnsDLB = array("usermaster.*");
					$conditionDLB = array('usermaster.code' => $dlbCode);
					$RecordsDLB = $this->GlobalModel->selectQuery($orderColumnsDLB, $tableNameDLB, $conditionDLB);
					if ($RecordsDLB) {
						$dlbName = $RecordsDLB->result()[0]->name . ' ( ' . $RecordsDLB->result()[0]->mobile . ' )' . '<br><b>Reason : </b>' . $row->reason;
					}
				}
				else {
					$dlbName = '';
				}
				$orderDate = date('d-m-Y h:i:s', strtotime($row->addDate));
				$actionHtml = '  <a class="dropdown-item  blue" href="' . base_url('Order/view/' . $row->orderCode) . '"><i class="ti-eye"></i> Open</a>';
				$data[] = array($srno, $row->orderCode, $orderDate, $row->name, $row->totalPrice, $dlbName, $actionHtml);
				$srno++;
			}
			$dataCount = sizeof($this->GlobalModel->selectQuery($orderColumnsArray, $tableName, $whereConditionArray, $orderBy, $join, $joinType, $like, "", "", $groupByColumn, $extraCondition)->result());
		}
		$output = array("draw" => intval($this->input->GET("draw")), "recordsTotal" => $dataCount, "recordsFiltered" => $dataCount, "data" => $data, "total" => $total);
		echo json_encode($output);
	}
	public function getFoodReleasedOrders()
	{
		$today = date('Y-m-d');
		$total = 0;
		$tableName = 'deliveryboystatuslines';
		$whereConditionArray = array('deliveryboystatuslines.orderStatus' => 'REL');
		$orderColumnsArray = array('deliveryboystatuslines.deliveryBoyCode,vendorordermaster.vendorCode,vendor.entityName,deliveryboystatuslines.orderCode,deliveryboystatuslines.reason,deliveryboystatuslines.addDate,clientmaster.name,vendorordermaster.grandTotal');
		$orderBy = array('deliveryboystatuslines.id' => 'DESC');
		$joinType = array('vendorordermaster' => "inner", 'clientmaster' => 'inner', 'vendor' => 'inner');
		$join = array('vendorordermaster' => 'vendorordermaster.code=deliveryboystatuslines.orderCode', 'vendor' => 'vendor.code=vendorordermaster.vendorCode', 'clientmaster' => 'clientmaster.code=vendorordermaster.clientCode');
		$like = array();
		$limit = $this->input->GET("length");
		$offset = $this->input->GET("start");
		$groupByColumn = array();
		$extraCondition = " (vendorordermaster.deliveryBoyCode='' or vendorordermaster.deliveryBoyCode is null) and deliveryboystatuslines.addDate BETWEEN '" . $today . " 00:00:01' and '" . $today . " 23:59:59'";
		$Records = $this->GlobalModel->selectQuery($orderColumnsArray, $tableName, $whereConditionArray, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
		$srno = $offset + 1;
		$data = array();
		$dataCount = 0;
		if ($Records) {
			foreach ($Records->result() as $row) {
				$dlbName = '';
				$dlbCode = $row->deliveryBoyCode;
				if ($dlbCode != '') {
					$tableNameDLB = "usermaster";
					$orderColumnsDLB = array("usermaster.*");
					$conditionDLB = array('usermaster.code' => $dlbCode);
					$RecordsDLB = $this->GlobalModel->selectQuery($orderColumnsDLB, $tableNameDLB, $conditionDLB);
					if ($RecordsDLB) {
						$dlbName = $RecordsDLB->result()[0]->name . ' ( ' . $RecordsDLB->result()[0]->mobile . ' )' . '<br><b>Reason : </b>' . $row->reason;
					}
				}
				else {
					$dlbName = '';
				}
				$orderDate = date('d-m-Y h:i A', strtotime($row->addDate));
				$actionHtml = '  <a class="dropdown-item  blue" href="' . base_url('foodOrderList/FoodOrderList/view/' . $row->orderCode) . '"><i class="ti-eye"></i> Open</a>';
				$data[] = array($srno, $row->orderCode, $orderDate, $row->name, $row->entityName, $row->grandTotal, $dlbName, $actionHtml);
				$srno++;
			}
			$dataCount = sizeof($this->GlobalModel->selectQuery($orderColumnsArray, $tableName, $whereConditionArray, $orderBy, $join, $joinType, $like, "", "", $groupByColumn, $extraCondition)->result());
		}
		$output = array("draw" => intval($this->input->GET("draw")), "recordsTotal" => $dataCount, "recordsFiltered" => $dataCount, "data" => $data, "total" => $total);
		echo json_encode($output);
	}
	public function getVegeGroceryOrders()
	{
		$today = date('Y-m-d');
		$total = 0;
		$orderStatus = $this->input->get('orderStatus');
		$tableName = 'ordermaster';
		$whereConditionArray = array();
		if ($orderStatus != "all") {
			if ($orderStatus == "PLC") {
				$whereConditionArray = array();
			}
			else {
				$whereConditionArray['ordermaster.orderStatus'] = $orderStatus;
			}
		}
		$whereConditionArray['ordermaster.isActive'] = 1;
		//$whereConditionArray['ordermaster.paymentStatus'] = "PID";
		$orderColumnsArray = array('ordermaster.*,ordermaster.editDate as edate,ordermaster.code as orderCode,ordermaster.addDate as orderaddDate,ordermaster.editID as orderEditID,clientmaster.*,clientprofile.pincode,customaddressmaster.*');
		$orderBy = array('ordermaster' . '.id' => 'DESC');
		$joinType = array('clientmaster' => 'inner', 'clientprofile' => 'inner', 'customaddressmaster' => "left");
		$join = array('customaddressmaster' => 'customaddressmaster.code = ordermaster.areaCode', 'clientmaster' => 'clientmaster' . '.code=' . 'ordermaster' . '.clientCode', 'clientprofile' => 'clientprofile' . '.clientCode=' . 'ordermaster' . '.clientCode');
		$like = array();
		$limit = $this->input->GET("length");
		$offset = $this->input->GET("start");
		$groupByColumn = array("ordermaster.code");
		$extraCondition = "";
		if ($orderStatus != 'all') {
			if ($orderStatus == "PND") {
				$extraCondition = "";
			}
			else if ($orderStatus == "PLC") {
				$extraCondition = "(ordermaster.orderStatus not in('PND','RJT', 'CAN','DEL','REL','RCH')) and (ordermaster.editDate between '" . $today . " 00:00:00' and '" . $today . " 23:59:59')";
			}
			else if ($orderStatus == "DEL") {
				$extraCondition = "ordermaster.editDate BETWEEN '" . $today . " 00:00:01' and '" . $today . " 23:59:59'";
			}

		}
		else {
			$extraCondition = "(ordermaster.orderStatus not in('RJT','CAN')) and ordermaster.addDate BETWEEN '" . $today . " 00:00:01' and '" . $today . " 23:59:59'";
		}
		$Records = $this->GlobalModel->selectQuery($orderColumnsArray, $tableName, $whereConditionArray, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);

		$r = $this->db->last_query();
		//echo $r;
		$srno = $offset + 1;
		$data = array();
		$dataCount = 0;
		if ($Records) {
			foreach ($Records->result() as $row) {
				$dlbName = '';
				$dlbCode = $row->deliveryBoyCode;
				if ($dlbCode != '') {
					$tableNameDLB = "usermaster";
					$orderColumnsDLB = array("usermaster.*");
					$conditionDLB = array('usermaster.code' => $dlbCode);
					$RecordsDLB = $this->GlobalModel->selectQuery($orderColumnsDLB, $tableNameDLB, $conditionDLB);
					if ($RecordsDLB) {
						$dlbName = $RecordsDLB->result()[0]->name . ' (' . $RecordsDLB->result()[0]->mobile . ')';
					}
				}
				else {
					$dlbName = '';
				}
				if ($orderStatus != 'all') {
					$orderDate = date('d-m-Y h:i A', strtotime($row->edate));
				}
				else {
					$orderDate = date('d-m-Y h:i A', strtotime($row->orderaddDate));
				}
				$orderStatus = $row->orderStatus;
				$actionHtml = '  <a class="dropdown-item  blue" href="' . base_url('Order/view/' . $row->orderCode) . '"><i class="ti-eye"></i> Open</a>';
				$data[] = array($srno, $row->orderCode, $orderDate, $row->name, $row->totalPrice, $dlbName, $actionHtml);
				$srno++;
			}
			$dataCount = sizeof($this->GlobalModel->selectQuery($orderColumnsArray, $tableName, $whereConditionArray, $orderBy, $join, $joinType, $like, "", "", $groupByColumn, $extraCondition)->result());
		}
		$output = array("draw" => intval($this->input->GET("draw")), "recordsTotal" => $dataCount, "recordsFiltered" => $dataCount, "data" => $data, "total" => $total, "r" => $r);
		echo json_encode($output);
	}

	public function getFoodOrders()
	{
		$today = date('Y-m-d');
		$orderStatus = $this->input->get('orderStatus');
		$tableName = "vendorordermaster";
		$orderColumns = array("vendorordermaster.*,clientmaster.name,clientmaster.mobile,vendor.entityName,usermaster.empCode,vendororderstatusmaster.statusSName,vendorordermaster.code");
		$condition = array();
		if ($orderStatus != "all") {
			if ($orderStatus == "PLC") {
				$whereConditionArray = array();
			}
			else {
				$condition['vendorordermaster.orderStatus'] = $orderStatus;
			}
		}
		$condition['vendorordermaster.isActive'] = 1;
		//$condition['vendorordermaster.paymentStatus'] = "PID";
		$joinType = array('clientmaster' => 'inner', 'vendor' => 'inner', 'usermaster' => 'left', 'vendororderstatusmaster' => 'inner');
		$join = array('clientmaster' => 'clientmaster.code=vendorordermaster.clientCode', 'vendor' => 'vendor.code=vendorordermaster.vendorCode', 'usermaster' => 'usermaster.code=vendorordermaster.deliveryBoyCode', 'vendororderstatusmaster' => 'vendororderstatusmaster.statusSName=vendorordermaster.orderStatus');
		$orderBy = array('vendorordermaster' . '.id' => 'DESC');
		$groupByColumn = array("vendorordermaster.code");
		$limit = $this->input->GET("length");
		$offset = $this->input->GET("start");
		$extraCondition = "";
		if ($orderStatus != 'all') {
			if ($orderStatus == "PND") {
				$extraCondition = "";
			}
			else if ($orderStatus == "PLC") {
				$extraCondition = "(vendorordermaster.orderStatus not in('PND','RJT', 'CAN','DEL','REL','RCH')) and (vendorordermaster.editDate between '" . $today . " 00:00:00' and '" . $today . " 23:59:59')";
			}
			else if ($orderStatus == "DEL") {
				$extraCondition = "vendorordermaster.editDate BETWEEN '" . $today . " 00:00:01' and '" . $today . " 23:59:59'";
			}

		}
		else {
			$extraCondition = "(vendorordermaster.orderStatus not in('RJT','CAN')) and vendorordermaster.addDate BETWEEN '" . $today . " 00:00:01' and '" . $today . " 23:59:59'";
		}
		$like = array();
		$Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
		//echo $this->db->last_query();
		//exit();

		$dataCount = 0;
		$data = array();
		$srno = ($this->input->post('start') ?: 0) + 1;
		if ($Records) {
			foreach ($Records->result() as $row) {

				$dlbName = '';
				$dlbCode = $row->deliveryBoyCode;
				if ($dlbCode != '') {
					$tableNameDLB = "usermaster";
					$orderColumnsDLB = array("usermaster.*");
					$conditionDLB = array('usermaster.code' => $dlbCode);
					$orderByDLB = array('usermaster' . '.id' => 'DESC');
					$joinTypeDLB = array();
					$joinDLB = array();
					$RecordsDLB = $this->GlobalModel->selectQuery($orderColumnsDLB, $tableNameDLB, $conditionDLB, $orderByDLB, $joinDLB, $joinTypeDLB);
					if ($RecordsDLB) {
						$dlbName = $RecordsDLB->result()[0]->name . ' (' . $RecordsDLB->result()[0]->mobile . ')';
					}
				}
				else {
					$dlbName = '';
				}

				if ($orderStatus != 'all') {
					$orderDate = date('d-m-Y h:i A', strtotime($row->editDate));
				}
				else {
					$orderDate = date('d-m-Y h:i A', strtotime($row->addDate));
				}
				$actionHtml = '  <a class="dropdown-item  blue" href="' . base_url('foodOrderList/FoodOrderList/view/' . $row->code) . '"><i class="ti-eye"></i> Open</a>';
				$data[] = array(
					$srno,
					$row->code,
					$orderDate,
					$row->name,
					$row->entityName,
					$row->grandTotal,
					$dlbName,
					$actionHtml
				);

				$srno++;
			}
			$dataCount = sizeof($this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, array(), '', '', '', $extraCondition)->result());
		}
		$output = array(
			"draw" => intval($this->input->post("draw") ?: 1),
			"recordsTotal" => $dataCount,
			"recordsFiltered" => $dataCount,
			"data" => $data,
		);
		echo json_encode($output);
	}

	public function getDeliveryBoys()
	{
		$statusType = $this->input->get('statusType');
		$data = array();
		$limit = $this->input->GET("length");
		$offset = $this->input->GET("start");
		$dataCount = 0;
		$table = "usermaster";
		$orderColumns = array("usermaster.*");
		$condition = array("usermaster.isActive" => 1, "usermaster.role" => "DLB");
		$orderBy = array("usermaster.id" => "ASC");
		$like = array();
		$groupBy = array();
		$extraCondition = "";
		$orderShowFlag = false;
		if ($statusType == 'all') {
			$condition['deliveryBoyActiveOrder.isActive'] = '1';
			$join = array("deliveryBoyActiveOrder" => "usermaster.code=deliveryBoyActiveOrder.deliveryBoyCode");
			$joinType = array("deliveryBoyActiveOrder" => "inner");
		}
		else {
			if ($statusType == 'present') {
				$condition['deliveryBoyActiveOrder.loginStatus'] = '1';
				$condition['deliveryBoyActiveOrder.isActive'] = '1';
			}
			else if ($statusType == 'absent') {
				$condition['deliveryBoyActiveOrder.loginStatus'] = '0';
				$condition['deliveryBoyActiveOrder.isActive'] = '1';
			}
			else {
				$condition['deliveryBoyActiveOrder.loginStatus'] = '1';
				$condition['deliveryBoyActiveOrder.orderCount'] = '1';
				$condition['deliveryBoyActiveOrder.isActive'] = '1';
				$orderShowFlag = true;
			}
			array_push($orderColumns, "deliveryBoyActiveOrder.orderCode");
			array_push($orderColumns, "deliveryBoyActiveOrder.orderType");
			$join = array("deliveryBoyActiveOrder" => "usermaster.code=deliveryBoyActiveOrder.deliveryBoyCode");
			$joinType = array("deliveryBoyActiveOrder" => "inner");

		}

		$Result = $this->GlobalModel->selectQuery($orderColumns, $table, $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupBy, $extraCondition);
		//echo $this->db->last_query();
		//exit();
		if ($Result) {
			$srno = 1;
			foreach ($Result->result_array() as $key) {
				if ($key['deliveryType'] == "slot") {
					$status = '<span class="label label-success">Slot</span>';
				}
				else {
					$status = '<span class="label label-warning">Food/Vege/Grocery</span>';
				}
				$actionHtml = "";
				if ($orderShowFlag) {
					if ($key['orderType'] == "food") {
						$status = '<span class="label label-warning">FoodVege/Grocery</span>';
						$actionHtml = '  <a class="dropdown-item  blue" href="' . base_url('foodOrderList/FoodOrderList/view/' . $key["orderCode"]) . '"><i class="ti-eye"></i> Open Order</a>';
					}
					else {
						$status = '<span class="label label-success">Slot</span>';
						$actionHtml = '  <a class="dropdown-item  blue" href="' . base_url('Order/view/' . $key["orderCode"]) . '"><i class="ti-eye"></i> Open Order</a>';
					}
				}

				$data[] = array(
					$srno,
					$key['name'],
					$key['mobile'],
					$status . $actionHtml
				);
				$srno++;
			}

			$dataCount = sizeof($this->GlobalModel->selectQuery($orderColumns, $table, $condition, $orderBy, $join, $joinType, $like, "", "", $groupBy, $extraCondition)->result());
		}

		$output = array(
			"draw" => intval($this->input->post("draw") ?: 1),
			"recordsTotal" => $dataCount,
			"recordsFiltered" => $dataCount,
			"data" => $data
		);
		echo json_encode($output);
	}

	public function sendForgotPasswordOTP()
	{
		$mobileNumber = $this->input->post("mobileNumber");
		$checkUser = $this->db->query("select usermaster.mobile from usermaster where (usermaster.mobile='" . $mobileNumber . "' or usermaster.username='" . $mobileNumber . "') and usermaster.role != 'DLB'");
		if ($checkUser->num_rows() > 0) {
			$mobile = $checkUser->result_array()[0]['mobile'];
			/*$otp = rand(1000,9999);
			 $result = $this->sendsms->sendOtpMessage($otp,$mobileNumber);*/
			$otp = '123123';
			$result['status'] = 'success';
			if ($result['status'] == 'success') {
				$data = array(
					'contactNumber' => $mobile,
					'otp' => $otp,
				);
				$check = $this->db->query("select id from registerOTP where contactNumber='" . $mobile . "'");
				if ($check->num_rows() > 0) {
					$id = $check->result()[0]->id;
					$code = $this->GlobalModel->doEditWithField($data, 'registerOTP', 'id', $id);
				}
				else {
					$code = $this->GlobalModel->addWithoutYear($data, 'registerOTP', 'OTP');
				}
				$response['status'] = true;
				$response['message'] = "OTP Sent Successfully";
			}
			else {
				$response['status'] = false;
				$response['message'] = "Failed to send OTP";
			}
		}
		else {
			$response['status'] = false;
			$response['message'] = "Invalid Username or Mobile";
		}
		echo json_encode($response);
	}

	public function resetPassword()
	{
		$password = trim($this->input->post("password"));
		$otp = trim($this->input->post("otp"));
		$mobileNumber = trim($this->input->post("mobileNumber"));
		$checkUser = $this->db->query("select usermaster.mobile from usermaster where (usermaster.mobile='" . $mobileNumber . "' or usermaster.username='" . $mobileNumber . "') and usermaster.role != 'DLB'");
		if ($checkUser->num_rows() > 0) {
			$mobile = $checkUser->result_array()[0]['mobile'];
			$result = $this->verifyOTP($otp, $mobile);
			if ($result == 1) {
				$data['password'] = md5($password);
				$update = $this->GlobalModel->doEditWithField($data, "usermaster", "mobile", $mobile);
				if ($update != false) {
					$response['status'] = true;
					$response['message'] = "Password updated successfully";
				}
				else {
					$response['status'] = false;
					$response['message'] = "Failed to update password";
					$response['errorno'] = 3;
				}
			}
			else {
				if ($result == 2) {
					$response['status'] = false;
					$response['message'] = "Invalid OTP. Please enter correct OTP";
					$response['errorno'] = 1;
				}
				else {
					$response['status'] = false;
					$response['message'] = "Something went wrong. Please resend OTP";
					$response['errorno'] = 2;
				}
			}
		}
		else {
			$response['status'] = false;
			$response['message'] = "Something went wrong. Please resend OTP";
			$response['errorno'] = 2;
		}
		echo json_encode($response);
	}

	public function verifyOTP($otp, $mobileNumber)
	{
		$checkData = $this->GlobalModel->selectQuery('registerOTP.*', 'registerOTP', array('registerOTP.contactNumber' => $mobileNumber));
		if ($checkData) {
			$otpTbl = $checkData->result_array()[0]['otp'];
			if ($otpTbl == $otp) {
				$this->GlobalModel->deleteForeverFromField('contactNumber', $mobileNumber, 'registerOTP');
				return 1;
			}
			else {
				return 2;
			}
		}
		else {
			return 3;
		}
	}

	public function userDeleteProcess()
	{
		$this->load->view('sample');
	}



}
?>