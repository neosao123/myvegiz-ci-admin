<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Homeslider extends CI_Controller
{
    var $session_key;
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('form', 'url', 'html');
        $this->load->library('form_validation');
        $this->load->model('GlobalModel');
        $this->load->model('GlobalModel1');
        $this->load->model('ApiModel');
        $this->session_key = $this->session->userdata('key' . SESS_KEY);
        if (!isset($this->session->userdata['logged_in' . $this->session_key]['code'])) {
            redirect('Admin/login', 'refresh');
        }
    }
    public function listRecords()
    {
        $data['error'] = $this->session->flashdata('response');
        $data['city'] = $this->GlobalModel->selectActiveData('citymaster');
        $data['productmaster'] = $this->GlobalModel->selectData('productmaster');
        $this->load->view('dashboard/header');
        $this->load->view('dashboard/homeslider/list', $data);
        $this->load->view('dashboard/footer');
    }
    public function getSliderList()
    {
        $tables = array('homeslider');
        $tableName = 'homeslider';
        $orderColumns = array("homeslider.*");
        $search = strtolower(($this->input->post("search") ?? $this->input->get("search"))['value'] ?? '');
        $condition = array();
        // $condition = array('homeslider.mainCategoryCode'=>'MCAT_1');
        $orderBy = array('homeslider.id' => 'desc');
        $joinType = array();
        $join = array();
        $groupByColumn = array();
        $limit = $this->input->post("length") ?? $this->input->get("length");
        $offset = $this->input->post("start") ?? $this->input->get("start");
        $srno = (intval($offset) > 0 ? intval($offset) : 0) + 1;
        $like = array();
        $extraCondition = "(homeslider.isDelete=0 or homeslider.isDelete is null)";
        $like = array();
        $data = array();
        $dataCount = 0;
        $Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
        $srno = (intval($offset) > 0 ? intval($offset) : 0) + 1;
        $data = array();
        $productPhoto = "";
        if ($Records) {
            foreach ($Records->result() as $row) {
                $path = base_url() . 'uploads/homeslider/' . $row->imagePath;
                $image = '<div class="m-r-10"><img src="' . $path . '?' . time() . '" alt="user" class="circle" width="45"></div>';

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
										<a class="dropdown-item" href="' . base_url() . 'index.php/Homeslider/edit/' . $row->code . '"><i class="ti-pencil-alt"></i> Edit</a>
										<a class="dropdown-item  mywarning" data-seq="' . $row->code . '" id="' . $row->code . '"><i class="ti-trash" href></i> Delete</a>
									</div>
								</div>';
                $data[] = array($srno, $row->code, $image, $status, $actionHtml);
                $srno++;
            }
            $dataCount = sizeof($this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, "", "", $groupByColumn, $extraCondition)->result_array());
        }

        $output = array("draw" => intval($this->input->post("draw") ?? $this->input->get("draw") ?? 0), "recordsTotal" => $dataCount, "recordsFiltered" => $dataCount, "data" => $data);
        echo json_encode($output);
    }
    public function add()
    {
        $this->load->view('dashboard/header');
        $this->load->view('dashboard/homeslider/add');
        $this->load->view('dashboard/footer');
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
        $text = $role . " " . $userName . ' added new home slider image from ' . $ip;
        $log_text = array('code' => "demo", 'addID' => $addID, 'logText' => $text);
        $data = array('addID' => $addID, 'addIP' => $ip, 'isActive' => $this->input->post("isActive"));
        $code = $this->GlobalModel->addWithoutYear($data, 'homeslider', 'HMSLD');
        $filedoc = 'false';
        if ($code != 'false') {
            $sliderPhoto = "";
            $uploadRootDir = 'uploads/';
            $uploadDir = 'uploads/homeslider/';
            if (!empty($_FILES['imagePath']['name'])) {
                $tmpFile = $_FILES['imagePath']['tmp_name'];
                $ext = pathinfo($_FILES['imagePath']['name'], PATHINFO_EXTENSION);
                $name = $code . time() . '_.' . $ext;
                $filename = $uploadDir . '/' . $name;
                move_uploaded_file($tmpFile, $filename);
                if (file_exists($filename)) {
                    $subData = array('imagePath' => $name);
                    $filedoc = $this->GlobalModel->doEdit($subData, 'homeslider', $code);
                }
                else {
                    unlink($filename);
                }
            }
        }
        if ($code != 'false' && $filedoc != 'false') {
            $response['status'] = true;
            $response['message'] = "Home slider image Successfully Added.";
            $this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
        }
        else {
            $response['status'] = false;
            $response['message'] = "Failed To Add Home slider";
        }
        $this->session->set_flashdata('response', json_encode($response));
        redirect(base_url() . 'index.php/Homeslider/listRecords', 'refresh');
    }
    public function edit()
    {
        $code = $this->uri->segment(3);
        $data['query'] = $this->GlobalModel->selectDataById($code, 'homeslider');
        $this->load->view('dashboard/header');
        $this->load->view('dashboard/homeslider/edit', $data);
        $this->load->view('dashboard/footer');
    }
    public function update()
    {
        $code = $this->input->post('code');
        $imageData = $_FILES['imagePath']['name'];
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
        $text = $role . " " . $userName . ' updated slider of code"' . $code . '" from ' . $ip;
        $log_text = array('code' => "demo", 'addID' => $addID, 'logText' => $text);

        $data = array('editID' => $addID, 'editIP' => $ip, 'isActive' => $this->input->post("isActive"));
        $result = $this->GlobalModel->doEdit($data, 'homeslider', $code);
        $filedoc = 'false';
        if ($imageData != '') {
            $sliderPhoto = "";
            $uploadRootDir = 'uploads/';
            $uploadDir = 'uploads/homeslider/';
            if (!empty($_FILES['imagePath']['name'])) {
                $tmpFile = $_FILES['imagePath']['tmp_name'];
                $ext = pathinfo($_FILES['imagePath']['name'], PATHINFO_EXTENSION);
                $name = $code . time() . '_.' . $ext;
                $filename = $uploadDir . '/' . $name;
                move_uploaded_file($tmpFile, $filename);
                if (file_exists($filename)) {
                    $subData = array('imagePath' => $name);
                    $filedoc = $this->GlobalModel->doEdit($subData, 'homeslider', $code);
                }
                else {
                    unlink($filename);
                }
            }
        }
        if ($result != 'false' || $filedoc != 'false') {
            $response['status'] = true;
            $response['message'] = "Home slider Successfully Updated.";
            $this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
        }
        else {
            $response['status'] = false;
            $response['message'] = "No change In Slider" . $error;
        }
        $this->session->set_flashdata('response', json_encode($response));
        redirect(base_url() . 'index.php/Homeslider/listRecords', 'refresh');
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
        $dataQ = $this->GlobalModel->selectDataById($code, 'homeslider');
        $productName = '';
        foreach ($dataQ->result() as $row) {
            $productName = $row->productCode;
            $imagePath = $row->imagePath;
        }
        $text = $role . " " . $userName . ' deleted slider for citycode "' . $productName . '" from ' . $ip;
        $log_text = array('code' => "demo", 'addID' => $addID, 'logText' => $text);
        $this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
        $data = array('deleteID' => $addID, 'deleteIP' => $ip, 'imagePath' => '');
        $resultData = $this->GlobalModel->doEdit($data, 'homeslider', $code);
        if (file_exists('uploads/homeslider/' . $imagePath))
            unlink('uploads/homeslider/' . $imagePath);
        echo $this->GlobalModel->delete($code, 'homeslider');
    }
    public function view()
    {
        $code = $this->input->post('code') ?? $this->input->get('code');
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
        $tableName = 'homeslider';
        $orderColumns = array("homeslider.*");
        $condition = array('homeslider.code' => $code);
        $data = array();
        $dataCount = 0;
        $Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition);
        if ($Records) {
            $code = $Records->result_array()[0]['code'];
            $text = $role . " " . $userName . ' viewed slider of code "' . $code . '" from ' . $ip;
            $log_text = array('code' => "demo", 'addID' => $addID, 'logText' => $text);
            $this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
            // Activity Track Ends                
            $data['result'] = $Records->result_array()[0];
        }
        else {
            $data['result'] = false;
        }
        $this->load->view('dashboard/homeslider/view', $data);
    }
    public function deleteImage()
    {
        $code = $this->input->post('value');
        $Result = $this->GlobalModel->selectDataByid($code, 'productphotos');
        $productCode = $Result->result()[0]->productCode;
        $productPhoto = $Result->result()[0]->productPhoto;
        unlink('uploads/homeslider/' . $productCode . '/' . $productPhoto);
        echo $deleteData = $this->GlobalModel->deleteForever($code, 'productphotos');
    }
    public function deleteLineRecord()
    {
        $lineCode = $this->input->post('code');
        echo $this->GlobalModel->deleteForever($lineCode, 'productbenefits');
    }
    public function getProductCode()
    {
        $productName = $this->input->get('productName');
        $records = $this->GlobalModel->selectDataByField('productName', $productName, 'productmaster');
        echo $productCode = $records->result()[0]->code;
    }
    public function deleteProductImage()
    {
        $code = $this->input->post('code');
        $Result = $this->GlobalModel->selectDataByid($code, 'homeslider');
        $imagePath = $Result->result()[0]->imagePath;
        $path = 'uploads/homeslider/' . $imagePath;
        if (file_exists($path)) {
            unlink($path);
        }
        $data = array('imagePath' => '', 'type' => '');
        echo $deleteData = $this->GlobalModel->doEdit($data, 'homeslider', $code);
    }
    public function test()
    {
        $records = $this->GlobalModel->selectData('homeslider');
        print_r($records->result());
    }
    public function checkMimeType()
    {
        if (!empty($_FILES['imagePath']['name'])) {
            $tmpFile = $_FILES['imagePath']['tmp_name'];
            $mime = get_mime_by_extension($tmpFile);
            $res['mime'] = $mime;
        }
        else {
            $res['mime'] = 'false';
        }
        echo json_encode($res);
    }
}
?>