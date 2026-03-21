<?php
defined('BASEPATH') or exit('No direct script access allowed');
class City extends CI_Controller
{
    var $session_key;
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('form', 'url', 'html');
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
        $data['error'] = $this->session->flashdata('response');
        $data['query'] = $this->GlobalModel->selectDataExcludeDelete('citymaster');
        $this->load->view('dashboard/header');
        $this->load->view('dashboard/city/list', $data);
        $this->load->view('dashboard/footer');
    }

    public function add()
    {
        $data['currency'] = $this->GlobalModel->selectData('currencymaster');
        $this->load->view('dashboard/header');
        $this->load->view('dashboard/city/add', $data);
        $this->load->view('dashboard/footer');
    }

    public function edit()
    {
        $code = $this->uri->segment(3);
        $table_name = 'citymaster';
        $orderColumns = array("citymaster.*");
        $cond = array('citymaster' . '.code' => $code);
        $data['currency'] = $this->GlobalModel->selectData('currencymaster');
        $data['query'] = $this->GlobalModel->selectQuery($orderColumns, $table_name, $cond);
        $this->load->view('dashboard/header');
        $this->load->view('dashboard/city/edit', $data);
        $this->load->view('dashboard/footer');
    }

    public function getCityList()
    {
        $tableName = 'citymaster';
        $orderColumns = array("citymaster.*");
        $search = $this->input->GET("search")['value'];
        $condition = array();
        $orderBy = array('citymaster.id' => 'desc');
        $joinType = array();
        $join = array();
        $groupByColumn = array();
        $limit = $this->input->GET("length");
        $offset = $this->input->GET("start");
        $srno = $offset + 1;
        $like = array();
        $extraCondition = " (citymaster.isDelete=0 or citymaster.isDelete is null)";
        $like = array("citymaster.code" => $search . "~both","citymaster.cityName" => $search . "~both");
        $data = array();
        $dataCount = 0;
        $Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
        //echo  $this->db->last_query();
        if ($Records) {
            foreach ($Records->result() as $row) {
                if ($row->isActive == "1") {
                    $status = " <span class='label label-sm label-success'>Active</span>";
                } else {
                    $status = " <span class='label label-sm label-warning'>Inactive</span>";
                }
                $actionHtml = '<div class="btn-group">
                                <button type="button" class="btn btn-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="ti-settings"></i>
                                </button>
                                <div class="dropdown-menu animated slideInUp" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 35px, 0px);">
                                    <a class="dropdown-item  blue" data-toggle="modal" data-target="#responsive-modal" data-seq="' . $row->code . '" href><i class="ti-eye"></i> Open</a>
                                    <a class="dropdown-item" href="' . base_url() . 'index.php/City/edit/' . $row->code . '"><i class="ti-pencil-alt"></i> Edit</a>
                                    <a class="dropdown-item mywarning " data-seq="' . $row->code . '" id="' . $row->code . '"><i class="ti-trash" ></i> Delete</a>
                                </div>
                            </div>';
                $data[] = array($srno, $row->code, $row->cityName, $status, /*$row->minOrder, $row->deliveryCharge,*/ $actionHtml);
                $srno++;
            }
            $dataCount = sizeof($this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, "", "", $groupByColumn, $extraCondition)->result());
        }
        $output = array("draw" => intval($_GET["draw"]), "recordsTotal" => $dataCount, "recordsFiltered" => $dataCount, "data" => $data);
        echo json_encode($output);
    }

    public function save()
    {
        $cityName = strtoupper($this->input->post("cityName"));
        //Activity Track Starts
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
        $text = $role . " " . $userName . ' added new City "' . $cityName . '" from ' . $ip;
        $log_text = array('code' => "demo", 'addID' => $addID, 'logText' => $text);
        //Activity Track Ends
        $result = $this->GlobalModel->checkDuplicateRecord('cityName', $cityName, 'citymaster');
        if ($result != FALSE) {
            $data = array('error_message' => 'Duplicate City Name');
            $data['currency'] = $this->GlobalModel->selectData('currencymaster');
            $this->load->view('dashboard/header');
            $this->load->view('dashboard/city/add', $data);
            $this->load->view('dashboard/footer');
        } else {
            $this->form_validation->set_rules('cityName', 'City Name', 'trim|required|min_length[2]|max_length[50]');
            //$this->form_validation->set_rules('minOrder', 'Minimum Order', 'trim|required|numeric');
            //$this->form_validation->set_rules('deliveryCharge', 'City Name', 'trim|required|numeric');
            //$this->form_validation->set_rules('minOrderCurrency', 'Min. Order Currency', 'trim|required');
            //$this->form_validation->set_rules('deliveryChargeCurrency', 'Delivery Currency', 'trim|required');
            $this->form_validation->set_rules('latitude', 'GPS Latitude', 'trim|required|numeric');
            $this->form_validation->set_rules('longitude', 'GPS Longitude', 'trim|required|numeric');
            if ($this->form_validation->run() == FALSE) {
                $data['error_message'] = '* Fields are Required!';
                $data['currency'] = $this->GlobalModel->selectData('currencymaster');
                $this->load->view('dashboard/header');
                $this->load->view('dashboard/city/add', $data);
                $this->load->view('dashboard/footer');
            } else {
                $data = array('cityName' => trim($this->input->post('cityName')), 'addID' => $addID, 'addIP' => $ip, 'isActive' => trim($this->input->post("isActive")));
                //$data['minOrder'] = trim($this->input->post('minOrder'));
                //$data['deliveryCharge'] = trim($this->input->post('deliveryCharge'));
                $//data['minOrderCurrency'] = trim($this->input->post('minOrderCurrency'));
                //$data['deliveryChargeCurrency'] = trim($this->input->post('deliveryChargeCurrency'));
                //$data['deliveryChargesPerKm'] = trim($this->input->post('deliveryChargesPerKm'));
                //$data['minFreeDeliveryKm'] = trim($this->input->post('minFreeDeliveryKm'));
                $data['latitude'] = trim($this->input->post('latitude'));
                $data['longitude'] = trim($this->input->post('longitude'));
                $result = $this->GlobalModel->addWithoutYear($data, 'citymaster', 'CTY');
                if ($result != 'false') {
                    $response['status'] = true;
                    $response['message'] = "City Successfully Added.";
                    $this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
                } else {
                    $response['status'] = false;
                    $response['message'] = "Failed To Add City";
                }
                // print_r($response);
                $this->session->set_flashdata('response', json_encode($response));
                redirect(base_url() . 'city/listRecords');
            }
        }
    }
    public function update()
    {
        $code = $this->input->post('code');
        $cityName = trim($this->input->post('cityName'));
        //Activity Track Starts
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
        $text = $role . " " . $userName . ' updated City "' . $cityName . '" from ' . $ip;
        $log_text = array('code' => "demo", 'addID' => $addID, 'logText' => $text);
        //Activity Track Ends
		
		$condition2 = array('cityName' =>$cityName, 'code!=' => $code);
		$result = $this->GlobalModel->checkDuplicateRecordNew($condition2, 'citymaster');
		if ($result == 1) {
			$data['error_message'] = 'Duplicate City Name';
			$table_name = 'citymaster';
			$orderColumns = array("citymaster.*");
			$cond = array('citymaster' . '.code' => $code);
			$data['currency'] = $this->GlobalModel->selectData('currencymaster');
			$data['query'] = $this->GlobalModel->selectQuery($orderColumns, $table_name, $cond);
			$this->load->view('dashboard/header');
			$this->load->view('dashboard/city/edit', $data);
			$this->load->view('dashboard/footer');
		}else{
		
			$this->form_validation->set_rules('cityName', 'City Name', 'trim|required|min_length[2]|max_length[50]');
			//$this->form_validation->set_rules('minOrder', 'Minimum Order', 'trim|required|numeric');
			//$this->form_validation->set_rules('deliveryCharge', 'City Name', 'trim|required|numeric');
			//$this->form_validation->set_rules('minOrderCurrency', 'Min. Order Currency', 'trim|required');
			//$this->form_validation->set_rules('deliveryChargeCurrency', 'Delivery Currency', 'trim|required');
			$this->form_validation->set_rules('latitude', 'GPS Latitude', 'trim|required|numeric');
			$this->form_validation->set_rules('longitude', 'GPS Longitude', 'trim|required|numeric');
			if ($this->form_validation->run() == FALSE) {
				$data['error_message'] = '* Fields are Required!';
				$table_name = 'citymaster';
				$orderColumns = array("citymaster.*");
				$cond = array('citymaster' . '.code' => $code);
				$data['currency'] = $this->GlobalModel->selectData('currencymaster');
				$data['query'] = $this->GlobalModel->selectQuery($orderColumns, $table_name, $cond);
				$this->load->view('dashboard/header');
				$this->load->view('dashboard/city/edit', $data);
				$this->load->view('dashboard/footer');
			} else {
				$data = array(
					'cityName' => $cityName,
					'editID' => $addID,
					'editIP' => $ip,
					'isActive' => trim($this->input->post("isActive"))
				);
				//$data['minOrder'] = trim($this->input->post('minOrder'));
				//$data['deliveryCharge'] = trim($this->input->post('deliveryCharge'));
				//$data['minOrderCurrency'] = trim($this->input->post('minOrderCurrency'));
				//$data['deliveryChargeCurrency'] = trim($this->input->post('deliveryChargeCurrency'));
				//$data['deliveryChargesPerKm'] = trim($this->input->post('deliveryChargesPerKm'));
				//$data['minFreeDeliveryKm'] = trim($this->input->post('minFreeDeliveryKm'));
				$data['latitude'] = trim($this->input->post('latitude'));
				$data['longitude'] = trim($this->input->post('longitude'));
				$result = $this->GlobalModel->doEdit($data, 'citymaster', $code);
				if ($result != 'false') {
					$response['status'] = true;
					$response['message'] = "City Successfully Updated.";
					$this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
				} else {
					$response['status'] = false;
					$response['message'] = "No change In City";
				}
				$this->session->set_flashdata('response', json_encode($response));
				redirect(base_url() . 'city/listRecords');
			}
		}
    }
    public function delete()
    {
        $code = $this->input->post('code');
        //Activity Track Starts
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
        $dataQ = $this->GlobalModel->selectDataByField('code', $code, 'citymaster');
        $cityName = '';
        foreach ($dataQ->result() as $row) {
            $cityName = $row->cityName;
        }
        $text = $role . " " . $userName . ' deleted City "' . $cityName . '" from ' . $ip;
        $log_text = array('code' => "demo", 'addID' => $addID, 'logText' => $text);
        $this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
        $data = array('deleteID' => $addID, 'deleteIP' => $ip);
        $resultData = $this->GlobalModel->doEdit($data, 'citymaster', $code);
        //Activity Track Ends
        echo $this->GlobalModel->delete($code, 'citymaster');
        // redirect(base_url() . 'index.php/City/listrecords', 'refresh');

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
        $table_name = 'citymaster';
        $orderColumns = array("citymaster.*");
        $cond = array('citymaster' . ".code" => $code);
        $like = array();
        $orderBy = array();
        $joinType = array();
        $join = array();
        $Records = $this->GlobalModel->selectQuery($orderColumns, $table_name, $cond, $orderBy, $join, $joinType, $like);
        $modelHtml = '<form>';
        $activeStatus = "";
        foreach ($Records->result() as $row) {
            if ($row->isActive == "1") {
                $activeStatus = '<span class="label label-sm label-success">Active</span>';
            } else {
                $activeStatus = '<span class="label label-sm label-warning">Inactive</span>';
            }
            $modelHtml .= '<div class="form-row">
                            <div class="col-md-12 mb-3">
                                <label><b>Code:</b> </label>
						        <input type="text" value="' . $row->code . '" class="form-control-line"  readonly>
						    </div>
					        <div class="col-md-12 mb-3">
					            <label><b> City Name:</b> </label>
						        <input type="text" class="form-control-line" value="' . $row->cityName . '"  readonly>
						    </div>
					        <div class="col-md-12 mb-3 d-none">
					            <label><b>Min. Order:</b> </label>
					            <input type="text" value="' . $row->minOrder . '" class="form-control-line"  readonly>
					        </div>
						    <div class="col-md-12 mb-3 d-none">
						        <label><b>Order Currency:</b> </label>
						        <input type="text" value="' . $row->minOrderCurrency . '" class="form-control-line"  readonly>
						    </div>
						    <div class="col-md-12 mb-3 d-none">
						        <label><b>Delivery Charge:</b> </label>
						        <input type="text" value="' . $row->deliveryCharge . '" class="form-control-line"  readonly>
						    </div>
						    <div class="col-md-12 mb-3 d-none">
						        <label><b>Del. Charge Currency:</b> </label>
						        <input type="text" value="' . $row->deliveryChargeCurrency . '" class="form-control-line"  readonly>
						    </div>
						   </div> 
					        <div class="form-group">' . $activeStatus . '</div>';
            //for activity
            $text = $role . " " . $userName . ' viewed City "' . $row->cityName . '" from ' . $ip;
            $log_text = array('code' => "demo", 'addID' => $addID, 'logText' => $text);
            $this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
            //Activity Track Ends

        }
        $modelHtml .= '</form>';
        echo $modelHtml;
    }
}
