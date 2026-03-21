<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Kolkata');
class Chargessettings extends CI_Controller {
    var $privilege;
    public function __construct() {
        parent::__construct();
		$this->load->helper('form','url','html');
      	$this->load->library('form_validation');
        $this->load->model('GlobalModel'); 
		$this->load->model('GlobalModel1');
		
		$this->session_key = $this->session->userdata('key' . SESS_KEY);
        if (!isset($this->session->userdata['logged_in' . $this->session_key]['code'])) {
            redirect('Admin/login', 'refresh');
        }
	}
	
	public function listRecords()
    {
		$data['error']=$this->session->flashdata('response');
    	$data['city'] = $this->GlobalModel->selectActiveData('citymaster'); 
    	$this->load->view('dashboard/header');
    	$this->load->view('dashboard/chargessettings/list',$data);
    	$this->load->view('dashboard/footer');
    	
    }
	
	public function getChargeList()
	{
		$cityCode = $this->input->get('cityCode');
		$tableName = "deliverycomissionandcharges";
		$search = $this->input->GET("search")['value'];
		$orderColumns = array("deliverycomissionandcharges.*,citymaster.cityName");
		$condition = array('deliverycomissionandcharges.cityCode' => $cityCode);
		$orderBy = array();
		$joinType = array('citymaster' => 'inner');
		$join = array('citymaster' => 'citymaster.code=deliverycomissionandcharges.cityCode');
		$groupByColumn = array();
		$limit = $this->input->GET("length");
		$offset = $this->input->GET("start");
		$extraCondition = "";  
		$like = array("citymaster.cityName" => $search . "~both","deliverycomissionandcharges.forWhichService" => $search . "~both");
		$Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
		//echo $this->db->last_query();
		$srno = $_GET['start'] + 1;
		if ($Records) {
			foreach ($Records->result() as $row) {
				$code = $row->code;

				if ($row->isActive == 1) {
					$status = "<span class='label label-sm label-success'>Active</span>";
				} else {
					$status = "<span class='label label-sm label-warning'>Inactive</span>";
				}

				$actionHtml = '<div class="btn-group">
								<button type="button" class="btn btn-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									<i class="ti-settings"></i>
								</button>
								<div class="dropdown-menu animated slideInUp" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 35px, 0px);">
									<a class="dropdown-item  blue" data-toggle="modal" data-target="#responsive-modal" data-seq="' . $row->code . '" href><i class="ti-eye"></i> Open</a>
									<a class="dropdown-item" href="' . base_url() . 'chargessettings/edit/' . $row->code . '"><i class="ti-pencil-alt"></i> Edit</a>
								</div>
							</div>';

				$data[] = array(
					$srno,
					$row->code,
					$row->cityName,
					ucwords(str_replace('_',' ', $row->forWhichService)),
					$status,
					$actionHtml
				);
				$srno++;
			}
			$dataCount = sizeof($this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, array(), '', '', '', $extraCondition)->result());
			$output = array(
				"draw"			  =>     intval($_GET["draw"]),
				"recordsTotal"    =>      $dataCount,
				"recordsFiltered" =>     $dataCount,
				"data"            =>     $data
			);
			echo json_encode($output);
		} else {
			$dataCount = 0;
			$data = array();
			$output = array(
				"draw"			  =>     intval($_GET["draw"]),
				"recordsTotal"    =>     $dataCount,
				"recordsFiltered" =>     $dataCount,
				"data"            =>     $data
			);
			echo json_encode($output);
		}
	}
	
	public function edit($code=null) {
		$code = $this->uri->segment(3);
		
		$table_name = 'deliverycomissionandcharges';
        $orderColumns = array("deliverycomissionandcharges.*,citymaster.cityName");
        $cond = array('deliverycomissionandcharges' . '.code' => $code);
		$like = array();
        $orderBy = array();
        $joinType = array('citymaster' => 'inner');
		$join = array('citymaster' => 'citymaster.code=deliverycomissionandcharges.cityCode');
		$data['query'] = $this->GlobalModel->selectQuery($orderColumns, $table_name, $cond,$orderBy, $join, $joinType, $like);
		//$data['city'] = $this->GlobalModel->selectActiveData('citymaster'); 
	    //$data['query'] = $this->GlobalModel->selectDataById($code, 'deliverycomissionandcharges');
        $this->load->view('dashboard/header');
        $this->load->view('dashboard/chargessettings/edit', $data);
        $this->load->view('dashboard/footer');
    }
	
	public function update(){
		
		$code = $this->input->post("code");
        $cityName = $this->input->post("cityName"); 
        $service = $this->input->post("service");
        $isFixedCharges = $this->input->post("isFixedCharges");
        $minOrderAmountForFixedCharge = $this->input->post("minOrderAmountForFixedCharge");
		$fixedChargesOrCommission = $this->input->post("fixedChargesOrCommission");
		$minimumKmForFixedCharges = $this->input->post("minimumKmForFixedCharges");
		$minimumChargesForFixedKm = $this->input->post("minimumChargesForFixedKm");
		$perKmCharges= $this->input->post("perKmCharges");
		$active=$this->input->post("isActive");
		
        //Activity Track Starts
        $statusFlag = 0;
        $addID = $this->session->userdata['logged_in' . $this->session_key]['code'];
        $userRole = $this->session->userdata['logged_in' . $this->session_key]['role'];
        $userA = $this->session->userdata['logged_in' . $this->session_key]['username'];
        $role = "";
        switch ($userRole) {
            case "ADM":
                $role = "Admin";
            break;
            case "USR":
                $role = "User";
            break;
        }
        $ip = $_SERVER['REMOTE_ADDR'];
        $text = $role . " " . $userA . ' updated Delivery commission and charges "' . $service . '" from ' . $ip;
        $log_text = array('code' => "demo", 'addID' => $addID, 'logText' => $text);
        //Activity Track Ends
	
		$this->form_validation->set_rules('isFixedCharges', 'Is Fixed Charges', 'required');
		if($isFixedCharges==1){
			$this->form_validation->set_rules('minOrderAmountForFixedCharge', 'Minimum Order Amount For Fixed Charge', 'required');
			$this->form_validation->set_rules('fixedChargesOrCommission', 'Fixed Charges Or Commission', 'required');
		}
		if($isFixedCharges==0){
			$this->form_validation->set_rules('minimumKmForFixedCharges', 'Minimum Km For Fixed Charges', 'required');
			$this->form_validation->set_rules('minimumChargesForFixedKm', 'Minimum Charges For Fixed Km', 'required');
			$this->form_validation->set_rules('perKmCharges', 'Per Km Charges', 'required');
		}
		if ($this->form_validation->run() == FALSE) {
            $data['error_message'] = '* Fields are Required!';
			$data['city'] = $this->GlobalModel->selectActiveData('citymaster'); 
			$data['query'] = $this->GlobalModel->selectDataById($code, 'deliverycomissionandcharges');
			$this->load->view('dashboard/header');
			$this->load->view('dashboard/chargessettings/edit', $data);
			$this->load->view('dashboard/footer');
		}else{
			$data = array(
					'isFixedChargesFlag'=>$isFixedCharges,
					'fixedChargesOrCommission'=>$fixedChargesOrCommission,
					'minOrderAmountForFixedCharge'=>$this->input->post("minOrderAmountForFixedCharge"),
					'minimumKmForFixedCharges'=>$this->input->post("minimumKmForFixedCharges"),
					'minimumChargesForFixedKm'=>$this->input->post("minimumChargesForFixedKm"),
					'perKmCharges'=>$this->input->post("perKmCharges"),
					'editID' => $addID,          
					'editIP' => $ip,
					'isActive' => $this->input->post("isActive"),
				);
           
			$result = $this->GlobalModel->doEdit($data, 'deliverycomissionandcharges', $code);
		    if ($result != 'false') {
				$response['status'] = true;
				$response['message'] = "Delivery commission and charges Successfully Updated.";
				$this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
			} else {
				$response['status'] = false;
				$response['message'] = "No change In Delivery commission and charges";
			}
			$this->session->set_flashdata('response', json_encode($response));
			redirect(base_url() . 'chargessettings/listRecords', 'refresh');
		
		}
	}
	
	 public function view()
    {
        $code = $this->input->get('code');
        $addID = $this->session->userdata['logged_in' . $this->session_key]['code'];
        $userRole = $this->session->userdata['logged_in' . $this->session_key]['role'];
        $userName = $this->session->userdata['logged_in' . $this->session_key]['username'];
        $role = "";
        switch ($userRole) {
            case "ADM":
                $role = "Admin";
                break;
            case "USR":
                $role = "User";
                break;
        }
        $ip = $_SERVER['REMOTE_ADDR'];
        $table_name = 'deliverycomissionandcharges';
        $orderColumns = array("deliverycomissionandcharges.*,citymaster.cityName");
        $cond = array('deliverycomissionandcharges' . ".code" => $code);
        $like = array();
        $orderBy = array();
        $joinType = array('citymaster' => 'inner');
		$join = array('citymaster' => 'citymaster.code=deliverycomissionandcharges.cityCode');
        $Records = $this->GlobalModel->selectQuery($orderColumns, $table_name, $cond, $orderBy, $join, $joinType, $like);
        $modelHtml = '<form>';
        $activeStatus = "";
        foreach ($Records->result() as $row) {
			if($row->isFixedChargesFlag == "1"){
				$checkStatus="Yes";
			}else{
				$checkStatus="No";
			}
			
            if ($row->isActive == "1") {
                $activeStatus = '<span class="label label-sm label-success">Active</span>';
            } else {
                $activeStatus = '<span class="label label-sm label-warning">Inactive</span>';
            }
            $modelHtml .= '<div class="form-row">
                            <div class="col-md-6 mb-3">
                                <label><b>Code:</b> </label>
						        <input type="text" value="' . $row->code . '" class="form-control-line"  readonly>
						    </div>
					        <div class="col-md-6 mb-3">
					            <label><b> City Name:</b> </label>
						        <input type="text" class="form-control-line" value="' . $row->cityName . '"  readonly>
						    </div>
							<div class="col-md-7 mb-3">
								<label><b>Is Fixed Charges:</b></label>
								'.$checkStatus.'
							</div>';
				if($row->isFixedChargesFlag == "1"){
				$modelHtml .= '<div class="col-md-12 mb-3">
					            <label><b>Minimum Order Amount For Fixed Charge:</b> </label>
					            <input type="text" value="' . $row->minOrderAmountForFixedCharge . '" class="form-control-line"  readonly>
					        </div>
						    <div class="col-md-6 mb-3">
						        <label><b>Fixed Charges or Commission:</b> </label>
						        <input type="text" value="' . $row->fixedChargesOrCommission . '" class="form-control-line"  readonly>
						    </div>';
				}
                if($row->isFixedChargesFlag == "0"){				
				$modelHtml .= '<div class="col-md-6 mb-3">
						        <label><b>Minimum Km for Fixed Charges:</b> </label>
						        <input type="text" value="' . $row->minimumKmForFixedCharges . '" class="form-control-line"  readonly>
						    </div>
							 <div class="col-md-6 mb-3">
						        <label><b>Minimum Charges for Fixed Km:</b> </label>
						        <input type="text" value="' . $row->minimumChargesForFixedKm . '" class="form-control-line"  readonly>
						    </div>
							<div class="col-md-6 mb-3">
						        <label><b>Per Km Charges:</b> </label>
						        <input type="text" value="' . $row->perKmCharges . '" class="form-control-line"  readonly>
						    </div>';
				}
				$modelHtml .= '</div>
					        <div class="form-group">' . $activeStatus . '</div>';
            //for activity
            $text = $role . " " . $userName . ' viewed delivery commission and charges "' . $row->code . '" from ' . $ip;
            $log_text = array('code' => "demo", 'addID' => $addID, 'logText' => $text);
            $this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
            //Activity Track Ends

        }
        $modelHtml .= '</form>';
        echo $modelHtml;
    }
}