<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Orderhook extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model('GlobalModel');
		$this->load->library('notificationlibv_3');
	}
    public function assign_order(string $orderCode) {
        echo $orderCode;
    }
}