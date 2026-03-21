<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Employee extends CI_Controller {
    var $session_key;
    public function __construct()
    {
        parent::__construct();
		 $this->load->helper('form','url','html');
      	 $this->load->library('form_validation');
        $this->load->model('GlobalModel');
		$this->load->model('GlobalModel1');
		$this->load->model('Testing');
		$this->session_key = $this->session->userdata('key'.SESS_KEY);
		if (!isset($this->session->userdata['logged_in' . $this->session_key]['code'])) {
			redirect('Admin/login', 'refresh');
		}
	}
    
    public function add()
    {
      
         $data['queryJobType'] = $this->GlobalModel->selectDataUser('jobtypemaster');
         $data['querySalaryGrade'] = $this->GlobalModel->selectDataUser('salarygrademaster');
         $data['queryDesignation'] = $this->GlobalModel->selectDataUser('designationmaster');
         $data['queryDept'] = $this->GlobalModel->selectDataUser('departmentmaster');
		 $data['queryEmploymentStatus'] = $this->GlobalModel->selectDataUser('employmentstatus');
		 $data['queryemployee'] = $this->GlobalModel->selectDataUser('employeemaster');
			 // print_r($data['queryemployee']->result());
			 // exit();
		$data['error']=$this->session->flashdata('response');
        $this->load->view('dashboard/header');
        $this->load->view('dashboard/employee/add',$data);
        //$this->load->view('dashboard/footer');
        
    }
	public function getemployeeName()
	{
		$firstName = $this->input->get('firstName');
		$middleName = $this->input->get('middleName');
		$lastName = $this->input->get('lastName');
		
		$datafirstName = $this->GlobalModel->similarResultFind('employeemaster','firstName',$firstName);
		$datamiddleName = $this->GlobalModel->similarResultFind('employeemaster','middleName',$middleName);
		$datalastName = $this->GlobalModel->similarResultFind('employeemaster','lastName',$lastName);
		//get firstName
	    $firstName = '';
		
		foreach( $datafirstName->result() as $first)
		{   

			$firstName.='<option value="' . $first->firstName . '">';
		}
		echo $firstName;
		
		//get middle Name
		 $middleName = '';
		
		foreach( $datamiddleName->result() as $middle)
		{   
        $middleName.='<option value="' . $middle->middleName . '">';
		}
		echo $middleName;
		
		//get last name
		 $lastName = '';
		
		foreach( $datalastName->result() as $last)
		{   

			$lastName.='<option value="' . $last->lastName . '">';
		}
		echo $lastName;
		
	}
	public function getemployeeDistrict()
	{
		$District = $this->input->get('District');
		$dataDistrict = $this->GlobalModel->similarResultFind('employeemaster','permanentDistrict',$District);
		$District = '';
		foreach($dataDistrict->result() as $row)
		{   

			$District.='<option value="' . $row->permanentDistrict . '">';
		}
		echo $District;
	} 
	public function getEmployeeList()
	{
		
		$tables=array('employeemaster','jobtypemaster');
		
		$requiredColumns=array
		(
			array('code', 'firstName','middleName','lastName','gender', 'contact1','entityCode','contact2','email', 'employmentStatus','jobType', 'isActive'),
			array('jobTypeName')
			
		);
		
		$conditions=array(
		array('jobType','code')
		
		);
		//Filter Data
		$firstName=$this->input->GET("firstName");
		$middleName=$this->input->GET("middleName");
		$lastName=$this->input->GET("lastName");
		
		$deptCode=$this->input->GET("departmentName");
		
		$gender=$this->input->GET("gender");
		$permanentDistrict=$this->input->GET("employeeDistrict");
		$employmentStatus=$this->input->GET("employmentStatus");
		$designation=$this->input->GET("employeeDesignation");
		$jobType=$this->input->GET("employeeJobType");
		$pincode=$this->input->GET("pincode");
		
		$extraConditionColumnNames=array(
		array("firstName","middleName","lastName","deptCode","gender","permanentDistrict","employmentStatus","designation","jobType","currentPinCode")
		);
		
		$extraConditions=array(
		array($firstName,$middleName,$lastName,$deptCode,$gender,$permanentDistrict,$employmentStatus,$designation,$jobType,$pincode)
		);
		
         $Records = $this->GlobalModel1->make_datatables($tables,$requiredColumns,$conditions,$extraConditionColumnNames,$extraConditions);
		 // print_r($Records->result());
			$srno=$_GET['start']+1;
			$data=array();
		  foreach($Records->result() as $row) {
							
				 if($row->isActive_011 == "1")
				 {
				    $status = " <span class='label label-sm label-success'>Active</span>";
				 }
				 else
				 {
				   $status = " <span class='label label-sm label-warning'>Inactive</span>";
				 }
				 
				 
				$actionHtml='<div class="btn-group">
                                                        <button type="button" class="btn btn-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <i class="ti-settings"></i>
                                                        </button>
                                                        <div class="dropdown-menu animated slideInUp" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 35px, 0px);">
                                                            <a class="dropdown-item  blue" href="'.base_url().'index.php/Employee/view/'.$row->code_00.'" href><i class="ti-eye"></i> Open</a>
                                                            <a class="dropdown-item" href="'.base_url().'index.php/Employee/edit/'.$row->code_00.'"><i class="ti-pencil-alt"></i> Edit</a>
                                                            <a class="dropdown-item  mywarning" data-seq="'.$row->code_00.'" id="'.$row->code_00.'"><i class="ti-trash" href></i> Delete</a>
                                                            
                                                        </div>
                                                    </div>';
								 
               $data[] = array(
					$srno,
					$row->code_00,
                     $row->firstName_01.' '.$row->middleName_02.' '.$row->lastName_03,
                    $row->contact1_05.'<br>'.$row->contact2_07,
                    $row->email_08,
					$row->employmentStatus_09,
					$row->jobTypeName_10,
					$status,
					$actionHtml
               );
			   
			  
			$srno++;
		  }
		   $dataCount=$this->GlobalModel1->get_all_data($tables,$requiredColumns,$conditions,$extraConditionColumnNames,$extraConditions);
			   $output = array( 
                "draw"                    =>     intval($_GET["draw"]),  
                "recordsTotal"          =>      $dataCount,  
                "recordsFiltered"     =>     $dataCount,  
                "data"                    =>     $data  
				);
          echo json_encode($output);
	}
	
	
    public function save()
    {
		$joiningDateFormat = DateTime::createFromFormat('d/m/Y',$this->input->post("joiningDate"));
		$joiningDate = $joiningDateFormat->format('Y-m-d');
		
		$dobFormat = DateTime::createFromFormat('d/m/Y',$this->input->post("dob"));
		$dob = $dobFormat->format('Y-m-d');
		
		$firstNameA = trim(ucfirst(strtolower($this->input->post("firstName"))));
		$lastNameA = trim(ucfirst(strtolower($this->input->post("lastName")))); 
		$empName = $firstNameA." ".$lastNameA;
		
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
		$text = $role." ".$userName.' added new employee "'.$empName.'" from '.$ip; 
		
		$log_text = array(
						'code' => "demo",
						'addID'=>$addID,
						'logText' => $text
					);
		
		//Activity Track Ends
			 
		$this->form_validation->set_rules('firstName', 'First Name', 'required');
		$this->form_validation->set_rules('middleName', 'Middle Name', 'required');
   		$this->form_validation->set_rules('lastName', 'Last Name', 'required');
		$this->form_validation->set_rules('dob', 'Date of Birth', 'required');
		$this->form_validation->set_rules('gender', 'Gender ', 'required');
		//$this->form_validation->set_rules('entityCode', 'Organization Name', 'required');
		//$this->form_validation->set_rules('officeCode', 'Office Name', 'required');
   		$this->form_validation->set_rules('deptCode', 'Department Name', 'required');
		//$this->form_validation->set_rules('contractCode', 'Contract Name', 'required');
		//$this->form_validation->set_rules('siteCode', 'Site Name ', 'required');
		$this->form_validation->set_rules('employmentStatus', 'Employment Status ', 'required');
		$this->form_validation->set_rules('joiningDate', 'Joining Date', 'required');
		//$this->form_validation->set_rules('contractJoinDate', 'Contract Joining Date', 'required');
   		$this->form_validation->set_rules('jobType', 'Job type', 'required');
		$this->form_validation->set_rules('salaryGrade', 'Salary Grade', 'required');
		$this->form_validation->set_rules('designation', 'Designation', 'required');
   						   if ($this->form_validation->run()== FALSE) 
   						{	 
   								 $response['status']=false;
								 $response['message']="* fields are Required";
								  echo json_encode($response);
   								
						}
		  else
		  {
			  
        $data = array(
			  'title' => trim($this->input->post("title")),
              'firstName' => $firstNameA,
              'middleName' => trim(ucfirst(strtolower($this->input->post("middleName")))),
              'lastName' => $lastNameA,
              'dob' => $dob,
              'gender' => $this->input->post("gender"),
              'maritalStatus' => $this->input->post("maritalStatus"),
              'bloodGroup' => trim($this->input->post("bloodGroup")),
              'currentAddress' => trim($this->input->post("currentAddress")),
              'currentLandmark' => trim($this->input->post("currentLandmark")),
              'currentPinCode' => trim($this->input->post("currentPinCode")),
              'currentPlace' => trim($this->input->post("currentPlace")),
              'currentTaluka' => trim($this->input->post("currentTaluka")),
              'currentDistrict' => trim($this->input->post("currentDistrict")),
              'currentState' => trim($this->input->post("currentState")),
              'currentCountry' => trim($this->input->post("currentCountry")),
              'isPermanentAddressSame' => $this->input->post("isPermanentAddressSame"),
              'permanentAddress' => trim($this->input->post("permanentAddress")),
              'permanentLandmark' => trim($this->input->post("permanentLandmark")),
              'permanentPinCode' => trim($this->input->post("permanentPinCode")),
              'permanentPlace' => trim($this->input->post("permanentPlace")),
              'permanentTaluka' => trim($this->input->post("permanentTaluka")),
              'permanentDistrict' => trim($this->input->post("permanentDistrict")),
              'permanentState' => trim($this->input->post("permanentState")),
              'permanentCountry' => trim($this->input->post("permanentCountry")),
              'contact1' => trim($this->input->post("contact1")),
              'contact2' => trim($this->input->post("contact2")),
              'email' => trim($this->input->post("email")),
              'fbLink' => trim($this->input->post("fbLink")),
              'linkedInLink' => trim($this->input->post("linkedInLink")),
              'gPlusLink' => trim($this->input->post("gPlusLink")),
             
              'deptCode' => trim($this->input->post("deptCode")),
			  
              
			  
			  
              'employmentStatus' => trim($this->input->post("employmentStatus")),
              'empToken' => trim($this->input->post("empToken")),
              'joiningDate' => $joiningDate,
			  
              
			  
              'jobType' => trim($this->input->post("jobType")),
              'salaryGrade' => trim($this->input->post("salaryGrade")),
              'designation' => trim($this->input->post("designation")),
              'reportingTo' => trim($this->input->post("reportingTo")),
                    
              'empBankName' => trim($this->input->post("empBankName")),
			  // 'empBankName' => trim($this->input->post("empBankAcc")),
              'empBankAccountHolderName' => trim($this->input->post("empBankAccountHolderName")),
              'empBankBranchName' => trim($this->input->post("empBankBranchName")),
              'empBankIfscCode' => trim($this->input->post("empBankIfscCode")),
              'empBankMicrCode' => trim($this->input->post("empBankMicrCode")),
          
               'empBankAccountNo' => trim($this->input->post("empBankAccountNo")),
               'empAdharNumber' => trim($this->input->post("empAdharNumber")),
               'empPanNumber' => trim($this->input->post("empPanNumber")),
               'empPfAccountNumber' => trim($this->input->post("empPfAccountNumber")),
               'empEsiAccountNumber' => trim($this->input->post("empEsiAccountNumber")),
               'addID'=>$addID,
			   'addIP'=>$ip,
			   'isActive' =>$this->input->post("isActive")
          );
	 //print_r($data);
        $code=$this->GlobalModel->addNew($data, 'employeemaster', 'EMP');
        
		if($code != 'false')
		{
			
			
			$empBankPassbookFile = "";
			$empAdharFile = "";
			$empPanFile = "";
			$empPfAccountFile = ""; 
			$empEsiAccountFile = "";

			if(! file_exists(FCPATH.'uploads/employees/'.$code))
			{
				mkdir(FCPATH.'uploads/employees/'.$code);
			}

			 $uploadRootDir = 'uploads/';
			 $uploadDir = 'uploads/employees/'.$code;

			// For Bank Passbook file upload 
			if (!empty($_FILES['empBankPassbookFile']['name'])) 
			{
			  $tmpFile = $_FILES['empBankPassbookFile']['tmp_name'];
			  $ext = pathinfo($_FILES['empBankPassbookFile']['name'], PATHINFO_EXTENSION);
			  $filename = $uploadDir.'/'.$code.'-BANK_PASS.jpg';
			  move_uploaded_file($tmpFile,$filename);
			  if(file_exists($filename))
			  {
				  $empBankPassbookFile=$code.'-BANK_PASS.jpg';
			  }
			}
			else
			{                    
			  $dummyFile='file_not_found.png';
			  $tmpFile = $uploadRootDir.$dummyFile;
			  $filename = $uploadDir.'/'.$code.'-BANK_PASS.jpg';
			  copy($tmpFile, $filename);
				if(file_exists($filename))
				{
				
				  $empBankPassbookFile=$code.'-BANK_PASS.jpg';
				}
			}

			// For Adhar Card file upload 
			if (!empty($_FILES['empAdharFile']['name'])) 
			{
			  $tmpFile = $_FILES['empAdharFile']['tmp_name'];
			  $ext = pathinfo($_FILES['empAdharFile']['name'], PATHINFO_EXTENSION);
			  $filename = $uploadDir.'/'.$code.'-ADHAR.jpg';
			  move_uploaded_file($tmpFile,$filename);
			  if(file_exists($filename))
			  {
				  $empAdharFile=$code.'-ADHAR.jpg';
			  }
			}
			else
			{                    
			  $dummyFile='file_not_found.png';
			  $tmpFile = $uploadRootDir.$dummyFile;
			  $filename = $uploadDir.'/'.$code.'-ADHAR.jpg';
			  copy($tmpFile, $filename);
				if(file_exists($filename))
				{
				
				  $empAdharFile=$code.'-ADHAR.jpg';
				}
			}
			
			// For PAN Card file upload 
			if (!empty($_FILES['empPanFile']['name'])) 
			{
			  $tmpFile = $_FILES['empPanFile']['tmp_name'];
			  $ext = pathinfo($_FILES['empPanFile']['name'], PATHINFO_EXTENSION);
			  $filename = $uploadDir.'/'.$code.'-PAN.jpg';
			  move_uploaded_file($tmpFile,$filename);
			  if(file_exists($filename))
			  {
				  $empPanFile=$code.'-PAN.jpg';
			  }
			}
			else
			{                    
			  $dummyFile='file_not_found.png';
			  $tmpFile = $uploadRootDir.$dummyFile;
			  $filename = $uploadDir.'/'.$code.'-PAN.jpg';
			  copy($tmpFile, $filename);
				if(file_exists($filename))
				{
				
				  $empPanFile=$code.'-PAN.jpg';
				}
			} 

			// For PF Account file upload 
			if (!empty($_FILES['empPfAccountFile']['name'])) 
			{
			  $tmpFile = $_FILES['empPfAccountFile']['tmp_name'];
			  $ext = pathinfo($_FILES['empPfAccountFile']['name'], PATHINFO_EXTENSION);
			  $filename = $uploadDir.'/'.$code.'-PF.jpg';
			  move_uploaded_file($tmpFile,$filename);
			  if(file_exists($filename))
			  {
				  $empPfAccountFile=$code.'-PF.jpg';
			  }
			}
			else
			{                    
			  $dummyFile='file_not_found.png';
			  $tmpFile = $uploadRootDir.$dummyFile;
			  $filename = $uploadDir.'/'.$code.'-PF.jpg';
			  copy($tmpFile, $filename);
				if(file_exists($filename))
				{
				
				  $empPfAccountFile=$code.'-PF.jpg';
				}
			} 

			// For ESI Account file upload 
			if (!empty($_FILES['empEsiAccountFile']['name'])) 
			{
			  $tmpFile = $_FILES['empEsiAccountFile']['tmp_name'];
			  $ext = pathinfo($_FILES['empEsiAccountFile']['name'], PATHINFO_EXTENSION);
			  $filename = $uploadDir.'/'.$code.'-ESI.jpg';
			  move_uploaded_file($tmpFile,$filename);
			  if(file_exists($filename))
			  {
				  $empEsiAccountFile=$code.'-ESI.jpg';
			  }
			}
			else
			{                    
			  $dummyFile='file_not_found.png';
			  $tmpFile = $uploadRootDir.$dummyFile;
			  $filename = $uploadDir.'/'.$code.'-ESI.jpg';
			  copy($tmpFile, $filename);
				if(file_exists($filename))
				{
				
				  $empEsiAccountFile=$code.'-ESI.jpg';
				}
			}

		   
			$subData = array(
					 
				'empBankPassbookFile' => $empBankPassbookFile,
				'empAdharFile' => $empAdharFile,
				'empPanFile' =>  $empPanFile,
				'empPfAccountFile' => $empPfAccountFile,
				'empEsiAccountFile' => $empEsiAccountFile

			); 
			$this->GlobalModel->doEdit($subData,'employeemaster',$code);
			
				$response['status']=true;
				$response['message']="Employee Inforamtion Successfully  Added.";
				$this->GlobalModel->activityAdd($log_text,'activitymaster','ACT');
				
			echo json_encode($response);
	
		}
		
		else
			{
				$response['status']=false;
				$response['message']="Failed To Add Employee Information";
				
			echo json_encode($response);
		
			}
    } 
}// End Save	
	
    public function listRecords()
    {
        //$data['query'] = $this->GlobalModel->selectPerticularFieldFromAnotherTable('employeemaster','jobtypemaster','jobType','jobTypeName');
		
		$data['queryJobType'] = $this->GlobalModel->selectDataUser('jobtypemaster');
        $data['querySalaryGrade'] = $this->GlobalModel->selectDataUser('salarygrademaster');
        $data['queryDesignation'] = $this->GlobalModel->selectDataUser('designationmaster');
        //$data['queryContract'] = $this->GlobalModel->selectDataUser('contractmaster');
        $data['queryDept'] = $this->GlobalModel->selectDataUser('departmentmaster');
        $data['queryEmploymentStatus'] = $this->GlobalModel->selectDataUser('employmentstatus');
		$data['queryemployee'] = $this->GlobalModel->selectDataUser('employeemaster');
		$data['querydistrict'] = $this->GlobalModel->selectDataUser('employeemaster');
		
		
        $data['error']=$this->session->flashdata('response');
		$this->load->view('dashboard/header');
        $this->load->view('dashboard/employee/list',$data);
        $this->load->view('dashboard/footer');
        
    } // End List
	
    public function edit()
    {
        $code = $this->uri->segment(3); 
		$data['error']=$this->session->flashdata('response');
        $data['query'] = $this->GlobalModel->edit('employeemaster',$code);
       
        $data['queryJobType'] = $this->GlobalModel->selectDataUser('jobtypemaster');
        $data['querySalaryGrade'] = $this->GlobalModel->selectDataUser('salarygrademaster');
        $data['queryDesignation'] = $this->GlobalModel->selectDataUser('designationmaster');
		 $data['queryDept'] = $this->GlobalModel->selectDataUser('departmentmaster');
       $data['queryemployee'] = $this->GlobalModel->selectDataUser('employeemaster');
        $data['queryEmploymentStatus'] = $this->GlobalModel->selectDataUser('employmentstatus');
		
        $this->load->view('dashboard/header');
        $this->load->view('dashboard/employee/edit',$data);
        
    } // End Edit
	
	
	public function update()
	{
		$code =  $this->input->post('code');
		
		$joiningDateFormat = DateTime::createFromFormat('d/m/Y',$this->input->post("joiningDate"));
		$joiningDate = $joiningDateFormat->format('Y-m-d');
		
		$dobFormat = DateTime::createFromFormat('d/m/Y',$this->input->post("dob"));
		$dob = $dobFormat->format('Y-m-d');
		
		$firstNameA = trim(ucfirst(strtolower($this->input->post("firstName"))));
		$lastNameA = trim(ucfirst(strtolower($this->input->post("lastName"))));
		
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
		$text = $role." ".$userName.' updated employee "'.$firstNameA.' '.$lastNameA.'" from '.$ip; 
		
		$log_text = array(
						'code' => "demo",
						'addID'=>$addID,
						'logText' => $text
					); 
		
		//Activity Track Ends
		
		$this->form_validation->set_rules('firstName', 'First Name', 'required');
		$this->form_validation->set_rules('middleName', 'Middle Name', 'required');
   		$this->form_validation->set_rules('lastName', 'Last Name', 'required');
		$this->form_validation->set_rules('dob', 'Date of Birth', 'required');
		$this->form_validation->set_rules('gender', 'Gender ', 'required');
		//$this->form_validation->set_rules('entityCode', 'Organization Name', 'required');
		//$this->form_validation->set_rules('officeCode', 'Office Name', 'required');
   		$this->form_validation->set_rules('deptCode', 'Department Name', 'required');
		//$this->form_validation->set_rules('contractCode', 'Contract Name', 'required');
		//$this->form_validation->set_rules('siteCode', 'Site Name ', 'required');
		$this->form_validation->set_rules('employmentStatus', 'Employment Status ', 'required');
		$this->form_validation->set_rules('joiningDate', 'Joining Date', 'required');
		//$this->form_validation->set_rules('contractJoinDate', 'Contract Joining Date', 'required');
   		$this->form_validation->set_rules('jobType', 'Job type', 'required');
		$this->form_validation->set_rules('salaryGrade', 'Salary Grade', 'required');
		$this->form_validation->set_rules('designation', 'Designation', 'required');
   						   if ($this->form_validation->run()== FALSE) 
   						{	 
   								 $response['status']=false;
								 $response['message']="* fields are Required";
								  echo json_encode($response);
   								
						}
		  else
		  {
       $data = array(
			  'title' => trim($this->input->post("title")),
              'firstName' => $firstNameA,
              'middleName' => trim(ucfirst(strtolower($this->input->post("middleName")))),
              'lastName' => $lastNameA,
              'dob' => $dob,
              'gender' => $this->input->post("gender"),
              'maritalStatus' => $this->input->post("maritalStatus"),
              'bloodGroup' => trim($this->input->post("bloodGroup")),
              'currentAddress' => trim($this->input->post("currentAddress")),
              'currentLandmark' => trim($this->input->post("currentLandmark")),
              'currentPinCode' => trim($this->input->post("currentPinCode")),
              'currentPlace' => trim($this->input->post("currentPlace")),
              'currentTaluka' => trim($this->input->post("currentTaluka")),
              'currentDistrict' => trim($this->input->post("currentDistrict")),
              'currentState' => trim($this->input->post("currentState")),
              'currentCountry' => trim($this->input->post("currentCountry")),
              'isPermanentAddressSame' => $this->input->post("isPermanentAddressSame"),
              'permanentAddress' => trim($this->input->post("permanentAddress")),
              'permanentLandmark' => trim($this->input->post("permanentLandmark")),
              'permanentPinCode' => trim($this->input->post("permanentPinCode")),
              'permanentPlace' => trim($this->input->post("permanentPlace")),
              'permanentTaluka' => trim($this->input->post("permanentTaluka")),
              'permanentDistrict' => trim($this->input->post("permanentDistrict")),
              'permanentState' => trim($this->input->post("permanentState")),
              'permanentCountry' => trim($this->input->post("permanentCountry")),
              'contact1' => trim($this->input->post("contact1")),
              'contact2' => trim($this->input->post("contact2")),
              'email' => trim($this->input->post("email")),
              'fbLink' => trim($this->input->post("fbLink")),
              'linkedInLink' => trim($this->input->post("linkedInLink")),
              'gPlusLink' => trim($this->input->post("gPlusLink")),
              //'entityCode' => trim($this->input->post("entityCode")),
              //'officeCode' => trim($this->input->post("officeCode")),
              'deptCode' => trim($this->input->post("deptCode")),
              //'contractCode' => trim($this->input->post("contractCode")),
			  //'siteCode' => trim($this->input->post("siteCode")),
              'employmentStatus' => trim($this->input->post("employmentStatus")),
              'empToken' => trim($this->input->post("empToken")),
              'joiningDate' =>$joiningDate,
			  
			  
			  
              'jobType' => trim($this->input->post("jobType")),
              'salaryGrade' => trim($this->input->post("salaryGrade")),
              'designation' => trim($this->input->post("designation")),
              'reportingTo' => trim($this->input->post("reportingTo")),
                    
              'empBankName' => trim($this->input->post("empBankName")),
              'empBankAccountHolderName' => trim($this->input->post("empBankAccountHolderName")),
              'empBankBranchName' => trim($this->input->post("empBankBranchName")),
              'empBankIfscCode' => trim($this->input->post("empBankIfscCode")),
              'empBankMicrCode' => trim($this->input->post("empBankMicrCode")),
          
               'empBankAccountNo' => trim($this->input->post("empBankAccountNo")),
               'empAdharNumber' => trim($this->input->post("empAdharNumber")),
               'empPanNumber' => trim($this->input->post("empPanNumber")),
               'empPfAccountNumber' => trim($this->input->post("empPfAccountNumber")),
               'empEsiAccountNumber' => trim($this->input->post("empEsiAccountNumber")),
               'editID'=>$addID,
			   'editIP'=>$ip,
			   'isActive' =>$this->input->post("isActive")
            );
			// $emp=$_FILES['empAdharFile']['name'];
            // echo $emp;
			// exit();
        //Array binded data, table name and intial of code 
        $resultData=$this->GlobalModel->doEdit($data,'employeemaster',$code);
		$isActive = $this->input->post("isActive");
		if($isActive==1){
			$this->db->query("update usermaster set isActive=1 where empCode='".$code."'");	
		} else {
			$this->db->query("update usermaster set isActive=0 where empCode='".$code."'");	
		}
	    
         $uploadRootDir = 'uploads/';
         $uploadDir = 'uploads/employees/'.$code;
		 $resultFlag="0";
		 
        if(! file_exists(FCPATH.'uploads/employees/'.$code))
        {
            mkdir(FCPATH.'uploads/employees/'.$code);
        }

		 
		 // For Bank Passbook file upload 
        if (!empty($_FILES['empBankPassbookFile']['name'])) 
        {
          $tmpFile = $_FILES['empBankPassbookFile']['tmp_name'];
           $ext = pathinfo($_FILES['empBankPassbookFile']['name'], PATHINFO_EXTENSION);
          $filename = $uploadDir.'/'.$code.'-BANK_PASS.jpg';
          move_uploaded_file($tmpFile,$filename);
		  
		   if(file_exists($filename))
			  {
				  $empBankPassbookFile=$code.'-BANK_PASS.jpg';
			  }
   			  //Add to Database
				$subPassData = array(	 
					'empBankPassbookFile' => $empBankPassbookFile,

				);
				$this->GlobalModel->doEdit($subPassData,'employeemaster',$code);
				$resultFlag="1";
		  
        }
         // For Adhar Card file upload 
        if (!empty($_FILES['empAdharFile']['name'])) 
        {
          $tmpFile = $_FILES['empAdharFile']['tmp_name'];
           $ext = pathinfo($_FILES['empAdharFile']['name'], PATHINFO_EXTENSION);
          $filename = $uploadDir.'/'.$code.'-ADHAR.jpg';
          move_uploaded_file($tmpFile,$filename);
		   if(file_exists($filename))
			  {
				  $empAdharFile=$code.'-ADHAR.jpg';
			  }
   			  //Add to Database
				$subAdharData = array(	 
					'empAdharFile' => $empAdharFile,

				);
				$this->GlobalModel->doEdit($subAdharData,'employeemaster',$code);
				$resultFlag="1";
        }
		 // For PAN Card file upload 
        if (!empty($_FILES['empPanFile']['name'])) 
        {
          $tmpFile = $_FILES['empPanFile']['tmp_name'];
          $ext = pathinfo($_FILES['empPanFile']['name'], PATHINFO_EXTENSION);
          $filename = $uploadDir.'/'.$code.'-PAN.jpg';
          move_uploaded_file($tmpFile,$filename);
		   if(file_exists($filename))
			  {
				  $empPanFile=$code.'-PAN.jpg';
			  }
   			  //Add to Database
				$subPanData = array(	 
					'empPanFile' => $empPanFile,

				);
				$this->GlobalModel->doEdit($subPanData,'employeemaster',$code);
				$resultFlag="1";
        }
		// For PF Account file upload 
        if (!empty($_FILES['empPfAccountFile']['name'])) 
        {
          $tmpFile = $_FILES['empPfAccountFile']['tmp_name'];
          $ext = pathinfo($_FILES['empPfAccountFile']['name'], PATHINFO_EXTENSION);
          $filename = $uploadDir.'/'.$code.'-PF.jpg';
          move_uploaded_file($tmpFile,$filename);
		  if(file_exists($filename))
			  {
				  $empPfAccountFile=$code.'-PF.jpg';
			  }
   			  //Add to Database
				$subPfData = array(	 
					'empPfAccountFile' => $empPfAccountFile,

				);
				$this->GlobalModel->doEdit($subPfData,'employeemaster',$code);
				$resultFlag="1";
        }
		 // For ESI Account file upload 
        if (!empty($_FILES['empEsiAccountFile']['name'])) 
        {
          $tmpFile = $_FILES['empEsiAccountFile']['tmp_name'];
          $ext = pathinfo($_FILES['empEsiAccountFile']['name'], PATHINFO_EXTENSION);
          $filename = $uploadDir.'/'.$code.'-ESI.jpg';
          move_uploaded_file($tmpFile,$filename);
		  if(file_exists($filename))
			  {
				  $empEsiAccountFile=$code.'-PF.jpg';
			  }
   			  //Add to Database
				$subEsiData = array(	 
					'empEsiAccountFile' => $empEsiAccountFile,

				);
				$this->GlobalModel->doEdit($subEsiData,'employeemaster',$code);
				$resultFlag="1";
        }
	

	        	if($resultData != 'false' || $resultFlag=="1")
				{
	  			$response['status']=true;
				$response['message']="Employee Inforamtion Successfully  Updated.";
				$this->GlobalModel->activityAdd($log_text,'activitymaster','ACT');
				
			echo json_encode($response);
		}
		
		else
			{
				$response['status']=false;
				$response['message']="No Change In Data";
				
			echo json_encode($response);
		
			}
		 }
    }  // End Update
	
	
	
     // public function view()
    // {
        // $code = $this->uri->segment(3); 
        // $data['query'] = $this->GlobalModel->edit('employeemaster',$code);
        // $data['queryOrg'] = $this->GlobalModel->selectEntityInPerticularCategory('ORG');
        // $data['queryJobType'] = $this->GlobalModel->selectDataUser('jobtypemaster');
        // $data['querySalaryGrade'] = $this->GlobalModel->selectDataUser('salarygrademaster');
        // $data['queryDesignation'] = $this->GlobalModel->selectDataUser('designationmaster');
        // $data['queryContract'] = $this->GlobalModel->selectDataUser('contractmaster');
        // $data['queryEmploymentStatus'] = $this->GlobalModel->selectDataUser('employmentstatus');
		// $data['queryemployee'] = $this->GlobalModel->exceptSelfData('employeemaster','code',$code);

        // $entityCode = $data['query']->result()[0]->entityCode;
        // $officeCode = $data['query']->result()[0]->officeCode;

        // $data['office'] = $this->GlobalModel->getAnotherTableDataFromCode('officemaster','entityCode', $entityCode);
        // $data['dept'] = $this->GlobalModel->getAnotherTableDataFromCode('departmentmaster','departmentOffice', $officeCode);

         // $this->load->view('dashboard/header');
        // $this->load->view('dashboard/employee/view');
		// $this->load->view('dashboard/footer');
        
     // }
	 
    public function delete() {
        $code = $this->input->post('code');
		
		// Activity Track Starts
		
		$addID = $this->session->userdata['logged_in'.$this->session_key]['code'];
		$userRole = $this->session->userdata['logged_in'.$this->session_key]['role'];
		$userName = $this->session->userdata['logged_in'.$this->session_key]['username']; 
		$role = "";
		
		switch($userRole){
				case "ADM" : $role="Admin"; break;
				case "USR" : $role="User"; break;
			}
		
		$ip=$_SERVER['REMOTE_ADDR']; 
		$dataQ = $this->GlobalModel->selectDataByField('code',$code,'employeemaster');  
		$empName=''; 
		$fName='';
		$lName='';
		
		foreach ($dataQ->result() as $rowA) 
		{	
			$fName = $rowA->firstName;
			$lName = $rowA->lastName;
			$empName=$fName.' '.$lName; 
		}
		
		$text = $role." ".$userName.' deleted employee "'.$empName.'" from '.$ip; 
		
		$log_text = array(
							'code' => "demo",
							'addID'=>$addID,
							'logText' => $text
						);
		
		$this->GlobalModel->activityAdd($log_text,'activitymaster','ACT');
		
		$data=array(
						'deleteID' => $addID,
						'deleteIP' => $ip
                     );
		  
		$resultData=$this->GlobalModel->doEdit($data,'employeemaster',$code);
		
		// Activity Track Ends
		
       echo $this->GlobalModel->delete($code, 'employeemaster');
       // redirect(base_url() . 'index.php/employee/listRecords', 'refresh');
    }
	
	public function salaryStructureview()
	{
		
	}
	public function view()
	{
		$code = $this->uri->segment(3); 
		
		// Activity Track Starts
		
		$addID = $this->session->userdata['logged_in'.$this->session_key]['code'];
		$userRole = $this->session->userdata['logged_in'.$this->session_key]['role'];
		$userName = $this->session->userdata['logged_in'.$this->session_key]['username']; 
		$role = "";
		
		switch($userRole){
				case "ADM" : $role="Admin"; break;
				case "USR" : $role="User"; break;
			}
		$ip=$_SERVER['REMOTE_ADDR'];
		
		// Activity Track Ends
	 
        $data['query']=$this->GlobalModel->selectDataById($code,'employeemaster');
 
		$tables=array('employeemaster','jobtypemaster','salarygrademaster','designationmaster','departmentmaster');
		//removed ='entities','officemaster','sitemaster','contractmaster'
		$requiredColumns=array
		(
			 array('code','jobType','salaryGrade','designation','deptCode'),//employee table
			 array('jobTypeName','code'),
			 array('salaryGradeName','code'),
			 array('designationName','code'),
			// array('entityName','code'),
			// array('officeName','code'),
			 array('departmentName','code'),
			// array('siteName'),
			// array('contractName')
		);
		
		$conditions=array(
		
		 array('jobType','code'),
		 array('salaryGrade','code'),
		array('designation','code'),
		// array('entityCode','code'),
		// array('officeCode','code'),
		  array('deptCode','code'),
		// array('siteCode','code'),
		// array('contractCode','code')
		);
		$extraConditionColumnNames=array(
			array("code")
		
		);
		
		$extraConditions=array(
			array($code)
		);
		
		//filterbyDate
		$extraDateConditionColumnNames=array(
		);

		$extraDateConditions=array(
		);
		
		//check extra condition like or equal for like set true or equal to set false 
		$likeFlag=false;

        $data['employee'] = $this->GlobalModel1->make_datatables($tables,$requiredColumns,$conditions,$extraConditionColumnNames,$extraConditions,$extraDateConditionColumnNames,$extraDateConditions,$likeFlag);
		
		// For Activity 
		 
		$empName=''; 
		$fName='';
		$lName='';
		
		foreach ($data['query']->result() as $rowA) 
		{	
			$fName = $rowA->firstName;
			$lName = $rowA->lastName;
			$empName=$fName.' '.$lName; 
		}	 	
		
		$text = $role." ".$userName.' viewed employee "'.$empName.'" from '.$ip; 
		
		$log_text = array(
							'code' => "demo",
							'addID'=>$addID,
							'logText' => $text
						);
		
		$this->GlobalModel->activityAdd($log_text,'activitymaster','ACT');
		
		// Activity Track Ends


		
		$this->load->view('dashboard/header');
		$this->load->view('dashboard/employee/view',$data);
		$this->load->view('dashboard/footer');
		
	 }
	 
		public function rshow()
		{//employeeleavelineentries employeemonthlyotpayment empleaves advanceemitransaction salarystructure selectDataExcludeDelete
			//u_modulemaster 'u_submodulemaster' u_submoduleactionmaster userrolemaster
			
			print_r($this->GlobalModel->selectData('userrightsmaster')->result()[0]->emprights);
			//print_r($this->GlobalModel->selectDataById('CON18_105','contractmaster')->result()); selectDataExcludeDelete
		}
		public function pk()
		{
			print_r($this->Testing->ph());
			
		}
		public function test()
		{
			$data=$this->GlobalModel->selectData('employeemaster');
			print_r($data->result());
			
		}
}
?>