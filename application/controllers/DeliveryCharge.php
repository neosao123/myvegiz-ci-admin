<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class DeliveryCharge extends CI_Controller {
	
	public function __construct()
    {
        parent::__construct();
		 $this->load->helper('form','url','html');
   		$this->load->library('form_validation');
        $this->load->model('GlobalModel');
		$this->load->model('GlobalModel1');
		$this->session_key = $this->session->userdata('key'.SESS_KEY);
		if (!isset($this->session->userdata['logged_in' . $this->session_key]['code'])) {
			redirect('Admin/login', 'refresh');
		}
    }
	
	public function listRecords(){
		$data['error']=$this->session->flashdata('response');
		$deliverycharge=$this->GlobalModel->selectData('deliverycharge');
		if($deliverycharge->result() !='')
		{
			$data['delivery']=$this->GlobalModel->selectDataById('COMP18_1','deliverycharge');
		
			$data['currency']=$this->GlobalModel->selectData('currencymaster');
			$this->load->view('dashboard/header');
			$this->load->view('dashboard/deliveryCharge/list',$data);
			$this->load->view('dashboard/footer');
		}
		else{
			$data['currency']=$this->GlobalModel->selectData('currencymaster');
			$this->load->view('dashboard/header');
			$this->load->view('dashboard/deliveryCharge/add',$data);
			$this->load->view('dashboard/footer');
		}
		
		
	}
	public function add(){
		$data['currency']=$this->GlobalModel->selectData('currencymaster');
		$this->load->view('dashboard/header');
    	$this->load->view('dashboard/deliveryCharge/add',$data);
    	$this->load->view('dashboard/footer');
	}
	public function edit(){
		
		$data['delivery']=$this->GlobalModel->selectDataById('COMP18_1','deliverycharge');
		// print_r($data['delivery']->result());
		// exit();
		$data['currency']=$this->GlobalModel->selectData('currencymaster');
		$this->load->view('dashboard/header');
    	$this->load->view('dashboard/deliveryCharge/edit',$data);
    	$this->load->view('dashboard/footer');
	}
	public function save(){
		//Activity Track Starts
			$addID = $this->session->userdata['logged_in'.$this->session_key]['code'];
			$userRole = $this->session->userdata['logged_in'.$this->session_key]['role'];
			$userName = $this->session->userdata['logged_in'.$this->session_key]['username']; 
			$role = "";
			
			switch($userRole){
					case "ADM" : $role="Admin"; break;
					case "USR" : $role="User"; break; 
				}
			
			$ip=$_SERVER['REMOTE_ADDR'];
			$text = $role." ".$userName.' added new record in deliveryCharge from '.$ip; 
			
			$log_text = array(
							'code' => "demo",
							'addID'=>$addID,
							'logText' => $text
						);
			
			//Activity Track Ends
		$data=array(
		
		'companyName'=> trim($this->input->post('companyName')),
		'companyRegNo'=> trim($this->input->post('companyRegNo')),
		'contactNo'=> trim($this->input->post('contactNo')),
		'alternateContactNo'=> trim($this->input->post('altContactNo')),
		'email'=> trim($this->input->post('email')),
		
		'shippingAddress'=> trim($this->input->post('shippingAddress')),
		'shippingPinCode'=> trim($this->input->post('shippingPinCode')),
		'shippingPlace'=> trim($this->input->post('shippingPlace')),
		'shippingTaluka'=> trim($this->input->post('shippingTaluka')),
		'shippingDistrict'=> trim($this->input->post('shippingDistrict')),
		'shippingState'=> trim($this->input->post('shippingState')),
		'shippingCountry'=> trim($this->input->post('shippingCountry')),
		
		'xyz'=> trim($this->input->post('isBillingAddressSame')),
		
		'billingAddress'=> trim($this->input->post('billingAddress')),
		'billingPinCode'=> trim($this->input->post('billingPinCode')),
		'billingPlace'=> trim($this->input->post('billingPlace')),
		'billingTaluka'=> trim($this->input->post('billingTaluka')),
		'billingDistrict'=> trim($this->input->post('billingDistrict')),
		'billingState'=> trim($this->input->post('billingState')),
		'billingCountry'=> trim($this->input->post('billingCountry')),
		
		 'minOrder'=> trim($this->input->post('minOrder')),
	     'deliveryCharge'=> trim($this->input->post('deliveryCharge')),
		 'minOrderCurrency '=> trim($this->input->post('minOrderCurrency')),
		 'deliveryChargeCurrency'=> trim($this->input->post('deliveryChargeCurrency'))
		);
		
	   $resultData=$this->GlobalModel->addNew($data,'deliverycharge','COMP');
		
		if($resultData!='false' )
			{
				$response['status']=true;
				$response['message']="Delivery Charge  Successfully Added.";
							$this->GlobalModel->activityAdd($log_text,'activitymaster','ACT');

			}
			else
			{
				$response['status']=false;
				$response['message']="Failed To Add Delivery Charge";
			}
			
			$this->session->set_flashdata('response', json_encode($response));		
			redirect(base_url() . 'index.php/DeliveryCharge/listRecords', 'refresh');
	}
	public function update(){
		$code=$this->input->post('code');
		
			$addID = $this->session->userdata['logged_in'.$this->session_key]['code'];
			$userRole = $this->session->userdata['logged_in'.$this->session_key]['role'];
			$userName = $this->session->userdata['logged_in'.$this->session_key]['username']; 
			$role = "";
			$currencyName='INR';
			switch($userRole){
					case "ADM" : $role="Admin"; break;
					case "USR" : $role="User"; break; 
				}
			
			$ip=$_SERVER['REMOTE_ADDR'];
			$text = $role." ".$userName.' updated Currency "'.$currencyName.'" from '.$ip; 
			
			$log_text = array(
							'code' => "demo",
							'addID'=>$addID,
							'logText' => $text
						); 
			
			//Activity Track Ends 
			
		$data=array(
		'companyName'=> trim($this->input->post('companyName')),
		'companyRegNo'=> trim($this->input->post('companyRegNo')),
		'contactNo'=> trim($this->input->post('contactNo')),
		'alternateContactNo'=> trim($this->input->post('altContactNo')),
		'email'=> trim($this->input->post('email')),
		
		'shippingAddress'=> trim($this->input->post('shippingAddress')),
		'shippingPinCode'=> trim($this->input->post('shippingPinCode')),
		'shippingPlace'=> trim($this->input->post('shippingPlace')),
		'shippingTaluka'=> trim($this->input->post('shippingTaluka')),
		'shippingDistrict'=> trim($this->input->post('shippingDistrict')),
		'shippingState'=> trim($this->input->post('shippingState')),
		'shippingCountry'=> trim($this->input->post('shippingCountry')),
		
		'xyz'=> trim($this->input->post('isBillingAddressSame')),
		
		'billingAddress'=> trim($this->input->post('billingAddress')),
		'billingPinCode'=> trim($this->input->post('billingPinCode')),
		'billingPlace'=> trim($this->input->post('billingPlace')),
		'billingTaluka'=> trim($this->input->post('billingTaluka')),
		'billingDistrict'=> trim($this->input->post('billingDistrict')),
		'billingState'=> trim($this->input->post('billingState')),
		'billingCountry'=> trim($this->input->post('billingCountry')),
		
		 'minOrder'=> trim($this->input->post('minOrder')),
	     'deliveryCharge'=> trim($this->input->post('deliveryCharge')),
		 'minOrderCurrency '=> trim($this->input->post('minOrderCurrency')),
		 'deliveryChargeCurrency'=> trim($this->input->post('deliveryChargeCurrency'))
		 
		 );
		
	   $resultData=$this->GlobalModel->doEdit($data,'deliverycharge',$code);
		
		if($resultData!='false' )
			{
				$response['status']=true;
				$response['message']="DeliveryCharge  Successfully Updated.";
							$this->GlobalModel->activityAdd($log_text,'activitymaster','ACT');

			}
			else
			{
				$response['status']=false;
				$response['message']="No change In Delivery Charge";
			}
			
			$this->session->set_flashdata('response', json_encode($response));		
			redirect(base_url() . 'index.php/DeliveryCharge/listRecords', 'refresh');
	}
	
	public function test(){
		$data=$this->GlobalModel->selectDataExcludeDelete('deliverycharge');
		print_r($data->result());
     }
}
?>