<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Test extends CI_Controller
{
	var $session_key;
	public function __construct()
	{
		parent::__construct();
		$this->load->helper('form', 'url', 'html');
		$this->load->library('form_validation');
		$this->load->model('GlobalModel');
		$this->load->model('GlobalModel1');
		$this->load->model('ApiModel');
		$this->load->model('Testing'); 
		$this->load->library('notificationlibv_3');
	}

	public function testNotification(){
		$dataArr = array();		
		$re =  rand(9,9999);		
		$DeviceIdsArr[] = "dcc_2HI9TYmTsJs1lfmnqa:APA91bE1G34o0IEWSVa7L9daJji_7TV_G_Hf71cQv-YGvEu9HXXfThekXFVUJFYoGu68PDSUduEQ0quC3NmgtPycmJBnbemcZcc1QrnyOwIBgh7HjDtQWtthe8ybgo48xDSIVYMUDR_o";
		$dataArr['device_id'] = $DeviceIdsArr;
		$dataArr['message'] = "Test notification"; //Message which you want to send
		$dataArr['title'] = "He There";
		$dataArr['image'] = "";
		$dataArr['product_id'] = "123";
		$dataArr['random_id'] = $re;
		$dataArr['type'] = "order";
		$dataArr['channelId'] = 1;

		$notification['device_id'] = $DeviceIdsArr;
		$notification['message'] = "Test notification"; //Message which you want to send
		$notification['title'] = "He There";
		$notification['image'] = "";
		$notification['product_id'] = "123";
		$notification['random_id'] = $re;
		$notification['type'] = "order";
		$notification['channelId'] = 1;

		// $notify = $this->notificationlibv_3->sendDeliveryNotification($dataArr, $notification);
		$notify = $this->notificationlibv_3->sendDeliveryNotification($dataArr, $notification,"ringing");
		print_r($notify);
	}
 
 
 
	public function testa()
	{
		$tableName = "productmaster";
		$orderColumns = array("productmaster.*");
		$condition = array();
		$orderBy = array('productmaster' . '.id' => 'DESC');
		$joinType = array();
		$join = array();
		$groupByColumn = array();
		$limit = $this->input->GET("length");
		$offset = $this->input->GET("start");
		$extraCondition = "";

		$like = array();
		$Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
		echo "<pre>";
		print_r($Records->result());
		echo "</pre>";
	}

	public function sat()
	{
		$tableName = "stockinfo";
		$orderColumns = array("stockinfo.*");
		$condition = array();
		$orderBy = array('stockinfo' . '.id' => 'DESC');
		$joinType = array();
		$join = array();
		$groupByColumn = array();
		$limit = $this->input->GET("length");
		$offset = $this->input->GET("start");
		$extraCondition = "";

		$like = array();
		$Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
		echo "<pre>";
		print_r($Records->result());
		echo "</pre>";
	}

	public function cat()
	{
		$tableName = "categorymaster";
		$orderColumns = array("categorymaster.*");
		$condition = array();
		$orderBy = array('categorymaster' . '.id' => 'DESC');
		$joinType = array();
		$join = array();
		$groupByColumn = array();
		$limit = $this->input->GET("length");
		$offset = $this->input->GET("start");
		$extraCondition = "";

		$like = array();
		$Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
		echo "<pre>";
		print_r($Records->result());
		echo "</pre>";
	}


	public function order()
	{
		// print_r($this->db->list_fields('ordermaster'));echo"<br>";echo"<br>";
		print '<pre>';
		// $res = $this->GlobalModel->selectDataByField('code','ORDER58982_35','ordermaster')->result();
		// print_r($res);
		// $res1 = $this->GlobalModel->selectDataByField('code','ORDER91554_29','ordermaster')->result();
		// print_r($res1);
		// $date = $res[0]->placedTime;
		// $getdate = date('Y-m-d');
		// print_r($getdate);

		print_r($this->GlobalModel->selectDataExcludeDelete('orderbagcount')->result());
		// print_r($getdate);
		print '<pre>';
	}

	public function testOrder()
	{
		$orderColumns = array("count(id) rCount");
		$cond = array("ordermaster.isActive" => 1, "ordermaster.orderStatus" => "RJT", "ordermaster.editID" => "USREM_10");
		$orderBy = array('ordermaster' . ".id" => 'ASC');
		$join = array();
		$joinType = array();
		$like = array();
		$limit = "";
		$offset = "";
		$groupByColumn = array();
		$extraCondition = "ordermaster.areaCode IN('ADDR_1','ADDR_2')";

		$p_result = $this->GlobalModel->selectQuery($orderColumns, 'ordermaster', $cond, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
		echo "<pre>";
		print_r($p_result->result());
		echo "</pre>";
		exit();
	}


	public function getProductList()
	{
		$productCode = $this->input->get('productCode');
		$productCategory = $this->input->get('categoryCode');
		$startDate = $this->input->get('startDate');
		$endDate = $this->input->get('endDate');
		if ($startDate != '') {
			$startDate = date('Y-m-d', strtotime($startDate));
			$endDate = date('Y-m-d', strtotime($endDate));
			$startDate = $startDate . " 00:00:00";
			$endDate = $endDate . " 23:59:59";
		}
		$tableName = "productmaster";
		$orderColumns = array("productmaster.*,categorymaster.categoryName,categorymaster.categorySName,categorymaster.isActive,stockinfo.*");
		$condition = array('productmaster.code' => $productCode, 'productmaster.productCategory' => $productCategory, 'categorymaster.isActive' => 1);
		$orderBy = array('productmaster' . '.id' => 'DESC');
		$joinType = array('categorymaster' => 'inner', 'stockinfo' => 'inner');
		$join = array('categorymaster' => 'categorymaster.categorySName=productmaster.productCategory', 'stockinfo' => 'stockinfo.productCode=productmaster.code');
		$groupByColumn = array();
		$limit = $this->input->GET("length");
		$offset = $this->input->GET("start");
		$extraCondition = " productmaster.isDelete =0 OR productmaster.isDelete IS NULL";
		if ($startDate != "") {
			$extraCondition = "productmaster.addDate between '" . $startDate . "' AND '" . $endDate . "' and (productmaster.isDelete = 0 OR productmaster.isDelete IS NULL)";
		}
		$like = array();
		$Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);

		$srno = $_GET['start'] + 1;
		if ($Records) {
			foreach ($Records->result() as $row) {
				$code = $row->code;
				$productPhoto = "";
				$minStock = $row->minStock;
				$availStock = $row->stock;
				$tblname = 'productphotos';
				$limit = 1;
				$condData = array('isDelete' => 0, 'productCode' => $code);
				$offset = array();
				$photosData = $this->ApiModel->selectData($tblname, $limit, $offset, $condData);
				$start = '<div class="d-flex align-items-center">';
				$end = ' <h5 class="m-b-0 font-16 font-medium">' . $row->productName . '</h5></div></div>';
				foreach ($photosData->result() as $ph) {
					$path = base_url() . 'uploads/product/' . $ph->productCode . '/' . $ph->productPhoto;
					$productPhoto = '
					<div class="m-r-10"><img src="' . $path . '?' . time() . '" alt="user" class="circle" width="45"></div>
					<div class="">';
				}
				unset($photosData);
				$productName = $start . $productPhoto . $end;
				if ($availStock != "0" && $row->productStatus == "OOS") {
					$productStatus = '<span class="label label-sm label-warning">HOLD</span>';
				} else if ($availStock == "0" && $row->productStatus == "AVL") {
					$productStatus = '<span class="label label-sm label-danger">Out Off Stock</span>';
				} else if ($row->productStatus != "AVL") {
					$productStatus = '<span class="label label-sm label-danger">Out Off Stock</span>';
				} else {
					$productStatus = '<span class="label label-sm label-success">Available</span>';
				}

				if ($row->isActive == "1") {
					$status = " <span class='label label-sm label-success'>Active</span>";
				} else {
					$status = " <span class='label label-sm label-warning'>Inactive</span>";
				}
				$actionHtml = '<div class="btn-group">
				<button type="button" class="btn btn-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<i class="ti-settings"></i>
				</button>
				<div class="dropdown-menu animated slideInUp" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 35px, 0px);">
					<a class="dropdown-item  blue" data-toggle="modal" data-target="#responsive-modal" data-seq="' . $row->code . '" href><i class="ti-eye"></i> Open</a>
					<a class="dropdown-item" href="' . base_url() . 'index.php/product/edit/' . $row->code . '"><i class="ti-pencil-alt"></i> Edit</a>
					<a class="dropdown-item  mywarning" data-seq="' . $row->code . '" id="' . $row->code . '"><i class="ti-trash" href></i> Delete</a>
				</div>
			</div>';
				$DateFormat = DateTime::createFromFormat('Y-m-d', substr($row->addDate, 0, 10));
				$Date = $DateFormat->format('d/m/Y');
				$data[] = array(
					$srno,
					$row->code,
					$productName,
					$row->categoryName,
					// $row->storageName_30,
					$availStock,
					$row->minimumSellingQuantity,
					$row->productUom,
					$Date,
					$productStatus,
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

	//truncate table 
	function truncateTable()
	{
		// $this->db->truncate('categorymaster');
		$this->db->truncate('productmaster');
		$this->db->truncate('vendormaster');
		$this->db->truncate('inwardentries');
		$this->db->truncate('inwardlineentries');
		// $this->db->truncate('jobtypemaster');
		// $this->db->truncate('designationmaster');
		// $this->db->truncate('ordermaster');
		// $this->db->truncate('resetpassword');
		$this->db->truncate('stockinfo');
	}


	public function getOrderList()
	{
		$tables = array('ordermaster', 'clientmaster');

		$requiredColumns = array(
			array('code', 'clientCode', 'paymentref', 'paymentmode', 'paymentStatus', 'orderStatus', 'areaCode', 'address', 'phone', 'totalPrice', 'isActive', 'addDate', 'placedTime', 'shippedTime', 'deliveredTime', 'shippingCharges'),
			array('name')
		);

		$conditions = array(
			array('clientCode', 'code')
		);

		$placeList = $this->input->get('placeList');
		$call = $this->input->get('call');
		$pincode = $this->input->get('pincode');
		$orderCode = $this->input->get('orderCode');
		$orderStatus = $this->input->get('orderStatus');
		$fromDate = $this->input->get('fromDate');
		$toDate = $this->input->get('toDate');
		$areaCode = $this->input->get('areaCode');
		$deliveryCode = $this->input->get('deliveryCode');
		$extraCondition = "";
		$whereConditionArray = array();
		$extraConditionColumnNames = array();
		$extraDateConditionColumnNames = array();
		$extraDateConditions = array();

		if ($orderCode != '' || $pincode != '' || $orderStatus != '' || $fromDate != '') {

			if ($placeList == 1) {    ////////////////for placed List//////////////////////

				if ($orderCode != '' && $orderStatus != '' && $pincode != '' && $deliveryCode != '' && $areaCode != '') {
					$whereConditionArray = array(
						'ordermaster' . '.code' => $orderCode,
						'ordermaster' . '.orderStatus' => $orderStatus,
						'clientprofile' . '.pincode' => $pincode,
						'ordermaster' . '.editID' => $deliveryCode,
						'ordermaster' . '.areaCode' => $areaCode,
						'customaddressmaster.isService' => 1
					);
				}
				if ($orderCode != '' && $orderStatus != '' && $deliveryCode != '') {
					$whereConditionArray = array(
						'ordermaster' . '.code' => $orderCode,
						'ordermaster' . '.orderStatus' => $orderStatus,
						'ordermaster' . '.editID' => $deliveryCode,
						'customaddressmaster.isService' => 1
					);
				}
				if ($orderCode != '' && $pincode != '' && $deliveryCode != '') {
					$whereConditionArray = array(
						'ordermaster' . '.code' => $orderCode,
						'clientprofile' . '.pincode' => $pincode,
						'ordermaster' . '.editID' => $deliveryCode,
						'customaddressmaster.isService' => 1
					);
					$extraCondition = " (ordermaster.orderStatus = 'PLC' OR ordermaster.orderStatus='SHP' OR ordermaster.orderStatus='DEL') AND (ordermaster.isDelete=0 OR ordermaster.isDelete IS NULL)";
				}

				if ($orderStatus != '' && $pincode != '' && $deliveryCode != '') {
					//echo 'fine';
					$whereConditionArray = array(
						'ordermaster' . '.orderStatus' => $orderStatus,
						'clientprofile' . '.pincode' => $pincode,
						'ordermaster' . '.editID' => $deliveryCode,
						'customaddressmaster.isService' => 1
					);
				}
				if ($orderCode != '' && $orderStatus == '' && $pincode == '') {
					$whereConditionArray = array('ordermaster' . '.code' => $orderCode, 'customaddressmaster.isService' => 1);
					$extraCondition = " (ordermaster.orderStatus = 'PLC' OR ordermaster.orderStatus='SHP' OR ordermaster.orderStatus='DEL') AND (ordermaster.isDelete=0 OR ordermaster.isDelete IS NULL)";
				}

				if ($orderStatus != '' && $orderCode == '' && $pincode == '') {
					$whereConditionArray = array('ordermaster' . '.orderStatus' => $orderStatus, 'customaddressmaster.isService' => 1);
				}
				if ($pincode != '' && $orderStatus == '' && $orderCode == '') {
					$whereConditionArray = array('clientprofile' . '.pincode' => $pincode, 'customaddressmaster.isService' => 1);
					$extraCondition = " (ordermaster.orderStatus = 'PLC' OR ordermaster.orderStatus='SHP' OR ordermaster.orderStatus='DEL') AND (ordermaster.isDelete=0 OR ordermaster.isDelete IS NULL)";
				}

				if ($areaCode != '') {
					$whereConditionArray = array('ordermaster' . '.areaCode' => $areaCode, 'customaddressmaster.isService' => 1);
					$extraCondition = " (ordermaster.orderStatus = 'PLC' OR ordermaster.orderStatus='SHP' OR ordermaster.orderStatus='DEL') AND (ordermaster.isDelete=0 OR ordermaster.isDelete IS NULL)";
				}


				if ($deliveryCode != '') {
					$whereConditionArray = array('ordermaster' . '.editID' => $deliveryCode, 'customaddressmaster.isService' => 1);
					$extraCondition = " (ordermaster.orderStatus = 'PLC' OR ordermaster.orderStatus='SHP' OR ordermaster.orderStatus='DEL') AND (ordermaster.isDelete=0 OR ordermaster.isDelete IS NULL)";
				}


				if ($fromDate != '') {

					$fromDate = DateTime::createFromFormat('d/m/Y', $fromDate)->format('Y-m-d');
					$toDate = DateTime::createFromFormat('d/m/Y', $toDate)->format('Y-m-d');

					switch ($orderStatus) {
						case "PLC":
							$extraCondition = 'placedTime BETWEEN "' . $fromDate . ' 01:00:01" AND "' . $toDate . ' 12:59:59"';
							break;
						case "SHP":
							$extraCondition = 'shippedTime BETWEEN "' . $fromDate . ' 01:00:01" AND "' . $toDate . ' 12:59:59"';
							break;
						case "DEL":

							$extraCondition = 'deliveredTime BETWEEN "' . $fromDate . ' 01:00:01" AND "' . $toDate . ' 12:59:59"';
							break;
					}
				}

				if ($orderCode == '' && $pincode == '' && $orderStatus == '' && $fromDate != '') {
					$whereConditionArray = array('customaddressmaster.isService' => 1);
					$extraCondition = " (ordermaster.orderStatus = 'PLC' OR ordermaster.orderStatus='SHP' OR ordermaster.orderStatus='DEL') AND ordermaster.addDate BETWEEN '" . $fromDate . " 01:00:01' AND '" . $toDate . " 12:59:59' AND (ordermaster.isDelete=0 OR ordermaster.isDelete IS NULL)";
				}
			} else if ($placeList == 0) {


				if ($orderCode != '' && $orderStatus != '' && $pincode != '' && $areaCode != '') {
					$whereConditionArray = array(
						'ordermaster' . '.code' => $orderCode,
						'ordermaster' . '.orderStatus' => $orderStatus,
						'clientprofile' . '.pincode' => $pincode,
						'ordermaster.areaCode' => $areaCode,
						'customaddressmaster.isService' => 1
					);
				}
				if ($orderCode != '' && $orderStatus != '' && $areaCode != '') {
					$whereConditionArray = array(
						'ordermaster' . '.code' => $orderCode,
						'ordermaster' . '.orderStatus' => $orderStatus,
						'ordermaster.areaCode' => $areaCode,
						'customaddressmaster.isService' => 1
					);
				}

				if ($orderCode != '' && $orderStatus != '') {
					$whereConditionArray = array(
						'ordermaster' . '.code' => $orderCode,
						'ordermaster' . '.orderStatus' => $orderStatus,
						'customaddressmaster.isService' => 1
					);
				}

				if ($orderCode != '' && $areaCode != '') {
					$whereConditionArray = array(
						'ordermaster' . '.code' => $orderCode,
						'ordermaster.areaCode' => $areaCode,
						'customaddressmaster.isService' => 1
					);
					$extraCondition = "(ordermaster.orderStatus = 'CAN' OR ordermaster.orderStatus='PND' OR ordermaster.orderStatus='RJT') AND (ordermaster.isDelete=0 OR ordermaster.isDelete IS NULL)";
				}

				if ($orderCode != '' && $pincode != '') {
					$whereConditionArray = array(
						'ordermaster' . '.code' => $orderCode,
						'clientprofile' . '.pincode' => $pincode,
						'customaddressmaster.isService' => 1
					);
					$extraCondition = "(ordermaster.orderStatus = 'CAN' OR ordermaster.orderStatus='PND' OR ordermaster.orderStatus='RJT') AND (ordermaster.isDelete=0 OR ordermaster.isDelete IS NULL)";
				}

				if ($orderStatus != '' && $pincode != '') {
					//echo 'fine';
					$whereConditionArray = array(
						'ordermaster' . '.orderStatus' => $orderStatus,
						'ordermaster' . '.pincode' => $pincode,
						'customaddressmaster.isService' => 1
					);
				}

				if ($orderStatus != '' && $pincode != '' && $areaCode != '') {
					//echo 'fine';
					$whereConditionArray = array(
						'ordermaster' . '.orderStatus' => $orderStatus,
						'ordermaster' . '.pincode' => $pincode,
						'ordermaster.areaCode' => $areaCode,
						'customaddressmaster.isService' => 1
					);
				}

				if ($areaCode != '') {
					$whereConditionArray = array('ordermaster' . '.areaCode' => $areaCode, 'customaddressmaster.isService' => 1);
					$extraCondition = "(ordermaster.orderStatus = 'CAN' OR ordermaster.orderStatus='PND' OR ordermaster.orderStatus='RJT') AND (ordermaster.isDelete=0 OR ordermaster.isDelete IS NULL)";
				}

				if ($orderCode != '' && $orderStatus == '' && $pincode == '') {
					$whereConditionArray = array('ordermaster' . '.code' => $orderCode, 'customaddressmaster.isService' => 1);
					$extraCondition = "(ordermaster.orderStatus = 'CAN' OR ordermaster.orderStatus='PND' OR ordermaster.orderStatus='RJT') AND (ordermaster.isDelete=0 OR ordermaster.isDelete IS NULL)";
				}

				if ($orderStatus != '' && $orderCode == '' && $pincode == '') {
					$whereConditionArray = array('ordermaster' . '.orderStatus' => $orderStatus, 'customaddressmaster.isService' => 1);
				}
				if ($pincode != '' && $orderStatus == '' && $orderCode == '') {
					$whereConditionArray = array('clientprofile' . '.pincode' => $pincode, 'customaddressmaster.isService' => 1);

					$extraCondition = "(ordermaster.orderStatus = 'CAN' OR ordermaster.orderStatus='PND' OR ordermaster.orderStatus='RJT') AND (ordermaster.isDelete=0 OR ordermaster.isDelete IS NULL)";
				}

				if ($fromDate != '') {

					$fromDate = DateTime::createFromFormat('d/m/Y', $fromDate)->format('Y-m-d');
					$toDate = DateTime::createFromFormat('d/m/Y', $toDate)->format('Y-m-d');

					switch ($orderStatus) {
						case "CAN":

							$extraCondition = 'cancelledTime BETWEEN "' . $fromDate . ' 01:00:01" AND "' . $toDate . ' 12:59:59"';

							break;
						case "RJT":

							$extraCondition = 'rejectedTime BETWEEN "' . $fromDate . ' 01:00:01" AND "' . $toDate . ' 12:59:59"';
							break;
						case "PND":

							$extraCondition = 'addDate BETWEEN "' . $fromDate . ' 01:00:01" AND "' . $toDate . ' 12:59:59"';
							break;
					}
				}
				if ($orderCode == '' && $pincode == '' && $orderStatus == ''  && $fromDate != '') {
					$whereConditionArray = array('customaddressmaster.isService' => 1);
					$extraCondition = "(ordermaster.orderStatus = 'CAN' OR ordermaster.orderStatus='PND' OR ordermaster.orderStatus='RJT') AND ordermaster.addDate BETWEEN '" . $fromDate . " 01:00:01' AND '" . $toDate . " 12:59:59' AND (ordermaster.isDelete=0 OR ordermaster.isDelete IS NULL)";
				}
			}
		} else {
			if ($orderStatus == '' && $placeList == '1') {
				$orderStatus = 'PLC';
				$whereConditionArray = array('ordermaster' . '.orderStatus' => $orderStatus, 'customaddressmaster.isService' => 1);
			}
			if ($orderStatus == '' && $placeList == '0') {

				$orderStatus = 'PND';
				$whereConditionArray = array('ordermaster' . '.orderStatus' => $orderStatus, 'customaddressmaster.isService' => 1);
			}
		}


		$tableName = 'ordermaster';

		$orderColumnsArray = array('ordermaster.*,ordermaster.code as orderCode,ordermaster.editID as orderEditID,clientmaster.*,clientprofile.pincode,customaddressmaster.*'); //ordermaster.code as orderCode,clientprofile.pincode',


		$orderBy = array('ordermaster' . '.id' => 'DESC');

		$joinType = array('clientmaster' => 'inner', 'clientprofile' => 'inner', 'customaddressmaster' => "left"); //'clientmaster' =>'inner','clientprofile' =>'inner'

		$join = array('customaddressmaster' => 'customaddressmaster.code = ordermaster.areaCode', 'clientmaster' => 'clientmaster' . '.code=' . 'ordermaster' . '.clientCode', 'clientprofile' => 'clientprofile' . '.clientCode=' . 'ordermaster' . '.clientCode'); //'clientmaster' => 'clientmaster' . '.code=' . 'ordermaster' . '.clientCode','clientprofile' => 'clientprofile' . '.clientCode=' . 'ordermaster' . '.clientCode','customaddressmaster'=>'customaddressmaster.code = ordermaster.areaCode'


		$like = array();

		$limit = $this->input->GET("length");
		$offset = $this->input->GET("start");

		$groupByColumn = array();

		$Records = $this->GlobalModel->selectQuery($orderColumnsArray, $tableName, $whereConditionArray, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);


		$srno = $offset + 1;
		$data = array();
		$id = 1;
		$radio = '';
		foreach ($Records->result() as $row) {
			if ($srno == 1) {
				$id = $srno;
			}

			$dlbName = '';
			$dlbCode = $row->orderEditID;


			if ($dlbCode != '') {
				$tableNameDLB = "usermaster";
				$orderColumnsDLB = array("usermaster.*,employeemaster.firstName,employeemaster.lastName");
				$conditionDLB = array('usermaster.code' => $dlbCode);
				$orderByDLB = array('usermaster' . '.id' => 'DESC');

				$joinTypeDLB = array('employeemaster' => 'inner');
				$joinDLB = array('employeemaster' => 'employeemaster.code=usermaster.empCode');



				$RecordsDLB = $this->GlobalModel->selectQuery($orderColumnsDLB, $tableNameDLB, $conditionDLB, $orderByDLB, $joinDLB, $joinTypeDLB);

				$dlbName = $RecordsDLB->result()[0]->firstName . ' ' . $RecordsDLB->result()[0]->lastName;
			} else {
				$dlbName = '';
			}

			$chkSHP = '';
			$chkDEL = '';
			$chkRJT = '';

			$orderDate = '';
			$orderStatus = $row->orderStatus;
			$odStatus = $row->orderStatus;
			switch ($orderStatus) {
				case "PND":
					$orderStatus = "Pending";
					$orderDate = date('d-m-Y', strtotime($row->addDate));
					break;
				case "PLC":
					$orderStatus = "Placed";
					$orderDate = date('d-m-Y', strtotime($row->placedTime));
					break;
				case "SHP":
					$orderStatus = "Shipped";
					$chkSHP = 'checked';
					$orderDate = date('d-m-Y', strtotime($row->shippedTime));
					break;
				case "DEL":
					$orderStatus = "Delivered";
					$chkDEL = 'checked';
					$orderDate = date('d-m-Y', strtotime($row->deliveredTime));
					break;
				case "CAN":
					$orderStatus = "Cancelled By User";
					$orderDate = date('d-m-Y', strtotime($row->cancelledTime));
					break;
				case "RJT":
					$orderStatus = "Reject";
					$chkRJT = 'checked';
					$orderDate = date('d-m-Y', strtotime($row->rejectedTime));
					break;
			}
			$paymentStatus = $row->paymentStatus;

			switch ($paymentStatus) {
				case "PNDG":
					$paymentStatus = "Pending";
					break;
				case "PID":
					$paymentStatus = "Paid";
					break;
				case "RJCT":
					$paymentStatus = "Reject";
					break;
			}
			if ($row->shippingCharges == "") {
				$shippingCharges = "0";
			} else {
				$shippingCharges = $row->shippingCharges;
			}
			if ($odStatus == 'PND' || $odStatus == 'CAN' ||  $odStatus == 'RJT') {
				$actionHtml = '  <a class="dropdown-item  blue" href="' . base_url() . 'index.php/Order/view/' . $row->orderCode . '"><i class="ti-eye"></i> Open</a>';

				$data[] = array(
					$srno,
					$row->orderCode,
					$row->name,
					$row->place,
					$row->address,
					$row->phone,
					$orderStatus .
						$radio,
					$row->totalPrice,
					$orderDate,
					$actionHtml
				);
			} else {
				$actionHtml = '  
							<div class="btn-group">
									<button type="button" class="btn btn-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
										<i class="ti-settings"></i>
									</button>
									<div class="dropdown-menu animated slideInUp" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 35px, 0px);">
										 <a class="dropdown-item  blue" href="' . base_url() . 'index.php/Order/view/' . $row->orderCode . '/1"><i class="ti-eye"></i> Open</a>
										 <a class="dropdown-item  mywarning" href="' . base_url() . 'index.php/Order/invoice/' . $row->orderCode . '"><i class="ti-notepad" href></i> Invoice</a>
									 </div>
								</div>';
				/*<input type="radio" class="orderStatus" data-toggle="tooltip" data-placement="top"  name="OrderStatus'.$srno.'" id="orderStatus'.$id.'" value="SHP-'.$row->orderCode.'" title="Shipped" '.$chkSHP.'></div>*/
				$radio = '<div class="form-row"><div class="col-4">
								
								<div class="col-4">	<input type="radio" class="orderStatus" name="OrderStatus' . $srno . '" data-toggle="tooltip" data-placement="top"  id="orderStatus' . ($id = $id + 1) . '" value="DEL-' . $row->orderCode . '" title="Delivered" ' . $chkDEL . '> </div> 
								<div class="col-4">	<input type="radio" class="orderStatus" name="OrderStatus' . $srno . '" data-toggle="tooltip" data-placement="top"  id="orderStatus' . ($id = $id + 1) . '" value="RJT-' . $row->orderCode . '" title="Reject"  ' . $chkRJT . '> </div> </div> 
								 ';


				$data[] = array(
					$srno,
					$row->orderCode,
					$row->name,
					$row->place,
					$row->address,
					$row->phone,
					$orderStatus .
						$radio,
					$row->totalPrice,
					$dlbName,
					$orderDate,
					$actionHtml
				);
			}



			$srno++;
			$id++;
		}

		$dSize = sizeOf($data);

		$dataCount = $this->GlobalModel1->get_all_data($tables, $requiredColumns, $conditions, $extraConditionColumnNames, $extraCondition, $extraDateConditionColumnNames, $extraDateConditions, '');
		$output = array(
			"draw"                  => intval($_GET["draw"]),
			"recordsTotal"          => $dataCount,
			"recordsFiltered"       => $dSize,
			"data"                  => $data
		);
		echo json_encode($output);
	}

	function getCount()
	{
		$code = 'USREM_16';
		$areaLineData = $this->GlobalModel->selectDataByField('userCode', $code, 'useraddresslineentries');
		$response = array();
		$areaCodes = "";
		$size = sizeof($areaLineData->result());
		$count = 1;
		foreach ($areaLineData->result() as $row) {
			$comma = "";
			if ($size != $count) {
				$comma = ",";
			}
			$areaCodes .= "'" . $row->addressCode . "'" . $comma;
			$count++;
		}
		print_r($areaCodes);

		$orderColumns = array("*");
		$cond = array("ordermaster.isActive" => 1, "ordermaster.orderStatus" => "PND");
		$orderBy = array('ordermaster' . ".id" => 'ASC');
		$join = array();
		$joinType = array();
		$like = array();
		$limit = "";
		$offset = "";
		$groupByColumn = array();
		$extraCondition = "ordermaster.areaCode IN(" . $areaCodes . ")";
		$p_result = $this->GlobalModel->selectQuery($orderColumns, 'ordermaster', $cond, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);


		print '<br><pre>';
		print_r($this->db->last_query());

		print '<br>';
		print_r($p_result->result_array());
		print '<br>_______________________________________ getOrderList';
		$orderStatus = 'PND';
		$areaCode = '';
		$m_condition = "";
		if ($orderStatus == "") {
			$m_condition = " AND ordermaster.orderStatus IN ('PND','PLC')";
		} else if ($orderStatus == "PND") {
			$m_condition = " AND ordermaster.orderStatus = 'PND' ";
		} else if ($orderStatus == "PLC") {
			$m_condition = " AND ordermaster.orderStatus = 'PLC' ";
		} else {
			$m_condition = "";
		}

		$areaLineData = $this->GlobalModel->selectDataByField('userCode', $code, 'useraddresslineentries');
		$response = array();
		$areaCodes = "";
		$size = sizeof($areaLineData->result());
		$count = 1;
		foreach ($areaLineData->result() as $row) {
			$comma = "";
			if ($size != $count) {
				$comma = ",";
			}
			$areaCodes .= "'" . $row->addressCode . "'" . $comma;
			$count++;
		}

		$orderColumns = array("ordermaster.code as orderCode,ordermaster.shippingCharges as deliveryCharges,ordermaster.paymentmode,ordermaster.address,ordermaster.phone,ordermaster.totalPrice as orderTotalPrice,ordermaster.addDate as orderDate, ordermaster.latitude,ordermaster.longitude, ordermaster.bagNumber,orderstatusmaster.statusName as orderStatus, paymentstatusmaster.statusName as paymentStatus,clientmaster.code as clientCode,clientmaster.name");
		$join = array('clientmaster' => 'clientmaster.code = ordermaster.clientCode', 'orderstatusmaster' => 'ordermaster' . '.orderStatus=' . 'orderstatusmaster' . '.statusSName', 'paymentstatusmaster' => 'ordermaster' . '.paymentStatus=' . 'paymentstatusmaster' . '.statusSName');
		$joinType = array('clientmaster' => 'left', 'orderstatusmaster' => 'inner', 'paymentstatusmaster' => 'inner');
		$cond = array("ordermaster" . ".areaCode" => $areaCode);
		$orderBy = array('ordermaster' . ".id" => 'ASC');
		$like = array();
		$limit = "";
		$offset = 0;
		$groupByColumn = array();
		$extraCondition = "ordermaster.areaCode IN(" . $areaCodes . ")" . $m_condition;

		$resultQuery = $this->GlobalModel->selectQuery($orderColumns, 'ordermaster', $cond, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
		print '<br><pre>';
		print_r($this->db->last_query());
		print '<br>';
		print_r($resultQuery->result_array());
	}
}