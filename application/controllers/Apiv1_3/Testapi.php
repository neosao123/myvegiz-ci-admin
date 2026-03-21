<?php
 
require(APPPATH.'/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;
//date_default_timezone_set('Asia/Kolkata');
class Testapi extends REST_Controller {
	 public function __construct()
    { 
        parent::__construct();
        //$this->load->model('book_model');
		$this->load->helper('form','url','html');
   		$this->load->library('form_validation');
        $this->load->model('GlobalModel');
		$this->load->model('ApiModel');
		$this->load->library('notificationlibv_3');
    }
	
	function check_get(){
		//bagCount				
		$toDate = date('Y-m-d',strtotime('08-02-2020'));
		$checkExist = $this->GlobalModel->selectDataExcludeDelete('orderbagcount');
		$existsDate = $checkExist->result()[0]->toDate;
		$existsCount = $checkExist->result()[0]->count;
		
		if(strtotime($existsDate) == strtotime($toDate)) {
			$result = 'Exists Date = '.strtotime($existsDate) . ' && To Date = ' . strtotime($toDate);
		} else {
			$result = 'Hello'; 
		}
		$this->response(array("status" => "400", "message" => $result), 400);
	}
}
?>