<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class AndroidUsers extends CI_Controller {
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
        $data['query'] = $this->GlobalModel->selectData('clientmaster');
        $data['querypincode'] = $this->GlobalModel->selectData('clientprofile');
        $data['city'] = $this->GlobalModel->selectActiveData('citymaster');
        $this->load->view('dashboard/header');
        $this->load->view('dashboard/androidUsers/list', $data);
        $this->load->view('dashboard/footer');
    }
    public function add() {
        $data['city'] = $this->GlobalModel->selectActiveData('citymaster');
        $this->load->view('dashboard/header');
        $this->load->view('dashboard/androidUsers/add',$data);
        $this->load->view('dashboard/footer');
    }
    public function edit() {
        $code = $this->uri->segment(3);
        $data['city'] = $this->GlobalModel->selectActiveData('citymaster');
        /*$tables = array('clientmaster', 'clientprofile');
        $requiredColumns = array(array('code', 'name', 'emailId', 'mobile', 'isActive','cityCode'), array('clientCode', 'gender', 'city', 'local', 'flat', 'pincode', 'state', 'landMark'));
        $conditions = array(array('code', 'clientCode'));
        $extraConditionColumnNames = array(array('code'));
        $extraConditions = array(array($code));
        $data['users'] = $this->GlobalModel1->make_datatablesWithoutLimit($tables, $requiredColumns, $conditions, $extraConditionColumnNames, $extraConditions);
        */
		$table_name = 'clientmaster';
        $orderColumns = array("clientmaster.*");
        $cond = array('clientmaster.code'=>$code);
        $data['users'] = $this->GlobalModel->selectQuery($orderColumns, $table_name, $cond);
		//echo $this->db->last_query();
		$this->load->view('dashboard/header');
        $this->load->view('dashboard/androidUsers/edit', $data);
        $this->load->view('dashboard/footer');
    }
    public function getdesignationname() {
        $designationName = $this->input->get('designationName');
        $dataDesignationName = $this->GlobalModel->similarResultFind('designationmaster', 'designationName', $designationName);
        $designation = '';
        foreach ($dataDesignationName->result() as $designation) {
            $designation.= '<option value= "' . $designation->designationName . '">';
        }
        echo $designation;
    }
    public function getAndroidUsersList() {
        //Filter Data
        $code = $this->input->GET("code");
		$search = $this->input->GET("search")['value'];
        $mobile = $this->input->GET("mobile");
        $emailId = $this->input->GET("emailId");
        $status = $this->input->GET("isActive");
        $cityCode = $this->input->GET("cityCode");
		$export = $this->input->GET('export');
        $tableName = "clientmaster";
        $orderColumns = array("clientmaster.*,clientprofile.clientCode,clientprofile.gender,clientprofile.local,clientprofile.flat,clientprofile.pincode,clientprofile.state,clientprofile.landMark,citymaster.cityName");
        $condition = array('clientmaster.code' => $code, 'clientmaster.mobile' => $mobile, 'clientmaster.emailId' => $emailId, 'clientmaster.isActive' => $status, 'clientmaster.cityCode' => $cityCode);
        $orderBy = array('clientmaster' . '.id' => 'DESC');
        $joinType = array('clientprofile' => 'inner','citymaster'=>'left');
        $join = array('clientprofile' => 'clientprofile.clientCode=clientmaster.code','citymaster'=>'clientmaster.cityCode=citymaster.code');
        $groupByColumn = array('clientmaster.code');
        $limit = $this->input->GET("length");
        $offset = $this->input->GET("start");
        $extraCondition = " (clientmaster.isDelete =0 OR clientmaster.isDelete IS NULL)";
        $like = array("clientmaster.name" => $search . "~both","clientmaster.mobile" => $search . "~both","clientmaster.code" => $search . "~both");
        $Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
        $srno = $offset + 1;
        $data = array();
		//echo $this->db->last_query();  
        if ($Records) {
			 
            foreach ($Records->result() as $row) {
				if($export==0){
					
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
							<a class="dropdown-item" href="' . base_url() . 'AndroidUsers/view/' . $row->code . '"><i class="ti-eye"></i> Open</a>
							<a class="dropdown-item" href="' . base_url() . 'AndroidUsers/edit/' . $row->code . '"><i class="ti-pencil-alt"></i> Edit</a>
							<a class="dropdown-item  mywarning" data-seq="' . $row->code . '" id="' . $row->code . '"><i class="ti-trash" href></i> Delete</a>
							
						</div>
					</div>';
				
					$data[] = array($srno, $row->code, $row->name, $row->mobile, $row->emailId, $row->local, $row->cityName, $row->pincode, $status, $actionHtml);
				} else {
					
					if ($row->isActive == "1") {
						$status = "Active";
					} else {
						$status = "Inactive";
					}
					$name = (is_null($row->name) == true || $row->name=='') ? 'a' : $row->name;
					$pincode = (is_null($row->pincode) == true || $row->name=='') ? 'a' : $row->pincode;
					$emailId = (is_null($row->emailId) == true || $row->name=='') ? 'a' : $row->emailId;
					$local = (is_null($row->local) == true || $row->name=='') ? 'a' : $row->local;
					$city = (is_null($row->cityName) == true || $row->name=='') ? 'a' : $row->cityName;
					$data[] = array($srno, $row->code, $name, $row->mobile, $emailId, $local, $row->cityName, $pincode, $status);
				}
                $srno++;
            }
            $dataCount = sizeof($this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, "", "", $groupByColumn, $extraCondition)->result_array());
        } else {
            $dataCount = 0;
        }
        $output = array("draw" => intval($_GET["draw"]), "recordsTotal" => $dataCount, "recordsFiltered" => $dataCount, "data" => $data);
        echo json_encode($output);
    }
    public function update() {
        $code = $this->input->post('code');
        $name = $this->input->post('name');
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
        $text = $role . " " . $userName . ' updated status of "' . $code . '" from ' . $ip;
       
        $log_text = array('code' => "demo", 'addID' => $addID, 'logText' => $text);
        $data = array('name'=>trim($this->input->post("name")),'isActive' => ($this->input->post('isActive') == "1" ? 1 : 0));
        $result = $this->GlobalModel->doEdit($data, 'clientmaster', $code);
        if ($result == 'true') {
            $response['status'] = true;
            $response['message'] = "Android Users Successfully Updated.";
            $this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
        } else {
            $response['status'] = false;
            $response['message'] = "No change In Android Users";
        }
        $this->session->set_flashdata('response', json_encode($response));
        redirect(base_url() . 'index.php/AndroidUsers/listRecords');
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
        $dataQ = $this->GlobalModel->selectDataByField('code', $code, 'clientmaster');
        foreach ($dataQ->result() as $row) {
            $name = $row->name;
        }
        $text = $role . " " . $userName . ' deleted android users "' . $name . '" from ' . $ip;
        $log_text = array('code' => "demo", 'addID' => $addID, 'logText' => $text);
        $this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
        //Activity Track Ends
        echo $result = $this->GlobalModel->deleteWithField('clientCode', $code, 'clientprofile');
        echo $result = $this->GlobalModel->delete($code, 'clientmaster');
        //redirect(base_url() . 'index.php/designation/listRecords', 'refresh');
        
    }
    public function view() {
        //$code = $this->input->get('code');
        $code = $this->uri->segment(3);
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
        //Activity Track Ends
        $data['query'] = $this->GlobalModel->selectDataById($code, 'clientmaster');
        $data['queryprofile'] = $this->GlobalModel->selectDataByField('clientCode', $code, 'clientprofile');
		$citycode = $data['query']->result_array()[0]['cityCode'];
		if($citycode){
			$city = $this->GlobalModel->selectDataByField('code', $citycode, 'citymaster');
			$data['cityname'] = $city->result()[0]->cityName;
		} else {
			$data['cityname'] ="";
		}
        //Activity Track Ends
        $this->load->view('dashboard/header');
        $this->load->view('dashboard/androidUsers/view', $data);
        $this->load->view('dashboard/footer');
    }
    public function getOrderList() {
        $clientCode = $this->input->get("clientCode");
        $startDate = $this->input->get("startDate");
        $endDate = $this->input->get("endDate");
        $tables = array('ordermaster', 'orderstatusmaster', 'paymentstatusmaster', 'clientmaster');
        $requiredColumns = array(array('code', 'clientCode', 'paymentmode', 'paymentstatus', 'orderstatus', 'address', 'phone', 'totalPrice', 'addDate', 'isActive'), array('statusName'), array('statusName'), array('name'));
        $conditions = array(array('orderstatus', 'statusSName'), array('paymentstatus', 'statusSName'), array('clientCode', 'code'));
        $extraConditionColumnNames = array(array("clientCode"));
        $extraConditions = array(array($clientCode));
        $extraDateConditionColumnNames = array(array("addDate"));
        $extraDateConditions = array(array($startDate . '~' . $endDate));
        $likeFlag = false;
        $Records = $this->GlobalModel1->make_datatables($tables, $requiredColumns, $conditions, $extraConditionColumnNames, $extraConditions, $extraDateConditionColumnNames, $extraDateConditions, $likeFlag);
        // print_r($Records->result());
        // exit();
        $srno = $_GET['start'] + 1;
        $data = array();
        foreach ($Records->result() as $row) {
            $actionHtml = '<a class="dropdown-item  blue" data-toggle="modal" data-target="#responsive-modal" data-seq="' . $row->code_00 . '" href><i class="ti-eye"></i> Open</a>';
            $data[] = array($srno, $row->code_00, $row->paymentmode_02, $row->statusName_10, $row->statusName_20, $row->address_05, $row->totalPrice_07, $actionHtml);
            // print_r($data);
            // exit();
            $srno++;
        }
        $dataCount = $this->GlobalModel1->get_all_data($tables, $requiredColumns, $conditions, $extraConditionColumnNames, $extraConditions, $extraDateConditionColumnNames, $extraDateConditions, $likeFlag);
        $output = array("draw" => intval($_GET["draw"]), "recordsTotal" => $dataCount, "recordsFiltered" => $dataCount, "data" => $data);
        echo json_encode($output);
    }
    public function productView() {
        $orderCode = $this->input->get('code');
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
        //Activity Track Ends
        $linetableName = "orderlineentries";
        $lineorderColumns = array("orderlineentries.productCode,orderlineentries.weight,orderlineentries.productUom,orderlineentries.productPrice ,orderlineentries.quantity,orderlineentries.totalPrice as productTotalPrice,productmaster.productName");
        $linecond = array('orderlineentries' . ".orderCode" => $orderCode);
        $lineorderBy = array('orderlineentries' . ".id" => 'ASC');
        $linejoin = array('productmaster' => 'orderlineentries' . '.productCode=' . 'productmaster' . '.code');
        $linejoinType = array('productmaster' => 'inner');
        $orderProductList = $this->GlobalModel->selectQuery($lineorderColumns, $linetableName, $linecond, $lineorderBy, $linejoin, $linejoinType)->result_array();
        $productInfo = "";
        $modelHtml = '<form>';
        $modelHtml = '<div class="form-row"><div class="col-md-3 mb-3"><label> <b> Code:</b> </label></div>
										<div class="col-md-3 mb-3"><label> <b> Name:</b> </label></div>
																<div class="col-md-4 mb-3"><label> <b> Weight x Quantity :</b> </label></div>
									<div class="col-md-2 "><label><b> Price:</b> </label></div>';
        for ($j = 0;$j < sizeof($orderProductList);$j++) {
            $modelHtml.= '<div class="form-row">
						<div class="col-md-3 mb-3"><input type="text" value="' . $orderProductList[$j]['productCode'] . '" class="form-control-line"  readonly></div>
						<div class="col-md-3 mb-3">
						<input type="text" class="form-control-line" value="' . $orderProductList[$j]['productName'] . '"  readonly></div>
						<div class="col-md-4 mb-3">
						<input type="text" class="form-control-line" value="' . $orderProductList[$j]['weight'] . '  X  ' . $orderProductList[$j]['quantity'] . '" readonly></div> 
						<div class="col-md-2 mb-3">
						<input type="text" class="form-control-line" readonly value="' . $orderProductList[$j]['productTotalPrice'] . '"> </div>
						 </div> </br>';
            $productID = $orderProductList[$j]['productCode'];
        }
        //for activity
        $text = $role . " " . $userName . ' viewed Order Product "' . $productID . '" from ' . $ip;
        $log_text = array('code' => "demo", 'addID' => $addID, 'logText' => $text);
        $this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
        //Activity Track Ends
        $modelHtml.= '</form>';
        echo $modelHtml;
    }
    public function rshow() {
        print_r($this->GlobalModel->selectData('ordermaster')->result());
        exit();
    }
	
	
}
?>