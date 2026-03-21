<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Chat extends CI_Controller
{
	var $session_key;
	public function __construct()
	{
		parent::__construct();
		$this->load->model('GlobalModel');
		$this->load->model('ApiModel');
		$this->load->library('sendsms');
	}

	public function index()
	{
		$this->load->view('dashboard/header');
		$this->load->view('dashboard/chat');
		$this->load->view('dashboard/footer');
	}
	
}
?>