<?php

//session_start(); //we need to start session in order to access it through CI

class Authentication extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();

		// Load form helper library
		$this->load->helper('form');

		// Load form validation library
		$this->load->library('form_validation');

		// Load session library
		// $this->load->library('session');

		// Load database
		$this->load->model('GlobalModel');
	}


	// Check for user login process
	public function user_login_process()
	{
		if (isset($_POST['s'])) {
			$this->form_validation->set_rules('username', 'Username', 'trim|required');
			$this->form_validation->set_rules('password', 'Password', 'trim|required');

			$enc_password = md5($this->input->post('password'));

			if ($this->form_validation->run() == FALSE) {
				redirect("admin/login");
			} else {
				$this->input->post('username');

				$data = array(
					'username' => $this->input->post('username'),
					'password' => $enc_password
				);

				$result = $this->GlobalModel->login($data);

				if ($result == TRUE) {
					$username = $this->input->post('username');
					$result = $this->GlobalModel->read_user_information($username);
					if ($result != false) {
						if ($result[0]->role == 'DLB') {
							$this->session->set_flashdata('error_message', "You don't have access to use this site..Please contact administrator");
							redirect("Admin/login");
						} else {
							$filename = 'assets/rights/' . $result[0]->code . '.json';
							if (file_exists($filename)) {
								$t = 'MyVegizTesting' . time();
								$this->session->set_userdata('key' . SESS_KEY, $t);
								$email = "";
								$session_data = array(
									'code' => $result[0]->code,
									'username' => $result[0]->username,
									'role' => $result[0]->role,
									'name' => $result[0]->name,
									'email' => $result[0]->userEmail,
									'profilePhoto' => $result[0]->profilePhoto
								);
								$this->session->set_userdata('logged_in' . $t, $session_data);
								$addID = $this->session->userdata['logged_in' . $t]['code'];
								$userRole = $this->session->userdata['logged_in' . $t]['role'];
								$role = "";
								switch ($userRole) {
									case "ADM":
										$role = "Admin";
										break;
									case "USR":
										$role = "User";
										break;
									case "RET":
										$role = "Retailer";
										break;
									case "DIS":
										$role = "Distributor";
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
								redirect("Admin/welcome");
							} else {
								$this->session->set_flashdata('error_message', "Rights not yet assigned..Please contact administrator");
								redirect("Admin/login");
							}
						}
					}
				} else {
					$this->session->set_flashdata('error_message', 'Invalid Username or Password');
					redirect("Admin/login");
				}
			}
		} else {
		}
	}

	// Logout from admin page
	public function logout()
	{
		$session_key = $this->session->userdata('key' . SESS_KEY);

		$userNm = ($this->session->userdata['logged_in' . $session_key]['username']);
		$addID = $this->session->userdata['logged_in' . $session_key]['code'];
		$role = "";
		$userRole = $this->session->userdata['logged_in' . $session_key]['role'];
		//echo $userRole;
		switch ($userRole) {
			case "ADM":
				$role = "Admin";
				break;
			case "USR":
				$role = "User";
				break;
			case "RET":
				$role = "Retailer";
				break;
			case "DIS":
				$role = "Distributor";
				break;
		}


		// Removing session data 
		$sess_array = array(
			'username' => $userNm
		);

		$this->session->unset_userdata('logged_in' . $session_key, $sess_array);


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
		redirect("Admin/Login");
	}

	public function updateFirebaseToken()
	{
		$session_key = $this->session->userdata('key' . SESS_KEY);
		if (isset($this->session->userdata['logged_in' . $session_key]['code'])) {
			$addID = $this->session->userdata['logged_in' . $session_key]['code'];
			$data['websiteFirebaseToken'] = $this->input->post('fireToken');
			$result = $this->GlobalModel->doEdit($data, 'usermaster', $addID);
			echo $result;
		} else {
			echo 'false';
		}
	}
}
