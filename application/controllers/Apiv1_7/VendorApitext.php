<?php
require(APPPATH . '/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;
class VendorApitext extends REST_Controller
{

	public function __construct()
	{
		parent::__construct();
		//$this->load->model('book_model');		
		$this->load->helper('form', 'url', 'html');
		$this->load->library('form_validation');
		$this->load->model('GlobalModel');
		$this->load->model('ApiModel');
		$this->load->library('notificationlibv_3');
		//$this->load->library('sendsms');
	}


    public function viewHours_post(){
		
		$postData = $this->post();
	
		if($postData['vendorCode']!=""){
		$vendorCode = $postData['vendorCode'];
	   
	
		$condition['code'] = $postData['vendorCode'];
		$result = $this->GlobalModel->selectQuery("vendor.*","vendor",$condition);
		$condition1['vendorhours.vendorCode']=$vendorCode;
		$Records = $this->GlobalModel->selectQuery("vendorhours.*","vendorhours",$condition1);
		
		if ($Records) {
			$data = array();
			foreach ($Records->result_array() as $r) {
				$data[] = $r;
			}  
			$response['vendorhours'] = $data;
			return $this->response(array("status" => "200", "message" => 'Data Found', "result" => $response), 200);
		
		} else {
				return $this->response(array("status" => "300", "message" => "Please provide an correct old password."), 200);
			}
		} else {
			return $this->response(array("status" => "400", "message" => " * field(s) are required."), 200);
		}
		
		
	}
	
	
	
}	