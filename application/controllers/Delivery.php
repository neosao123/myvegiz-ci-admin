<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Delivery extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('url');
		$this->load->model('globalModel');
	}
	 
	public function index()
	{
		$file = base_url(). "apk/MyVegizDeliveryTracker.apk";
		$this->load->helper('download');
		$data = file_get_contents($file);
		force_download('MyVegizDeliveryTracker.apk', $data);
	}
	
	 
}
?>