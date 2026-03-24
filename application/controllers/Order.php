<?php
date_default_timezone_set("Asia/Kolkata");
defined('BASEPATH') or exit('No direct script access allowed');
class Order extends CI_Controller
{
	var $session_key;
	public function __construct()
	{
		parent::__construct();
		$this->load->model('GlobalModel');
		$this->load->model('GlobalModel1');
		$this->load->model('ApiModel');
		$this->load->library('notificationlibv_3');
		$this->load->library('firestore');
		$this->session_key = $this->session->userdata('key' . SESS_KEY);
		if (!isset($this->session->userdata['logged_in' . $this->session_key]['code'])) {
			redirect('Admin/login', 'refresh');
		}
	}

	public function pendingListRecords()
	{
		$data['clientmaster'] = $this->GlobalModel->selectData('clientmaster');
		$data['address'] = $this->GlobalModel->selectQuery('customaddressmaster.*', 'customaddressmaster', array('customaddressmaster.isActive' => 1));
		$data['city'] = $this->GlobalModel->selectActiveData('citymaster');
		$data['orderStatus'] = $this->GlobalModel->selectDataExcludeDelete('orderstatusmaster');
		$data['error'] = $this->session->flashdata('response');
		$data['orderCode'] = $this->GlobalModel->selectData('ordermaster');
		$data['ordermaster'] = $this->GlobalModel->selectData('ordermaster');

		$table_name = 'customaddressmaster';
		$orderColumns = array("customaddressmaster.*");
		$condFor = array('customaddressmaster' . '.isDelete' => 0, 'customaddressmaster' . '.isActive' => 1, 'customaddressmaster.isService' => 1);
		$data['address'] = $this->GlobalModel->selectQuery($orderColumns, $table_name, $condFor);
		$this->load->view('dashboard/header');
		$this->load->view('dashboard/order/pendingList', $data);
		$this->load->view('dashboard/footer');
	}

	public function placedListRecords()
	{
		$data['clientmaster'] = $this->GlobalModel->selectData('clientmaster');
		$data['city'] = $this->GlobalModel->selectActiveData('citymaster');
		$data['orderStatus'] = $this->GlobalModel->selectDataExcludedelete('orderstatusmaster');
		$data['error'] = $this->session->flashdata('response');
		$table_name = 'customaddressmaster';
		$orderColumns = array("customaddressmaster.*");
		$condFor = array('customaddressmaster' . '.isDelete' => 0, 'customaddressmaster' . '.isActive' => 1, 'customaddressmaster.isService' => 1);
		$data['address'] = $this->GlobalModel->selectQuery($orderColumns, $table_name, $condFor);
		$table_name = 'usermaster';
		$orderColumns = array("usermaster.*");
		$condFor = array('usermaster' . '.isDelete' => 0, 'usermaster' . '.isActive' => 1, 'usermaster.role' => "DLB");
		$data['user'] = $this->GlobalModel->selectQuery($orderColumns, $table_name, $condFor);
		$this->load->view('dashboard/header');
		$this->load->view('dashboard/order/placedList', $data);
		$this->load->view('dashboard/footer');
	}

	public function serviceUnavailableRecords()
	{
		$data['clientmaster'] = $this->GlobalModel->selectData('clientmaster');
		$data['city'] = $this->GlobalModel->selectActiveData('citymaster');
		$data['$address'] = $this->GlobalModel->selectData('customaddressmaster');
		$data['orderStatus'] = $this->GlobalModel->selectDataExcludedelete('orderstatusmaster');
		$data['error'] = $this->session->flashdata('response');
		$table_name = 'customaddressmaster';
		$orderColumns = array("customaddressmaster.*");
		$condFor = array('customaddressmaster' . '.isDelete' => 0, 'customaddressmaster' . '.isActive' => 1);
		$data['address'] = $this->GlobalModel->selectQuery($orderColumns, $table_name, $condFor);
		$this->load->view('dashboard/header');
		$this->load->view('dashboard/order/serviceUnavailableList', $data);
		$this->load->view('dashboard/footer');
	}

	public function placedTotal()
	{
		$total = 0;
		$pincode = $this->input->get('pincode');
		$orderCode = $this->input->get('orderCode');
		$orderStatus = $this->input->get('orderStatus');
		$fromDate = $this->input->get('fromDate');
		$toDate = $this->input->get('toDate');
		$areaCode = $this->input->get('areaCode');
		$cityCode = $this->input->get('cityCode');
		$deliveryCode = $this->input->get('deliveryCode');
		$tableName = 'ordermaster';
		//$whereConditionArray = array('ordermaster.paymentStatus'=>"PID",'customaddressmaster.isService' => 1, 'ordermaster.code' => $orderCode, 'ordermaster.orderStatus' => $orderStatus, 'clientprofile.pincode' => $pincode, 'ordermaster' . '.areaCode' => $areaCode, 'ordermaster' . '.editID' => $deliveryCode, "ordermaster.cityCode" => $cityCode);
		$whereConditionArray = array('customaddressmaster.isService' => 1, 'ordermaster.code' => $orderCode, 'ordermaster.orderStatus' => $orderStatus, 'clientprofile.pincode' => $pincode, 'ordermaster' . '.areaCode' => $areaCode, 'ordermaster' . '.editID' => $deliveryCode, "ordermaster.cityCode" => $cityCode);
		$OrderColumn = array("IFNULL(sum(ordermaster.totalPrice),0) as totalPrice");
		$orderBy = array('ordermaster' . '.id' => 'DESC');
		$joinType = array('clientmaster' => 'inner', 'clientprofile' => 'inner', 'customaddressmaster' => "left");
		$join = array('customaddressmaster' => 'customaddressmaster.code = ordermaster.areaCode', 'clientmaster' => 'clientmaster' . '.code=' . 'ordermaster' . '.clientCode', 'clientprofile' => 'clientprofile' . '.clientCode=' . 'ordermaster' . '.clientCode'); //'clientmaster' => 'clientmaster' . '.code=' . 'ordermaster' . '.clientCode','clientprofile' => 'clientprofile' . '.clientCode=' . 'ordermaster' . '.clientCode','customaddressmaster'=>'customaddressmaster.code = ordermaster.areaCode'
		$like = array();
		$limit = "";
		$offset = "";
		$groupByColumn = array("ordermaster.code");
		$dateCondition = "";
		if ($fromDate != "") {
			$fromDate = DateTime::createFromFormat('d/m/Y', $fromDate)->format('Y-m-d');
			$toDate = DateTime::createFromFormat('d/m/Y', $toDate)->format('Y-m-d');
			$dateCondition = " AND ordermaster.editDate BETWEEN '" . $fromDate . " 01:00:01' AND '" . $toDate . " 12:59:59'";
		}
		$extraCondition = " ordermaster.orderStatus NOT IN ('PND','CAN')" . $dateCondition;
		//Get sum of all Data        
		$dataTotal = $this->GlobalModel->selectQuery($OrderColumn, $tableName, $whereConditionArray, $orderBy, $join, $joinType, $like, "", "", $groupByColumn, $extraCondition);
		if ($dataTotal) {
			$total = $dataTotal->result_array()[0]['totalPrice'];
		}
		//echo $this->db->last_query(); 
		echo $total;
	}

	public function getPlacedOrders()
	{
		$total = 0;
		$pincode = $this->input->get('pincode');
		$orderCode = $this->input->get('orderCode');
		$orderStatus = $this->input->get('orderStatus');
		$fromDate = $this->input->get('fromDate');
		$toDate = $this->input->get('toDate');
		$areaCode = $this->input->get('areaCode');
		$deliveryCode = $this->input->get('deliveryCode');
		$cityCode = $this->input->get('cityCode');
		$tableName = 'ordermaster';
		//$whereConditionArray = array('ordermaster.paymentStatus'=>"PID",'customaddressmaster.isService' => 1, 'ordermaster.code' => $orderCode, 'ordermaster.orderStatus' => $orderStatus, 'clientprofile.pincode' => $pincode, 'ordermaster' . '.areaCode' => $areaCode, 'ordermaster' . '.editID' => $deliveryCode, "ordermaster.cityCode" => $cityCode,"ordermaster.isActive"=>1);
		$whereCondition = array('customaddressmaster.isService' => 1, 'ordermaster.code' => $orderCode, 'ordermaster.orderStatus' => $orderStatus, 'clientprofile.pincode' => $pincode, 'ordermaster' . '.areaCode' => $areaCode, 'ordermaster' . '.editID' => $deliveryCode, "ordermaster.cityCode" => $cityCode, "ordermaster.isActive" => 1);
		$orderColumns = array('ordermaster.*,ordermaster.editDate as edate,ordermaster.code as orderCode,ordermaster.addDate as orderaddDate,clientmaster.*,clientprofile.pincode,customaddressmaster.*,citymaster.cityName'); //ordermaster.code as orderCode,clientprofile.pincode',
		$orderBy = array('ordermaster' . '.id' => 'DESC');
		$joinType = array('citymaster' => 'left', 'clientmaster' => 'inner', 'clientprofile' => 'inner', 'customaddressmaster' => "left");
		$join = array('citymaster' => 'ordermaster.cityCode=citymaster.code', 'customaddressmaster' => 'customaddressmaster.code = ordermaster.areaCode', 'clientmaster' => 'clientmaster' . '.code=' . 'ordermaster' . '.clientCode', 'clientprofile' => 'clientprofile' . '.clientCode=' . 'ordermaster' . '.clientCode'); //'clientmaster' => 'clientmaster' . '.code=' . 'ordermaster' . '.clientCode','clientprofile' => 'clientprofile' . '.clientCode=' . 'ordermaster' . '.clientCode','customaddressmaster'=>'customaddressmaster.code = ordermaster.areaCode'
		$like = array();
		$limit = $this->input->GET("length");
		$offset = $this->input->GET("start");
		$groupByColumn = array("ordermaster.code");
		$dateCondition = "";
		if ($fromDate != "") {
			$fromDate = DateTime::createFromFormat('d/m/Y', $fromDate)->format('Y-m-d');
			$toDate = DateTime::createFromFormat('d/m/Y', $toDate)->format('Y-m-d');
			$dateCondition = " AND ordermaster.editDate BETWEEN '" . $fromDate . " 01:00:01' AND '" . $toDate . " 23:59:59'";
		}
		$extraCondition = " ordermaster.orderStatus NOT IN ('PND','CAN','PLC','REL') " . $dateCondition;
		$Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $whereCondition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
		$srno = $offset + 1;
		$data = array();
		$dataCount = 0;
		$id = 1;
		$qResult = $this->db->last_query();
		$radio = '';
		if ($Records) {
			foreach ($Records->result() as $row) {
				$fromCity = '<br><span class="badge badge-primary"> Order From <b>' . $row->cityName . '</b></span>';
				if ($srno == 1) {
					$id = $srno;
				}
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
						$dlbName = $RecordsDLB->result()[0]->name;
					}
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
						$orderDate = date('d-m-Y h:i A', strtotime($row->orderaddDate));
						break;
					case "PLC":
						$orderStatus = "Placed";
						$orderDate = date('d-m-Y h:i A', strtotime($row->edate));
						break;
					case "PRE":
						$orderStatus = "Preparing";
						$chkSHP = 'checked';
						$orderDate = date('d-m-Y h:i A', strtotime($row->edate));
						break;
					case "RFP":
						$orderStatus = "Ready For Pickup";
						$chkSHP = 'checked';
						$orderDate = date('d-m-Y h:i A', strtotime($row->edate));
						break;
					case "RCH":
						$orderStatus = "Reached";
						$chkSHP = 'checked';
						$orderDate = date('d-m-Y h:i A', strtotime($row->edate));
						break;
					case "DEL":
						$orderStatus = "Delivered";
						$chkDEL = 'checked';
						$orderDate = date('d-m-Y h:i A', strtotime($row->edate));
						break;
					case "CAN":
						$orderStatus = "Cancelled By User";
						$orderDate = date('d-m-Y h:i A', strtotime($row->cancelledTime));
						break;
					case "RJT":
						$orderStatus = "Rejected";
						$chkRJT = 'checked';
						$orderDate = date('d-m-Y h:i A', strtotime($row->edate));
						break;
					case "PUP":
						$orderStatus = "On the Way";
						$chkRJT = 'checked';
						$orderDate = date('d-m-Y h:i A', strtotime($row->edate));
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
				$productcount = 0;
				$countdata = $this->db->query("select count(*) as cnt from orderlineentries where orderCode='" . $row->orderCode . "'");
				if ($countdata) {
					foreach ($countdata->result() as $r) {
						$productcount = $r->cnt;
					}
				}
				$itemcount = '<br><h5 class="">No. of Items - <span  class="badge badge-danger"><b>' . $productcount . '</b></span></h5>';
				if ($odStatus == 'PND' || $odStatus == 'CAN' || $odStatus == 'RJT') {
					$actionHtml = '  <a class="dropdown-item  blue" href="' . base_url() . 'Order/view/' . $row->orderCode . '"><i class="ti-eye"></i> Open</a>';
					$data[] = array($srno, $row->orderCode . $itemcount, $row->name . $fromCity, $row->place, $row->address, $row->phone, $orderStatus . $radio, $row->totalPrice, $orderDate, $actionHtml);
				} else {
					$trans = "";
					if ($odStatus == 'PLC') {
						$trans = '<a class="dropdown-item  transfer" data-toggle="modal" data-target="#responsive-modal" data-seq="' . $row->orderCode . '"><i class="mdi mdi-repeat" href></i> Transfer</a>';
					}
					$actionHtml = '  
							<div class="btn-group">
									<button type="button" class="btn btn-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
										<i class="ti-settings"></i>
									</button>
									<div class="dropdown-menu animated slideInUp" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 35px, 0px);">
										 <a class="dropdown-item  blue" href="' . base_url() . 'Order/view/' . $row->orderCode . '/1"><i class="ti-eye"></i> Open</a>
										 <a class="dropdown-item  mywarning" href="' . base_url() . 'Order/invoice/' . $row->orderCode . '"><i class="ti-notepad" href></i> Invoice</a>
										 ' . $trans . '
									 </div>
								</div>';
					if ($odStatus == 'PRE' || $odStatus == 'RCH') {
						$radio = '<div class="form-row">
								<div class="col-12">
									<label><input type="checkbox" class="orderStatus" name="OrderStatus' . $srno . '" data-toggle="tooltip" data-placement="top"  id="orderStatus' . ($id = $id + 1) . '" value="RFP-' . $row->orderCode . '" title="Ready For Pickup"> Ready For Pickup</label>
								</div> 
							</div>';
					} else if ($odStatus == 'PUP') {
						$radio = '<div class="form-row">
								<div class="col-12">
									<label><input type="checkbox" class="orderStatus" name="OrderStatus' . $srno . '" data-toggle="tooltip" data-placement="top"  id="orderStatus' . ($id = $id + 1) . '" value="DEL-' . $row->orderCode . '" title="Reject"> Delivered</label>
								</div> 
							</div>';
					} else {
						$radio = '';
					}
					$total += $row->totalPrice;
					$data[] = array($srno, $row->orderCode . $itemcount, $row->name . $fromCity, $row->place, $row->address, $row->phone, $orderStatus . $radio, $row->totalPrice, $dlbName, $orderDate, $actionHtml);
				}
				$srno++;
				$id++;
			}
			if ($Records) {
				$dataCount = sizeOf($this->GlobalModel->selectQuery($orderColumns, $tableName, $whereCondition, $orderBy, $join, $joinType, $like, '', '', $groupByColumn, $extraCondition)->result());
			} else {
				$dataCount = 0;
			}
		}
		$output = array("draw" => intval($this->input->GET("draw")), "recordsTotal" => $dataCount, "recordsFiltered" => $dataCount, "data" => $data, "total" => $total);
		echo json_encode($output);
	}

	public function getOrderList()
	{
		$orderCode = $this->input->post('orderCode');
		$cityCode = $this->input->post('cityCode');
		$orderStatus = $this->input->post('orderStatus');
		$areaCode = $this->input->post('areaCode');
		$fromDate = $this->input->post('fromDate');
		$toDate = $this->input->post('toDate');

		$tables = 'ordermaster';
		$orderColumns = "ordermaster.*,ordermaster.code as orderCode,clientmaster.name,citymaster.cityName,clientprofile.pincode,customaddressmaster.place";
		$tableName = 'ordermaster';
		//$whereConditionArray = array('ordermaster.paymentStatus'=>"PID",'customaddressmaster.isService' => 1, 'ordermaster.code' => $orderCode, 'ordermaster.orderStatus' => $orderStatus, 'ordermaster' . '.areaCode' => $areaCode, "ordermaster.cityCode" => $cityCode,"ordermaster.isActive"=>1);
		$whereConditionArray = array('customaddressmaster.isService' => 1, 'ordermaster.code' => $orderCode, 'ordermaster.orderStatus' => $orderStatus, 'ordermaster' . '.areaCode' => $areaCode, "ordermaster.cityCode" => $cityCode, "ordermaster.isActive" => 1);
		$orderBy = array('ordermaster' . '.id' => 'DESC');
		$joinType = array('citymaster' => 'left', 'clientmaster' => 'inner', 'clientprofile' => 'inner', 'customaddressmaster' => "left");
		$join = array('citymaster' => 'ordermaster.cityCode=citymaster.code', 'customaddressmaster' => 'customaddressmaster.code = ordermaster.areaCode', 'clientmaster' => 'clientmaster' . '.code=' . 'ordermaster' . '.clientCode', 'clientprofile' => 'clientprofile' . '.clientCode=' . 'ordermaster' . '.clientCode'); //'clientmaster' => 'clientmaster' . '.code=' . 'ordermaster' . '.clientCode','clientprofile' => 'clientprofile' . '.clientCode=' . 'ordermaster' . '.clientCode','customaddressmaster'=>'customaddressmaster.code = ordermaster.areaCode'
		$like = array();
		$limit = $this->input->post("length");
		$offset = $this->input->post("start");
		$groupByColumn = array("ordermaster.code");
		$dateCondition = "";
		if ($fromDate != "" && $toDate != "") {
			$fromDate = DateTime::createFromFormat('d/m/Y', $fromDate)->format('Y-m-d');
			$toDate = DateTime::createFromFormat('d/m/Y', $toDate)->format('Y-m-d');
			$dateCondition = " and (ordermaster.editDate BETWEEN '" . $fromDate . " 00:00:01' and '" . $toDate . " 23:59:59' )";
		}
		$extraCondition = " ordermaster.orderStatus not in ('PRE','CAN','RFP','DEL','RJT','PUP') " . $dateCondition;
		$Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $whereConditionArray, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
		//print_r($this->db->last_query());
		//exit();
		$srno = $offset + 1;
		$data = array();
		$dataCount = 0;
		$total = 0;
		$qResult = $this->db->last_query();
		if ($Records) {
			foreach ($Records->result() as $row) {
				$fromCity = '<br><span class="badge badge-primary"> Order From <b>' . $row->cityName . '</b></span>';
				if ($srno == 1) {
					$id = $srno;
				}
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
						$dlbName = $RecordsDLB->result()[0]->name;
					}
				} else {
					$dlbName = '';
				}
				$orderDate = '';
				$orderStatus = $row->orderStatus;
				$odStatus = $row->orderStatus;
				switch ($orderStatus) {
					case "PND":
						$orderStatus = "Pending";
						$orderDate = date('d-m-Y h:i A', strtotime($row->addDate));
						break;
					case "PLC":
						$orderStatus = "Placed";
						$orderDate = date('d-m-Y h:i A', strtotime($row->editDate));
						break;
					case "REL":
						$orderStatus = "Released";
						$orderDate = date('d-m-Y h:i A', strtotime($row->editDate));
						break;
					case "DEL":
						$orderStatus = "Delivered";
						$orderDate = date('d-m-Y h:i A', strtotime($row->edate));
						break;
					case "CAN":
						$orderStatus = "Cancelled By User";
						$orderDate = date('d-m-Y h:i A', strtotime($row->cancelledTime));
						break;
					case "RJT":
						$orderStatus = "Rejected";
						$orderDate = date('d-m-Y h:i A', strtotime($row->editDate));
						break;
					case "PRE":
						$orderStatus = "Prepairing";
						$orderDate = date('d-m-Y h:i A', strtotime($row->editDate));
						break;
					case "RFP":
						$orderStatus = "Ready For Pickup";
						$orderDate = date('d-m-Y h:i A', strtotime($row->editDate));
						break;
					case "PUP":
						$orderStatus = "On the Way";
						$orderDate = date('d-m-Y h:i A', strtotime($row->editDate));
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
				$productcount = 0;
				$countdata = $this->db->query("select count(*) as cnt from orderlineentries where orderCode='" . $row->orderCode . "'");
				if ($countdata) {
					foreach ($countdata->result() as $r) {
						$productcount = $r->cnt;
					}
				}
				$itemcount = '<br><h5 class="">No. of Items - <span  class="badge badge-danger"><b>' . $productcount . '</b></span></h5>';
				if ($odStatus == 'PND' || $odStatus == 'CAN') {
					$actionHtml = '  <a class="dropdown-item  blue" href="' . base_url('Order/view/' . $row->orderCode) . '"><i class="ti-eye"></i> Open</a>';
					$data[] = array(
						$srno,
						$row->orderCode . $itemcount,

						$row->name . $fromCity,

						$row->place,
						$row->address,
						$row->phone,
						$orderStatus,
						$row->totalPrice,
						$dlbName,
						$orderDate,
						$actionHtml
					);
				} else {
					$trans = "";
					if ($odStatus == 'REL') {
						$trans = '<a class="dropdown-item transfer" data-seq="' . $row->orderCode . '"><i class="mdi mdi-repeat" href></i> Release & Reassign</a>';
					}
					$actionHtml = '  
						<div class="btn-group">
								<button type="button" class="btn btn-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									<i class="ti-settings"></i>
								</button>
								<div class="dropdown-menu animated slideInUp" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 35px, 0px);">
										<a class="dropdown-item  blue" href="' . base_url('Order/view/' . $row->orderCode) . '/1"><i class="ti-eye"></i> Open</a>
										<a class="dropdown-item  mywarning" href="' . base_url('Order/invoice/' . $row->orderCode) . '"><i class="ti-notepad" href></i> Invoice</a>
										' . $trans . '
									</div>
							</div>';
					$data[] = array(
						$srno,
						$row->orderCode . $itemcount,

						$row->name .  $fromCity,

						$row->place,
						$row->address,
						$row->phone,
						$orderStatus,
						$row->totalPrice,
						$dlbName,
						$orderDate,
						$actionHtml
					);
				}
				$srno++;
				//$id++;
			}
			$dataCount = sizeof($this->GlobalModel->selectQuery($orderColumns, $tableName, $whereConditionArray, $orderBy, $join, $joinType, $like, "", "", $groupByColumn, $extraCondition)->result());
			$totalData = $this->GlobalModel->selectQuery("IFNULL(SUM(ordermaster.totalPrice),0)as total", $tableName, $whereConditionArray, $orderBy, $join, $joinType, $like, "", "", $groupByColumn, $extraCondition)->result();
			$total = $totalData[0]->total;
		}
		$output = array("qResult" => $qResult, "draw" => intval($this->input->post("draw")), "recordsTotal" => $dataCount, "recordsFiltered" => $dataCount, "data" => $data, "total" => $total);
		echo json_encode($output);
	}

	public function getOrderListByOnlyDates()
	{
		// ,'inwardlineentries'
		$offset = $_GET['start'];
		$limit = $_GET['length'];
		$value = array("length" => $limit, "start" => $offset);
		$placeList = $this->input->get('placeList');
		$clientCode = $this->input->get('clientCode');
		$orderCode = $this->input->get('orderCode');
		$orderStatus = $this->input->get('orderStatus');
		$orderState = $this->input->get('orderState');
		$fromDate = $this->input->get('fromDate');
		$toDate = $this->input->get('toDate');
		$fromDate = DateTime::createFromFormat('d/m/Y', $fromDate)->format('Y-m-d');
		$toDate = DateTime::createFromFormat('d/m/Y', $toDate)->format('Y-m-d');
		$tableName = 'SELECT ordermaster.*,clientmaster.name,clientmaster.code as clientCodeOf FROM ordermaster  INNER JOIN clientmaster ON ordermaster.clientCode=clientmaster.code';
		if ($placeList == '') {
			if ($clientCode != '' || $orderCode != '') {
				if ($clientCode != '' && $orderCode == '') {
					$clientCodeCond = "ordermaster.clientCode = '" . $clientCode . "' AND ";
				} else if ($orderCode != '' && $clientCode == '') {
					$orderCodeCond = "ordermaster.code= '" . $orderCode . "' AND ";
				} else {
					$clientCodeCond = "ordermaster.clientCode = '" . $clientCode . "' AND ";
					$orderCodeCond = "ordermaster.code= '" . $orderCode . "' AND ";
				}
				$extraCondition = " " . $clientCodeCond . " " . $orderCodeCond . " (ordermaster.orderStatus = 'CAN' OR ordermaster.orderStatus='PND' OR ordermaster.orderStatus='RJT') AND ordermaster.addDate BETWEEN '" . $fromDate . " 01:00:01' AND '" . $toDate . " 12:59:59' AND (ordermaster.isDelete=0 OR ordermaster.isDelete IS NULL)";
			} else {
				$conditionColumns = array();
				$conditionValues = array();
				$extraCondition = " (ordermaster.orderStatus = 'CAN' OR ordermaster.orderStatus='PND' OR ordermaster.orderStatus='RJT') AND ordermaster.addDate BETWEEN '" . $fromDate . " 01:00:01' AND '" . $toDate . " 12:59:59' AND (ordermaster.isDelete=0 OR ordermaster.isDelete IS NULL)";
			}
		}
		///else
		else {
			$conditionColumns = array();
			$conditionValues = array();
			if ($clientCode != '' || $orderCode != '') {
				//	echo 'else in';
				if ($clientCode != '' && $orderCode == '') {
					$clientCodeCond = "ordermaster.clientCode = '" . $clientCode . "' AND ";
				} else if ($orderCode != '' && $clientCode == '') {
					$orderCodeCond = "ordermaster.code= '" . $orderCode . "' AND ";
				} else {
					$clientCodeCond = "ordermaster.clientCode = '" . $clientCode . "' AND ";
					$orderCodeCond = "ordermaster.code= '" . $orderCode . "' AND ";
				}
				$extraCondition = " " . $clientCodeCond . " " . $orderCodeCond . "(ordermaster.orderStatus = 'PLC' OR ordermaster.orderStatus='SHP' OR ordermaster.orderStatus='DEL') AND ordermaster.addDate BETWEEN '" . $fromDate . " 01:00:01' AND '" . $toDate . " 12:59:59' AND (ordermaster.isDelete=0 OR ordermaster.isDelete IS NULL)";
			} else {
				$extraCondition = " (ordermaster.orderStatus = 'PLC' OR ordermaster.orderStatus='SHP' OR ordermaster.orderStatus='DEL') AND ordermaster.addDate BETWEEN '" . $fromDate . " 01:00:01' AND '" . $toDate . " 12:59:59' AND (ordermaster.isDelete=0 OR ordermaster.isDelete IS NULL)";
			}
		}
		$select = 1;
		$Records = $this->GlobalModel->selectActiveDataByMultipleFields($conditionColumns, $conditionValues, $tableName, $extraCondition, $select); // Query # Get Data From Inward By Above Condition
		//print_r($Records->result());
		$srno = $_GET['start'] + 1;
		$data = array();
		$radio = '';
		foreach ($Records->result() as $row) {
			$chkSHP = '';
			$chkDEL = '';
			$chkRJT = '';
			$orderStatus = $row->orderStatus;
			$odStatus = $row->orderStatus;
			switch ($orderStatus) {
				case "PND":
					$orderStatus = "Pending";
					break;
				case "PLC":
					$orderStatus = "Placed";
					break;
				case "SHP":
					$orderStatus = "Shipped";
					$chkSHP = 'checked';
					break;
				case "DEL":
					$orderStatus = "Deliverd";
					$chkDEL = 'checked';
					break;
				case "RJT":
					$orderStatus = "Rejected";
					$chkRJT = 'checked';
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
			}
			if ($odStatus == 'PND' || $odStatus == 'CAN' || $odStatus == 'RJT') {
				$actionHtml = '  <a class="dropdown-item  blue" href="' . base_url() . 'Order/view/' . $row->code . '"><i class="ti-eye"></i> Open</a>';
			} else {
				$actionHtml = '  
							<div class="btn-group">
									<button type="button" class="btn btn-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
										<i class="ti-settings"></i>
									</button>
									<div class="dropdown-menu animated slideInUp" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 35px, 0px);">
										 <a class="dropdown-item  blue" href="' . base_url() . 'Order/view/' . $row->code . '/1"><i class="ti-eye"></i> Open</a>
										 <a class="dropdown-item  mywarning" href="' . base_url() . 'Order/invoice/' . $row->code . '"><i class="ti-notepad" href></i> Invoice</a>
									 </div>
								</div>';
				$radio = '<div class="form-row"><div class="col-4">
								<input type="radio" class="orderStatus" data-toggle="tooltip" data-placement="top"  name="OrderStatus' . $srno . '" id="orderStatus' . $srno . '" value="SHP-' . $row->code . '" title="Shipped" ' . $chkSHP . '></div>
								<div class="col-4">	<input type="radio" class="orderStatus" name="OrderStatus' . $srno . '" data-toggle="tooltip" data-placement="top"  id="orderStatus' . $srno . '" value="DEL-' . $row->code . '" title="Delivered" ' . $chkDEL . '> </div> 
								<div class="col-4">	<input type="radio" class="orderStatus" name="OrderStatus' . $srno . '" data-toggle="tooltip" data-placement="top"  id="orderStatus' . $srno . '" value="RJT-' . $row->code . '" title="Reject"  ' . $chkRJT . '> </div> </div> 
								 ';
			}
			$data[] = array($srno, $row->code, $row->name, $row->paymentmode, $paymentStatus, $orderStatus . $radio, $row->phone, $row->totalPrice, $actionHtml);
			$srno++;
		}
		$forCount = $this->GlobalModel->selectActiveDataByMultipleFields($conditionColumns, $conditionValues, $tableName, $extraCondition, $select); // Query # Get Data From Inward By Above Condition
		$dataCount = sizeOf($forCount->result());
		$output = array("draw" => intval($_GET["draw"]), "recordsTotal" => $dataCount, "recordsFiltered" => $dataCount, "data" => $data);
		echo json_encode($output);
	}

	//view
	public function view()
	{
		$code = $this->uri->segment(3);
		$data['placeFlag'] = $this->uri->segment(4);
		$data['query'] = $this->GlobalModel->selectDataById($code, 'ordermaster');
		
		//echo $this->db->last_query();
		
		$clientCode = $data['query']->result()[0]->clientCode;
		$cityCode = $data['query']->result()[0]->cityCode;
		$areaCode = $data['query']->result()[0]->areaCode;
		$deliveryBoyCode = $data['query']->result()[0]->deliveryBoyCode;
		$getName = $this->GlobalModel->selectDataById($clientCode, 'clientmaster');
		$data['clientName'] = $getName->result()[0]->name;
		$data['client'] = $this->GlobalModel->selectData('clientmaster');
		$data['city'] = "";
		if ($cityCode != "") {
			$citydata = $this->GlobalModel->selectDataById($cityCode, 'citymaster');
			if ($citydata) {
				$data['city'] = $citydata->result()[0]->cityName;
			}
		}
		$data['dBoyList'] = false;
		//get  all users to confirm order - 
		if ($deliveryBoyCode != "") {
			$tableName = "usermaster";
			$orderColumns = array("usermaster.*");
			$condition = array("usermaster.code" => $deliveryBoyCode, "usermaster.isActive" => 1);
			$orderBy = array('usermaster' . '.id' => 'DESC');
			$Records2 = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy);
			if ($Records2) {
				$data['dBoyList'] = $Records2;
			}
		}
		$data['paymentStatus'] = $this->GlobalModel->selectDataExcludeDelete('paymentstatusmaster');
		$data['lineData'] = $this->GlobalModel->selectDataByField('orderCode', $code, 'orderlineentries');
		$data['orderStatus'] = $this->GlobalModel->selectDataExcludeDelete('orderstatusmaster');
		$this->load->view('dashboard/header');
		$this->load->view('dashboard/order/view', $data);
		$this->load->view('dashboard/footer');
	}

	public function getPendingDeliveryBoys()
	{
		$statusType = 'PND';
		$data = array();
		$dataCount = 0;
		$table = "usermaster";
		$orderColumns = "usermaster.*";
		$condition = array("usermaster.isActive" => 1, "usermaster.role" => "DLB");
		$orderBy = array("usermaster.id" => "ASC");
		$like = array();
		$groupBy = array();
		$extraCondition = "";
		if ($statusType == 'all') {
			$join = array();
			$joinType = array();
		} else {
			if ($statusType == 'present') {
				$condition['deliveryBoyActiveOrder.loginStatus'] = '1';
			} else if ($statusType == 'absent') {
				$condition['deliveryBoyActiveOrder.loginStatus'] =  '0';
			} else {
				$condition['deliveryBoyActiveOrder.loginStatus'] = '1';
				$condition['deliveryBoyActiveOrder.orderCount'] = '0';
			}
			$join = array("deliveryBoyActiveOrder" => "usermaster.code=deliveryBoyActiveOrder.deliveryBoyCode");
			$joinType = array("deliveryBoyActiveOrder" => "inner");
		}
        //echo $this->db->last_query();
		$Result = $this->GlobalModel->selectQuery($orderColumns, $table, $condition, $orderBy, $join, $joinType, $like, "", "", $groupBy, $extraCondition);
		$html = '<option value="" readonly>Select another delivery boy</option>';
		if ($Result) {
			foreach ($Result->result_array() as $key) {
				$html .= '<option value="' . 	$key['code'] . '">' . $key['username'] . '</option>';
			}
		} else {
			$html = false;
		}
		echo $html;
	}

	//function confirm on view page
	public function confirm()
	{
		$editID = $this->session->userdata['logged_in' . $this->session_key]['code'];
		$orderCode = $this->input->post('orderCode');
		$clientCode = $this->input->post('clientCode');
		$paymentStatus = $this->input->post('paymentStatus');
		$deliveryBoy = $this->input->post('deliveryB');
		$timeStamp = date("Y-m-d h:i:s");
		$string = "";
		$string = "Accepted & Preparing";
		$addID = $this->session->userdata['logged_in' . $this->session_key]['code'];
		$userRole = $this->session->userdata['logged_in' . $this->session_key]['role'];
		$userName = $this->session->userdata['logged_in' . $this->session_key]['username'];
		switch ($userRole) {
			case "ADM":
				$role = "Admin";
				break;
			case "USR":
				$role = "User";
				break;
		}
		$ip = $_SERVER['REMOTE_ADDR'];
		$text = $role . " " . $userName . ' ' . $string . ' Order "' . $orderCode . '" from ' . $ip;
		$log_text = array('addId' => $addID, 'logText' => $text);
		$actvity = $this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
		$timeStamp = date("Y-m-d h:i:s");
		$data = array('orderStatus' => 'PRE', 'placedTime' => $timeStamp, 'editID' => $editID, 'editIP' => $ip, 'editDate' => $timeStamp, 'preparingMinutes' => 25);

		$result = $this->GlobalModel->doEdit($data, 'ordermaster', $orderCode);
		if ($result != 'false') {
			$bookLineResult = 'true';
			$order_status = $this->GlobalModel->selectQuery(array("vendororderstatusmaster.*"), "vendororderstatusmaster", array("vendororderstatusmaster.statusSName" => "PRE"));
			if ($order_status && count(array($order_status)) > 0) {
				//if ($order_status && count($order_status)>0) {
				$order_status_record = $order_status->result()[0];
				$statusTitle = $order_status_record->messageTitle;
				#replace $ template in title 
				$statusDescription = $order_status_record->messageDescription;
				$statusDescription = str_replace("$", $orderCode, $statusDescription);
				$dataBookLine = array(
					"orderCode" => $orderCode,
					"statusPutCode" => $editID,
					"statusLine" => 'PRE',
					"statusTime" => date("Y-m-d H:i:s"),
					"reason" => "Admin accepted and preparing order",
					"statusTitle" => $statusTitle,
					"statusDescription" => $statusDescription,
					"isActive" => 1
				);
				$bookLineResult = $this->GlobalModel->addWithoutYear($dataBookLine, 'bookorderstatuslineentries', 'BOL');
			}
			if ($bookLineResult != 'false') {
				/*$settingResult = $this->GlobalModel->selectQuery('settings.*', 'settings', array('settings.code' => 'SET_5', 'settings.isActive' => 1));
				if ($settingResult) {
					$touchPoint = $settingResult->result_array()[0]['settingValue'];
					$dataUpCnt['commissionAmount'] = $touchPoint;
					$dataUpCnt['deliveryBoyCode'] = $deliveryBoy;
					$dataUpCnt['orderCode'] = $orderCode;
					$dataUpCnt['orderType'] = "vegetable";
					$dataUpCnt['isActive'] = 1;
					$delboyCommission = $this->GlobalModel->addNew($dataUpCnt, 'deliveryboyearncommission', 'DBEC');
				}*/

				//send notification to delivery boy
				$userData = $this->GlobalModel->selectQuery("usermaster.firebase_id", "usermaster", array("usermaster.code" => $deliveryBoy));
				if ($userData) {
					$DeviceIdsArr = array();
					foreach ($userData->result_array() as $c) {
						$DeviceIdsArr[] = $c['firebase_id'];
					}
					$message = 'Be ready, we are preparing order #' . $orderCode . ' Yay, you have received touch point amount';
					$title = 'Order Accepted';
					$this->sendFirebaseNotification($DeviceIdsArr, $title, $message, $orderCode);
				}

				//notification
				$random = rand(0, 999);
				$dataNoti = array("title" => 'Order Successfully Placed', "message" => 'Order Successfully Placed', "order_id" => $orderCode, "random_id" => $random, 'type' => 'order');
				$clientCode =  $this->input->post('clientCode');
				$checkdevices = $this->GlobalModel->selectQuery('clientdevicedetails.firebaseId', 'clientdevicedetails', array('clientdevicedetails.clientCode' => $clientCode));
				if ($checkdevices) {
					$DeviceIdsArr = array();
					foreach ($checkdevices->result() as $c) {
						$DeviceIdsArr[] = $c->firebaseId;
					}
					if (!empty($DeviceIdsArr)) {
						$dataArr = array();
						$dataArr['device_id'] = $DeviceIdsArr;
						$dataArr['message'] = $dataNoti['message']; //Message which you want to send
						$dataArr['title'] = $dataNoti['title'];
						$dataArr['order_id'] = $dataNoti['order_id'];
						$dataArr['random_id'] = $dataNoti['random_id'];
						$dataArr['type'] = $dataNoti['type'];
						$notification['device_id'] = $DeviceIdsArr;
						$notification['message'] = $dataNoti['message']; //Message which you want to send
						$notification['title'] = $dataNoti['title'];
						$notification['order_id'] = $dataNoti['order_id'];
						$notification['random_id'] = $dataNoti['random_id'];
						$notification['type'] = $dataNoti['type'];
						//$notify = $this->notificationlibv_3->pushNotification($dataArr, $notification);
					}
				}

				$this->firestore->update_order_status($orderCode, 'PRE');

				$response['status'] = true;
				$response['message'] = "Order Successfully Placed.";
			}
		} else {
			$response['status'] = false;
			$response['message'] = "Failed To Place Order";
		}
		$this->session->set_flashdata('response', json_encode($response));
		redirect(base_url() . 'Order/pendingListRecords');
	}

	public function sendFirebaseNotification(?array $DeviceIdsArr, $title, $message, $orderId)
	{
		$random = rand(0, 999);
		$random = date('his') . $random;
		$dataArr = array();
		$dataArr['device_id'] = $DeviceIdsArr;
		$dataArr['message'] = $message; //Message which you want to send
		$dataArr['title'] = $title;
		$dataArr['order_id'] = $orderId;
		$dataArr['random_id'] = $random;
		$dataArr['type'] = 'order';
		$notification['device_id'] = $DeviceIdsArr;
		$notification['message'] = $message; //Message which you want to send
		$notification['title'] = $title;
		$notification['order_id'] = $orderId;
		$notification['random_id'] = $random;
		$notification['type'] = 'order';
		$notify = $this->notificationlibv_3->sendDeliveryNotification($dataArr, $notification, "ringing");
	}

	public function revoke()
	{
		$orderCode = $this->input->post('orderCode');
		$data = array('orderStatus' => 'PND');
		$result = $this->GlobalModel->doEdit($data, 'ordermaster', $orderCode);
		$getLineData = $this->GlobalModel->selectDataByField('orderCode', $orderCode, 'orderlineentries');
		foreach ($getLineData->result() as $line) {
			$productCode = $line->productCode;
			$quantity = $line->quantity;
			$consumeStock = $this->GlobalModel->stockChange($productCode, $quantity, 'add');
		}
		$addID = $this->session->userdata['logged_in' . $this->session_key]['code'];
		$userRole = $this->session->userdata['logged_in' . $this->session_key]['role'];
		$userName = $this->session->userdata['logged_in' . $this->session_key]['username'];
		switch ($userRole) {
			case "ADM":
				$role = "Admin";
				break;
			case "USR":
				$role = "User";
				break;
		}
		$ip = $_SERVER['REMOTE_ADDR'];
		$text = $role . " " . $userName . ' Revoked Order "' . $orderCode . '" from ' . $ip;
		$log_text = array('addID' => $addID, 'logText' => $text);
		if ($result != 'false') {
			echo $this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
		} else {
			echo 'false';
		}
	}

	public function shipped()
	{
		$orderCode = $this->input->post('orderCode');
		$paymentStatus = $this->input->post('paymentStatus');
		$orderStatus = $this->input->post('orderStatus');
		$timeStamp = date("Y-m-d h:i:s");
		switch ($orderStatus) {
			case "SHP":
				$data = array('orderStatus' => 'SHP', 'paymentStatus' => $paymentStatus, 'shippedTime' => $timeStamp);
				break;
			case "DEL":
				$data = array('orderStatus' => 'DEL', 'paymentStatus' => 'PID', 'deliveredTime' => $timeStamp);
				break;
			case "RJT":
				$data = array('orderStatus' => 'RJT', 'paymentStatus' => $paymentStatus, 'rejectedTime' => $timeStamp);
				break;
		}
		$result = $this->GlobalModel->doEdit($data, 'ordermaster', $orderCode);
		$addID = $this->session->userdata['logged_in' . $this->session_key]['code'];
		$userRole = $this->session->userdata['logged_in' . $this->session_key]['role'];
		$userName = $this->session->userdata['logged_in' . $this->session_key]['username'];
		switch ($userRole) {
			case "ADM":
				$role = "Admin";
				break;
			case "USR":
				$role = "User";
				break;
		}
		$ip = $_SERVER['REMOTE_ADDR'];
		$text = $role . " " . $userName . ' Shipped Order "' . $orderCode . '" from ' . $ip;
		$log_text = array('editID' => $addID, 'logText' => $text);
		if ($result != 'false') {
			$response['status'] = true;
			$response['message'] = "Order Successfully .";
			//$this->GlobalModel->activityAdd($log_text,'activitymaster','ACT');
		} else {
			$response['status'] = false;
			$response['message'] = "Failed To Place Order";
		}
		// print_r($response);
		$this->session->set_flashdata('response', json_encode($response));
		redirect(base_url() . 'Order/pendingListRecords');
	}

	public function orderOperations()
	{
		$status = $this->input->post('status');
		$statusArray = explode('-', $status);
		$orderStatus = $statusArray[0];
		$orderCode = $statusArray[1];
		$string = '';
		$timeStamp = date("Y-m-d h:i:s");
		$addID = $this->session->userdata['logged_in' . $this->session_key]['code'];
		$userRole = $this->session->userdata['logged_in' . $this->session_key]['role'];
		$userName = $this->session->userdata['logged_in' . $this->session_key]['username'];
		switch ($userRole) {
			case "ADM":
				$role = "Admin";
				break;
			case "USR":
				$role = "User";
				break;
		}
		$ip = $_SERVER['REMOTE_ADDR'];
		$text = $role . " " . $userName . ' ' . $string . ' Order "' . $orderCode . '" from ' . $ip;
		$log_text = array('addId' => $addID, 'logText' => $text);

		$ordersdata = $this->GlobalModel->selectDataByField('code', $orderCode, 'ordermaster');
		$clientCode = $ordersdata->result()[0]->clientCode;
		$deliveryBoyCode = $ordersdata->result()[0]->deliveryBoyCode;

		$data = array();
		$random = rand(0, 999);
		switch ($orderStatus) {
			case "RFP":
				$data = array('orderStatus' => 'RFP', 'paymentStatus' => 'PNDG', 'shippedTime' => $timeStamp, 'editDate' => $timeStamp);
				$string = 'Ready For Pickup';
				$reason = "Order Ready For Pickup by Admin";
				$not = array('title' => 'Order is Ready', 'message' => 'Order is Ready For Pickup', 'order_id' => $orderCode, 'random_id' => $random);
				break;
			case "DEL":
				$data = array('orderStatus' => 'DEL', 'paymentStatus' => 'PID', 'deliveredTime' => $timeStamp, 'editDate' => $timeStamp);
				$string = 'Delivered';
				$reason = "Order Marked Delivered By Admin";
				$not = array('title' => 'Order is Successfully Delivered', 'message' => 'Order is Successfully Delivered', 'order_id' => $orderCode, 'random_id' => $random);
				break;
			case "RJT":
				$data = array('orderStatus' => 'RJT', 'paymentStatus' => 'RJCT', 'rejectedTime' => $timeStamp, 'editDate' => $timeStamp);
				$string = 'Rejected';
				$reason = "Order Rejected by Admin";
				$not = array('title' => 'Order is Rejected', 'message' => 'Order is Rejected', 'order_id' => $orderCode, 'random_id' => $random);
				break;
		}
		$result = $this->GlobalModel->doEdit($data, 'ordermaster', $orderCode);
		if ($result != 'false') {
			$bookLineResult = 'true';
			$order_status = $this->GlobalModel->selectQuery(array("vendororderstatusmaster.*"), "vendororderstatusmaster", array("vendororderstatusmaster.statusSName" => $orderStatus));
			//$order_status = $this->GlobalModel->selectQuery("vendororderstatusmaster.*", "vendororderstatusmaster",array(), array("vendororderstatusmaster.statusSName" => array("=",$orderStatus))); 
			if ($order_status && count(array($order_status)) > 0) {
				$order_status_record = $order_status->result()[0];
				$statusTitle = $order_status_record->messageTitle;
				#replace $ template in title 
				$statusDescription = $order_status_record->messageDescription;
				$statusDescription = str_replace("$", $orderCode, $statusDescription);
				$dataBookLine = array(
					"orderCode" => $orderCode,
					"statusPutCode" => $addID,
					"statusLine" => $orderStatus,
					"statusTime" => date("Y-m-d H:i:s"),
					"reason" => $reason,
					"statusTitle" => $statusTitle,
					"statusDescription" => $statusDescription,
					"isActive" => 1
				);
				$bookLineResult = $this->GlobalModel->addWithoutYear($dataBookLine, 'bookorderstatuslineentries', 'BOL');
			}
			if ($bookLineResult != 'false') {
				if ($orderStatus == "DEL") {
					$restDelv['orderCode'] = null;
					$restDelv['orderType'] = null;
					$restDelv['editID'] = $addID;
					$restDelv['editIP'] = $ip;
					$restDelv['orderCount'] = 0;
					$delbActiveOrder = $this->GlobalModel->doEditWithField($restDelv, 'deliveryBoyActiveOrder', 'deliveryBoyCode', $deliveryBoyCode);
				}
				//place notification to user and delilvery boy
				$DeviceIdsArr = array();
				$delBoy = $this->GlobalModel->selectQuery("usermaster.firebase_id", "usermaster", array("usermaster.code" => $deliveryBoyCode));
				if ($delBoy) {
					$firebaseId = $delBoy->result_array()[0]['firebase_id'];
					if ($firebaseId != "" && $firebaseId != null) {
						$DeviceIdsArr[] = $firebaseId;
					}
				}

				$checkdevices = $this->GlobalModel->selectQuery('clientdevicedetails.firebaseId', 'clientdevicedetails', array('clientdevicedetails.clientCode' => $clientCode));
				if ($checkdevices) {
					foreach ($checkdevices->result() as $c) {
						$DeviceIdsArr[] = $c->firebaseId;
					}
				}
				if (!empty($DeviceIdsArr)) {
					$dataArr = array();
					$dataArr['device_id'] = $DeviceIdsArr;
					$dataArr['message'] = $not['message']; //Message which you want to send
					$dataArr['title'] = $not['title'];
					$dataArr['order_id'] = $not['order_id'];
					$dataArr['random_id'] = $not['random_id'];
					$dataArr['type'] = 'order';
					$notification['device_id'] = $DeviceIdsArr;
					$notification['message'] = $not['message']; //Message which you want to send
					$notification['title'] = $not['title'];
					$notification['order_id'] = $not['order_id'];
					$notification['random_id'] = $not['random_id'];
					$notification['type'] = 'order';
					$notify = $this->notificationlibv_3->sendDeliveryNotification($dataArr, $notification, "ringing");
				}
				$this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
				
				$this->firestore->update_order_status($orderCode, $orderStatus);
				
				$res['status'] = true;
			} else {
				$res['status'] = false;
			}
		} else {
			$res['status'] = false;
		}
		echo json_encode($res);
	}

	public function getOrderDetails()
	{
		$orderCode = $this->input->get('orderCode');
		$noPic = $this->input->get('noPic');
		$tableName = 'orderlineentries';
		$orderColumns = array("orderlineentries.orderCode,orderlineentries.productCode,orderlineentries.weight,orderlineentries.productUom,orderlineentries.productPrice,orderlineentries.quantity,orderlineentries.totalPrice,orderlineentries.isActive,productmaster.productName");
		$condition = array('orderlineentries.orderCode' => $orderCode);
		$orderBy = array('orderlineentries.id' => 'desc');
		$joinType = array('productmaster' => 'inner');
		$join = array('productmaster' => 'productmaster.code=orderlineentries.productCode');
		$groupByColumn = array('orderlineentries.productCode');
		//$groupByColumn = array('orderlineentries.productCode');
		$limit = $this->input->GET("length");
		$offset = $this->input->GET("start");
		$srno = $offset + 1;
		$extraCondition = "orderlineentries.isActive=1";
		$like = array();
		$data = array();
		
		$Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
		//echo $this->db->last_query();
		if ($Records) {
			foreach ($Records->result() as $row) {
				$productPhoto = '';
				$productCode = $row->productCode;
				$tblname = 'productphotos';
				$limit = 1;
				$condData = array('isDelete' => 0, 'productCode' => $productCode);
				$offset = array();
				$photosData = $this->ApiModel->selectData($tblname, $limit, $offset, $condData);
				$start = '<div class="d-flex align-items-center">';
				$actionHtml = "";
				$end = ' <h5 class="m-b-0 font-16 font-medium">' . $row->productName . '</h5></div></div>';
				foreach ($photosData->result() as $ph) {
					$path = base_url() . 'uploads/product/' . $ph->productCode . '/' . $ph->productPhoto;
					$productPhoto = '<div class="m-r-10"><img src="' . $path . '?' . time() . '" alt="user" class="circle" width="45"></div><div class="">';
				}
				unset($photosData);
				if ($noPic != 1) {
					$productName = $start . $productPhoto . $end;
					$data[] = array($srno, $row->productCode, $productName, $row->weight, $row->productUom, $row->productPrice, $row->quantity, $row->totalPrice, $actionHtml);
				} else {
					$productName = $row->productName;
					$data[] = array($srno, $row->productCode, $productName, $row->weight, $row->productUom, $row->productPrice, $row->quantity, $row->totalPrice);
				}
				$srno++;
			}
		}
		$dataCount = sizeOf($this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, '', '', $groupByColumn, $extraCondition)->result());
		$output = array("draw" => intval($_GET["draw"]), "recordsTotal" => $dataCount, "recordsFiltered" => $dataCount, "data" => $data);
		echo json_encode($output);
	}

	public function delete()
	{
		$code = $this->input->post('code');
		// $paymentStatus = $this->input->post('paymentStatus');
		$timeStamp = date("Y-m-d h:i:s");
		$data = array(
			'orderStatus' => 'RJT', 'paymentStatus' => 'RJCT', 'editDate' => $timeStamp,
		);
		//print_r($data);
		
		$orderData = $this->GlobalModel->selectQuery("ordermaster.*", 'ordermaster', array("ordermaster.code" => $code));
		if ($orderData) {
			$orderData = $orderData->result_array()[0];
			$deliveryBoyCode = $orderData['deliveryBoyCode'];			
			$dBoydata=array("orderCount"=>0,"orderCode"=>"","orderType"=>"");
			$this->GlobalModel->doEditWithField($dBoydata, 'deliveryBoyActiveOrder', 'deliveryBoyCode',$deliveryBoyCode);
		}
		
		$result = $this->GlobalModel->doEdit($data, 'ordermaster', $code);
		//	 print_r($result);
		if ($result == 'true') {
			$addID = $this->session->userdata['logged_in' . $this->session_key]['code'];
			$userRole = $this->session->userdata['logged_in' . $this->session_key]['role'];
			$userName = $this->session->userdata['logged_in' . $this->session_key]['username'];
			switch ($userRole) {
				case "ADM":
					$role = "Admin";
					break;
				case "USR":
					$role = "User";
					break;
			}
			$ip = $_SERVER['REMOTE_ADDR'];
			$text = $role . " " . $userName . ' Rejected Order "' . $code . '" from ' . $ip;
			$log_text = array('addId' => $addID, 'logText' => $text);
			
			
			
			echo $this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
		} else {
			echo 'false';
		}
	}

	public function invoice()
	{
		$orderCode = $this->uri->segment(3);
		$data['query'] = $this->GlobalModel->selectDataById($orderCode, 'ordermaster');
		$cityCode = $data['query']->result()[0]->cityCode;
		$clientCode = $data['query']->result()[0]->clientCode;
		$clienData = $this->GlobalModel->selectDataById($clientCode, 'clientmaster');
		$data['clientName'] = $clienData->result()[0]->name;
		$data['mobile'] = $clienData->result()[0]->mobile;
		$data['lineData'] = $this->GlobalModel->selectDataByField('orderCode', $orderCode, 'orderlineentries');
		$data['company'] = $this->GlobalModel->selectDataById($cityCode, 'citymaster');
		$tables = array('orderlineentries', 'productmaster'); //,'clientmaster'
		$requiredColumns = array(array('orderCode', 'productCode', 'weight', 'productUom', 'productPrice', 'quantity', 'totalPrice', 'isActive'), array('productName'));
		$conditions = array(array('productCode', 'code'));
		$extraConditionColumnNames = array(array('orderCode'));
		$extraConditions = array(array($orderCode));
		$Records = $this->GlobalModel1->make_datatables($tables, $requiredColumns, $conditions, $extraConditionColumnNames, $extraConditions);
		$lineData = '';
		$no = 1;
		foreach ($Records->result() as $row) {
			$lineData .= '<tr>
				<td>&nbsp;</td>
				<td><div align="center">' . $no . '</div></td>
				<td><div align="center">' . $row->productName_10 . '</div></td>
				<td><div align="center">' . $row->weight_02 . ' ' . $row->productUom_03 . '</div></td>
				<td><div align="center">' . $row->productPrice_04 . '</div></td>
				<td><div align="center">' . $row->quantity_05 . '</div></td>
				<td><div align="center">' . $row->totalPrice_06 . '</div></td>
				<td>&nbsp;</td>
			</tr>';
			$no++;
		}
		//echo $lineData;
		$data['lineData'] = $lineData;
		// print_r($data['linedata']);
		// exit();
		$this->load->view('dashboard/header');
		$this->load->view('dashboard/order/invoice', $data);
		$this->load->view('dashboard/footer');
	}

	public function getOrderUnavailableList()
	{
		$pincode = $this->input->get('pincode');
		$cityCode = $this->input->get('cityCode');
		$orderCode = $this->input->get('orderCode');
		$orderStatus = $this->input->get('orderStatus');
		// $pincode=$this->input->get('pincode');
		if ($orderCode == '' && $orderStatus == '' && $pincode == '') {
			$whereConditionArray = array('ordermaster' . '.orderStatus' => 'PND');
		} elseif ($orderCode != '') {
			$whereConditionArray = array('ordermaster' . '.code' => $orderCode);
		} elseif ($orderStatus != '') {
			$whereConditionArray = array('ordermaster' . '.orderStatus' => $orderStatus);
		} else {
			$whereConditionArray = array('ordermaster' . '.code' => $orderCode, 'ordermaster' . '.orderStatus' => $orderStatus);
		}
		$tableName = 'ordermaster';
		if ($cityCode != "") $whereConditionArray['ordermaster.cityCode'] = $cityCode;
		$orderColumnsArray = array('ordermaster.*,ordermaster.code as orderCode,customaddressmaster.*,clientmaster.*,clientprofile.pincode,citymaster.cityName');
		$orderBy = array('ordermaster' . '.id' => 'DESC');
		$joinType = array('citymaster' => 'left', 'clientmaster' => 'inner', 'clientprofile' => 'inner', 'customaddressmaster' => "inner");
		$join = array('citymaster' => 'ordermaster.cityCode=citymaster.code', 'customaddressmaster' => 'customaddressmaster.code = ordermaster.areaCode', 'clientmaster' => 'clientmaster' . '.code=' . 'ordermaster' . '.clientCode', 'clientprofile' => 'clientprofile' . '.clientCode=' . 'ordermaster' . '.clientCode'); //'clientmaster' => 'clientmaster' . '.code=' . 'ordermaster' . '.clientCode','clientprofile' => 'clientprofile' . '.clientCode=' . 'ordermaster' . '.clientCode','customaddressmaster'=>'customaddressmaster.code = ordermaster.areaCode'
		$extraCondition = "(customaddressmaster.isService = 0 OR customaddressmaster.isService IS NULL) ";
		$like = array();
		$limit = $this->input->GET("length");
		$offset = $this->input->GET("start");
		$groupByColumn = array();
		$Records = $this->GlobalModel->selectQuery($orderColumnsArray, $tableName, $whereConditionArray, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
		$srno = $offset + 1;
		$data = array();
		if ($Records) {
			foreach ($Records->result() as $row) {
				$actionHtml = '';
				$productcount = 0;
				$countdata = $this->db->query("select count(*) as cnt from orderlineentries where orderCode='" . $row->orderCode . "'");
				if ($countdata) {
					foreach ($countdata->result() as $r) {
						$productcount = $r->cnt;
					}
				}
				$itemcount = '<br><h4 class="">No. of Items - <span  class="badge badge-danger"><b>' . $productcount . '</b></span></h4>';
				$fromCity = '<br><span class="badge badge-primary"> Order From <b>' . $row->cityName . '</b></span>';
				$data[] = array(
					$srno, $row->orderCode . $itemcount, $row->name . $fromCity, $row->place, $row->address, $row->phone, $row->orderStatus,
					// $radio,
					$row->totalPrice,
					// $actionHtml
				);
				$srno++;
			}
			//$tables,$requiredColumns,$extraConditionColumnNames,$extraConditions,$conditions
			$dataCount = sizeof($this->GlobalModel->selectQuery($orderColumnsArray, $tableName, $whereConditionArray, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition)->result());
			$output = array("draw" => intval($_GET["draw"]), "recordsTotal" => $dataCount, "recordsFiltered" => $dataCount, "data" => $data);
			echo json_encode($output);
		} else {
			$dataCount = 0;
			$data = array();
			$output = array("draw" => 0, "recordsTotal" => $dataCount, "recordsFiltered" => $dataCount, "data" => $data);
			echo json_encode($output);
		}
	}

	public function assignDeliveryBoy()
	{
		$addID = $this->session->userdata['logged_in' . $this->session_key]['code'];
		$code = $this->input->get('slt');
		$deliveryBoyCode = $this->input->post('deliveryBoyCode');
		$orderCode = $this->input->post('orderCode');
		$orderType = $this->input->post('orderType');
		$orderStatus = 'PLC';
		$data = array('deliveryBoyCode' => $deliveryBoyCode, 'orderStatus' => $orderStatus);
		$Records = $this->GlobalModel->doEdit($data, 'ordermaster', $orderCode);
		$DeviceIdsArr = [];
		$notify = [];
		//echo $this->db->last_query();
		if ($Records != 'false') {
			$dataUpCnt['orderCount'] = 1;
			$dataUpCnt['orderCode'] = $orderCode;
			$dataUpCnt['orderType'] = $orderType;
			$delbActiveOrder = $this->GlobalModel->doEditWithField($dataUpCnt, 'deliveryBoyActiveOrder', 'deliveryBoyCode', $deliveryBoyCode);
			if ($delbActiveOrder != 'false') {
				$bookLineResult = 'true';
				$order_status = $this->GlobalModel->selectQuery("vendororderstatusmaster.*", "vendororderstatusmaster", array("vendororderstatusmaster.statusSName" => $orderStatus));
				if ($order_status && count($order_status->result_array()) > 0) {
					$order_status_record = $order_status->result()[0];
					$statusTitle = $order_status_record->messageTitle;
					#replace $ template in title 
					$statusDescription = $order_status_record->messageDescription;
					$statusDescription = str_replace("$", $orderCode, $statusDescription);
					$reason = "Delivery Boy Assigned by Admin";
					$dataBookLine = array(
						"orderCode" => $orderCode,
						"statusPutCode" => $addID,
						"statusLine" => $orderStatus,
						"statusTime" => date("Y-m-d H:i:s"),
						"reason" => $reason,
						"statusTitle" => $statusTitle,
						"statusDescription" => $statusDescription,
						"isActive" => 1
					);
					$bookLineResult = $this->GlobalModel->addWithoutYear($dataBookLine, 'bookorderstatuslineentries', 'BOL');

					//send notifcation
					//notification

					$tableName = "usermaster";
					$orderColumns = array("usermaster.firebase_id");
					$condition = array("usermaster.code " => $deliveryBoyCode);
					$userRecords = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition);

					if ($userRecords->result()[0]->firebase_id != null) {
						$title = "New Order Assigned";
						$message = "You have been assigned an order $orderCode by the administrator. Continue to deliver this assigned order.";
						$random = rand(0, 999);
						$DeviceIdsArr[] = $userRecords->result()[0]->firebase_id;
						$dataArr = array();
						$dataArr['device_id'] = $DeviceIdsArr;
						$dataArr['message'] = $message; //Message which you want to send
						$dataArr['title'] = $title;
						$dataArr['order_id'] = $orderCode;
						$dataArr['random_id'] = $random;
						$dataArr['type'] = 'order';
						$notification['device_id'] = $DeviceIdsArr;
						$notification['message'] = $message; //Message which you want to send
						$notification['title'] = $title;
						$notification['order_id'] = $orderCode;
						$notification['random_id'] = $random;
						$notification['type'] = 'order';
						$notify = $this->notificationlibv_3->sendDeliveryNotification($dataArr, $notification, "ringing");
						log_message("error", "notify result => " . trim(json_encode($notify)));
					}
				}
			}

			$response['status'] = true;
			$response['notifyid'] = $DeviceIdsArr;
			$response['message'] = "delivery boy successfully asssigned !";
		} else {
			$response['status'] = false;
			$response['message'] = "Failed to assign delivery boy!";
		}
		echo json_encode($response);
	}

	public function test()
	{
		$res = $this->db->query("select * from ordermaster");
		if ($res->num_rows() > 0) {
			echo "<pre>";
			print_r($res->result());
			echo "</pre>";
		}
	}

	public function transfer()
	{
		$orderCode = $code = $this->input->get('code');
		$modelHtml = '<div class="row">
						<div class="col-md-3 mb-3"><label><b>Order Code:</b></label></div>
						<div class="col-md-9 mb-9"><h5>' . $code . '<h5></div>
					</div>';
		$where_array = array('code' => $code);
		$Records = $this->GlobalModel->getRecordsWithArray('*', 'ordermaster', $where_array);
		$cityCode = "";
		foreach ($Records->result() as $row) {
			$code = $row->editID;
			$cityCode = $row->cityCode;
		}
		$tableNameDLB = "usermaster";
		$orderColumnsDLB = array("usermaster.*,citymaster.cityName,deliveryboystatuslines.reason,deliveryboystatuslines.addDate");
		$conditionDLB = array('usermaster.code' => $code, 'usermaster.cityCode' => $cityCode, 'deliveryboystatuslines.orderCode' => $orderCode, 'deliveryboystatuslines.orderStatus' => 'REL');
		$orderByDLB = array('deliveryboystatuslines' . '.id' => 'DESC');
		$joinTypeDLB = array('citymaster' => 'inner', "deliveryboystatuslines" => "inner");
		$joinDLB = array('citymaster' => 'usermaster.cityCode=citymaster.code', 'deliveryboystatuslines' => 'usermaster.code=deliveryboystatuslines.deliveryBoyCode');
		$like = array();
		$groupBy = array("deliveryboystatuslines.deliveryBoyCode");
		$Records1 = $this->GlobalModel->selectQuery($orderColumnsDLB, $tableNameDLB, $conditionDLB, $orderByDLB, $joinDLB, $joinTypeDLB, $like, "", "", $groupBy);
		$modelHtml .= '<div class="row"><div class="col-12"><h5> Released History</h5></div>
	        <div class="col-12 table-responsive"><table class="table table-bordered"><thead><tr><th>Delivery Boy</th><th>Reason</th><th>Date</th></tr></thead>';
		if ($Records1) {
			foreach ($Records1->result() as $rs)
				$dlbName = $rs->firstName . ' ' . $rs->lastName;
			$modelHtml .= '<tr><td>' . $dlbName . '</td>';
			$modelHtml .= '<td> ' . $rs->reason . '</td>';
			$modelHtml .= '<td> ' . date('d/m/Y H:i', strtotime($rs->addDate)) . '</td>';
			$modelHtml .= '</tr>';
		}
		$modelHtml .= '</table></div></div>';
		$tableName = "usermaster";
		$orderColumns = array("usermaster.*,citymaster.cityName");
		$condition = array("usermaster.isActive" => 1, 'usermaster.cityCode' => $cityCode);
		$orderBy = array('usermaster' . '.id' => 'DESC');
		$joinType = array('useraddresslineentries' => 'inner', 'citymaster' => 'inner');
		$join = array('citymaster' => 'usermaster.cityCode=citymaster.code', 'useraddresslineentries' => 'useraddresslineentries' . '.userCode=' . 'usermaster' . '.code');
		$Records2 = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, "", "", array("usermaster.code"));
		if ($Records2) {
			$modelHtml .= '<form method="post">
				<input type="hidden" value="' . $orderCode . '" id="order_code" readonly>
				<input type="hidden" value="' . $row->areaCode . '" id="areaCode" readonly>
				                <label>Reassign Order To :</label>
								<select class="form-control" id="select_boy">
								<option value="">Select delivery boy</option>';
			foreach ($Records2->result() as $row2) {
				$modelHtml .= '<option value="' . $row2->code . '">' . $row2->name . '</option>';
			}
			$modelHtml .= '</select>
				<div class="row text-center" >
					<div class="col-md-3 mb-3 mt-3">
					<button class="btn btn-block btn-md btn-info" type="button" id="transfer_change">Transfer Now?</button>
					</div>
				</div>
			</form>';
		} else {
			$modelHtml .= "Another delivery boy not available";
		}
		echo $modelHtml;
	}

	public function delivery_update()
	{
		$code = $this->input->post('slt');
		$order_code = $this->input->post('order_code');
		$areaCode = $this->input->post('areaCode');
		//exit();
		$data = array('deliveryBoyCode' => $code, 'orderStatus' => 'PND');
		$Records = $this->GlobalModel->doEdit($data, 'ordermaster', $order_code);
		if ($Records != 'false') {

			$dataUpCnt['orderCount'] = 1;
			$dataUpCnt['orderCode'] = $order_code;
			$dataUpCnt['orderType'] = 'vegetable';
			$delbActiveOrder = $this->GlobalModel->doEditWithField($dataUpCnt, 'deliveryBoyActiveOrder', 'deliveryBoyCode', $code);
			$addID = $this->session->userdata['logged_in' . $this->session_key]['code'];
			$order_status = $this->model->selectQuery(array("vendororderstatusmaster.*"), "vendororderstatusmaster", array(), array("vendororderstatusmaster.statusSName" => array("=", "PND")));
			if ($order_status && count($order_status) > 0) {
				$order_status_record = $order_status[0];
				$statusTitle = $order_status_record->messageTitle;
				#replace $ template in title 
				$statusDescription = $order_status_record->messageDescription;
				$statusDescription = str_replace("$", $order_code, $statusDescription);
				$dataBookLine = array(
					"orderCode" => $order_code,
					"statusPutCode" =>  $addID,
					"statusLine" => 'PND',
					"statusTime" => date("Y-m-d H:i:s"),
					"reason" => "Order Assigned Delivery Boy By Admin",
					"statusTitle" => $statusTitle,
					"statusDescription" => $statusDescription,
					"isActive" => 1
				);
				$bookLineResult = $this->GlobalModel->addWithoutYear($dataBookLine, 'bookorderstatuslineentries', 'BOL');
			}

			//notification
			$tableName = "customaddressmaster";
			$orderColumns = array("customaddressmaster.place");
			$condition = array("customaddressmaster.code " => $areaCode);
			$areaRecords = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition);
			$tableName = "usermaster";
			$orderColumns = array("usermaster.firebase_id");
			$condition = array("usermaster.code " => $code);
			$userRecords = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition);
			if ($areaRecords->num_rows() > 0) {
				if ($userRecords->result()[0]->firebase_id != null) {
					$title = "New Order Assigned by Admin";
					$message = "You have received an order for delivery at " . $areaRecords->result()[0]->place;
					$random = rand(0, 999);
					$DeviceIdsArr[] = $userRecords->result()[0]->firebase_id;
					$dataArr = array();
					$dataArr['device_id'] = $DeviceIdsArr;
					$dataArr['message'] = $message; //Message which you want to send
					$dataArr['title'] = $title;
					$dataArr['order_id'] = $order_code;
					$dataArr['random_id'] = $random;
					$dataArr['type'] = 'order';
					$notification['device_id'] = $DeviceIdsArr;
					$notification['message'] = $message; //Message which you want to send
					$notification['title'] = $title;
					$notification['order_id'] = $order_code;
					$notification['random_id'] = $random;
					$notification['type'] = 'order';
					$notify = $this->notificationlibv_3->sendDeliveryNotification($dataArr, $notification, "ringing");
				}
			}
			//end notification
			$response['status'] = true;
			$response['message'] = "Order was successfully Assigned to  delivery boy!";
		} else {
			$response['status'] = false;
			$response['message'] = "Failed to Assign the order!";
		}
		echo json_encode($response);
	}

	public function checkDeliveryBoyOrders()
	{
		$orderCode = $this->input->get('code');

		$Result = $this->GlobalModel->selectDataByField('code', $orderCode, 'ordermaster');
		$res = $Result->result()[0]->deliveryBoyCode;
		if ($res == "") {
			$response['status'] = false;
		} else {

			$orderResult = $this->db->query("select count(*) as cnt from deliveryBoyActiveOrder where orderCode='" . $orderCode . "' and deliveryBoyCode='" . $res . "'");
			if ($orderResult) {
				$response['status'] = true;
				$response['dbCode'] = $res;
			} else {
				$response['status'] = false;
			}
		}
		echo json_encode($response);
	}

	public function expiredByAdmin()
	{

		$isExpired = $this->input->post('isExpired');
		$orderCode = $this->input->post('orderCode');
		//$orderType = $this->input->post('orderType');
		$dbCode = $this->input->post('dbCode');

		if ($dbCode != "") {
			$query1 = $this->db->query("select orderCode from deliveryBoyActiveOrder where deliveryBoyCode='" . $dbCode . "'");
			if ($query1->result()[0]->orderCode == $orderCode) {
				//assign order status 0 to previous delivery boy
				$dataUpCnt['orderCount'] = 0;
				$dataUpCnt['orderCode'] = null;
				$dataUpCnt['orderType'] = null;
				$dataUpCnt['editDate'] = date('Y-m-d H:i:s');
				$dataUpCnt['editIP'] = $_SERVER['REMOTE_ADDR'];
				$fromDeliveryBoyResult = $this->GlobalModel->doEditWithField($dataUpCnt, 'deliveryBoyActiveOrder', 'deliveryBoyCode', $dbCode);
			}
		}

		$data = array(
			'isExpired' => $isExpired,
			'isDelete' => 1,
			'isActive' => 0,
			'deliveryBoyCode' => NULL
		);
		$Records = $this->GlobalModel->doEdit($data, 'ordermaster', $orderCode);
		//echo $this->db->last_query(); 
		if ($Records != 'false') {
			$response['status'] = true;
			$response['message'] = "successfully Changed Expired Status!";
		} else {
			$response['status'] = false;
			$response['message'] = "Failed to Change Expired Status!";
		}
		echo json_encode($response);
	}
}
