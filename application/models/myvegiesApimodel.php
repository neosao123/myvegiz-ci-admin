<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller 
{
	public function __construct()
    {
        parent::__construct();
        $this->load->model('GlobalModel');
		$this->load->model('ApiModel');
    }

    public function index()
	{
		/*$data['userFname'] = ($this->session->userdata['logged_in']['userFname']); 
		$data['userMname'] = ($this->session->userdata['logged_in']['userMname']);
		$data['userLname'] = ($this->session->userdata['logged_in']['userLname']);
		$data['userRole']  = ($this->session->userdata['logged_in']['role']);
	    	$code=($this->session->userdata['logged_in']['code']);*/
		$this->load->view('dashboard/header');
		$this->load->view('dashboard/dashboard');
		$this->load->view('dashboard/footer');

	}
	public function login()
	{
		if($this->session->userdata('logged_in'.$session_key))
		{
			redirect("admin/index");
		}
		else
		{
			$data['error_message'] = $this->session->flashdata('error_message');
			$data['logout_message'] =$this->session->flashdata('logout_message');
			$this->load->view('dashboard/login',$data);
		}
	}
	
	public function tempHeader()
	{
		$this->load->view('dashboard/tempheader');
		
		$this->load->view('dashboard/footer');
	}
	
	function test()
	{
		
		$condition = array(
						"code" => 'CLNT18_2'
					);
					
					$resultData = $this->ApiModel->read_user_information($condition);
		
		print_r($resultData[0]);
		//$condData=array('productCategory'=>'code_1');
		//$condition=array('productCategoryy'=>'CURRE_1');
			//		$product_result = $this->ApiModel->selectData('productmaster',$category_limit,$category_offset)->result_array();
	 // print_r($product_result);
		// $category_offset=$postData["offset"];
			// $category_limit=5;
			// $totalRecords=sizeof($this->ApiModel->selectData('categorymaster','','')->result());
			// $result = $this->ApiModel->selectData('categorymaster',$category_limit,$category_offset)->result_array();  
			//print_r($result);
			exit();
			// if($result){
				// for($i=0;$i<sizeof($result);$i++){
				// $result[$i]['product']=$result;
				// }
				// echo json_encode(array("status" => "200","totalRecords" => $totalRecords,"result"=>$result), 200);
			// }
	}
	

}