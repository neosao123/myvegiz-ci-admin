<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Uom extends CI_Controller {
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
    	$data['query'] = $this->GlobalModel->selectDataExcludeDelete('uommaster');
		// print_r($data['query']->result());
		// exit();
    	$this->load->view('dashboard/header');
    	$this->load->view('dashboard/uom/list',$data);
    	$this->load->view('dashboard/footer');
    	
    }
    public function add()
    {
    	$this->load->view('dashboard/header');
    	$this->load->view('dashboard/uom/add');
    	$this->load->view('dashboard/footer');
    	
    }

    public function edit()
    {
          $code=$this->uri->segment(3);
		
        //$data['query'] = $this->GlobalModel->selectDataById($code,'uommaster');
		
		$table_name = 'uommaster';
        $orderColumns = array("uommaster.*");
        $cond=array('uommaster' . '.code' => $code);
        // $like= array();
        // $orderBy = array();
        // $joinType=array();
        // $join = array();

         $data['query']=$this->GlobalModel->selectQuery($orderColumns,$table_name,$cond);
		
       
    	$this->load->view('dashboard/header');
    	$this->load->view('dashboard/uom/edit',$data);
    	$this->load->view('dashboard/footer');
    	
    }

    // public function view()
    // {
        // $code=$this->uri->segment(3);
        // $data['query'] = $this->GlobalModel->selectDataById($code,'uommaster');
    	// $this->load->view('dashboard/header');
    	// $this->load->view('dashboard/Uom/view',$data);
    	// $this->load->view('dashboard/footer');
    	
    // }
   public function getUomList()
	{
		 $tableName = 'uommaster';
		$orderColumns = array("uommaster.*");
		$search = $this->input->GET("search")['value'];
		$condition = array();
		$orderBy = array('uommaster.id'=>'desc');
		$joinType = array();
		$join = array();
		$groupByColumn = array();
		$limit = $this->input->GET("length");
		$offset = $this->input->GET("start");
		$srno = $offset + 1;
		$like = array();
		$extraCondition=" (uommaster.isDelete=0 or uommaster.isDelete is null)";
		$like = array("uommaster.uomDescription" => $search . "~both","uommaster.code" => $search . "~both","uommaster.uomName" => $search . "~both","uommaster.uomSName" => $search . "~both");
		$data=array();
		$dataCount = 0;
		$Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
		$srno=$_GET['start']+1;
		$data=array();
		if($Records){
			foreach($Records->result() as $row) {
				if($row->isActive == "1"){
				    $status = " <span class='label label-sm label-success'>Active</span>";
				}else{
				   $status = " <span class='label label-sm label-warning'>Inactive</span>";
				}
				 $actionHtml='<div class="btn-group">
					<button type="button" class="btn btn-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<i class="ti-settings"></i>
					</button>
					<div class="dropdown-menu animated slideInUp" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 35px, 0px);">
						<a class="dropdown-item  blue" data-toggle="modal" data-target="#responsive-modal" data-seq="'.$row->code.'" href><i class="ti-eye"></i> Open</a>
						<a class="dropdown-item" href="'.base_url().'Uom/edit/'.$row->code.'"><i class="ti-pencil-alt"></i> Edit</a>
						<a class="dropdown-item mywarning " data-seq="'.$row->code.'" id="'.$row->code.'"><i class="ti-trash" ></i> Delete</a>
					</div>
				</div>';
								 
               $data[] = array(
					$srno,
					$row->code,
                    $row->uomName,
                    $row->uomSName,
					$row->uomDescription,
					$status,
					$actionHtml
               );
			  $srno++;
            }
            $dataCount = sizeof($this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, "", "", $groupByColumn, $extraCondition)->result());
		}
        $output = array("draw" => intval($_GET["draw"]), "recordsTotal" => $dataCount, "recordsFiltered" => $dataCount, "data" => $data);
        echo json_encode($output);
	}
     public function save()
    { 
               $uomSName= strtoupper($this->input->post("uomSName"));
			   
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
				$text = $role." ".$userName.' added new UOM "'.$uomSName.'" from '.$ip; 
				
				$log_text = array(
								'code' => "demo",
								'addID'=>$addID,
								'logText' => $text
							);
				
				//Activity Track Ends
				
				$result = $this->GlobalModel->checkDuplicateRecord('uomSName',$uomSName,'uommaster');
			   
                  if ($result!=FALSE) 
                  {
                  $data = array(
                      'error_message' => 'Duplicate Uom Short Name'
                                       );
                  $this->load->view('dashboard/header');
                  $this->load->view('dashboard/uom/add', $data);
                  $this->load->view('dashboard/footer');
                  }
                  else
                  {
					  
						   $this->form_validation->set_rules('uomName', 'UOM Name', 'required');
						 $this->form_validation->set_rules('uomSName', 'UOM Short Name', 'required');
						
						   if ($this->form_validation->run()== FALSE) 
						{	 
						
								$data['error_message'] ='* Fields are Required!';
								 	$this->load->view('dashboard/header');
									$this->load->view('dashboard/uom/add',$data);
									$this->load->view('dashboard/footer');
						}
							
						 else
						 {
						$data =array(
                        'uomName'=>trim($this->input->post('uomName')),
                        'uomSName'=>strtoupper($this->input->post('uomSName')),
                        'uomDescription'=>trim($this->input->post('uomDescription')),
						'addID'=>$addID,
						'addIP'=>$ip,
                        'isActive' => trim($this->input->post("isActive"))
              );

               $result = $this->GlobalModel->addWithoutYear($data,'uommaster','UOM');
			    if($result!='false')
			{
				$response['status']=true;
				$response['message']="UOM Successfully Added.";
				$this->GlobalModel->activityAdd($log_text,'activitymaster','ACT');
				
			}
			else
			{
				$response['status']=false;
				$response['message']="Failed To Add UOM";
			}
			 // print_r($response);
			 $this->session->set_flashdata('response', json_encode($response));
              redirect(base_url().'uom/listRecords');
            }
	   }
    }
    public function update()
    {
        $code =  $this->input->post('code');
		$uomName = trim($this->input->post('uomName'));
		
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
		$text = $role." ".$userName.' updated UOM "'.$uomName.'" from '.$ip; 
		$log_text = array(
						'code' => "demo",
						'addID'=>$addID,
						'logText' => $text
					);
					
		//Activity Track Ends 
		 
						   $this->form_validation->set_rules('uomName', 'UOM Name', 'required');
						 $this->form_validation->set_rules('uomSName', 'UOM Short Name', 'required');
						
						   if ($this->form_validation->run()== FALSE) 
						{	 
						
								$data['error_message'] ='* Fields are Required!';
								  $data['query'] = $this->GlobalModel->selectDataById($code,'uommaster');
                                  $this->load->view('dashboard/header');
                                  $this->load->view('dashboard/uom/edit',$data);
    	                          $this->load->view('dashboard/footer');
						}
							
						 else
						 {
		 
							$data =array(
								'uomName'=>$uomName,
								'uomSName'=>trim($this->input->post('uomSName')),
								'uomDescription'=>trim($this->input->post('uomDescription')),
								'editID'=>$addID,
								'editIP'=>$ip,
								'isActive' => trim($this->input->post("isActive"))
							);

           $result = $this->GlobalModel->doEdit($data,'uommaster',$code);
		   if($result!='false')
			{
				$response['status']=true;
				$response['message']="UOM Successfully Updated.";
				$this->GlobalModel->activityAdd($log_text,'activitymaster','ACT');
			}
			else
			{
				$response['status']=false;
				$response['message']="No change In UOM";
			}
			  //print_r($result);
			  //print_r($response);
			 $this->session->set_flashdata('response', json_encode($response));
        redirect(base_url().'uom/listRecords');
	   }
    }
    public function delete()
        {
			$code = $this->input->post('code');
			
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
			$dataQ = $this->GlobalModel->selectDataByField('code',$code,'uommaster');  
			$uomName='';
			
			foreach ($dataQ->result() as $row) 
			{	
				$uomName = $row->uomName; 
			}
			
			$text = $role." ".$userName.' deleted UOM "'.$uomName.'" from '.$ip; 
			
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
		  
			$resultData=$this->GlobalModel->doEdit($data,'uommaster',$code);
			
			//Activity Track Ends
			
            echo $this->GlobalModel->delete($code,'uommaster');
            
           // redirect(base_url() . 'index.php/uom/listrecords', 'refresh');
         } 
	public function view()
	{
		 $code = $this->input->get('code');
		
			$addID = $this->session->userdata['logged_in'.$this->session_key]['code'];
			$userRole = $this->session->userdata['logged_in'.$this->session_key]['role'];
			$userName = $this->session->userdata['logged_in'.$this->session_key]['username']; 
			$role = "";
			
			switch($userRole){
					case "ADM" : $role="Admin"; break;
					case "USR" : $role="User"; break;
				}
		
		$ip=$_SERVER['REMOTE_ADDR'];
		$table_name = 'uommaster';
        $orderColumns = array("uommaster.*");
        $cond=array('uommaster' . ".code" => $code);
        $like= array();
        $orderBy = array();
        $joinType=array();
        $join = array();

        $Records=$this->GlobalModel->selectQuery($orderColumns,$table_name,$cond,$orderBy,$join,$joinType,$like);
       
		$modelHtml='<form>';
		$activeStatus="";
		foreach($Records->result() as $row)
		{
			
			
			
			if($row->isActive == "1")
            {
                $activeStatus='<span class="label label-sm label-success">Active</span>';
            }
			else
			{
				$activeStatus='<span class="label label-sm label-warning">Inactive</span>';
			}
												  
			$modelHtml.='<div class="form-row"><div class="col-md-3 mb-3"><label><b>Code:</b> </label>
						<input type="text" value="'.$row->code.'" class="form-control-line"  readonly></div>
					<div class="col-md-5 mb-3"><label><b> Uom Name:</b> </label>
						<input type="text" class="form-control-line" value="'.$row->uomName.'"  readonly></div> 
					<div class="col-md-4 mb-3"><label><b>Uom Short Name:</b> </label>
						<input type="text" class="form-control-line" value="'.$row->uomSName.'"  readonly></div></div>     
					<div class="col-md-12 mb-3"><label><b>Uom Description:</b> </label>
						<input type="text" class="form-control-line" row="2" cols="50" readonly value="'.$row->uomDescription.'"> </div>
					<div class="form-group">'.$activeStatus .'</div>';
					
					//for activity
					
					$text = $role." ".$userName.' viewed UOM "'.$row->uomName.'" from '.$ip; 
					
					$log_text = array(
										'code' => "demo",
										'addID'=>$addID,
										'logText' => $text
									);
					$this->GlobalModel->activityAdd($log_text,'activitymaster','ACT');
					
					//Activity Track Ends
		}
		
		$modelHtml.='</form>';
		echo $modelHtml;
	}
    
  public function test(){
	  
		 
	  $data['query'] = $this->GlobalModel->selectDataExcludeDelete('uommaster');
	  print_r($data['query']->result());
	 
  }

}
?>