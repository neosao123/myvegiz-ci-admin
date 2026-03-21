<?php
defined('BASEPATH') or exit('No direct script access allowed');
//session_start(); //we need to start session in order to access it through CI

class Authentication extends CI_Controller
{

	public function __construct()
	{
		parent::__construct(); 
		// Load form helper library
		$this->load->helper('form'); 
		$this->load->library('form_validation');  
		$this->load->model('GlobalModel');
		$this->load->model('VendorModel');
	}
	// Check for user login process
	public function user_login_process()
	{
		if (isset($_POST['s'])) {
			$this->form_validation->set_rules('username', 'Username', 'trim|required');
			$this->form_validation->set_rules('password', 'Password', 'trim|required');
			$enc_password = md5($this->input->post('password'));
			if ($this->form_validation->run() == FALSE) {
			    $this->session->set_flashdata('error_message', 'Fields are required..');
				redirect("Login");
			} else {
				$this->input->post('username');
				$data = array(
					'username' => $this->input->post('username'),
					'password' => $enc_password
				);
				$resultLogin = $this->VendorModel->login($data); 
			
				if ($resultLogin) {
					$username = $this->input->post('username');
					$result = $this->VendorModel->read_user_information($username);
					if ($result != false) {
						//Project Name with time appended Key for session
						$t = 'MyVegizVendor' . time();
						$this->session->set_userdata('partner_key' . SESS_KEY_PARTNER, $t);
						$code = $result[0]->code;
						$session_data = array(
							'code' => $code,
							'username' => $result[0]->entityName,
							'role' => 'VEN',
							'userFname' => $result[0]->firstName,
							'userMname' => $result[0]->middleName,
							'userLname' => $result[0]->lastName,
							'email' => $result[0]->email,
							'profilePhoto' => $result[0]->entityImage
						);
						
						// Add user data in session 
						$this->session->set_userdata('part_logged_in' . $t, $session_data);
						$addID = $this->session->userdata['part_logged_in' . $t]['code'];
						$userRole = $this->session->userdata['part_logged_in' . $t]['role'];
						$role = "";
						switch ($userRole) {
							case "ADM":
								$role = "Admin";
								break;
							case "USR":
								$role = "User";
								break;
							case "VEN":
								$role = "Vendor";
								break;
							case "DLB":
								$role = "DeliveyBoy";
								break;
						} 
						//Activity Track Starts
						$ip = $_SERVER['REMOTE_ADDR'];
						$text = $role . ' : ' . $username . ' Logged in from ' . $ip;
						$log_text = array(
							'code' => "demo",
							'addID' => $addID,
							'logText' => $text
						);
						//Activity Track Ends		
						$this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
			
						redirect("Home");
					}
				} else {
					$this->session->set_flashdata('error_message', 'Invalid Username or Password');
					redirect("Login");
				}
			}
		} else {
			$this->session->set_flashdata('error_message', 'Provide all fields..');
			redirect("Login");
		}
	}

	// Logout from admin page
	public function logout()
	{
		$session_key = $this->session->userdata('partner_key' . SESS_KEY_PARTNER); 
		$userNm = ($this->session->userdata['part_logged_in' . $session_key]['username']);
		$addID = $this->session->userdata['part_logged_in' . $session_key]['code'];
		$role = "";
		$userRole = $this->session->userdata['part_logged_in' . $session_key]['role'];
		//echo $userRole;
		switch ($userRole) {
			case "ADM":
				$role = "Admin";
				break;
			case "USR":
				$role = "User";
				break;
			case "VEN":
				$role = "Vendor";
				break;
			case "DIS":
				$role = "DeliveryBoy";
				break;
		}
		// Removing session data 
		$sess_array = array(
			'username' => $userNm
		);

		$this->session->unset_userdata('part_logged_in' . $session_key, $sess_array);


		//Activity Track Starts
		$ip = $_SERVER['REMOTE_ADDR'];
		$text = $role . ' : ' . $userNm . ' Logged out from ' . $ip;

		$log_text = array(
			'code' => "demo",
			'addID' => $addID,
			'logText' => $text 
		);
		//Activity Track Ends

		$this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');

		$this->session->set_flashdata('logout_message', 'Successfully Logged out');
		redirect("index.php/Login");
	}

	public function updateFirebaseToken()
	{
		$session_key = $this->session->userdata('key' . SESS_KEY_PARTNER);
		if(isset( $this->session->userdata['part_logged_in' . $session_key]['code'])){ 
			$addID = $this->session->userdata['part_logged_in' . $session_key]['code'];
			$data['firebaseId'] = $this->input->post('fireToken');
			$result = $this->GlobalModel->doEdit($data, 'vendor', $addID);
			echo $result;
		} else {
			echo 'false';
		} 
	}
}
