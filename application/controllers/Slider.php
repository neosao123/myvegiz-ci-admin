<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Slider extends CI_Controller {
    var $session_key;
    public function __construct() {
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
    public function listRecords() {
        $data['error'] = $this->session->flashdata('response');
		$data['city'] = $this->GlobalModel->selectActiveData('citymaster');
        $data['productmaster'] = $this->GlobalModel->selectData('productmaster');
        $this->load->view('dashboard/header');
        $this->load->view('dashboard/slider/list', $data);
        $this->load->view('dashboard/footer');
    }
    public function getSliderList() {
		$cityCode = $this->input->get('cityCode');
		$type = $this->input->get('type');
        $tables = array('productslider');
        $tableName = 'productslider';
		$orderColumns = array("productslider.*,citymaster.cityName,maincategorymaster.mainCategoryName");
		$search = $this->input->GET("search")['value'];
		$condition = array('citymaster.isActive'=>1,'citymaster.code'=>$cityCode,'productslider.type'=>$type,'productslider.mainCategoryCode'=>'MCAT_1');
		$orderBy = array('productslider.id'=>'desc');
		$joinType = array('citymaster'=>'inner','maincategorymaster'=>'inner');
		$join = array('citymaster'=>'productslider.productCode=citymaster.code','maincategorymaster'=>'productslider.mainCategoryCode=maincategorymaster.code');
		$groupByColumn = array();
		$limit = $this->input->GET("length");
		$offset = $this->input->GET("start");
		$srno = $offset + 1; 
		$like = array();
		$extraCondition=" productslider.isDelete=0 or productslider.isDelete is null";
		$like = array();
		$data=array();
		$dataCount = 0;
		$Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
		$srno = $_GET['start'] + 1;
        $data = array();
        $productPhoto = "";
		if($Records){ 
			foreach ($Records->result() as $row) {
				$path = base_url() . 'uploads/slider/' . $row->imagePath;
				if($row->type=='image'){
					$image = '<div class="m-r-10"><img src="' . $path . '?' . time() . '" alt="user" class="circle" width="45"></div>';
				} else {
					$image = '<div class="m-r-10">
					         <video width="100" height="100" controls>
                             <source src="'.$path.'" type="video/mp4">
					         </video>
					         </div>';
				}
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
										<a class="dropdown-item" href="' . base_url() . 'index.php/slider/edit/' . $row->code . '"><i class="ti-pencil-alt"></i> Edit</a>
										<a class="dropdown-item  mywarning" data-seq="' . $row->code . '" id="' . $row->code . '"><i class="ti-trash" href></i> Delete</a>
									</div>
								</div>';
				$data[] = array($srno, $row->code, $row->mainCategoryName, $row->cityName, $image, $status, $actionHtml);
				$srno++;
			}
		}
        $dataCountRes = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, "", "", $groupByColumn, $extraCondition);
		if($dataCountRes){
			$dataCount = sizeof($this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, "", "", $groupByColumn, $extraCondition)->result_array());
		}
        
        $output = array("draw" => intval($_GET["draw"]), "recordsTotal" => $dataCount, "recordsFiltered" => $dataCount, "data" => $data);
        echo json_encode($output);
    }
    public function add() {
        $data['citymaster'] = $this->GlobalModel->selectData('citymaster');         
        $this->load->view('dashboard/header');
        $this->load->view('dashboard/slider/add', $data);
        $this->load->view('dashboard/footer');
    }
    public function save() {
        $productCode = trim($this->input->post("productCode"));
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
        $text = $role . " " . $userName . ' added new slider for citycode "' . $productCode . '" from ' . $ip;
        $log_text = array('code' => "demo", 'addID' => $addID, 'logText' => $text);
        $data = array('productCode' => $productCode,'type' => $this->input->post('type'), 'addID' => $addID, 'addIP' => $ip, 'isActive' => $this->input->post("isActive"),'mainCategoryCode'=>'MCAT_1');
        $code = $this->GlobalModel->addWithoutYear($data, 'productslider', 'PRODS');
		$filedoc =  'false';
        if ($code != 'false') {
            $sliderPhoto = "";
            $uploadRootDir = 'uploads/';
            $uploadDir = 'uploads/slider/';
            if (!empty($_FILES['imagePath']['name'])) {
                $tmpFile = $_FILES['imagePath']['tmp_name'];
                $ext = pathinfo($_FILES['imagePath']['name'], PATHINFO_EXTENSION);
                $name = $code . time() . '_.' . $ext;
                $filename = $uploadDir . '/' . $name;
                move_uploaded_file($tmpFile, $filename);
                if (file_exists($filename)) {
                    $subData = array('imagePath' => $name);
					
					//print_r($subData);
					//exit();
                    $filedoc = $this->GlobalModel->doEdit($subData, 'productslider', $code);
                } else {
                    unlink($filename);
                }
            }
        }
        if ($code != 'false' && $filedoc!='false') {
            $response['status'] = true;
            $response['message'] = "Slider Successfully Added.";
            $this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
        } else {
            $response['status'] = false;
            $response['message'] = "Failed To Add Slider";
        }
        $this->session->set_flashdata('response', json_encode($response));
        redirect(base_url() . 'index.php/Slider/listRecords', 'refresh');
    }
    public function rshow() {
        print_r($this->GlobalModel->selectData('productmaster')->result());
    }
    public function edit() {
        $code = $this->uri->segment(3);
        $data['query'] = $this->GlobalModel->selectDataById($code, 'productslider');
        $data['citymaster'] = $this->GlobalModel->selectData('citymaster');
        $this->load->view('dashboard/header');
        $this->load->view('dashboard/slider/edit', $data);
        $this->load->view('dashboard/footer');
    }
    public function update() {
        $code = $this->input->post('code');        
        $productCode = trim($this->input->post("productCode"));
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
        $text = $role . " " . $userName . ' updated slider for citycode "' . $productCode . '" from ' . $ip;
        $log_text = array('code' => "demo", 'addID' => $addID, 'logText' => $text);
      
        $data = array('productCode' => $productCode,'type' => $this->input->post('type'), 'editID' => $addID, 'editIP' => $ip, 'isActive' => $this->input->post("isActive"));
        $result = $this->GlobalModel->doEdit($data, 'productslider', $code);
        $filedoc = 'false';
        if ($imageData != '') {
            $sliderPhoto = "";
            $uploadRootDir = 'uploads/';
            $uploadDir = 'uploads/slider/';
            if (!empty($_FILES['imagePath']['name'])) {
                $tmpFile = $_FILES['imagePath']['tmp_name'];
                $ext = pathinfo($_FILES['imagePath']['name'], PATHINFO_EXTENSION);
                $name = $code . time() . '_.' . $ext;
                $filename = $uploadDir . '/' . $name;
                move_uploaded_file($tmpFile, $filename);
                if (file_exists($filename)) {
                    $subData = array('imagePath' => $name);
                    $filedoc = $this->GlobalModel->doEdit($subData, 'productslider', $code);
                } else {
                    unlink($filename);
                }
            }
        }         
        if ($result != 'false' || $filedoc!='false') {
            $response['status'] = true;
            $response['message'] = "Slider Successfully Updated.";
            $this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
        } else {
            $response['status'] = false;
            $response['message'] = "No change In Slider" . $error;
        }
        $this->session->set_flashdata('response', json_encode($response));
        redirect(base_url() . 'index.php/Slider/listRecords', 'refresh');
    }
    public function delete() {
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
        $dataQ = $this->GlobalModel->selectDataById($code, 'productslider');
        $productName = '';
        foreach ($dataQ->result() as $row) {
            $productName = $row->productCode;
            $imagePath = $row->imagePath;
        }
        $text = $role . " " . $userName . ' deleted slider for citycode "' . $productName . '" from ' . $ip;
        $log_text = array('code' => "demo", 'addID' => $addID, 'logText' => $text);
        $this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
        $data = array('deleteID' => $addID, 'deleteIP' => $ip, 'imagePath' => '');
        $resultData = $this->GlobalModel->doEdit($data, 'productslider', $code);
		if(file_exists('uploads/slider/' . $imagePath)) unlink('uploads/slider/' . $imagePath);
        echo $this->GlobalModel->delete($code, 'productslider');
    }
    public function view() {
        $code = $this->input->get('code');
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
        $tableName = 'productslider';
		$orderColumns = array("productslider.*,citymaster.cityName");
		//$search = $this->input->GET("search")['value'];
		$condition = array('productslider.code'=>$code);
		$orderBy = array('productslider.id'=>'desc');
		$joinType = array('citymaster'=>'inner');
		$join = array('citymaster'=>'productslider.productCode=citymaster.code');
		$groupByColumn = array();
		$limit = $this->input->GET("length");
		$offset = $this->input->GET("start");
		$srno = $offset + 1;
		$like = array();
		$extraCondition=" productslider.isDelete=0 or productslider.isDelete is null";
		$like = array();
		$data=array();
		$dataCount = 0;
		$Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
		if($Records){
			$code = $Records->result_array()[0]['code'];
			$text = $role . " " . $userName . ' viewed slider of code "' . $code . '" from ' . $ip;
			$log_text = array('code' => "demo", 'addID' => $addID, 'logText' => $text);
			$this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
			// Activity Track Ends                
			$data['result'] = $Records->result_array()[0];
        } else {
			$data['result'] = false;
		}
		$this->load->view('dashboard/slider/view',$data);
    }
    public function deleteImage() {
        $code = $this->input->post('value');
        $Result = $this->GlobalModel->selectDataByid($code, 'productphotos');
        $productCode = $Result->result() [0]->productCode;
        $productPhoto = $Result->result() [0]->productPhoto;
        unlink('uploads/slider/' . $productCode . '/' . $productPhoto);
        echo $deleteData = $this->GlobalModel->deleteForever($code, 'productphotos');      
    }
    public function deleteLineRecord() {
        $lineCode = $this->input->post('code');
        echo $this->GlobalModel->deleteForever($lineCode, 'productbenefits');
    }
    public function getProductCode() {
        $productName = $this->input->get('productName');
        $records = $this->GlobalModel->selectDataByField('productName', $productName, 'productmaster');
        echo $productCode = $records->result() [0]->code;
    }
    public function deleteProductImage() {
        $code = $this->input->post('code');
        $Result = $this->GlobalModel->selectDataByid($code, 'productslider');
        $imagePath = $Result->result()[0]->imagePath;
        $path = 'uploads/slider/' . $imagePath;
        if(file_exists($path)){
            unlink($path);
        }
        $data = array('imagePath' => '','type'=>'');
        echo $deleteData = $this->GlobalModel->doEdit($data, 'productslider', $code);
    }
    public function test() {
        $records = $this->GlobalModel->selectData('productslider');
        print_r($records->result());
    }
    public function checkMimeType()
    {
        if (!empty($_FILES['imagePath']['name'])) {
            $tmpFile = $_FILES['imagePath']['tmp_name'];
            $mime = get_mime_by_extension($tmpFile);
            $res['mime'] = $mime;
        } else {
            $res['mime'] = 'false';
        }
        echo json_encode($res);
    }
}
?>