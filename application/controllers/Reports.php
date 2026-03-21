 <?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Reports extends CI_Controller {
	var $session_key;
	public function __construct()
    {
        parent::__construct();
        $this->load->model('GlobalModel');
		$this->load->model('GlobalModel1');
		$this->session_key = $this->session->userdata('key'.SESS_KEY);
		if (!isset($this->session->userdata['logged_in' . $this->session_key]['code'])) {
			redirect('Admin/login', 'refresh');
		}
    }
	
	public function orderListRecords(){
		$data['productmaster']=$this->GlobalModel->selectData('productmaster');
		$data['city'] = $this->GlobalModel->selectActiveData('citymaster');
		$data['error'] = $this->session->flashdata('response');
		$this->load->view('dashboard/header');
		$this->load->view('dashboard/reports/orderReport', $data);
		$this->load->view('dashboard/footer');
	}
	
	public function penaltyReport(){
		$data['error'] = $this->session->flashdata('response');
		$data['dbList'] = $this->GlobalModel->selectQuery("usermaster.code,usermaster.name","usermaster",array("usermaster.role"=>'DLB',"usermaster.isActive"=>1));
		$this->load->view('dashboard/header');
		$this->load->view('dashboard/reports/deliveryBoyPenulty', $data);
		$this->load->view('dashboard/footer');
	}
	
	public function vendorpenaltyReport(){
		$data['error'] = $this->session->flashdata('response');
		$data['vendorList'] = $this->GlobalModel->selectQuery("vendor.code,vendor.entityName","vendor",array("vendor.isActive"=>1));
		$this->load->view('dashboard/header');
		$this->load->view('dashboard/reports/vendorPenulty', $data);
		$this->load->view('dashboard/footer');
	}
	
	public function gertOrders(){
		$status = $this->input->GET('orderType');
		$dateStart = $this->input->GET('dateStart');
		$dateEnd = $this->input->GET('dateEnd');
		$productCode =$this->input->GET('productCode');
		$cityCode = $this->input->GET('cityCode');
		$addressCode =  $this->input->GET('addressCode');
		$export = $this->input->GET('export');
		$address = "";		
		if(isset($addressCode)){			
			foreach($addressCode as $ad)
			{
				$address != "" && $address .= ",";
				$address .= "'".$ad ."'";
			}
		}		
		$tableName = 'orderlineentries';
		$orderColumns = array("ordermaster.addDate,orderlineentries.productCode,orderlineentries.productUom, ifNull(sum(orderlineentries.quantity),0) as qty, ifNull(sum(orderlineentries.totalPrice),0) as price,productmaster.productName,productmaster.minimumSellingQuantity");
		$search = $this->input->GET("search")['value'];
		$condition = array('ordermaster.orderStatus'=>$status,'orderlineentries.productCode'=>$productCode,'ordermaster.cityCode'=>$cityCode);
		$orderBy = array();
		$joinType = array('ordermaster'=>'inner','productmaster'=>'inner');
		$join = array('ordermaster'=>'ordermaster.code=orderlineentries.orderCode','productmaster'=>'productmaster.code=orderlineentries.productCode');
		$groupByColumn = array('orderlineentries.productCode');
		$limit = $this->input->GET("length");
		$offset = $this->input->GET("start");
		$srno = $offset + 1;
		$like = array();
		if($dateStart!=""){
			$dateStart=date('Y-m-d',strtotime(str_replace('/','-',$dateStart)));
			$dateEnd=date('Y-m-d',strtotime(str_replace('/','-',$dateEnd)));
			$extraCondition ="ordermaster.addDate BETWEEN '".$dateStart."' AND '".$dateEnd." 23:59:59.999' and ordermaster.orderStatus not in ('DEL','RJT','CAN')";
		} else {
			$extraCondition ="ordermaster.addDate BETWEEN '". date('Y-m-d')."' AND '".date('Y-m-d')." 23:59:59.999' and ordermaster.orderStatus not in ('DEL','RJT','CAN')";
		}
		if(!empty($address)){
			$extraCondition .= " and  ordermaster.areaCode IN (".$address.")";
		}
		$like = array();
		$data=array();
		$totalamt=0;
		$Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
		$querya = $this->db->last_query();
		if($Records) {
			foreach($Records->result() as $row){
			    $qty  = $row->qty;
			    if($row->productUom=='GM'){
			        $qty  = ($qty * $row->minimumSellingQuantity)/1000;
			        $qty = $qty.' KG';
			    } else {
			        $qty = $qty * $row->minimumSellingQuantity;
			        $qty = $qty . ' '.$row->productUom;
			    }
				if($export==0){
					$data[] = array(
						$srno,
						$row->productCode,
						$row->productName,
						$row->minimumSellingQuantity.' / '.$row->productUom,
						$row->qty,
						$qty,
						$row->price,
						date('d/m/Y h:i:s',strtotime($row->addDate))
					);
				} else {
					$data[] = array(
						$srno,
						$row->productCode,
						$row->productName,
						$row->minimumSellingQuantity.' / '.$row->productUom,
						$row->qty,
						$qty,
						$row->price,
						date('d/m/Y h:i:s',strtotime($row->addDate))
					);
				}
				$srno =$srno+1;
				
			}
			$dataAmount = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, '', '', $groupByColumn, $extraCondition)->result();
			if(sizeOf($dataAmount)>0){
				foreach($dataAmount as $r){
					$totalamt += $r->price; 
				}
			}
			$dataCount = sizeof($this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, '', '', $groupByColumn, $extraCondition)->result());
			$output = array(
				"draw" => intval($_GET["draw"]),
				"recordsTotal" => $dataCount,
				"recordsFiltered" => $dataCount,
				"data" => $data,
				"address"=>$address,
				'totalamt'=>$totalamt,
				//"query"=>$querya
			);
			echo json_encode($output);
		} else {
			$dataCount = sizeof($data);
			$output = array(
				"draw" => intval($_GET["draw"]),
				"recordsTotal" => $dataCount,
				"recordsFiltered" => $dataCount,
				"data" => $data,
				"address"=>$address,
				'totalamt'=>$totalamt,
				//"query"=>$querya
			);
			echo json_encode($output);
		}
	}
	
	public function getDeliveryBoypenaltyList(){
		$dbCode = $this->input->GET('dbCode');
		$orderType = $this->input->GET('orderType');
		$export = $this->input->GET('export');
		$tableName = 'deliveryboyearncommission';
		$orderColumns = array("deliveryboyearncommission.*");
		$search = $this->input->GET("search")['value'];
		$condition = array('deliveryboyearncommission.commissionType'=>'penalty','deliveryboyearncommission.orderType'=>$orderType,'deliveryboyearncommission.deliveryBoyCode'=>$dbCode);
		$orderBy = array();
		$joinType = array();
		$join = array();
		$groupByColumn = array('');
		$limit = $this->input->GET("length");
		$offset = $this->input->GET("start");
		$srno = $offset + 1;
		$like = array();
		$extraCondition =" date(deliveryboyearncommission.addDate)='".date('Y-m-d')."'";
		$like = array();
		$data=array();
		$totalamt=0;
		$Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
		$querya = $this->db->last_query();
		if($Records) {
			foreach($Records->result() as $row){
				
					$data[] = array(
						$srno,
						date('d/m/Y h:i A',strtotime($row->addDate)),
						$row->orderCode,
						$row->orderAmount,
						$row->commissionAmount,
						ucfirst($row->orderType),
					);
				$srno =$srno+1;
				
			}
			$dataAmount = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, '', '', $groupByColumn, $extraCondition)->result();
			if(sizeOf($dataAmount)>0){
				foreach($dataAmount as $r){
					$totalamt += $r->commissionAmount; 
				}
			}
			$dataCount = sizeof($this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, '', '', $groupByColumn, $extraCondition)->result());
			$output = array(
				"draw" => intval($_GET["draw"]),
				"recordsTotal" => $dataCount,
				"recordsFiltered" => $dataCount,
				"data" => $data,
				'totalamt'=>$totalamt,
				"query"=>$querya
			);
			echo json_encode($output);
		} else {
			$dataCount = sizeof($data);
			$output = array(
				"draw" => intval($_GET["draw"]),
				"recordsTotal" => $dataCount,
				"recordsFiltered" => $dataCount,
				"data" => $data,
				'totalamt'=>$totalamt,
				"query"=>$querya
			);
			echo json_encode($output);
		}
	}
	
	public function getVendorpenaltyList(){
		$vendorCode = $this->input->GET('vendorCode');
		$fromDate = $this->input->GET('fromDate');
		$toDate = $this->input->GET('toDate');
		$export = $this->input->GET('export');
		
		$search = $limit = $offset = '';
		$srno = 1;
		$draw = 0;
		$total = 0;
		if ($export == 0) {
			$search = $this->input->GET("search")['value'];
			$limit = $this->input->GET("length");
			$offset = $this->input->GET("start");
			$srno = $offset + 1;
			$draw = $_GET["draw"];
		}
		
		$tableName = 'vendorordercommission';
		$orderColumns = array("vendorordercommission.*,vendor.entityName as vendorName");
		
		$condition = array('vendorordercommission.commissionType'=>'penalty','vendorordercommission.deliveryBoyCode'=>$vendorCode);
		$orderBy = array('vendorordercommission.id'=>'DESC');
		$joinType = array('vendor'=>'inner');
		$join = array('vendor'=>'vendor.code=vendorordercommission.deliveryBoyCode');
		$groupByColumn = array('');
		
		$like = array();
		if($fromDate!=""){
			$dateStart=date('Y-m-d',strtotime(str_replace('/','-',$fromDate)));
			$dateEnd=date('Y-m-d',strtotime(str_replace('/','-',$toDate)));
			$extraCondition ="vendorordercommission.addDate BETWEEN '".$dateStart."' AND '".$dateEnd." 23:59:59.999'";
		}
		$like = array();
		$data=array();
		$totalamt=0;
		$Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
		$querya = $this->db->last_query();
		if($Records) {
			foreach($Records->result() as $row){
				$data[] = array(
					$srno,
					$row->vendorName,
					date('d/m/Y h:i A',strtotime($row->addDate)),
					$row->orderCode,
					$row->grandTotal,
					$row->comissionAmount,
				);
				$srno =$srno+1;
				
			}
			$dataAmount = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, '', '', $groupByColumn, $extraCondition)->result();
			if(sizeOf($dataAmount)>0){
				foreach($dataAmount as $r){
					$totalamt += $r->comissionAmount; 
				}
			}
			$dataCount = sizeof($this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, '', '', $groupByColumn, $extraCondition)->result());
			$output = array(
				"draw" => intval($draw),
				"recordsTotal" => $dataCount,
				"recordsFiltered" => $dataCount,
				"data" => $data,
				'totalamt'=>$totalamt,
				//"query"=>$querya
			);
			echo json_encode($output);
		} else {
			$dataCount = sizeof($data);
			$output = array(
				"draw" => intval($draw),
				"recordsTotal" => $dataCount,
				"recordsFiltered" => $dataCount,
				"data" => $data,
				'totalamt'=>$totalamt,
				//"query"=>$querya
			);
			echo json_encode($output);
		}
	}

}?>