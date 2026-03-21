<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SystemAdmin extends CI_Controller {

	
	public function __construct()
    {
        parent::__construct();
         if(!$this->session->userdata('_logged_in_'))
			 return redirect('Authentication/login');
		$this->load->model('globalModel');
		$this->load->model('SqldbModel');
        $this->headerData['modules']=$this->session->userdata('modules');
        $this->headerData['userdata']=$this->session->userdata('_logged_in_');
        $this->getbranchIdFromSession();
        // if(!$this->checkUserURLAuthentication($this->uri->segment(1).'/'.$this->uri->segment(2)))
        // {
        //  	return redirect('Welcome/accessdenied');
        // }
    }

    function getbranchIdFromSession()
    {
       
        $br_data=$this->SqldbModel->selectCenter();
		$this->branchGlobalId=$br_data->result()[0]->Cntr_id;
       
    }

	 function checkUserURLAuthentication($curl)
    {
    	$flag=false;
    	//print_r($curl);
    	$json = json_decode($this->headerData['modules'], true);
       
        if($json['status'])
        {
        	foreach($json['modules'] as $module) 
            {
            	if($module['subStatus']){
	                foreach($module['sub_details'] as $submodule) 
	                {
	           			//print_r($submodule['rightpage']);
	           			if($submodule['rightpage']==$curl)
	           			{
	           				$flag=true;
	           				break;
	           			}
	           		}
           		}
           	}
       	}
       	return $flag;
    }

	//admin

	public function home()
	{
		$data['branches']=$this->SqldbModel->selectCenter();
		$data['branchid']=$this->branchGlobalId;
		$data['hmcompanies']=$this->SqldbModel->getHearingAidCompanies();
		$this->load->view('dashboard/header',$this->headerData);
		$this->load->view('dashboard/dashboard',$data);
	}

	public function Userhome()
	{
		$data['branches']=$this->SqldbModel->selectCenter();
		$data['branchid']=$this->branchGlobalId;
		$data['hmcompanies']=$this->SqldbModel->getHearingAidCompanies();
		$this->load->view('dashboard/header',$this->headerData);
		$this->load->view('dashboard/dashboarduser',$data);
	}

	public function profile()
	{
		$user_code=$this->session->userdata('_logged_in_')['code'];
		$data['user']=$this->globalModel->read_user_information($user_code);
		$data['updatestatus']=$this->session->flashdata('profile');
		$this->load->view('dashboard/header',$this->headerData);
		$this->load->view('dashboard/profile',$data);
	}

	public function accessoriesstock()
	{
		$fromdate=$this->input->get('fromdate');
		$todate=$this->input->get('todate');
		$branch=$this->input->get('id');
		if($branch=='')
		{
			$branch=$this->branchGlobalId;
		}
		//$data['query']=$this->SqldbModel->selectActiveData('tbl_Center_master');
		$data['branchid']=$branch;
		$data['fromdate']=$fromdate;
		$data['todate']=$todate;
		$data['query']=$this->SqldbModel->getallAccessoriesbyBranch($branch,$fromdate,$todate);
		$data['branches']=$this->SqldbModel->selectCenter();
		
		//print_r($data);
		$this->load->view('dashboard/header',$this->headerData);
		$this->load->view('dashboard/accessoriesstock',$data);
	}

	public function accessoriesstockGraph()
	{
		$id=$this->input->get('id');

		$GraphData=$this->SqldbModel->selectDataByCenterId('tbl_h_ac_master',$id);
		echo json_encode($GraphData->result());
	}

	public function accessoriesSales()
	{
		$selectedby=$this->input->get('selectid');
		if($selectedby=='')
		$data['selectedby']="byaccessories";
		else
		$data['selectedby']=$this->input->get('selectid');

		$data['branches']=$this->SqldbModel->selectCenter();

		$branch=$this->input->get('id');
		
		if($branch=='')
		{
			$branch=$this->branchGlobalId;
		}

		$data['branchid']=$branch;
		
		if($data['selectedby']=='byaccessories')
		$data['accessoriessales']=$this->SqldbModel->selectAccesseriesSaleByCenter($branch);
		else
		$data['accessoriessales']=$this->SqldbModel->selectAccesseriesSaleByPatient($branch);

		$this->load->view('dashboard/header',$this->headerData);
		$this->load->view('dashboard/accessoriessales',$data);	

	}

	public function accessoriesSalesGraph()
	{
		$id=$this->input->get('id');
		$GraphData=$this->SqldbModel->selectAccesseriesSaleByCenter($id);
		echo json_encode($GraphData->result());
	}

	// Patient Controller
	public function PatientWizard()
	{
		$branch=$this->input->get('id');
		if($branch=='')
		{
			$branch=$this->branchGlobalId;
		}
		$data['branchid']=$branch;
		
		$data['branches']=$this->SqldbModel->selectCenter();
		
		//print_r($data);
		$this->load->view('dashboard/header',$this->headerData);
		$this->load->view('dashboard/patientwizard',$data);
	}

	public function getPatientWizardData()
	{
		$id=$this->input->get('id');
		$data=$this->SqldbModel->selectPatientByCenter($id);
		echo json_encode($data->result());
	}


	public function patientGraph()
	{
		$id=$this->input->get('id');
		$GraphData=$this->SqldbModel->selectPatientByTypeandCenter($id);
		echo $GraphData;
	}

	public function patientmalefemaleGraph()
	{
		$id=$this->input->get('id');
		$Gender=$this->SqldbModel->getmalefemalebyBranch($id);
		echo json_encode($Gender->result());
	}

	public function patientTotalCountByBranch(){
		$branch=$this->input->get('id');
		$salecount=$this->SqldbModel->getTotalSaleUserCountByCenter($branch);
		echo json_encode($salecount->result());
	}

	public function PatientAnalysis()
	{
		$branch=$this->branchGlobalId;		
		$data['branchid']=$branch;
		$data['patienttypes']=$this->SqldbModel->selectActiveData('Ptntype');
		// $data['patientlist']=$this->SqldbModel->selectPatientByConditions($branch,$patientTypeId,$gender,$startdate,$enddate);
		$data['branches']=$this->SqldbModel->selectCenter();

		//print_r($data);
		$this->load->view('dashboard/header',$this->headerData);
		$this->load->view('dashboard/patientanalysis',$data);
	}

	function getPatientAnalysisDataByCondition()
	{
		$branch=$this->input->get('id');
		$patientTypeId=$this->input->get('ptyp');
		$gender=$this->input->get('gender');

		$startdate=$this->input->get('startdate');
		$enddate=$this->input->get('enddate');

		$startage=$this->input->get('startage');
		$endage=$this->input->get('endage');
		if($branch=='')
		{
			$branch=$this->branchGlobalId;
		}

		$patientlist=$this->SqldbModel->selectPatientByConditions($branch,$patientTypeId,$gender,$startdate,$enddate,$startage,$endage);
		echo json_encode($patientlist->result());
	}


	//petty cash
	public function PettyCashWizard()
	{
		$branch=$this->input->get('id');
		if($branch=='')
		{
			$branch=$this->branchGlobalId;
		}
		$data['branchid']=$branch;
		
		$data['branches']=$this->SqldbModel->selectCenter();
		
		$this->load->view('dashboard/header',$this->headerData);
		$this->load->view('dashboard/pettycashbilling',$data);
	}

	public function getPettyCashDataByCondition()
	{
		$c_id=$this->input->get('id');
        $startdate=$this->input->get('startdate');
        $enddate=$this->input->get('enddate');

		$data['PittyCashData']=$this->SqldbModel->getPettyCashDatabyCondition($c_id,$startdate,$enddate)->result();
		echo json_encode($data);
	}
	public function getPettyCashTotalBranchCount()
	{
		$c_id=$this->input->get('id');
		$data=$this->SqldbModel->getPettyCashTotalBranchCount($c_id);
		echo json_encode($data->result());
	}

	//other
	//start mould
	public function mouldWizard()
	{
		$branch=$this->input->get('id');
		if($branch=='')
		{
			$branch=$this->branchGlobalId;
		}
		$data['branchid']=$branch;
		
		$data['branches']=$this->SqldbModel->selectCenter();
		
		//print_r($data);
		$this->load->view('dashboard/header',$this->headerData);
		$this->load->view('dashboard/mouldstatus',$data);
		
	}

	public function getMouldData()
	{
		$branch=$this->input->get('id');
		$data=$this->SqldbModel->getMouldByCenter($branch);
		echo json_encode($data->result());
	}
	//end nould

	//start inward
	public function inwardWizard()
	{
		$branch=$this->input->get('id');
		if($branch=='')
		{
			$branch=$this->branchGlobalId;
		}
		$data['branchid']=$branch;
		
		$data['branches']=$this->SqldbModel->selectCenter();
		
		//print_r($data);
		$this->load->view('dashboard/header',$this->headerData);
		$this->load->view('dashboard/inward',$data);
		
	}

	function getInwardData()
	{
		$branch=$this->input->get('id');

		$startdate=$this->input->get('startdate');
        $enddate=$this->input->get('enddate');
		
		$data=$this->SqldbModel->getInwardbyCondition($branch,$startdate,$enddate);
		echo json_encode($data->result());	
	}

	//end inward

	//start outward
	public function outwardWizard()
	{
		$branch=$this->input->get('id');
		if($branch=='')
		{
			$branch=$this->branchGlobalId;
		}
		$data['branchid']=$branch;
		
		$data['branches']=$this->SqldbModel->selectCenter();
		
		//print_r($data);
		$this->load->view('dashboard/header',$this->headerData);
		$this->load->view('dashboard/outward',$data);
		
	}

	function getOutwardData()
	{
		$branch=$this->input->get('id');

		$startdate=$this->input->get('startdate');
        $enddate=$this->input->get('enddate');
		
		$data=$this->SqldbModel->getOutwardbyCondition($branch,$startdate,$enddate);
		echo json_encode($data->result());	
	}

	//end outward

	//start doctor
	public function doctorWizard()
	{
		$branch=$this->input->get('id');
		if($branch=='')
		{
			$branch=$this->branchGlobalId;
		}
		$data['branchid']=$branch;
		
		$data['branches']=$this->SqldbModel->selectCenter();
		
		//print_r($data);
		$this->load->view('dashboard/header',$this->headerData);
		$this->load->view('dashboard/doctorwizard',$data);
		
	}

	function getDoctorData()
	{
		$branch=$this->input->get('id');

		$startdate=$this->input->get('startdate');
        $enddate=$this->input->get('enddate');
		
		$data=$this->SqldbModel->getDoctorbyCondition($branch,$startdate,$enddate);
		echo json_encode($data->result());	
	}

	//end doctor

	//start doctor
	public function enquiryWizard()
	{
		$branch=$this->input->get('id');
		if($branch=='')
		{
			$branch=$this->branchGlobalId;
		}
		$data['branchid']=$branch;
		
		$data['branches']=$this->SqldbModel->selectCenter();
		
		//print_r($data);
		$this->load->view('dashboard/header',$this->headerData);
		$this->load->view('dashboard/enquiry',$data);
	}

	function getEnquiryData()
	{
		$branch=$this->input->get('id');

		$startdate=$this->input->get('startdate');
        $enddate=$this->input->get('enddate');
		
		$data=$this->SqldbModel->getDashboardEnquiryDatabyDate($branch,$startdate,$enddate);
		echo json_encode($data->result());	
	}

	//end doctor
}