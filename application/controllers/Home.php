<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Home extends CI_Controller
{
	public function index()
	{
		$this->load->view('header');
		$this->load->view('index');
		$this->load->view('footer');
	}
	public function about()
	{
		$this->load->view('header');
		$this->load->view('aboutUs');
		$this->load->view('footer');
	}
	public function careers()
	{
		$this->load->view('header');
		$this->load->view('careers');
		$this->load->view('footer');
	}
	public function contact()
	{
		$this->load->view('header');
		$this->load->view('contact');
		$this->load->view('footer');
	}

	public function projects()
	{
		$this->load->view('header');
		$this->load->view('ourProject');
		$this->load->view('footer');
	}

	public function blog()
	{
		$this->load->view('header');
		$this->load->view('blog');
		$this->load->view('footer');
	}

	public function projDetail()
	{
		$this->load->view('header');
		$this->load->view('projectdetails');
		$this->load->view('footer');
	}

	public function gallery()
	{
		$this->load->view('header');
		$this->load->view('gallery');
		$this->load->view('footer');
	}
	public function dashboard()
	{
		$this->load->view('dashboard/header');
		// $this->load->view('dashboard/vacancie/add');
		$this->load->view('dashboard/footer');
	}
	/*public function privacypolicy()
	{
		$this->load->view('privacy');
	}*/
	public function termsandcondition()
	{
		$this->load->view('termscondition');
	}
    public function contactdetails()
	{
		$this->load->view('contactdetails');
	}
}
