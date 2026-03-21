<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UserCancelOrder extends CI_Controller 
{
	var $session_key;
	public function __construct()
    {
        parent::__construct();
        $this->load->model('GlobalModel');
    }
    
   
	public function index()
	{
	    $data['clientmaster'] = $this->GlobalModel->selectData('clientmaster');
		$data['address'] = $this->GlobalModel->selectQuery('customaddressmaster.*','customaddressmaster',array('customaddressmaster.isActive'=>1));
		$data['city'] = $this->GlobalModel->selectActiveData('citymaster');
        $data['orderStatus'] = $this->GlobalModel->selectDataExcludeDelete('orderstatusmaster');
        $data['error'] = $this->session->flashdata('response');
		$data['orderCode'] = $this->GlobalModel->selectData('ordermaster');
        $data['vendor'] = $this->GlobalModel->selectQuery('vendor.*', 'vendor', array('vendor.isActive' => 1));
		$data['deliveryboy'] = false;
		$tableName = "usermaster";

		$orderColumns = array("usermaster.*,employeemaster.firstName,employeemaster.lastName");

		$condition = array('usermaster.isActive' => 1);
		$orderBy = array('usermaster' . '.id' => 'DESC');
		$joinType = array('employeemaster' => 'inner');
		$join = array('employeemaster' => 'usermaster.empCode=employeemaster.code');
		$Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType);
		if ($Records) {
			$data['deliveryboy'] = $Records;
		}	
        

		$this->session_key = $this->session->userdata('key'.SESS_KEY);
		 $this->load->view('dashboard/header');
		 $this->load->view('dashboard/userCancelOrder/list',$data);
		 $this->load->view('dashboard/footer');
	}
	
	public function getcancelorderlist()
	{ 
	   $startDate = $this->input->get('fromDate');
		$endDate = $this->input->get('toDate');
		$vendorCode = $this->input->get('vendorCode');
		$deliveryboyCode = $this->input->get('deliveryboyCode');
		$orderStatus = $this->input->get('orderStatus');
		$orderCode = $this->input->get('orderCode');
		if ($orderStatus == "") {
			//$orderStatus = "PND";
		}
		$datw="";
		if ($startDate != '') {
			$startDate = DateTime::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
			$endDate = DateTime::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
			$startDate = $startDate . " 00:00:00";
			$endDate = $endDate . " 23:59:59";
		 $datw=" AND vendorordermaster.editDate between '".$startDate."' And '".$endDate."'";
		}
		$tableName = "vendorordermaster";
		$orderColumns = array("vendorordermaster.*,clientmaster.name,clientmaster.mobile,vendor.entityName,usermaster.empCode,vendororderstatusmaster.statusSName,vendorordermaster.code");
		$condition = array("vendorordermaster.vendorCode" => $vendorCode, "vendorordermaster.deliveryBoyCode" => $deliveryboyCode, "vendorordermaster.orderStatus" => $orderStatus, "vendorordermaster.code" => $orderCode);
		if ($deliveryboyCode != "") {
			$joinType = array('clientmaster' => 'inner', 'vendor' => 'inner', 'usermaster' => 'inner', 'vendororderstatusmaster' => 'inner');
			$join = array('clientmaster' => 'clientmaster.code=vendorordermaster.clientCode', 'vendor' => 'vendor.code=vendorordermaster.vendorCode', 'usermaster' => 'usermaster.code=vendorordermaster.deliveryBoyCode', 'vendororderstatusmaster' => 'vendororderstatusmaster.statusSName=vendorordermaster.orderStatus');
		} else {
			$joinType = array('clientmaster' => 'inner', 'vendor' => 'inner', 'usermaster' => 'left', 'vendororderstatusmaster' => 'inner');
			$join = array('clientmaster' => 'clientmaster.code=vendorordermaster.clientCode', 'vendor' => 'vendor.code=vendorordermaster.vendorCode', 'usermaster' => 'usermaster.code=vendorordermaster.deliveryBoyCode', 'vendororderstatusmaster' => 'vendororderstatusmaster.statusSName=vendorordermaster.orderStatus');
		}
		$orderBy = array('vendorordermaster' . '.id' => 'DESC');
		$groupByColumn = array("vendorordermaster.code");
		$limit = $this->input->GET("length");
		$offset = $this->input->GET("start");
		$extraCondition = "`vendorordermaster`.`orderStatus` = 'CAN' OR `vendorordermaster`.`orderStatus` = 'RJT' AND (vendorordermaster.isDelete=0 OR vendorordermaster.isDelete IS NULL)".$datw;	 
		$like = array();
		$Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
		
		//print_r($this->db->last_query());
		//exit; 
		
		$data = array();
		$srno = $_GET['start'] + 1;
		if ($Records) {
			foreach ($Records->result() as $row) { 
				$statusTime = $row->addDate;
				$recordsLineStatus = $this->GlobalModel->selectQuery("bookorderstatuslineentries.addDate as orderaddDate,bookorderstatuslineentries.statusTime", "bookorderstatuslineentries", array("bookorderstatuslineentries.orderCode" => $row->code), array(), array(), array(), array(), 1);
				if ($recordsLineStatus) {
					$statusTime = $recordsLineStatus->result_array()[0]['statusTime'];
				}
				if ($row->isActive == 1) {
					$status = "<span class='label label-sm label-success'>Active</span>";
				} else {
					$status = "<span class='label label-sm label-warning'>Inactive</span>";
				}
				$orderDate = date('d-m-Y h:i:s', strtotime($row->addDate));
				$orderStatus = $row->orderStatus;
				$odStatus = $row->orderStatus;
				switch ($orderStatus) {
					case "PND":
						$orderStatus = "Pending";
						$orderDate = date('d-m-Y h:i:s', strtotime($row->addDate));
						break;
					case "PLC":
						$orderStatus = "Placed";
						$orderDate = date('d-m-Y h:i:s', strtotime($statusTime));
						break;
					case "SHP":
						$orderStatus = "Shipped";
						$chkSHP = 'checked';
						$orderDate = date('d-m-Y h:i:s', strtotime($statusTime));
						break;
					case "DEL":
						$orderStatus = "Delivered";
						$chkDEL = 'checked';
						$orderDate = date('d-m-Y h:i:s', strtotime($statusTime));
						break;
					case "CAN":
						$orderStatus = "Cancelled By User";
						$orderDate = date('d-m-Y h:i:s', strtotime($statusTime));
						break;
					case "RJT":
						$orderStatus = "Reject";
						$chkRJT = 'checked';
						$orderDate = date('d-m-Y h:i:s', strtotime($statusTime));
						break;
					case "PRE":
						$orderStatus = "Preparing";
						$chkRJT = 'checked';
						$orderDate = date('d-m-Y h:i:s', strtotime($statusTime));
						break;
					case "FRE":
						$orderStatus = "Food Ready";
						$chkRJT = 'checked';
						$orderDate = date('d-m-Y h:i:s', strtotime($statusTime));
						break;
					case "REL":
						$orderStatus = "Release";
						$chkRJT = 'checked';
						$orderDate = date('d-m-Y h:i:s', strtotime($statusTime));
					break;
				}
				
				$deliveryboy="";
				if($row->deliveryBoyCode!="")
				{
					$query=$this->db->query("select * from usermaster where code='".$row->deliveryBoyCode."'");
					
					$deliveryboy=$query->result()[0]->username." (".$row->deliveryBoyCode.")";
					
				}else
				{
					$deliveryboy="";
				}
				
				$actionHtml = '  <a class="dropdown-item  blue" href="' . base_url('foodOrderList/FoodOrderList/view/' . $row->code). '"><i class="ti-eye"></i> Open</a>';
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
				"draw"			  =>     intval($_GET["draw"]),
				"recordsTotal"    =>      $dataCount,
				"recordsFiltered" =>     $dataCount,
				"data"            =>     $data,

			);
			echo json_encode($output);
		} else {
			$dataCount = 0;
			$data = array();
			$output = array(
				"draw"			  =>     intval($_GET["draw"]),
				"recordsTotal"    =>     $dataCount,
				"recordsFiltered" =>     $dataCount,
				"data"            =>     $data,

			);
			echo json_encode($output);
		}
	
	}
	
	
	
	public function getOrderList()
	 {
        $tables = array('ordermaster', 'clientmaster');
        $requiredColumns = array(array('code', 'clientCode', 'paymentref', 'paymentmode', 'paymentStatus', 'orderStatus', 'areaCode', 'address', 'phone', 'totalPrice', 'isActive', 'addDate', 'placedTime', 'shippedTime', 'deliveredTime', 'shippingCharges'), array('name'));
        $conditions = array(array('clientCode', 'code'));
        $placeList = $this->input->get('placeList');
        $call = $this->input->get('call');
        $pincode = $this->input->get('pincode');
        $orderCode = $this->input->get('orderCode');
        $orderStatus = $this->input->get('orderStatus');
        $fromDate = $this->input->get('fromDate');
        $toDate = $this->input->get('toDate');
        $areaCode = $this->input->get('areaCode');
        $deliveryCode = $this->input->get('deliveryCode');
		$cityCode = $this->input->get('cityCode');
        $extraCondition = "";
        $whereConditionArray = array();
        $extraConditionColumnNames = array();
        $extraDateConditionColumnNames = array();
        $extraDateConditions = array();
        if ($orderCode != '' || $pincode != '' || $orderStatus != '' || $fromDate != '' || $areaCode != '' || $deliveryCode != '' || $toDate != '')
        {
            if ($placeList == 1)
            { 
				////////////////for placed List//////////////////////
                if ($orderCode == '' && $pincode == '' && $orderStatus == '' && $fromDate == '' && $areaCode == '' && $deliveryCode == '')
                {
                    $whereConditionArray = array('customaddressmaster.isService' => 1);
                    $extraCondition = " (ordermaster.orderStatus = 'PLC' OR ordermaster.orderStatus='SHP' OR ordermaster.orderStatus='RJT' OR ordermaster.orderStatus='DEL') AND ordermaster.addDate BETWEEN '" . $fromDate . " 01:00:01' AND '" . $toDate . " 12:59:59' AND (ordermaster.isDelete=0 OR ordermaster.isDelete IS NULL)";
                }
                if ($fromDate != '')
                {
                    $fromDate = DateTime::createFromFormat('d/m/Y', $fromDate)->format('Y-m-d');
                    $toDate = DateTime::createFromFormat('d/m/Y', $toDate)->format('Y-m-d');
                    switch ($orderStatus)
                    {
                        case "PLC":
                            $extraCondition = 'placedTime BETWEEN "' . $fromDate . ' 01:00:01" AND "' . $toDate . ' 12:59:59"';
                        break;
                        case "SHP":
                            $extraCondition = 'shippedTime BETWEEN "' . $fromDate . ' 01:00:01" AND "' . $toDate . ' 12:59:59"';
                        break;
                        case "RJT":
                            $extraCondition = 'rejectedTime BETWEEN "' . $fromDate . ' 01:00:01" AND "' . $toDate . ' 12:59:59"';
                        break;
                        case "DEL":
                            $extraCondition = 'deliveredTime BETWEEN "' . $fromDate . ' 01:00:01" AND "' . $toDate . ' 12:59:59"';
                        break;
                    }
                }
                if ($areaCode != '')
                {
                    $whereConditionArray = array('ordermaster' . '.areaCode' => $areaCode, 'customaddressmaster.isService' => 1);
                    $extraCondition = " (ordermaster.orderStatus = 'PLC' OR ordermaster.orderStatus='SHP' OR ordermaster.orderStatus='RJT' OR ordermaster.orderStatus='DEL') AND (ordermaster.isDelete=0 OR ordermaster.isDelete IS NULL)";
                }
                if ($deliveryCode != '')
                {
                    $whereConditionArray = array('ordermaster' . '.editID' => $deliveryCode, 'customaddressmaster.isService' => 1);
                }
                if ($orderCode != '')
                {
                    $whereConditionArray = array('ordermaster' . '.code' => $orderCode);
                }
                if ($orderStatus != '')
                {
                    $whereConditionArray = array('ordermaster' . '.orderStatus' => $orderStatus);
                }
                if ($pincode != '')
                {
                    $whereConditionArray = array('clientprofile' . '.pincode' => $pincode);
                }
                //2 value condition
                if ($orderStatus != '' && $deliveryCode != '')
                {
                    $whereConditionArray = array('ordermaster' . '.orderStatus' => $orderStatus, 'ordermaster' . '.editID' => $deliveryCode, 'customaddressmaster.isService' => 1);
                }
                if ($orderStatus != '' && $pincode != '')
                {
                    //echo 'fine';
                    $whereConditionArray = array('ordermaster' . '.orderStatus' => $orderStatus, 'clientprofile' . '.pincode' => $pincode, 'customaddressmaster.isService' => 1);
                }
                if ($orderStatus != '' && $areaCode != '')
                {
                    $whereConditionArray = array('ordermaster' . '.areaCode' => $areaCode, 'ordermaster' . '.orderStatus' => $orderStatus, 'customaddressmaster.isService' => 1);
                }
                if ($areaCode != '' && $deliveryCode != '')
                {
                    $whereConditionArray = array('ordermaster' . '.areaCode' => $areaCode, 'ordermaster' . '.editID' => $deliveryCode, 'customaddressmaster.isService' => 1);
                }
                if ($orderStatus != '' && $orderCode != '')
                {
                    $whereConditionArray = array('ordermaster' . '.orderStatus' => $orderStatus, 'ordermaster' . '.code' => $orderCode, 'customaddressmaster.isService' => 1);
                }
                //three value condition
                if ($orderStatus != '' && $areaCode != '' && $deliveryCode != '')
                {
                    $whereConditionArray = array('ordermaster' . '.areaCode' => $areaCode, 'ordermaster' . '.orderStatus' => $orderStatus, 'ordermaster' . '.editID' => $deliveryCode, 'customaddressmaster.isService' => 1);
                }
                if ($orderCode != '' && $orderStatus != '' && $deliveryCode != '')
                {
                    $whereConditionArray = array('ordermaster' . '.code' => $orderCode, 'ordermaster' . '.orderStatus' => $orderStatus, 'ordermaster' . '.editID' => $deliveryCode, 'customaddressmaster.isService' => 1);
                }
                if ($pincode != '' && $orderStatus == '' && $orderCode == '')
                {
                    $whereConditionArray = array('clientprofile' . '.pincode' => $pincode, 'ordermaster' . '.code' => $orderCode, 'customaddressmaster.isService' => 1);
                    $extraCondition = " (ordermaster.orderStatus = 'PLC' OR ordermaster.orderStatus='SHP' OR ordermaster.orderStatus='RJT' OR  ordermaster.orderStatus='DEL') AND (ordermaster.isDelete=0 OR ordermaster.isDelete IS NULL)";
                }
                if ($orderStatus != '' && $orderCode == '' && $pincode == '')
                {
                    $whereConditionArray = array('ordermaster' . '.orderStatus' => $orderStatus, 'ordermaster' . '.code' => $orderCode, 'customaddressmaster.isService' => 1);
                }
                if ($orderCode != '' && $orderStatus == '' && $pincode == '')
                {
                    $whereConditionArray = array('ordermaster' . '.code' => $orderCode, 'customaddressmaster.isService' => 1);
                    $extraCondition = " (ordermaster.orderStatus = 'PLC' OR ordermaster.orderStatus='SHP' OR ordermaster.orderStatus='RJT' OR ordermaster.orderStatus='DEL') AND (ordermaster.isDelete=0 OR ordermaster.isDelete IS NULL)";
                }
                if ($orderStatus != '' && $pincode != '' && $deliveryCode != '')
                {
                    //echo 'fine';
                    $whereConditionArray = array('ordermaster' . '.orderStatus' => $orderStatus, 'clientprofile' . '.pincode' => $pincode, 'ordermaster' . '.editID' => $deliveryCode, 'customaddressmaster.isService' => 1);
                }
                if ($orderCode != '' && $pincode != '' && $deliveryCode != '')
                {
                    $whereConditionArray = array('ordermaster' . '.code' => $orderCode, 'clientprofile' . '.pincode' => $pincode, 'ordermaster' . '.editID' => $deliveryCode, 'customaddressmaster.isService' => 1);
                    $extraCondition = " (ordermaster.orderStatus = 'PLC' OR ordermaster.orderStatus='SHP' OR ordermaster.orderStatus='RJT' OR ordermaster.orderStatus='DEL') AND (ordermaster.isDelete=0 OR ordermaster.isDelete IS NULL)";
                }
                if ($orderCode != '' && $orderStatus != '' && $pincode != '' && $deliveryCode != '' && $areaCode != '')
                {
                    $whereConditionArray = array('ordermaster' . '.code' => $orderCode, 'ordermaster' . '.orderStatus' => $orderStatus, 'clientprofile' . '.pincode' => $pincode, 'ordermaster' . '.editID' => $deliveryCode, 'ordermaster' . '.areaCode' => $areaCode, 'customaddressmaster.isService' => 1);
                }
            }
            else if ($placeList == 0)
            {
                if ($orderCode != '' && $orderStatus != '' && $pincode != '' && $areaCode != '')
                {
                    $whereConditionArray = array('ordermaster' . '.code' => $orderCode, 'ordermaster' . '.orderStatus' => $orderStatus, 'clientprofile' . '.pincode' => $pincode, 'ordermaster.areaCode' => $areaCode, 'customaddressmaster.isService' => 1);
                }
                if ($orderCode != '' && $orderStatus != '' && $areaCode != '')
                {
                    $whereConditionArray = array('ordermaster' . '.code' => $orderCode, 'ordermaster' . '.orderStatus' => $orderStatus, 'ordermaster.areaCode' => $areaCode, 'customaddressmaster.isService' => 1);
                }
                if ($orderCode != '' && $orderStatus != '')
                {
                    $whereConditionArray = array('ordermaster' . '.code' => $orderCode, 'ordermaster' . '.orderStatus' => $orderStatus, 'customaddressmaster.isService' => 1);
                }
                if ($areaCode != '' && $orderStatus != '')
                {
                    $whereConditionArray = array('ordermaster.areaCode' => $areaCode, 'ordermaster' . '.orderStatus' => $orderStatus, 'customaddressmaster.isService' => 1);
                }
                if ($orderCode != '' && $areaCode != '')
                {
                    $whereConditionArray = array('ordermaster' . '.code' => $orderCode, 'ordermaster.areaCode' => $areaCode, 'customaddressmaster.isService' => 1);
                    $extraCondition = "(ordermaster.orderStatus = 'CAN' OR ordermaster.orderStatus='PND') AND (ordermaster.isDelete=0 OR ordermaster.isDelete IS NULL)";
                }
                if ($orderCode != '' && $pincode != '')
                {
                    $whereConditionArray = array('ordermaster' . '.code' => $orderCode, 'clientprofile' . '.pincode' => $pincode, 'customaddressmaster.isService' => 1);
                    $extraCondition = "(ordermaster.orderStatus = 'CAN' OR ordermaster.orderStatus='PND') AND (ordermaster.isDelete=0 OR ordermaster.isDelete IS NULL)";
                }
                if ($orderStatus != '' && $pincode != '')
                {
                    //echo 'fine';
                    $whereConditionArray = array('ordermaster' . '.orderStatus' => $orderStatus, 'clientprofile' . '.pincode' => $pincode, 'customaddressmaster.isService' => 1);
                }
                if ($orderStatus != '' && $pincode != '' && $areaCode != '')
                {
                    //echo 'fine';
                    $whereConditionArray = array('ordermaster' . '.orderStatus' => $orderStatus, 'clientprofile' . '.pincode' => $pincode, 'ordermaster.areaCode' => $areaCode, 'customaddressmaster.isService' => 1);
                }
                if ($areaCode != '')
                {
                    $whereConditionArray = array('ordermaster' . '.areaCode' => $areaCode, 'customaddressmaster.isService' => 1);
                    $extraCondition = "(ordermaster.orderStatus = 'CAN' OR ordermaster.orderStatus='PND') AND (ordermaster.isDelete=0 OR ordermaster.isDelete IS NULL)";
                }
                if ($orderCode != '' && $orderStatus == '' && $pincode == '')
                {
                    $whereConditionArray = array('ordermaster' . '.code' => $orderCode, 'customaddressmaster.isService' => 1);
                    $extraCondition = "(ordermaster.orderStatus = 'CAN' OR ordermaster.orderStatus='PND') AND (ordermaster.isDelete=0 OR ordermaster.isDelete IS NULL)";
                }
                if ($orderStatus != '' && $orderCode == '' && $pincode == '')
                {
                    $whereConditionArray = array('ordermaster' . '.orderStatus' => $orderStatus, 'customaddressmaster.isService' => 1);
                }
                if ($pincode != '' && $orderStatus == '' && $orderCode == '')
                {
                    $whereConditionArray = array('clientprofile' . '.pincode' => $pincode, 'customaddressmaster.isService' => 1);
                    $extraCondition = "(ordermaster.orderStatus = 'CAN' OR ordermaster.orderStatus='PND') AND (ordermaster.isDelete=0 OR ordermaster.isDelete IS NULL)";
                }
                if ($fromDate != '' && $toDate!="")
                {
                    $fromDate = DateTime::createFromFormat('d/m/Y', $fromDate)->format('Y-m-d');
                    $toDate = DateTime::createFromFormat('d/m/Y', $toDate)->format('Y-m-d');
                    switch ($orderStatus)
                    {
                        case "CAN":
                            $extraCondition = 'cancelledTime BETWEEN "' . $fromDate . ' 00:00:01" AND "' . $toDate . ' 12:59:59"';
                        break;
                        case "PND":
                            $extraCondition = 'ordermaster.addDate BETWEEN "' . $fromDate . ' 00:00:01" AND "' . $toDate . ' 12:59:59"';
                        break;
                    }
                }
                if ($orderCode == '' && $pincode == '' && $orderStatus == '' && $fromDate != '')
                {
                    $whereConditionArray = array('customaddressmaster.isService' => 1);
                    $extraCondition = "(ordermaster.orderStatus = 'CAN' OR ordermaster.orderStatus='PND') AND ordermaster.addDate BETWEEN '" . $fromDate . " 01:00:01' AND '" . $toDate . " 12:59:59' AND (ordermaster.isDelete=0 OR ordermaster.isDelete IS NULL)";
                }
            }
        }
        else
        {
            if ($orderStatus == '' && $placeList == '1')
            {
                $orderStatus = 'PLC';
                $whereConditionArray = array('ordermaster' . '.orderStatus' => $orderStatus, 'customaddressmaster.isService' => 1);
            }
          
			  if ($orderStatus !== '' && $placeList == 0)
            {
                $orderStatus = "CAN";
                $whereConditionArray = array('ordermaster.orderStatus' => $orderStatus, 'customaddressmaster.isService' => 1);
            }
        }
		if(trim($cityCode)!="") $whereConditionArray['ordermaster.cityCode'] = $cityCode;
        $tableName = 'ordermaster';
		$Condition=array('ordermaster.isActive'=>1,"ordermaster.cityCode"=>$cityCode,"ordermaster.addDate"=>$fromDate,"ordermaster.addDate"=>$toDate,"ordermaster.areaCode"=>$areaCode,"ordermaster.code"=>$orderCode);
        $orderColumnsArray = array('ordermaster.*,ordermaster.code as orderCode,ordermaster.addDate as orderaddDate,ordermaster.editID as orderEditID,clientmaster.*,clientprofile.pincode,customaddressmaster.*,citymaster.cityName'); //ordermaster.code as orderCode,clientprofile.pincode',
        $orderBy = array('ordermaster' . '.id' => 'DESC');
        $joinType = array('citymaster'=>'left','clientmaster' => 'inner', 'clientprofile' => 'inner', 'customaddressmaster' => "left"); //'clientmaster' =>'inner','clientprofile' =>'inner'
        $join = array('citymaster' => 'citymaster.code=ordermaster.cityCode','customaddressmaster' => 'customaddressmaster.code = ordermaster.areaCode', 'clientmaster' => 'clientmaster' . '.code=' . 'ordermaster' . '.clientCode', 'clientprofile' => 'clientprofile' . '.clientCode=' . 'ordermaster' . '.clientCode'); 
		//'clientmaster' => 'clientmaster' . '.code=' . 'ordermaster' . '.clientCode','clientprofile' => 'clientprofile' . '.clientCode=' . 'ordermaster' . '.clientCode','customaddressmaster'=>'customaddressmaster.code = ordermaster.areaCode'
        $like = array();
        $limit = $this->input->GET("length");
        $offset = $this->input->GET("start");
        $groupByColumn = array();
        $Records = $this->GlobalModel->selectQuery($orderColumnsArray, $tableName, $whereConditionArray, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
        if ($Records)
        {
            $srno = $offset + 1;
            $data = array();
            $id = 1;
            $radio = '';
            foreach ($Records->result() as $row)
            {
				$fromCity = '<br><span class="badge badge-primary"> Order From <b>'.$row->cityName.'</b></span>';
                if ($srno == 1)
                {
                    $id = $srno;
                }
                $dlbName = '';
                $dlbCode = $row->orderEditID;
                if ($dlbCode != '')
                {
                    $tableNameDLB = "usermaster";
                    $orderColumnsDLB = array("usermaster.*,employeemaster.firstName,employeemaster.lastName");
                    $conditionDLB = array('usermaster.code' => $dlbCode);
                    $orderByDLB = array('usermaster' . '.id' => 'DESC');
                    $joinTypeDLB = array('employeemaster' => 'inner');
                    $joinDLB = array('employeemaster' => 'employeemaster.code=usermaster.empCode');
                    $RecordsDLB = $this->GlobalModel->selectQuery($orderColumnsDLB, $tableNameDLB, $conditionDLB, $orderByDLB, $joinDLB, $joinTypeDLB);
                    $dlbName = $RecordsDLB->result() [0]->firstName . ' ' . $RecordsDLB->result() [0]->lastName;
                }
                else
                {
                    $dlbName = '';
                }
                $chkSHP = '';
                $chkDEL = '';
                $chkRJT = '';
                $orderDate = '';
                $orderStatus = $row->orderStatus;
                $odStatus = $row->orderStatus;
                switch ($orderStatus)
                {
                    case "PND":
                        $orderStatus = "Pending";
                        $orderDate = date('d-m-Y h:i:s', strtotime($row->orderaddDate));
                    break;
                    case "PLC":
                        $orderStatus = "Placed";
                        $orderDate = date('d-m-Y h:i:s', strtotime($row->placedTime));
                    break;
                    case "SHP":
                        $orderStatus = "Shipped";
                        $chkSHP = 'checked';
                        $orderDate = date('d-m-Y h:i:s', strtotime($row->shippedTime));
                    break;
                    case "DEL":
                        $orderStatus = "Delivered";
                        $chkDEL = 'checked';
                        $orderDate = date('d-m-Y h:i:s', strtotime($row->deliveredTime));
                    break;
                    case "CAN":
                        $orderStatus = "Cancelled By User";
                        $orderDate = date('d-m-Y h:i:s', strtotime($row->cancelledTime));
                    break;
                    case "RJT":
                        $orderStatus = "Reject";
                        $chkRJT = 'checked';
                        $orderDate = date('d-m-Y h:i:s', strtotime($row->editDate));
                    break;
                }
                $paymentStatus = $row->paymentStatus;
                switch ($paymentStatus)
                {
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
                if ($row->shippingCharges == "")
                {
                    $shippingCharges = "0";
                }
                else
                {
                    $shippingCharges = $row->shippingCharges;
                }
                $productcount = 0;
                $countdata = $this->db->query("select count(*) as cnt from orderlineentries where orderCode='" . $row->orderCode . "'");
                if ($countdata)
                {
                    foreach ($countdata->result() as $r)
                    {
                        $productcount = $r->cnt;
                    }
                }
				$itemcount = '<br><h4 class="">No. of Items - <span  class="badge badge-danger"><b>' . $productcount . '</b></span></h4>';	
                if ($odStatus == 'PND' || $odStatus == 'CAN')
                {
                    $actionHtml = '  <a class="dropdown-item  blue" href="' . base_url() . 'index.php/Order/view/' . $row->orderCode . '"><i class="ti-eye"></i> Open</a>';
                    $data[] = array($srno, $row->orderCode . $itemcount, $row->name.$fromCity, $row->place, $row->address, $row->phone, $orderStatus . $radio, $row->totalPrice, $orderDate, $actionHtml);
                }
                else
                {
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
                    $data[] = array($srno, $row->orderCode . $itemcount, $row->name .$fromCity, $row->place, $row->address, $row->phone, $orderStatus . $radio, $row->totalPrice, $dlbName, $orderDate, $actionHtml);
                }
                $srno++;
                $id++;
            }
            $dSize = sizeOf($data);
            $dataCount = sizeof($this->GlobalModel->selectQuery($orderColumnsArray, $tableName, $whereConditionArray, $orderBy, $join, $joinType, $like, "", "", $groupByColumn, $extraCondition)->result_array());
            $output = array("draw" => intval($_GET["draw"]), "recordsTotal" => $dataCount, "recordsFiltered" => $dSize, "data" => $data);
            echo json_encode($output);
        }
        else
        {
            $dataCount = 0;
            $output = array("draw" => intval($_GET["draw"]), "recordsTotal" => $dataCount, "recordsFiltered" => $dataCount, "data" => "");
            echo json_encode($output);
        }
    }
	
	/*
	public function getOrderDetails()
    {
		$addID = $this->session->userdata['logged_in'.$this->session_key]['code'];
		
        $orderCode = $this->input->get('orderCode');
        $noPic = $this->input->get('noPic');
        $tableName = 'vendororderlineentries';
        $orderColumns = array("vendororderlineentries.*,vendoritemmaster.itemName,vendoritemmaster.salePrice,vendoritemmaster.itemPhoto");
        $condition = array('vendororderlineentries.orderCode' => $orderCode);
        $orderBy = array('vendororderlineentries.id' => 'desc');
        $joinType = array('vendoritemmaster' => 'inner');
        $join = array('vendoritemmaster' => 'vendoritemmaster.code=vendororderlineentries.vendorItemCode');
        $groupByColumn = array();
        $limit = $this->input->GET("length");
        $offset = $this->input->GET("start");
        $srno = $offset + 1;
        $extraCondition = "vendororderlineentries.isActive=1";
        $like = array();
        $data = array();
        $Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
        if ($Records)
        {
            foreach ($Records->result() as $row)
            {
				$start = '<div class="d-flex align-items-center">';
                $end = ' <h5 class="m-b-0 font-16 font-medium">' . $row->itemName . '</h5></div></div>';
				$itemPhotoCheck = $row->itemPhoto;
                if ($itemPhotoCheck != "")
                {
					$itemPhoto = base_url().'uploads/'.$addID.'/vendoritem/'.$itemPhotoCheck;
					$photo = '<div class="m-r-10"><img src="' . $itemPhoto . '?' . time() . '" alt="user" class="circle" width="45"></div><div class="">';
                    $itemName = $start . $photo . $end;
                    $data[] = array($srno, $row->vendorItemCode, $itemName, $row->salePrice, $row->quantity, $row->priceWithQuantity);
                }
                else
                {
					$itemName = ' <h5 class="m-b-0 font-16 font-medium">' . $row->itemName . '</h5></div></div>';
                    $data[] = array($srno, $row->vendorItemCode, $itemName, $row->salePrice, $row->quantity, $row->priceWithQuantity);
                }
                $srno++;
            }
        }
        // $dataCount = sizeOf($this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, '', '', $groupByColumn, $extraCondition)->result());
        $dataCount = 0;
        $dataCount1 = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, '', '', $groupByColumn, $extraCondition);
		if($dataCount1){
			$dataCount = sizeOf($dataCount1->result());
		}
        $output = array("draw" => intval($_GET["draw"]), "recordsTotal" => $dataCount, "recordsFiltered" => $dataCount, "data" => $data);
        echo json_encode($output);
    }
	*/
	public function view()
	{
		$orderCode = $this->uri->segment('3');
		
		
		
	}
	
	
	/*public function view()
	{
		$code=$this->uri->segment(3); 
		$orderColumns = "vendorordermaster.*,clientmaster.mobile,clientmaster.name";
		$condition = array("vendorordermaster.code"=>$code,"vendorordermaster.clientCode"=>$clientcode,"vendorordermaster.grandTotal"=>$grandTotal,"vendorordermaster.address"=>$address,"vendorordermaster.subTotal"=>$subTotal,"vendorordermaster.shippingCharges"=>$shippingCharges,"vendorordermaster.paymentMode"=>$paymentMode);
		$join = array("clientmaster"=>"vendorordermaster.clientCode=clientmaster.code");
		$joinType = array("clientmaster"=>"inner");
        $data['query'] = $this->GlobalModel->selectQuery($orderColumns,'vendorordermaster',$condition,array(),$join,$joinType);
		// $entitycategoryCode = $data['query']->result_array()[0]['entitycategoryCode'];
		// $data['entitycategory'] = $this->GlobalModel->selectDataById($entitycategoryCode,'entitycategory');
		// $data['cuisineslines']=$this->GlobalModel->selectQuery('vendorcuisinelineentries.cuisineCode,cuisinemaster.cuisineName',"vendorcuisinelineentries",array("vendorcuisinelineentries.vendorCode"=>$code),array(),array("cuisinemaster"=>"cuisinemaster.code=vendorcuisinelineentries.cuisineCode"),array("cuisinemaster"=>'inner'));
		$this->load->view('dashboard/header');
    	$this->load->view('dashboard/foodOrderList/view',$data);
    	$this->load->view('dashboard/footer');
	}*/
	public function test()
	{
		$res = $this->db->query("select * from vendoritemmaster");
		if($res->num_rows()>0)
		{
			echo "<pre>";
			print_r($res->result());
			echo "</pre>";
		}
		
	}
}