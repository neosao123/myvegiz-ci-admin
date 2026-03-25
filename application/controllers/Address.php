<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Address extends CI_Controller
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
        $data['city'] = $this->GlobalModel->selectDistinctData('cityName', 'citymaster');
        $data['place'] = $this->GlobalModel->selectDistinctData('place', 'customaddressmaster');
        $data['state'] = $this->GlobalModel->selectDistinctData('state', 'customaddressmaster');
        $data['district'] = $this->GlobalModel->selectDistinctData('district', 'customaddressmaster');
        $this->load->view('dashboard/header');
        $this->load->view('dashboard/address/list', $data);
        $this->load->view('dashboard/footer');
    }
    public function add()
    {
        $data['city'] = $this->GlobalModel->selectActiveData('citymaster');
        $this->load->view('dashboard/header');
        $this->load->view('dashboard/address/add', $data);
        $this->load->view('dashboard/footer');
    }
    public function edit()
    {
        $code = $this->uri->segment(3);
        $data['city'] = $this->GlobalModel->selectActiveData('citymaster');
        $table_name = 'customaddressmaster';
        $orderColumns = array("customaddressmaster.*");
        $cond = array('customaddressmaster' . '.code' => $code);
        $data['query'] = $this->GlobalModel->selectQuery($orderColumns, $table_name, $cond);
        $this->load->view('dashboard/header');
        $this->load->view('dashboard/address/edit', $data);
        $this->load->view('dashboard/footer');
    }
    public function getAddrList()
    {
        $state = $this->input->post('state');
        $dist = $this->input->post('district');
        $place = $this->input->post('place');
        $role = $this->input->post('service');
        $cityCode = $this->input->post('cityCode');
        $tableName = "customaddressmaster";
        $search = $this->input->post("search")['value'] ?? null;
        $orderColumns = array("customaddressmaster.*,citymaster.cityName");
        $condition = array('customaddressmaster.place' => $place, 'customaddressmaster.state' => $state, 'customaddressmaster.isService' => $role, 'customaddressmaster.district' => $dist, 'citymaster.cityName' => $cityCode);
        $orderBy = array('customaddressmaster' . '.id' => 'DESC');
        $joinType = array('citymaster' => 'left');
        $join = array('citymaster' => 'customaddressmaster.cityCode=citymaster.code');
        $groupByColumn = array();
        $limit = $this->input->post("length");
        $offset = $this->input->post("start");
        $extraCondition = " (customaddressmaster.isDelete ='0' or customaddressmaster.isDelete IS NULL) ";
        $like = array('customaddressmaster.code' => $search . '~both', 'customaddressmaster.place' => $search . '~both', 'customaddressmaster.state' => $search . '~both', 'customaddressmaster.district' => $search . '~both', 'customaddressmaster.pincode' => $search . '~both');
        $Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
        $srno = intval($offset) + 1;
        $data = array();
        if ($Records) {
            foreach ($Records->result() as $row) {
                if ($row->isService == "1") {
                    $service = " <span class='label label-sm label-success'>Service Available</span>";
                }
                else {
                    $service = " <span class='label label-sm label-warning'>Service Unavailable</span>";
                }
                if ($row->isActive == "1") {
                    $status = " <span class='label label-sm label-success'>Active</span>";
                }
                else {
                    $status = " <span class='label label-sm label-warning'>Inactive</span>";
                }
                $actionHtml = '<div class="btn-group">
					<button type="button" class="btn btn-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<i class="ti-settings"></i>
					</button>
					<div class="dropdown-menu animated slideInUp" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 35px, 0px);">
					<a class="dropdown-item  blue" data-toggle="modal" data-target="#responsive-modal" data-seq="' . $row->code . '" href><i class="ti-eye"></i> Open</a>
					<a class="dropdown-item" href="' . base_url() . 'Address/edit/' . $row->code . '"><i class="ti-pencil-alt"></i> Edit</a>
					<a class="dropdown-item mywarning " data-seq="' . $row->code . '" id="' . $row->code . '"><i class="ti-trash" ></i> Delete</a>
					</div>
					</div>';
                $data[] = array($srno, $row->code, $row->cityName, $row->state, $row->district, $row->place, $row->pincode, $service, $status, $actionHtml);
                $srno++;
            }
            $dataCount = sizeof($this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, array(), '', '', $groupByColumn, $extraCondition)->result());
            $output = array("draw" => intval($this->input->post("draw")), "recordsTotal" => $dataCount, "recordsFiltered" => $dataCount, "data" => $data);
            echo json_encode($output);
        }
        else {
            $dataCount = 0;
            $output = array("draw" => intval($this->input->post("draw")), "recordsTotal" => $dataCount, "recordsFiltered" => $dataCount, "data" => $data);
            echo json_encode($output);
        }
    }
    public function save()
    {
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
        $text = $role . " " . $userName . ' added new address from ' . $ip;
        $log_text = array('code' => "demo", 'addID' => $addID, 'logText' => $text);
        //Activity Track Ends
        $this->form_validation->set_rules('cityCode', 'City Name', 'required');
        $this->form_validation->set_rules('state', 'State Name', 'required');
        $this->form_validation->set_rules('district', 'District Name', 'required');
        $this->form_validation->set_rules('place', 'Place Name', 'required');
        $this->form_validation->set_rules('pincode', 'Pincode ', 'required');
        $this->form_validation->set_rules('taluka', 'Taluka ', 'required');
        $this->form_validation->set_rules('longitude', 'longitude ', 'required');
        $this->form_validation->set_rules('latitude', 'latitude ', 'required');
        $this->form_validation->set_rules('radius', 'radius ', 'required');
        if ($this->form_validation->run() == FALSE) {
            $data['city'] = $this->GlobalModel->selectActiveData('citymaster');
            $data['error_message'] = '* Fields are Required!';
            $this->load->view('dashboard/header');
            $this->load->view('dashboard/address/add', $data);
            $this->load->view('dashboard/footer');
        }
        else {
            // $data = array('cityCode' => trim($this->input->post('cityCode')), 'state' => trim($this->input->post('state')), 'district' => trim($this->input->post('district')), 'taluka' => trim($this->input->post('taluka')), 'pincode' => trim($this->input->post('pincode')), 'place' => ucwords(strtolower(trim($this->input->post('place')))), 'isService' => trim($this->input->post("isService")), 'addID' => $addID, 'addIP' => $ip, 'isActive' => trim($this->input->post("isActive")));
            $data = array('cityCode' => trim($this->input->post('cityCode')), 'state' => trim($this->input->post('state')), 'district' => trim($this->input->post('district')), 'taluka' => trim($this->input->post('taluka')), 'pincode' => trim($this->input->post('pincode')), 'place' => ucwords(strtolower(trim($this->input->post('place')))), 'isService' => trim($this->input->post("isService")), 'longitude' => trim($this->input->post("longitude")), 'latitude' => trim($this->input->post("latitude")), 'radius' => trim($this->input->post("radius")), 'addID' => $addID, 'addIP' => $ip, 'isActive' => trim($this->input->post("isActive")));
            $result = $this->GlobalModel->addWithoutYear($data, 'customaddressmaster', 'ADDR');
            if ($result != 'false') {
                $response['status'] = true;
                $response['message'] = "Address Successfully Added.";
                $this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
            }
            else {
                $response['status'] = false;
                $response['message'] = "Failed To Add Address";
            }
            // print_r($response);
            $this->session->set_flashdata('response', json_encode($response));
            redirect(base_url() . 'address/listRecords');
        }
    }
    public function update()
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
        $text = $role . " " . $userName . ' updated Address from ' . $ip;
        $log_text = array('code' => "demo", 'addID' => $addID, 'logText' => $text);
        //Activity Track Ends
        $this->form_validation->set_rules('cityCode', 'City Name', 'required');
        $this->form_validation->set_rules('state', 'State Name', 'required');
        $this->form_validation->set_rules('district', 'District Name', 'required');
        $this->form_validation->set_rules('place', 'Place Name', 'required');
        $this->form_validation->set_rules('pincode', 'Pincode ', 'required');
        $this->form_validation->set_rules('taluka', 'Taluka ', 'required');
        $this->form_validation->set_rules('longitude', 'longitude ', 'required');
        $this->form_validation->set_rules('latitude', 'latitude ', 'required');
        $this->form_validation->set_rules('radius', 'radius ', 'required');
        if ($this->form_validation->run() == FALSE) {
            $data['city'] = $this->GlobalModel->selectActiveData('citymaster');
            $data['error_message'] = '* Fields are Required!';
            $data['query'] = $this->GlobalModel->selectDataById($code, 'customaddressmaster');
            $this->load->view('dashboard/header');
            $this->load->view('dashboard/address/edit', $data);
            $this->load->view('dashboard/footer');
        }
        else {
            $data = array('cityCode' => trim($this->input->post('cityCode')), 'state' => trim($this->input->post('state')), 'district' => trim($this->input->post('district')), 'taluka' => trim($this->input->post('taluka')), 'pincode' => trim($this->input->post('pincode')), 'place' => ucwords(strtolower(trim($this->input->post('place')))), 'isService' => trim($this->input->post("isService")), 'longitude' => $this->input->post("longitude"), 'latitude' => $this->input->post("latitude"), 'radius' => $this->input->post("radius"), 'addID' => $addID, 'addIP' => $ip, 'isActive' => trim($this->input->post("isActive")));
            $result = $this->GlobalModel->doEdit($data, 'customaddressmaster', $code);


            if ($result != 'false') {
                $response['status'] = true;
                $response['message'] = "Address Successfully Updated.";
                $this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
            }
            else {
                $response['status'] = false;
                $response['message'] = "No change In Address";
            }
            //print_r($result);
            //print_r($response);
            $this->session->set_flashdata('response', json_encode($response));
            redirect(base_url() . 'Address/listRecords');
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
        $text = $role . " " . $userName . ' deleted Address  from ' . $ip;
        $log_text = array('code' => "demo", 'addID' => $addID, 'logText' => $text);
        $this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
        $data = array('isService' => 0, 'isActive' => 0, 'isDelete' => 1, 'deleteID' => $addID, 'deleteIP' => $ip);
        $resultData = $this->GlobalModel->doEdit($data, 'customaddressmaster', $code);
        //Activity Track Ends
        echo $this->GlobalModel->delete($code, 'customaddressmaster');
    // redirect(base_url() . 'index.php/uom/listrecords', 'refresh');

    }
    public function view()
    {
        $code = $this->input->post('code');
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
        $table_name = 'customaddressmaster';
        $orderColumns = array("customaddressmaster.*,citymaster.cityName");
        $cond = array('customaddressmaster' . ".code" => $code);
        $like = array();
        $orderBy = array();
        $joinType = array('citymaster' => 'left');
        $join = array('citymaster' => 'customaddressmaster.cityCode=citymaster.code');
        $Records = $this->GlobalModel->selectQuery($orderColumns, $table_name, $cond, $orderBy, $join, $joinType, $like);
        $modelHtml = '<form>';
        $activeStatus = "";
        foreach ($Records->result() as $row) {
            if ($row->isService == "1") {
                $serviceStatus = '<span class="label label-sm label-success">Service Available</span>';
            }
            else {
                $serviceStatus = '<span class="label label-sm label-warning">Service Unavailable</span>';
            }
            if ($row->isActive == "1") {
                $activeStatus = '<span class="label label-sm label-success">Active</span>';
            }
            else {
                $activeStatus = '<span class="label label-sm label-warning">Inactive</span>';
            }
            $modelHtml .= '<div class="form-row"><div class="col-md-6 mb-2"><label><b>Code:</b> </label>
			<input type="text" value="' . $row->code . '" class="form-control-line"  readonly></div>
			<div class="col-md-6 mb-2"><label><b> City:</b> </label>
			<input type="text" class="form-control-line" value="' . $row->cityName . '"  readonly></div>
			<div class="col-md-12 mb-2"><label><b>Place:</b> </label>
			<input type="text" class="form-control-line" value="' . $row->place . '"  readonly></div>
			<div class="col-md-6 mb-2"><label><b>Taluka:</b> </label>
			<input type="text" class="form-control-line" readonly value="' . $row->taluka . '"> </div>
			<div class="col-md-6 mb-2"><label><b>District:</b> </label>
			<input type="text" class="form-control-line" value="' . $row->district . '"  readonly></div>
			<div class="col-md-6 mb-2"><label><b>Pincode:</b> </label>
			<input type="text" class="form-control-line" value="' . $row->pincode . '"  readonly></div>
			<div class="col-md-6 mb-2"><label><b> State:</b> </label>
			<input type="text" class="form-control-line" value="' . $row->state . '"  readonly></div> 
			<div class="col-md-4 mb-2"><label><b>Latitude:</b> </label>
			<input type="text" class="form-control-line" readonly value="' . $row->latitude . '"> </div>
			<div class="col-md-4 mb-2"><label><b>Longitude:</b> </label>
			<input type="text" class="form-control-line" value="' . $row->longitude . '"  readonly></div>
			<div class="col-md-4 mb-2"><label><b>Radius:</b> </label>
			<input type="text" class="form-control-line" value="' . $row->radius . '"  readonly></div>
			
			<div class="col-md-6 mb-2"><label><b>Service:</b> </label>   ' . $serviceStatus . '</div>
			<div class="col-md-6 mb-2"><label><b>Status:</b> </label>    ' . $activeStatus . '</div>';
            //for activity
            $text = $role . " " . $userName . ' viewed Address from ' . $ip;
            $log_text = array('code' => "demo", 'addID' => $addID, 'logText' => $text);
            $this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
        //Activity Track Ends

        }
        $modelHtml .= '</form>';
        echo $modelHtml;
    }
    public function getAllData()
    {
        $place = $this->input->get('place');
        $dataPlace = $this->GlobalModel->similarResultFind('customaddressmaster', 'place', $place);
        $place = '';
        foreach ($dataPlace->result() as $pin) {
            $place .= '<option value="' . $pin->place . '">';
        }
        echo $place;
    }
    public function getStateData()
    {
        $state = $this->input->get('state');
        $datastate = $this->GlobalModel->similarResultFindWithDistinct('customaddressmaster', 'state', $state);
        $state = '';
        foreach ($datastate->result() as $pin) {
            $state .= '<option value="' . $pin->state . '">';
        }
        echo $state;
    }
    public function getDistData()
    {
        $district = $this->input->get('district');
        $datadistrict = $this->GlobalModel->similarResultFindWithDistinct('customaddressmaster', 'district', $district);
        $district1 = '';
        foreach ($datadistrict->result() as $pin) {
            $district1 .= '<option value="' . $pin->district . '">';
        }
        echo $district1;
    }
    public function getTalData()
    {
        $tal = $this->input->get('taluka');
        $dataTal = $this->GlobalModel->similarResultFindWithDistinct('customaddressmaster', 'taluka', $tal);
        $taluka1 = '';
        foreach ($dataTal->result() as $pin) {
            $taluka1 .= '<option value="' . $pin->taluka . '">';
        }
        echo $taluka1;
    }
}
?>