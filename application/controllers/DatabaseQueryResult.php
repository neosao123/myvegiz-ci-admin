<?php
class DatabaseQueryResult extends CI_Controller{
	public function __construct() {
        parent::__construct();
		$this->load->model('GlobalModel');
		$this->load->library('sendsms');
		 
	}
	public function index(){
	    $otp = 123;
	    $contact = 8983185204;
		$result = $this->sendsms->sendOTPMessage($otp, $contact);
		print_r($result);
	}
	 
}
?>