<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Maincategory extends CI_Controller {
	var $session_key;
	public function __construct()
	{
		parent::__construct();
		$this->load->helper('form','url','html');
		$this->load->library('form_validation');
		$this->load->model('GlobalModel');
		$this->load->model('GlobalModel1');
		$this->session_key = $this->session->userdata('key'.SESS_KEY);
		if (!isset($this->session->userdata['logged_in' . $this->session_key]['code'])) {
			redirect('Admin/login', 'refresh');
		}
	}
	
	public function listRecords()
	{
		$data['error']=$this->session->flashdata('response'); 
		$this->load->view('dashboard/header');
		$this->load->view('dashboard/maincategory/list',$data);
		$this->load->view('dashboard/footer');
	}
	
	public function getMainCategoryList()
	{
		$tables=array('maincategorymaster');		
		$requiredColumns=array
		(
			array('code', 'mainCategoryName', 'isActive','categoryPhoto') 
		);
		$conditions=array(
		);
		$extraConditionColumnNames=array(
		); 
		$extraConditions=array(
		);
		
		$Records = $this->GlobalModel1->make_datatables($tables,$requiredColumns,$conditions,$extraConditionColumnNames,$extraConditions);
		// print_r($Records->result());
		$srno=$_GET['start']+1;
		$data=array();
		foreach($Records->result() as $row) 
		{
			
			if($row->isActive_02 == "1")
			{
				$status = " <span class='label label-sm label-success'>Active</span>";
			}
			else
			{
				$status = " <span class='label label-sm label-warning'>Inactive</span>";
			}
			
			if($row->categoryPhoto_03!="")
			{
				$path = 'uploads/maincategory/'.$row->categoryPhoto_03;
				if(file_exists($path))
				{
					$path = base_url($path);
					$path = '<img style="width:50px;height:50px" src="'.$path.'">';
				}
				else 
				{
					$path = '<b>No Image</b>';
				}
			}
			else
			{
				$path = '<b>No Image</b>';
			}
			
			
			$actionHtml='<div class="btn-group">
					<button type="button" class="btn btn-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<i class="ti-settings"></i>
					</button>
					<div class="dropdown-menu animated slideInUp" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 35px, 0px);">
						<a class="dropdown-item edit" data-seq="'.$row->code_00.'" id="'.$row->code_00.'"><i class="ti-pencil-alt"></i> Edit</a>
					</div>
				</div>';
			
			$data[] = array(
				$srno,
				$row->code_00,
				$row->mainCategoryName_01,
				$path,
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
	
	public function edit()
	{
		$code=$this->input->post('code');
        $data['query'] = $this->GlobalModel->selectDataById($code,'maincategorymaster');    	 
    	$this->load->view('dashboard/maincategory/edit',$data);    	 
	}
	
	public function update()
	{
		$code= $this->input->post('code');		 
		$uploadRootDir = 'uploads/';
		$uploadDir = 'uploads/maincategory';
		if(!file_exists($uploadDir)) mkdir($uploadDir,0755,false);
		
		if (!empty($_FILES['imagePath']['name'])) {
			$tmpFile = $_FILES['imagePath']['tmp_name'];
			$ext = pathinfo($_FILES['imagePath']['name'], PATHINFO_EXTENSION);
			$name = $code . time() . '_.' . $ext;
			$filename = $uploadDir . '/' . $name;
			move_uploaded_file($tmpFile, $filename);
			if (file_exists($filename)) {
				$subData = array('categoryPhoto' => $name);
				$filedoc = $this->GlobalModel->doEdit($subData, 'maincategorymaster', $code);
				$response['status']="true";
				$response['message']="Image Successfully Updated.";
			} else {
				unlink($filename);
				$response['status']="false";
				$response['message']="Failed to upload the Image";
			}			 
		}
		else
		{
			$response['status']= "false";
			$response['message']="Cannot upload empty image";
		}		
		echo json_encode($response);
	}
}	