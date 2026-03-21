<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Tag extends CI_Controller {
    var $session_key;
    public function __construct() {
        parent::__construct();
        $this->load->helper('form', 'url', 'html');
        $this->load->library('form_validation');
        $this->load->model('GlobalModel');
        $this->load->model('GlobalModel1');
        $this->session_key = $this->session->userdata('key' . SESS_KEY);
        if(!isset($this->session->userdata['logged_in' . $this->session_key]['code'])){
			redirect('Admin/login','refresh');
		}
    }
    public function listRecords() {
        $data['error'] = $this->session->flashdata('response');
        $data['query'] = $this->GlobalModel->selectDataExcludeDelete('citymaster');
        $this->load->view('dashboard/header');
        $this->load->view('dashboard/Tag/list', $data);
        $this->load->view('dashboard/footer');
    }
    
   
    public function getTagList() {
		$search = strtolower($this->input->GET("search") ['value']);
		$activeKey = "";
		if ($search == "active"){
            $activeKey = "1";
            $search = "";
        }else if ($search == "inactive"){
            $activeKey = "0";
            $search = "";
		}
        $table = "tagmaster";
        $orderColumns = array("tagmaster.*");
        $condition = array("tagmaster.isActive" => $activeKey); 
        $orderBy = array("tagmaster.isActive" => "DESC","tagmaster.id" => "DESC");
        $join = array();
        $joinType = array();
        $groupBy = array();
        $limit = $this->input->GET("length"); 
        $offset = $this->input->GET("start");
        $like = array("tagmaster.code" => $search . "~both", "tagmaster.tagTitle" => $search . "~both", "tagmaster.tagColor" => $search . "~both");
        $extraCondition=" (tagmaster.isDelete=0 or tagmaster.isDelete is null)";
		$Records = $this->GlobalModel->selectQuery($orderColumns, $table, $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupBy, $extraCondition);
		$r = $this->db->last_query();
        $srno = $offset + 1;
        $data = array();
		$dataCount=0;
		if($Records!=false){
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
                                      <a class="dropdown-item  view" data-toggle="modal" data-target="#responsive-modal" data-seq="' . $row->code . '" href="#"><i class="ti-eye"></i> Open</a>
                                    <a class="dropdown-item edit" data-seq="' . $row->code . '" data-title="' . $row->tagTitle . '" data-active="' . $row->isActive . '" data-color="' . $row->tagColor . '" href="#"><i class="ti-pencil-alt"></i> Edit</a>
                                    <a class="dropdown-item delete" data-seq="' . $row->code . '"  href="#"><i class="ti-trash"></i> Delete</a>
                                  
                                </div>
                            </div>';
                $data[] = array($srno, $row->code, $row->tagTitle,$row->tagColor, $status , $actionHtml);
                $srno++;
            }
            $dataCount = sizeof($this->GlobalModel->selectQuery($orderColumns, $table, $condition, $orderBy, $join, $joinType, $like, "", "", $groupBy, $extraCondition)->result());
		}
        $output = array(
			"draw" => intval($_GET["draw"]), 
			"recordsTotal" => $dataCount, 
			"recordsFiltered" => $dataCount, 
			"data" => $data,
			"extra" => $r,
		);
        echo json_encode($output);
    }
    public function save() {
        $code = trim($this->input->post("code"));
        $tagTitle = trim($this->input->post("tagTitle"));
        $tagColor = trim($this->input->post("tagColor"));
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
        $text = $role . " " . $userName . ' added new tag "' . $tagTitle . '" with color "'.$tagColor.'" from ' . $ip;
        $log_text = array('code' => "demo", 'addID' => $addID, 'logText' => $text);
        $result = $this->GlobalModel->checkDuplicateRecordNotEqualtoCode('tagTitle', $tagTitle, 'tagmaster',$code);
        if ($result != FALSE) {
            $response['status'] = false;
            $response['message'] = "Duplicate Tag";
        } else {
			$data = array(
				'tagTitle' => $tagTitle,
				'tagColor' => $tagColor,
				'isActive' => trim($this->input->post("isActive")),
				'isDelete' => 0,
			);
			if($code!="" && $code!=NULL){
				$data['editID'] = $addID;
				$data['editIP'] = $ip;
				$result = $this->GlobalModel->doEdit($data, 'tagmaster', $code);
				$message="Tag Successfully Updated.";
				$inMsg="Failed To Update Tag";
			}else{
				$data['addID'] = $addID;
				$data['addIP'] = $ip;
				$result = $this->GlobalModel->addWithoutYear($data, 'tagmaster', 'T');
				$message="Tag Successfully Added.";
				$inMsg="Failed To Add Tag";
			}
            if ($result != 'false') {
				$response['status'] = true;
                $response['message'] = $message;
                $this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
            } else {
                $response['status'] = false;
                $response['message'] = $inMsg;
            }
		}
		echo json_encode($response);
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
        $dataQ = $this->GlobalModel->selectDataByField('code', $code, 'tagmaster');
        $tagTitle = '';
        foreach ($dataQ->result() as $row) {
            $tagTitle = $row->tagTitle;
        }
        $text = $role . " " . $userName . ' deleted Tag "' . $tagTitle . '" from ' . $ip;
        $log_text = array('code' => "demo", 'addID' => $addID, 'logText' => $text);
        $this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
        $data = array('deleteID' => $addID, 'deleteIP' => $ip);
        $resultData = $this->GlobalModel->doEdit($data, 'tagmaster', $code);
        //Activity Track Ends
        echo $this->GlobalModel->delete($code, 'tagmaster');
    }
    public function view() {
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
        $table_name = 'tagmaster';
        $orderColumns = array("tagmaster.*");
        $cond = array("tagmaster.code" => $code);
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
            $modelHtml.= '<div class="form-row">
                        <div class="col-md-6 mb-3">
                            <label><b>Code:</b> </label>
						    <input type="text" value="' . $row->code . '" class="form-control-line"  readonly>
						</div>
					    <div class="col-md-6 mb-3">
					        <label><b> Tag Title:</b> </label>
						    <input type="text" class="form-control-line" value="' . $row->tagTitle . '"  readonly>
						</div>
						</div>
						<div class="form-row">
					    <div class="col-md-6 mb-3">
					        <label><b>Tag Color:</b> </label>
					        <input type="text" value="' . $row->tagColor . '" class="form-control-line"  readonly>
					    </div>
						<div class="col-md-6 mb-3 " style="margin-top:25px;">
						'.$activeStatus.'
						</div>
					</div> ';
            //for activity
            $text = $role . " " . $userName . ' viewed Tag "' . $row->tagTitle . '" from ' . $ip;
            $log_text = array('code' => "demo", 'addID' => $addID, 'logText' => $text);
            $this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
        }
        $modelHtml.= '</form>';
        echo $modelHtml;
    }
	
    
}
?>