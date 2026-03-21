<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Serviceavailable extends CI_Controller 
{
	var $session_key;
	public function __construct()
    {
        parent::__construct();
        
		$this->load->helper('form','url','html');
   		$this->load->library('form_validation');
        $this->load->model('GlobalModel');
		$this->load->model('GlobalModel1');
		$this->session_key = $this->session->userdata('partner_key'.SESS_KEY_PARTNER);
		if (!isset($this->session->userdata['part_logged_in' .  $this->session_key]['code'])) {
			redirect('Login', 'refresh');
		}
    }
    
   
	public function index()
	{
		$addID = $this->session->userdata['part_logged_in'.$this->session_key]['code'];
    	$data['error']=$this->session->flashdata('response');
		$data['menucategory'] = $this->GlobalModel->selectQuery('menucategory.*','menucategory',array('menucategory.isActive'=>1));
		$data['menuitem'] = $this->GlobalModel->selectQuery('vendoritemmaster.*','vendoritemmaster',array('vendoritemmaster.isActive'=>1,'vendoritemmaster.vendorCode'=>$addID));
		$data['fromDate'] = $this->GlobalModel->selectQuery('vendoritemmaster.*','vendoritemmaster',array('vendoritemmaster.isActive'=>1));
		$data['toDate'] = $this->GlobalModel->selectQuery('vendoritemmaster.*','vendoritemmaster',array('vendoritemmaster.isActive'=>1));
		$this->load->view('header');
		$this->load->view('serviceAvailable/serviceAvailable',$data);
		$this->load->view('footer'); 
	}
	 
	public function getVendorItemList()
	{
	    $addID = $this->session->userdata['part_logged_in'.$this->session_key]['code'];
		$menucategoryCode = $this->input->get('menucategoryCode');
		$itemCode=$this->input->get('itemCode');
		$fromDate=$this->input->get('fromDate');
		$toDate=$this->input->get('toDate');
	    	
		$tableName="vendoritemmaster";
		$orderColumns = array("vendoritemmaster.*,menucategory.menuCategoryName");
		$condition=array('vendoritemmaster.isActive'=>1,"vendoritemmaster.vendorCode"=>$addID,"vendoritemmaster.menuCategoryCode"=>$menucategoryCode,"vendoritemmaster.code"=>$itemCode,"vendoritemmaster.addDate"=>$fromDate,"vendoritemmaster.addDate"=>$toDate);
		$orderBy = array('vendoritemmaster' . '.id' => 'DESC');
		$joinType=array("menucategory"=>'inner');
		$join = array("menucategory"=>"vendoritemmaster.menuCategoryCode=menucategory.code");
		$groupByColumn=array();
		$limit=$this->input->GET("length");
		$offset=$this->input->GET("start");
		$extraCondition=" (vendoritemmaster.isDelete=0 OR vendoritemmaster.isDelete IS NULL)";
		$like = array();
		$Records = $this->GlobalModel->selectQuery($orderColumns,$tableName,$condition,$orderBy,$join,$joinType,$like,$limit,$offset,$groupByColumn,$extraCondition);
		$r = $this->db->last_query();
		$srno=$_GET['start']+1;
		$dataCount=0;
		$data =array();
		if($Records)
		{
			foreach($Records->result() as $row) 
			{ 	
				$code=$row->code; 
				if($row->itemActiveStatus==1)
				{
					$itemActiveStatus = "<span class='label label-sm label-success'>Active</span>";
					$checked = 'checked';
					$toggle = '<input type="checkbox" class="toggle" checked data-size="mini" id="'.$row->code.'">';
				}
				else
				{
					$itemActiveStatus = "<span class='label label-sm label-danger'>Inactive</span>";
					$checked = '';
					$toggle = '<input type="checkbox" class="toggle" data-size="mini" id="'.$row->code.'">';
				}
				if($row->isAdminApproved==1)
				{
					$isAdminApproved = '<span class="label label-sm label-success">Yes</span>'; 
				}
				else
				{
					$isAdminApproved = "<span class='label label-sm label-danger'>No</span>";
				} 
				 
				$activeAction = ' <div class="custom-control custom-checkbox"> 
						<input type="checkbox" value="1" class="custom-control-input actionStatus" '.$checked.' data-id="'.$row->code.'" id="itm'.$row->code.'">
						<label class="custom-control-label" for="itm'.$row->code.'"> '.$itemActiveStatus.' </label>
					</div>';
				    
				$data[] = array(
						$srno,
						$row->code,
						$row->itemName,
						$row->menuCategoryName,
						// '<input type="checkbox" class="toggle" checked data-size="mini" id="'.$row->code.'" '.$checked.'/>',
						$toggle,
					); 
				$srno++;
			} 
			$dataCount=sizeof($this->GlobalModel->selectQuery($orderColumns,$tableName,$condition,$orderBy,$join,$joinType,$like,'','',$groupByColumn,$extraCondition)->result());
		}  			
		$output = array( 
			"draw"			  =>     intval($_GET["draw"]),  
			"recordsTotal"    =>     $dataCount,  
			"recordsFiltered" =>     $dataCount,  
			"data"            =>     $data ,
			'r'				  => 	 $r	
		);
		echo json_encode($output);	 
	}
	
	public function chnageService(){
		$code = $this->input->post('code');
		$flag = $this->input->post('flag');
		
		$data=array(
			'itemActiveStatus' => $flag,
		);
		  
		$resultData=$this->GlobalModel->doEdit($data,'vendoritemmaster',$code);
		if($resultData=='true') echo true;
        else echo false;
	}
	
	public function addonServiceavailable()
	{
		$addID = $this->session->userdata['part_logged_in'.$this->session_key]['code'];
		$data['vendoritem'] = $this->GlobalModel->selectQuery('vendoritemmaster.*','vendoritemmaster',array('vendoritemmaster.vendorCode'=>$addID));
    	$data['error']=$this->session->flashdata('response');
		$this->load->view('header');
		$this->load->view('serviceAvailable/addonServiceavailable',$data);
		$this->load->view('footer');
	}
	
	public function addonServiceavailableList()
	{
		$addID = $this->session->userdata['part_logged_in'.$this->session_key]['code'];
	    $vendorItemCode = $this->input->get('vendorItemCode'); 
		$tableName="customizedcategory";
		
		$orderColumns = array("customizedcategory.*,vendoritemmaster.itemName,vendoritemmaster.vendorCode");
		$condition=array("vendoritemmaster.vendorCode"=>$addID,"vendoritemmaster.code"=>$vendorItemCode);
		$orderBy = array('customizedcategory' . '.id' => 'DESC');
		$joinType=array("vendoritemmaster"=>'inner');
		$join = array("vendoritemmaster"=>"customizedcategory.vendorItemCode=vendoritemmaster.code");
	 
		$groupByColumn=array();
		$limit=$this->input->GET("length");
		$offset=$this->input->GET("start");
		$extraCondition=" (customizedcategory.isDelete=0 OR customizedcategory.isDelete IS NULL)";
		$like = array();
		$Records = $this->GlobalModel->selectQuery($orderColumns,$tableName,$condition,$orderBy,$join,$joinType,$like,$limit,$offset,$groupByColumn,$extraCondition);
		$r = $this->db->last_query();
		$srno=$_GET['start']+1;
		$dataCount=0;
		$data =array();
		if($Records)
		{
			foreach($Records->result() as $row) 
			{ 	
				$code=$row->code; 
				if($row->isEnabled==1)
				{
					$itemActiveStatus = "<span class='label label-sm label-success'>Active</span>";
					$checked = 'checked';
					$toggle = '<input type="checkbox" class="toggleAddon" checked data-size="mini" id="'.$row->code.'">';
				}
				else
				{
					$itemActiveStatus = "<span class='label label-sm label-danger'>Inactive</span>";
					$checked = '';
					$toggle = '<input type="checkbox" class="toggleAddon" data-size="mini" id="'.$row->code.'">';
				}
				     
				$data[] = array(
						$srno,
						$row->code, 
						$row->itemName,
						$row->categoryTitle,
						ucfirst($row->categoryType),
						$toggle,
					); 
				$srno++;
			} 
			$dataCount=sizeof($this->GlobalModel->selectQuery($orderColumns,$tableName,$condition,$orderBy,$join,$joinType,$like,'','',$groupByColumn,$extraCondition)->result());
		}  			
		$output = array( 
			"draw"			  =>     intval($_GET["draw"]),  
			"recordsTotal"    =>     $dataCount,  
			"recordsFiltered" =>     $dataCount,  
			"data"            =>     $data ,
			'r'				  => 	 $r	
		);
		echo json_encode($output);
	}
	
	public function changeServiceAddon()
	{
		$code = $this->input->post('code');
		$flag = $this->input->post('flag');
		$data=array(
			'isEnabled' => $flag,
		);
		  
		$resultData=$this->GlobalModel->doEdit($data,'customizedcategory',$code);
		if($resultData=='true') echo true;
        else echo false;
	}
	
}