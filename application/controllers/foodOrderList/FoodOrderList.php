<?php
defined('BASEPATH') or exit('No direct script access allowed');

class FoodOrderList extends CI_Controller
{
	var $session_key;
	public function __construct()
	{
		parent::__construct();
		$this->load->model('GlobalModel');
		$this->load->library('notificationlibv_3');
		$this->session_key = $this->session->userdata('key' . SESS_KEY);
	}

	public function index()
	{
		$data['vendor'] = $this->GlobalModel->selectQuery('vendor.*', 'vendor', array('vendor.isActive' => 1));
		$data['city'] = $this->GlobalModel->selectQuery('citymaster.*', 'citymaster', array('citymaster.isActive' => 1));
		$data['orderstatus'] = $this->GlobalModel->selectQuery('vendororderstatusmaster.*', 'vendororderstatusmaster');
		$data['orderCode'] = $this->GlobalModel->selectQuery('vendorordermaster.*', 'vendorordermaster', array('vendorordermaster.isActive' => 1));
		$data['deliveryboy'] = false;
		$tableName = "usermaster";
		$orderColumns = array("usermaster.code,usermaster.name");
		$condition = array('usermaster.isActive' => 1, 'usermaster.role' => 'DLB');
		$orderBy = array('usermaster' . '.id' => 'DESC');
		$joinType = array();
		$join = array();
		$Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType);
		if ($Records) {
			$data['deliveryboy'] = $Records;
		}
		$this->session_key = $this->session->userdata('key' . SESS_KEY);
		$this->load->view('dashboard/header');
		$this->load->view('dashboard/foodOrderList/foodOrder', $data);
		$this->load->view('dashboard/footer');
	}

	public function getOrderList()
	{
		$startDate = $this->input->post('startDate') ?? $this->input->get('startDate');
		$endDate = $this->input->post('endDate') ?? $this->input->get('endDate');
		$vendorCode = $this->input->post('vendorCode') ?? $this->input->get('vendorCode');
		$deliveryboyCode = $this->input->post('deliveryboyCode') ?? $this->input->get('deliveryboyCode');
		$orderStatus = $this->input->post('orderStatus') ?? $this->input->get('orderStatus');
		$orderCode = $this->input->post('orderCode') ?? $this->input->get('orderCode');
		if ($orderStatus == "") {
		//$orderStatus = "PND";
		}
		$datw = "";
		if ($startDate != '') {
			$startDate = DateTime::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
			$endDate = DateTime::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
			$startDate = $startDate . " 00:00:00";
			$endDate = $endDate . " 23:59:59";
			$datw = " AND vendorordermaster.editDate between '" . $startDate . "' And '" . $endDate . "'";
		}
		$tableName = "vendorordermaster";
		$orderColumns = array("vendorordermaster.*,clientmaster.name,clientmaster.mobile,vendor.entityName,usermaster.empCode,vendororderstatusmaster.statusSName,vendorordermaster.code");
		$condition = array("vendorordermaster.vendorCode" => $vendorCode, "vendorordermaster.deliveryBoyCode" => $deliveryboyCode, "vendorordermaster.orderStatus" => $orderStatus, "vendorordermaster.code" => $orderCode);
		if ($deliveryboyCode != "") {
			$joinType = array('clientmaster' => 'inner', 'vendor' => 'inner', 'usermaster' => 'inner', 'vendororderstatusmaster' => 'inner');
			$join = array('clientmaster' => 'clientmaster.code=vendorordermaster.clientCode', 'vendor' => 'vendor.code=vendorordermaster.vendorCode', 'usermaster' => 'usermaster.code=vendorordermaster.deliveryBoyCode', 'vendororderstatusmaster' => 'vendororderstatusmaster.statusSName=vendorordermaster.orderStatus');
		}
		else {
			$joinType = array('clientmaster' => 'inner', 'vendor' => 'inner', 'usermaster' => 'left', 'vendororderstatusmaster' => 'inner');
			$join = array('clientmaster' => 'clientmaster.code=vendorordermaster.clientCode', 'vendor' => 'vendor.code=vendorordermaster.vendorCode', 'usermaster' => 'usermaster.code=vendorordermaster.deliveryBoyCode', 'vendororderstatusmaster' => 'vendororderstatusmaster.statusSName=vendorordermaster.orderStatus');
		}
		$orderBy = array('vendorordermaster' . '.id' => 'DESC');
		$groupByColumn = array("vendorordermaster.code");
		$limit = $this->input->post("length") ?? $this->input->get("length");
		$offset = $this->input->post("start") ?? $this->input->get("start");
		$extraCondition = "vendorordermaster.orderStatus NOT IN ('CAN','REL','RJT') AND (vendorordermaster.isDelete=0 OR vendorordermaster.isDelete IS NULL)" . $datw;
		$like = array();
		$Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
		//echo $this->db->last_query();
		// exit; 
		$data = array();
		$srno = (intval($offset) > 0 ? intval($offset) : 0) + 1;
		if ($Records) {
			foreach ($Records->result() as $row) {
				$statusTime = $row->addDate;
				$recordsLineStatus = $this->GlobalModel->selectQuery("bookorderstatuslineentries.addDate as orderaddDate,bookorderstatuslineentries.statusTime", "bookorderstatuslineentries", array("bookorderstatuslineentries.orderCode" => $row->code), array(), array(), array(), array(), 1);
				if ($recordsLineStatus) {
					$statusTime = $recordsLineStatus->result_array()[0]['statusTime'];
				}
				if ($row->isActive == 1) {
					$status = "<span class='label label-sm label-success'>Active</span>";
				}
				else {
					$status = "<span class='label label-sm label-warning'>Inactive</span>";
				}
				$orderDate = date('d-m-Y h:i:s', strtotime($row->addDate));
				$orderStatus = $row->orderStatus;
				$odStatus = $row->orderStatus;
				switch ($orderStatus) {
					case "PND":
						$orderStatus = "Pending";
						$orderDate = date('d-m-Y h:i A', strtotime($row->addDate));
						break;
					case "PLC":
						$orderStatus = "Placed";
						$orderDate = date('d-m-Y h:i A', strtotime($statusTime));
						break;
					case "SHP":
						$orderStatus = "Shipped";
						$chkSHP = 'checked';
						$orderDate = date('d-m-Y h:i A', strtotime($statusTime));
						break;
					case "DEL":
						$orderStatus = "Delivered";
						$chkDEL = 'checked';
						$orderDate = date('d-m-Y h:i A', strtotime($statusTime));
						break;
					case "CAN":
						$orderStatus = "Cancelled By User";
						$orderDate = date('d-m-Y h:i A', strtotime($statusTime));
						break;
					case "RJT":
						$orderStatus = "Rejected";
						$chkRJT = 'checked';
						$orderDate = date('d-m-Y h:i A', strtotime($statusTime));
						break;
					case "PRE":
						$orderStatus = "Preparing";
						$chkRJT = 'checked';
						$orderDate = date('d-m-Y h:i A', strtotime($statusTime));
						break;
					case "FRE":
						$orderStatus = "Food Ready";
						$chkRJT = 'checked';
						$orderDate = date('d-m-Y h:i A', strtotime($statusTime));
						break;
					case "REL":
						$orderStatus = "Release";
						$chkRJT = 'checked';
						$orderDate = date('d-m-Y h:i A', strtotime($statusTime));
						break;
					case "PUP":
						$orderStatus = "On the Way";
						$chkRJT = 'checked';
						$orderDate = date('d-m-Y h:i A', strtotime($statusTime));
						break;
					case "RFP":
						$orderStatus = "Ready For Pickup";
						$chkSHP = 'checked';
						$orderDate = date('d-m-Y h:i A', strtotime($statusTime));
						break;
				}

				$deliveryboy = "";
				if ($row->deliveryBoyCode != "") {
					$query = $this->db->query("select * from usermaster where code='" . $row->deliveryBoyCode . "'");
					$deliveryboy = $query->result()[0]->name;
				}
				else {
					$deliveryboy = "";
				}

				$actionHtml = '  <a class="btn btn-success" href="' . base_url('foodOrderList/FoodOrderList/view/' . $row->code) . '"><i class="ti-eye"></i> Open</a>';
				if ($row->orderStatus != 'PND' && $row->orderStatus != 'CAN' && $row->orderStatus != 'RJT') {
					$actionHtml .= '   <a class="btn btn-success d-none" href="' . base_url() . 'foodOrderList/FoodOrderList/tracking/' . $row->code . '"><i class="ti-direction" href></i> Tracking</a>';
				}
				$data[] = array(
					$srno,
					$row->code,
					$row->name,
					$row->entityName,
					$row->address,
					$row->mobile,
					$orderStatus,
					$row->grandTotal,
					$orderDate,
					$deliveryboy,
					$actionHtml
				);

				$srno++;
			}
			$dataCount = sizeof($this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, array(), '', '', '', $extraCondition)->result());
			$output = array(
				"draw" => intval($this->input->post("draw") ?? $this->input->get("draw") ?? 0),
				"recordsTotal" => $dataCount,
				"recordsFiltered" => $dataCount,
				"data" => $data,

			);
			echo json_encode($output);
		}
		else {
			$dataCount = 0;
			$data = array();
			$output = array(
				"draw" => intval($this->input->post("draw") ?? $this->input->get("draw") ?? 0),
				"recordsTotal" => $dataCount,
				"recordsFiltered" => $dataCount,
				"data" => $data,

			);
			echo json_encode($output);
		}
	}

	public function getOrderDetails()
	{
		$addID = $this->session->userdata['logged_in' . $this->session_key]['code'];
		$orderCode = $this->input->post('orderCode') ?? $this->input->get('orderCode');
		$noPic = $this->input->post('noPic') ?? $this->input->get('noPic');
		$tableName = 'vendororderlineentries';
		$orderColumns = array("vendororderlineentries.*,vendoritemmaster.vendorCode,vendoritemmaster.itemName,vendoritemmaster.salePrice,vendoritemmaster.itemPhoto");
		$condition = array('vendororderlineentries.orderCode' => $orderCode);
		$orderBy = array('vendororderlineentries.id' => 'desc');
		$joinType = array('vendoritemmaster' => 'inner');
		$join = array('vendoritemmaster' => 'vendoritemmaster.code=vendororderlineentries.vendorItemCode');
		$groupByColumn = array();
		$limit = $this->input->post("length") ?? $this->input->get("length");
		$offset = $this->input->post("start") ?? $this->input->get("start");
		$srno = (intval($offset) > 0 ? intval($offset) : 0) + 1;
		$addonText = '';
		$extraCondition = "vendororderlineentries.isActive=1";
		$like = array();
		$data = array();
		$Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
		if ($Records) {
			foreach ($Records->result() as $row) {
				$addonText = '';
				if ($row->addonsCode != '' && $row->addonsCode != NULL) {
					$row->addonsCode = rtrim($row->addonsCode, ',');
					$savedaddonsCodes = explode(',', $row->addonsCode);
					foreach ($savedaddonsCodes as $addon) {
						$joinType1 = array('customizedcategory' => 'inner');
						$condition1 = array('customizedcategorylineentries.code' => $addon);
						$join1 = array('customizedcategory' => "customizedcategory.code=customizedcategorylineentries.customizedCategoryCode");
						$getAddonDetails = $this->GlobalModel->selectQuery("customizedcategory.categoryTitle,customizedcategory.categoryType,customizedcategorylineentries.subCategoryTitle,customizedcategorylineentries.price", "customizedcategorylineentries", $condition1, array(), $join1, $joinType1, array(), array(), '', array(), '');
						$prevMainCateg = $prevMainCateg1 = '';
						if ($getAddonDetails) {
							foreach ($getAddonDetails->result() as $ad) {
								$mainCategory = $ad->categoryTitle;
								$addonText .= '<ul>
									<li><b>' . $ad->categoryTitle . ' - ' . ucfirst($ad->categoryType) .
									'</b><ul>
										<li>' . $ad->subCategoryTitle . ' - ' . $ad->price . '</li>
									</ul>
							           </li>
								</ul>';
							}
						}
					}
				}
				$start = '<div class="d-flex align-items-center">';
				$end = ' <h5 class="m-b-0 font-16 font-medium">' . $row->itemName . '</h5></div></div>';
				$itemPhotoCheck = $row->itemPhoto;
				if ($itemPhotoCheck != "") {
					$itemPhoto = base_url('partner/uploads/' . $row->vendorCode . '/vendoritem/' . $itemPhotoCheck);
					$photo = '<div class="m-r-10"><img src="' . $itemPhoto . '?' . time() . '" alt="user" class="circle" width="45"></div><div class="">';
					$itemName = $start . $photo . $end;
					$data[] = array($srno, $row->vendorItemCode, $itemName . '<br>' . $addonText, $row->salePrice, $row->quantity, $row->priceWithQuantity);
				}
				else {
					$itemName = ' <h5 class="m-b-0 font-16 font-medium">' . $row->itemName . '</h5></div></div>';
					$data[] = array($srno, $row->vendorItemCode, $itemName . '<br>' . $addonText, $row->salePrice, $row->quantity, $row->priceWithQuantity);
				}
				$srno++;
			}
		}
		// $dataCount = sizeOf($this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, '', '', $groupByColumn, $extraCondition)->result());
		$dataCount = 0;
		$dataCount1 = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, '', '', $groupByColumn, $extraCondition);
		if ($dataCount1) {
			$dataCount = sizeOf($dataCount1->result());
		}
		$output = array("draw" => intval($this->input->post("draw") ?? $this->input->get("draw") ?? 0), "recordsTotal" => $dataCount, "recordsFiltered" => $dataCount, "data" => $data);
		echo json_encode($output);
	}

	public function view()
	{
		$addID = $this->session->userdata['logged_in' . $this->session_key]['code'];
		$data['discountInPercent'] = 0;
		$data['orderData'] = false;
		$code = $this->uri->segment(4);
		$tableName = "vendorordermaster";
		$orderColumns = array("vendorordermaster.*,clientmaster.name,clientmaster.mobile,usermaster.username,clientmaster.cityCode");
		$condition = array('vendorordermaster.code' => $code);
		$orderBy = array('vendorordermaster' . '.id' => 'DESC');
		$joinType = array('clientmaster' => 'inner', 'usermaster' => 'left');
		$join = array('clientmaster' => 'clientmaster.code=vendorordermaster.clientCode', 'usermaster' => 'usermaster.code=vendorordermaster.deliveryBoyCode');
		$groupByColumn = array();
		$limit = $this->input->post("length") ?? $this->input->get("length");
		$offset = $this->input->post("start") ?? $this->input->get("start");
		$extraCondition = " (vendorordermaster.isDelete=0 OR vendorordermaster.isDelete IS NULL)";
		$like = array();
		$Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
		//echo $this->db->last_query();
		if ($Records) {
			$data['query'] = $Records;
			$coupanCode = $Records->result_array()[0]['coupanCode'];
			$dataCC = $this->GlobalModel->selectQuery('vendoroffer.discount', 'vendoroffer', array('vendoroffer.coupanCode' => $coupanCode, 'vendoroffer.vendorCode' => $addID));
			if ($dataCC) {
				$data['discountInPercent'] = $dataCC->result_array()[0]['discount'];
			}
		}
		else {
			$data['query'] = false;
		}
		$data['orderStatus'] = $this->GlobalModel->selectDataExcludeDelete('vendororderstatusmaster');
		$data['paymentStatus'] = $this->GlobalModel->selectDataExcludeDelete('paymentstatusmaster');
		$this->load->view('dashboard/header');
		$this->load->view('dashboard/foodOrderList/view', $data);
		$this->load->view('dashboard/footer');
	}

	public function getPendingDeliveryBoys()
	{
		$deliveryBoyCode = $this->input->post('deliveryBoyCode') ?? $this->input->get('deliveryBoyCode');
		$cityCode = $this->input->post('cityCode') ?? $this->input->get('cityCode');
		$statusType = 'PND';
		$data = array();
		$dataCount = 0;
		$table = "usermaster";
		$orderColumns = "usermaster.*,";
		$condition = array("usermaster.isActive" => 1, "usermaster.cityCode" => $cityCode, "usermaster.role" => "DLB");

		$orderBy = array("usermaster.id" => "ASC");
		$like = array();
		$groupBy = array();
		$extraCondition = "";
		if ($statusType == 'all') {
		//$join = array("employeemaster"=>"usermaster.empCode=employeemaster.code");
		//$joinType = array("employeemaster"=>"inner"); 
		}
		else {
			if ($statusType == 'present') {
				$condition['deliveryBoyActiveOrder.loginStatus'] = '1';
			}
			else if ($statusType == 'absent') {
				$condition['deliveryBoyActiveOrder.loginStatus'] = '0';
			}
			else {
				$condition['deliveryBoyActiveOrder.loginStatus'] = '1';
				$condition['deliveryBoyActiveOrder.orderCount'] = '0';
			}
			$join = array("deliveryBoyActiveOrder" => "usermaster.code=deliveryBoyActiveOrder.deliveryBoyCode");
			$joinType = array("deliveryBoyActiveOrder" => "inner");
		}

		$Result = $this->GlobalModel->selectQuery($orderColumns, $table, $condition, $orderBy, $join, $joinType, $like, "", "", $groupBy, $extraCondition);
		//echo $this->db->last_query();
		$html = '<option value="" readonly>Select another delivery boy</option>';
		if ($Result) {
			foreach ($Result->result_array() as $key) {
				if ($deliveryBoyCode != $key['code']) {
					$html .= '<option value="' . $key['code'] . '">' . $key['name'] . '</option>';
				}
			}
		}
		else {
			$html = false;
		}
		echo $html;
	}

	public function transferOrder()
	{
		$orderCode = $this->input->post('orderCode');
		$fromDeliveryBoy = $this->input->post('fromDeliveryBoy');
		$toDeliveryBoy = $this->input->post('toDeliveryBoy');
		$orderStatus = $this->input->post('orderStatus');
		$orderType = $this->input->post('orderType');
		if ($orderStatus == 'PND') {
			$orderData['orderStatus'] = 'PLC';
		}
		if ($orderType == 'food') {
			$orderData['deliveryBoyCode'] = $toDeliveryBoy;
			$orderUpdateResult = $this->GlobalModel->doEditWithField($orderData, 'vendorordermaster', 'code', $orderCode);
		}
		else {
			$orderData['deliveryBoyCode'] = $toDeliveryBoy;
			$orderUpdateResult = $this->GlobalModel->doEditWithField($orderData, 'ordermaster', 'code', $orderCode);
		}

		//assign order status 0 to previous delivery boy
		$dataUpCnt['orderCount'] = 0;
		$dataUpCnt['orderCode'] = null;
		$dataUpCnt['orderType'] = null;
		$dataUpCnt['editDate'] = date('Y-m-d H:i:s');
		$dataUpCnt['editIP'] = $_SERVER['REMOTE_ADDR'];
		$fromDeliveryBoyResult = $this->GlobalModel->doEditWithField($dataUpCnt, 'deliveryBoyActiveOrder', 'deliveryBoyCode', $fromDeliveryBoy);

		//assign order status 1 to new delivery boy
		$dataUpCnt['orderCount'] = 1;
		$dataUpCnt['orderCode'] = $orderCode;
		$dataUpCnt['orderType'] = $orderType;
		$dataUpCnt['editDate'] = date('Y-m-d H:i:s');
		$dataUpCnt['editIP'] = $_SERVER['REMOTE_ADDR'];
		$toDeliveryBoyResult = $this->GlobalModel->doEditWithField($dataUpCnt, 'deliveryBoyActiveOrder', 'deliveryBoyCode', $toDeliveryBoy);

		$addID = $this->session->userdata['logged_in' . $this->session_key]['code'];
		$bookLineResult = 'true';
		$order_status = $this->GlobalModel->selectQuery("vendororderstatusmaster.*", "vendororderstatusmaster", array("vendororderstatusmaster.statusSName" => $orderStatus));
		if ($order_status && count($order_status->result_array()) > 0) {
			$order_status_record = $order_status->result()[0];
			$statusTitle = $order_status_record->messageTitle;
			#replace $ template in title 
			$statusDescription = $order_status_record->messageDescription;
			//echo $statusDescription;
			$statusDescription = str_replace("$", $orderCode, $statusDescription);
			$reason = "Delivery Boy Assigned by Admin";
			$dataBookLine = array(
				"orderCode" => $orderCode,
				"statusPutCode" => $addID,
				"statusLine" => $orderData['orderStatus'],
				"statusTime" => date("Y-m-d H:i:s"),
				"reason" => $reason,
				"statusTitle" => $statusTitle,
				"statusDescription" => $statusDescription,
				"isActive" => 1
			);
			$bookLineResult = $this->GlobalModel->addWithoutYear($dataBookLine, 'bookorderstatuslineentries', 'BOL');
		}

		if ($toDeliveryBoyResult != 'false') {

			//also transfer touch point amount
			if ($orderStatus == "PRE" || $orderStatus == "RFP" || $orderStatus == "PUP" || $orderStatus == "RCH") {
				$tableName = 'deliveryboyearncommission';
				$transData["deliveryBoyCode"] = $toDeliveryBoy;
				$this->GlobalModel->doEditWithField($transData, $tableName, 'orderCode', $orderCode);
			}

			//send notification to the delivery boy
			$random = rand(0, 999);
			$dataNoti = array("title" => 'Transfer Order!', "message" => 'You have new transfered order', "order_id" => $orderCode, "random_id" => $random, 'type' => 'order');
			$delBoy = $this->GlobalModel->selectQuery("usermaster.firebase_id", "usermaster", array("usermaster.code" => $toDeliveryBoy));
			if ($delBoy) {
				$firebaseId = $delBoy->result_array()[0]['firebase_id'];
				if ($firebaseId != "" && $firebaseId != null) {
					$DeviceIdsArr = [$firebaseId];
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
					$notify = $this->notificationlibv_3->sendDeliveryNotification($dataArr, $notification, "ringing");
				}
			}

			$response["status"] = true;
			$response["message"] = "Order Successfully Transfered to another delivery boy";
		}
		else {
			$response["status"] = false;
			$response["message"] = "Failed to transfer order";
		}
		echo json_encode($response);
	}

	public function getOrderStatusList()
	{
		$dataCount = 0;
		$data = array();
		$cnt = 0;
		$orderCode = $this->input->post('orderCode') ?? $this->input->get('orderCode');
		$orderColumns = "vendororderstatusmaster.statusName,bookorderstatuslineentries.statusPutCode,bookorderstatuslineentries.reason,bookorderstatuslineentries.statusTime";
		$table = "bookorderstatuslineentries";
		$condition["bookorderstatuslineentries.orderCode"] = $orderCode;
		$orderBy["bookorderstatuslineentries.id"] = "DESC";
		$join["vendororderstatusmaster"] = "bookorderstatuslineentries.statusLine=vendororderstatusmaster.statusSName";
		$joinType["vendororderstatusmaster"] = "inner";
		$result = $this->GlobalModel->selectQuery($orderColumns, $table, $condition, $orderBy, $join, $joinType);
		$ra = $this->db->last_query();
		if ($result) {
			foreach ($result->result_array() as $r) {
				$cnt++;
				$statusPutCode = $r["statusPutCode"];
				$name = "";
				$firstTwoCharacters = substr($statusPutCode, 0, 3);
				if ($firstTwoCharacters == "CLN") {
					$q = $this->GlobalModel->selectQuery("clientmaster.name", "clientmaster", array("clientmaster.code" => $statusPutCode));
					if ($q) {
						$name = $q->result_array()[0]['name'];
					}
				}
				else if ($firstTwoCharacters == "USR") {
					$q = $this->GlobalModel->selectQuery("usermaster.username", "usermaster", array("usermaster.code" => $statusPutCode));
					if ($q) {
						$name = $q->result_array()[0]['username'];
					}
				}
				else {
					$q = $this->GlobalModel->selectQuery("vendor.entityName", "vendor", array("vendor.code" => $statusPutCode));
					if ($q) {
						$name = $q->result_array()[0]['entityName'];
					}
				}
				$ar = array(
					$cnt,
					$r['statusName'],
					date("d-M-Y h:i A", strtotime($r['statusTime'])),
					$name,
					$r['reason']
				);
				$data[] = $ar;
			}
			$dataCount = sizeof($this->GlobalModel->selectQuery($orderColumns, $table, $condition, $orderBy, $join, $joinType)->result_array());
		}
		$output = array("r" => $ra, "draw" => intval($this->input->post("draw") ?? $this->input->get("draw") ?? 0), "recordsTotal" => $dataCount, "recordsFiltered" => $dataCount, "data" => $data);
		echo json_encode($output);
	}
	public function checkDeliveryBoyOrders()
	{
		$orderCode = $this->input->post('code') ?? $this->input->get('code');


		$Result = $this->GlobalModel->selectDataByField('code', $orderCode, 'vendorordermaster');
		$res = $Result->result()[0]->deliveryBoyCode;
		if ($res == "") {
			$response['status'] = false;
		}
		else {

			$orderResult = $this->db->query("select count(*) as cnt from deliveryBoyActiveOrder where orderCode='" . $res . "' and deliveryBoyCode='" . $res . "'");
			if ($orderResult) {
				$response['status'] = true;
				$response['dbCode'] = $res;
			}
			else {
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
			'isActive' => 0
		);
		$Records = $this->GlobalModel->doEdit($data, 'vendorordermaster', $orderCode);
		//echo $this->db->last_query(); 
		if ($Records != 'false') {
			$response['status'] = true;
			$response['message'] = "successfully Changed Expired Status!";
		}
		else {
			$response['status'] = false;
			$response['message'] = "Failed to Change Expired Status!";
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

	public function tracking()
	{
		$orderCode = $this->uri->segment(4);
		$query = $this->GlobalModel->selectDataById($orderCode, 'vendorordermaster');
		$data['query'] = $query;
		$deliveryBoyCode = $query->result()[0]->deliveryBoyCode;
		$clientCode = $query->result()[0]->clientCode;
		$restaurantCode = $query->result()[0]->vendorCode;
		$dlbquery = $this->GlobalModel->selectDataById($deliveryBoyCode, 'usermaster');
		$data['dlbName'] = $dlbquery->result()[0]->name;
		$data['dlbMobile'] = $dlbquery->result()[0]->mobile;
		$profilePhoto = $dlbquery->result()[0]->profilePhoto;
		$data['dlbPic'] = base_url() . 'uploads/profilePhoto.jpg';
		if ($profilePhoto != '' && $profilePhoto != NULL) {
			$data['dlbPic'] = base_url() . 'uploads/profilePhoto/' . $profilePhoto;
		}
		$clientMaster = $this->GlobalModel->selectQuery('clientmaster.name,clientmaster.mobile', 'clientmaster', array('clientmaster.code' => $clientCode, 'clientmaster.isActive' => 1));
		$data['clientName'] = $clientMaster->result()[0]->name;
		$data['clientMobile'] = $clientMaster->result()[0]->mobile;
		$clientData = $this->GlobalModel->selectQuery('clientprofile.latitude,clientprofile.longitude', 'clientprofile', array('clientprofile.clientCode' => $clientCode, 'clientprofile.isActive' => 1, 'clientprofile.isSelected' => 1));
		$data['clLatitude'] = $clientData->result()[0]->latitude;
		$data['clLongitude'] = $clientData->result()[0]->longitude;
		$restaurant = $this->GlobalModel->selectQuery('vendor.entityName,vendor.ownerContact,vendor.latitude,vendor.longitude', 'vendor', array('vendor.code' => $restaurantCode, 'vendor.isActive' => 1));
		$data['ResLatitude'] = $restaurant->result()[0]->latitude;
		$data['ResLongitude'] = $restaurant->result()[0]->longitude;
		$data['ResName'] = $restaurant->result()[0]->entityName;
		$data['ResMobile'] = $restaurant->result()[0]->ownerContact;
		$filename = 'assets/order_tracking/' . $orderCode . '.json';
		if (file_exists($filename)) {
			$jsonString = file_get_contents('assets/order_tracking/' . $orderCode . '.json');
			$fileData = json_decode($jsonString, true);
			if (!empty($fileData[0])) {
				$data['latitude'] = $fileData[0]['latitude'];
				$data['longitude'] = $fileData[0]['longitude'];
			}
		}
		$this->load->view('dashboard/header');
		$this->load->view('dashboard/foodOrderList/tracking', $data);
		$this->load->view('dashboard/footer');
	}
}
