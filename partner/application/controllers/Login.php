<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller 
{
	var $session_key;
	public function __construct()
    {
        parent::__construct();
        $this->load->model('GlobalModel'); 
    }
	public function index()
	{ 
		$data['error_message'] = $this->session->flashdata('error_message');
		$data['logout_message'] =$this->session->flashdata('logout_message');
		$this->load->view('login',$data); 
	}
}