<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller 
{
	var $session_key;
	public function __construct()
    {
        parent::__construct();
        $this->session_key = $this->session->userdata('partner_key'.SESS_KEY_PARTNER);
        $this->load->model('GlobalModel');
		if (!isset($this->session->userdata['part_logged_in' .  $this->session_key]['code'])) {
			redirect('Login', 'refresh');
		}
    }

    public function index()
	{
	
		$code = ($this->session->userdata['part_logged_in'.$this->session_key]['code']);
		$username = ($this->session->userdata['part_logged_in'.$this->session_key]['username']);
		$data['entityName'] = $username ;
		
		$data['totalOrders']=0;
		$extraCondition1 = "vendorordermaster.orderStatus IN ('RFP','PUP','PRE','RCH','DEL','RJT') ";
		$dataTotalOrders = $this->GlobalModel->selectQuery('COUNT(id) as totalOrders','vendorordermaster',array('vendorordermaster.vendorCode'=>$code,'vendorordermaster.isActive'=>1),array(), array(), array(),array(),'','',array(),$extraCondition1);
		if($dataTotalOrders){
			$data['totalOrders'] = $dataTotalOrders->result_array()[0]['totalOrders'];
		}
		
		$data['pendingRes'] = 0 ;
		//pending and cancelled order count
		$dataPendingRes = $this->GlobalModel->selectQuery('COUNT(id) as countPending','vendorordermaster',array('vendorordermaster.vendorCode'=>$code,'vendorordermaster.orderStatus'=>'PND','vendorordermaster.isActive'=>1));
		if($dataPendingRes){
			$data['pendingRes'] = $dataPendingRes->result_array()[0]['countPending'];
		}
		
		$data['cancelledRes'] = 0;
		$dataCancelledRes = $this->GlobalModel->selectQuery('COUNT(id) as countCancle','vendorordermaster',array('vendorordermaster.vendorCode'=>$code,'vendorordermaster.orderStatus'=>'CAN'));
		if($dataCancelledRes){
			$data['cancelledRes'] = $dataCancelledRes->result_array()[0]['countCancle'];
		}
		
		$data['placeOrder'] = 0;
		$extraCondition = "vendorordermaster.orderStatus IN ('RFP','PUP','PRE','RCH') ";
		$dataPlaceOrder = $this->GlobalModel->selectQuery('COUNT(id) as countPlaced','vendorordermaster',array('vendorordermaster.vendorCode'=>$code),array(), array(), array(),array(),'','',array(),$extraCondition);
		if($dataPlaceOrder){
			$data['placeOrder'] = $dataPlaceOrder->result_array()[0]['countPlaced'];
		}
		
		
		$data['deliverdOrder'] = 0;
		$dataDeliverdOrder = $this->GlobalModel->selectQuery('COUNT(id) as countDeliverd','vendorordermaster',array('vendorordermaster.vendorCode'=>$code,'vendorordermaster.orderStatus'=>'DEL'));
		if($dataDeliverdOrder){
			$data['deliverdOrder'] = $dataDeliverdOrder->result_array()[0]['countDeliverd'];
		}
		
		
		$data['rejectOrder'] = 0;
		$dataRejectedOrder = $this->GlobalModel->selectQuery('COUNT(id) as countRejected','vendorordermaster',array('vendorordermaster.vendorCode'=>$code,'vendorordermaster.orderStatus'=>'RJT'));
		if($dataRejectedOrder){
			$data['rejectOrder'] = $dataRejectedOrder->result_array()[0]['countRejected'];
		}
		
		$data['vendoroffer'] = 0;
		$dataVendorOffer = $this->GlobalModel->selectQuery('COUNT(id) as countOffer','vendoroffer',array('vendoroffer.vendorCode'=>$code));
		if($dataVendorOffer){
			$data['vendoroffer'] = $dataVendorOffer->result_array()[0]['countOffer'];
		}
		
		$data['vendorofferActive'] = 0;
		$vendorofferActive = $this->GlobalModel->selectQuery('COUNT(id) as activeOffer','vendoroffer',array('vendoroffer.vendorCode'=>$code,'vendoroffer.isActive'=>'1'));
		if($vendorofferActive){
			$data['vendorofferActive'] = $vendorofferActive->result_array()[0]['activeOffer'];
		}
		
		$data['vendorofferInactive'] = 0;
		$vendorofferInactive = $this->GlobalModel->selectQuery('COUNT(id) as inactiveOffer','vendoroffer',array('vendoroffer.vendorCode'=>$code,'vendoroffer.isActive'=>'0'));
		if($vendorofferInactive){
			$data['vendorofferInactive'] = $vendorofferInactive->result_array()[0]['inactiveOffer'];
		}
		
		$data['vendoritems'] = 0;
		$dataVendorItems = $this->GlobalModel->selectQuery('COUNT(id) as countItems','vendoritemmaster',array('vendoritemmaster.vendorCode'=>$code,'vendoritemmaster.isAdminApproved'=>1));
		if($dataVendorItems){
			$data['vendoritems'] = $dataVendorItems->result_array()[0]['countItems'];
		}
		
		$data['custChoiceCat'] = 0;
		$orderBy = array();
		$joinType = array('customizedcategory' => 'inner'); 
		$join = array('customizedcategory' => 'customizedcategory.vendorItemCode=vendoritemmaster.code');
		$datacustChoiceCat = $this->GlobalModel->selectQuery('COUNT(vendoritemmaster.id) as countCustChoiceCat','vendoritemmaster',array('vendoritemmaster.vendorCode'=>$code,'customizedcategory.categoryType'=>'choice'),$orderBy, $join, $joinType,array(),'','',array(),'');
		if($datacustChoiceCat){
			$data['custChoiceCat'] = $datacustChoiceCat->result_array()[0]['countCustChoiceCat'];
		}
		
		$data['custAddonCat'] = 0;
		$joinType = array('customizedcategory' => 'inner'); 
		$join = array('customizedcategory' => 'customizedcategory.vendorItemCode=vendoritemmaster.code');
		$datacustAddonCat = $this->GlobalModel->selectQuery('COUNT(vendoritemmaster.id) as countCustAddonCat','vendoritemmaster',array('vendoritemmaster.vendorCode'=>$code,'customizedcategory.categoryType'=>'addon'),$orderBy, $join, $joinType,array(),'','',array(),'');
		if($datacustAddonCat){
			$data['custAddonCat'] = $datacustAddonCat->result_array()[0]['countCustAddonCat'];
		}
		
		$data['orginalCommissionAmount']=0;
		$cond=array('vendorordercommission.commissionType'=>'regular','vendorordercommission.deliveryBoyCode'=>$code);
		$extraCon="vendorordercommission.commissionType='' OR vendorordercommission.commissionType IS NULL"; 
		$orginalComm = $this->GlobalModel->selectQuery('ifnull(SUM(vendorordercommission.grandTotal),0) as orginalComm','vendorordercommission',$cond,array(), array(), array(),array(),'','',array(),$extraCon);
        if($orginalComm){
			$data['orginalCommissionAmount'] = $orginalComm->result_array()[0]['orginalComm'];
		}
		
		$data['penaltyCommissionAmount']=0;
		$cond1=array('vendorordercommission.commissionType'=>'penalty','vendorordercommission.deliveryBoyCode'=>$code);
		$penaltyComm = $this->GlobalModel->selectQuery('ifnull(SUM(vendorordercommission.grandTotal),0) as penalty','vendorordercommission',$cond1,array(), array(), array(),array(),'','',array(),'');
        if($penaltyComm){
			$data['penaltyCommissionAmount'] = $penaltyComm->result_array()[0]['penalty'];
		}
	
		$data['totalEarning']=$data['orginalCommissionAmount']-$data['penaltyCommissionAmount'];
		//pending and cancelled order count
		// $data['pendingRes'] = $this->GlobalModel->getCountOfValueWithDate('ordermaster','orderStatus','PND');
		// $data['cancelledRes'] = $this->GlobalModel->getCountOfValueWithDate('ordermaster','orderStatus','CAN');
		
		//active area	
		$data['activePlace'] = $this->GlobalModel->getCountOfPerticularValue('customaddressmaster','isService','1');
		 
		//placed and delivered and rejected order count
		// $data['placeOrder'] = $this->GlobalModel->getCountOfAdmAction('ordermaster','orderStatus','PLC','placedTime');
		// $data['deliverdOrder'] = $this->GlobalModel->getCountOfAdmAction('ordermaster','orderStatus','DEL','deliveredTime');
		// $data['rejectOrder'] = $this->GlobalModel->getCountOfAdmAction('ordermaster','orderStatus','RJT','rejectedTime');
		
		//recent inward count
		$data['recentInward'] = $this->GlobalModel->getCountWthField('inwardentries','code');
		
		//reset password user count
		$data['resetPwd'] = $this->GlobalModel->getTableRecordCount('resetpassword');
		
		//Today's sales and purchase amount count 
		$data['saleAmt'] = $this->GlobalModel->getCountWithAmount('ordermaster','totalPrice','deliveredTime');
		$data['purchaseAmt'] = $this->GlobalModel->getCountWithAmount('inwardentries','total','inwardDate');
		
		//valueable coustmer count
		$data['customer'] = $this->GlobalModel->getCountOfPerticularValue('clientmaster','isDelete',0);
		$data['oreder'] = $this->GlobalModel->selectData('ordermaster');
		// print_r($data['oreder']->result());
		// exit();
		
		$orderColumns = array("count(resetpassword.id) pCount,usermaster.code,usermaster.role");
		$cond=array("resetpassword.isActive"=>1,"usermaster.role"=>"DLB");
		$orderBy = array('resetpassword' . ".id" => 'ASC');
		$join = array("usermaster"=>"usermaster.code = resetpassword.userCode");
		$joinType=array("usermaster"=>"inner");
		$like=array();
		$limit="";
		$offset="";
		$groupByColumn=array();
		$extraCondition="";
		
		$p_result = $this->GlobalModel->selectQuery($orderColumns,'resetpassword',$cond,$orderBy,$join,$joinType,$like,$limit,$offset,$groupByColumn, $extraCondition);
		if($p_result)
		{
			$data["dlbReset"]=$p_result->result_array()[0]["pCount"];
		}
		else
		{
			$data["dlbReset"]=0;
		}
		
		
		$orderColumns1 = array("count(resetpassword.id) puCount,clientmaster.code,clientmaster.isDelete");
		$cond1=array("resetpassword.isActive"=>1,"clientmaster.isDelete"=>"0");
		$orderBy1 = array('resetpassword' . ".id" => 'ASC');
		$join1 = array("clientmaster"=>"clientmaster.code = resetpassword.userCode");
		$joinType1=array("clientmaster"=>"inner");
		$like1=array();
		$limit1="";
		$offset1="";
		$groupByColumn1=array();
		$extraCondition1="";
		
		$c_result = $this->GlobalModel->selectQuery($orderColumns1,'resetpassword',$cond1,$orderBy1,$join1,$joinType1,$like1,$limit1,$offset1,$groupByColumn1, $extraCondition1);
		if($c_result)
		{
			$data["usrReset"]=$c_result->result_array()[0]["puCount"];
		}
		else
		{
			$data["usrReset"]=0;
		}

		
		
		$this->load->view('header');
		$this->load->view('dashboard',$data);
		$this->load->view('footer');

	}
	
}