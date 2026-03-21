<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class MyProfile extends CI_Controller {
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
  
	public function edit1()
    {
        $code=$this->uri->segment(3);
        $data['query'] = $this->GlobalModel->selectDataById($code,'vendor');
    	$this->load->view('header');
		$this->load->view('myprofile/edit',$data);
		$this->load->view('footer');
    }
	 
	public function update()
	{
		$code = $this->input->post('code');
		$ownerContact = trim($this->input->post("ownerContact")); 
		//Activity Track Starts 
		$addID = $this->session->userdata['part_logged_in'.$this->session_key]['code'];
		$userRole = $this->session->userdata['part_logged_in'.$this->session_key]['role'];
		$userName = $this->session->userdata['part_logged_in'.$this->session_key]['username']; 
		$role = ""; 
		switch($userRole)
		{
			case "ADM" : $role="Admin"; break;
			case "VEN" : $role="Vendor"; break;
			case "USR" : $role="User"; break; 
		}
		
		$ip=$_SERVER['REMOTE_ADDR'];
		$text = $role." ".$userName.' updated Vendor login credentials "'.$ownerContact.'" from '.$ip; 
		
		$log_text = array(
			'code' => "demo",
			'addID'=>$addID,
			'logText' => $text
		);			
			//Activity Track Ends 
		$result = $this->GlobalModel->checkDuplicateRecordNotEqualtoCode('ownerContact',$ownerContact,'vendor',$code);
		
		if ($result==true) 
		{ 
			$response['status']=false;
			$response['message']='Duplicate number exists!';    
		}
		else
		{		
			$this->form_validation->set_rules('ownerContact', 'user name', 'trim|required');
			$this->form_validation->set_rules('password', 'password', 'trim|required');
			$this->form_validation->set_rules('confirmPassword', 'confirm password', 'trim|required');
			if ($this->form_validation->run()== FALSE) 
			{	 
				$response['status']=false;
				$response['message']='* Fields are Required!';    
			}		
			else
			{
				$password = trim($this->input->post('password'));
				$confirmPassword = trim($this->input->post('confirmPassword'));
				if($password==$confirmPassword)
				{
					$update_password = md5(trim($this->input->post('password')));				 
					$data = array( 
						'ownerContact' => trim($this->input->post("ownerContact")),
						'password' => $update_password,					 
						'editID'=>$addID,
						'editIP'=>$ip, 
					); 					
					$result = $this->GlobalModel->doEdit($data, 'vendor', $code); 
					if($result!='false')
					{
						$response['status']=true;
						$response['message']="Vendor Successfully Updated.";
						$this->GlobalModel->activityAdd($log_text,'activitymaster','ACT'); 
					}
					else
					{
						$response['status']=false;
						$response['message']="No change In Vendor";  
					}	 
				}
				else
				{ 
					$response['status']=false;
					$response['message']='Password does not match!';			
				}
			}
        }
		echo json_encode($response);
	}
	  
	public function edit()
	{
		$code=$this->uri->segment(3);
        $data['query'] = $this->GlobalModel->selectDataById($code,'vendor');
		$entitycategoryCode = $data['query']->result_array()[0]['entitycategoryCode'];
		$data['entitycategory'] = $this->GlobalModel->selectDataById($entitycategoryCode,'entitycategory');
		$data['cuisineslines']=$this->GlobalModel->selectQuery('vendorcuisinelineentries.cuisineCode,cuisinemaster.cuisineName',"vendorcuisinelineentries",array("vendorcuisinelineentries.vendorCode"=>$code),array(),array("cuisinemaster"=>"cuisinemaster.code=vendorcuisinelineentries.cuisineCode"),array("cuisinemaster"=>'inner'));
    	$this->load->view('header');
		$this->load->view('myprofile/view',$data);
		$this->load->view('footer');
	}	 
	
	public function configUpdate()
	{
		$code = $this->input->post('configVendorCode');
		$packagingType = trim($this->input->post('packagingType'));
		$cartPackagingPrice = trim($this->input->post('cartPackagingPrice')); 
		//Activity Track Starts 
		$addID = $this->session->userdata['part_logged_in'.$this->session_key]['code'];
		$userRole = $this->session->userdata['part_logged_in'.$this->session_key]['role'];
		$userName = $this->session->userdata['part_logged_in'.$this->session_key]['username']; 
		$role = ""; 
		switch($userRole)
		{
			case "ADM" : $role="Admin"; break;
			case "VEN" : $role="Vendor"; break;
			case "USR" : $role="User"; break; 
		}
		
		$ip=$_SERVER['REMOTE_ADDR'];
		$text = $role." ".$userName.' updated Vendor Packaging Details to "'.$packagingType.'" from '.$ip; 
		
		$log_text = array(
			'code' => "demo",
			'addID'=>$addID,
			'logText' => $text
		);			
		//Activity Track Ends 
		 		
		$this->form_validation->set_rules('packagingType', 'Packaging Type', 'trim|required');
		if($packagingType=="CART"){
			$this->form_validation->set_rules('cartPackagingPrice', 'Cart Packaging Price', 'trim|required');
		} 
		if ($this->form_validation->run()== FALSE) 
		{	 
			$response['status']=false;
			$response['message']='* Fields are Required!';    
		}		
		else
		{			
			$data = array( 
				'cartPackagingPrice' => $cartPackagingPrice,
				'packagingType' => $packagingType,					 
				'editID'=>$addID,
				'editIP'=>$ip, 
			); 					
			$result = $this->GlobalModel->doEdit($data, 'vendor', $code); 
			if($result!='false')
			{
				$response['status']=true;
				$response['message']="Vendor Successfully Updated.";
				$this->GlobalModel->activityAdd($log_text,'activitymaster','ACT'); 
			}
			else
			{
				$response['status']=false;
				$response['message']="No change In Vendor";  
			}	 
		 
		}
        
		echo json_encode($response);
	}
	
	public function viewHours(){
	    $code = $this->uri->segment(3);
		$condition['vendor.code']=$code;
		$data['vendor'] = $this->GlobalModel->selectQuery("vendor.*","vendor",$condition);
		$condition1['vendorhours.vendorCode']=$code;
		$data['vendorhours'] = $this->GlobalModel->selectQuery("vendorhours.*","vendorhours",$condition1);
		$this->load->view('header');
		$this->load->view('myprofile/vendorhours', $data);
		$this->load->view('footer');
	}
}