<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Kolkata');
class Offer extends CI_Controller {
    var $session_key;
    public function __construct() {
        parent::__construct();
		$this->load->helper('form','url','html');
      	$this->load->library('form_validation');
        $this->load->model('GlobalModel');
		$this->load->model('GlobalModel1');
		$this->load->model('Testing');
		$this->load->library('notificationlibv_3');
		$this->session_key = $this->session->userdata('key'.SESS_KEY);
		if (!isset($this->session->userdata['logged_in' . $this->session_key]['code'])) {
			redirect('Admin/login', 'refresh');
		}
	}
	
	public function listRecords() {
		$dataarray = array('isDelete'=>1,'isActive'=>0);
		$datawhere = array('isActive'=>1,'expireDate<'=>date('Y-m-d'));
		$result = $this->GlobalModel->doEditwitharray($dataarray,'offers',$datawhere);
		$data['error'] = $this->session->flashdata('response');
		$data['city'] = $this->GlobalModel->selectActiveData('citymaster');
		$this->load->view('dashboard/header');
        $this->load->view('dashboard/offer/list',$data);
		$this->load->view('dashboard/footer'); 
	}
	
	public function add() {
		$data['city'] = $this->GlobalModel->selectActiveData('citymaster');
		$this->load->view('dashboard/header');
        $this->load->view('dashboard/offer/add',$data);
		$this->load->view('dashboard/footer'); 
	}
	
	public function getOfferList() {	
		$tableName="offers";
		$cityCode = $this->input->get('cityCode');
		$orderColumns = array("offers.*,citymaster.cityName");
		$condition=array('offers.isDelete !='=>1,"offers.cityCode"=>$cityCode);
		$orderBy = array('offers' . '.id' => 'desc');
		$joinType=array('citymaster'=>'left');
		$join = array('citymaster'=>'offers.cityCode=citymaster.code');
		$groupByColumn=array();
		$limit=$this->input->GET("length");
		$offset=$this->input->GET("start");
		$extraCondition="";
		$like = array();
		$Records = $this->GlobalModel->selectQuery($orderColumns,$tableName,$condition,$orderBy,$join,$joinType,$like,$limit,$offset,$groupByColumn,$extraCondition);
		$srno=$_GET['start']+1;
		if($Records){
			foreach($Records->result() as $row)
			{
				if($row->isActive == 1)
				{
					$status = "<span class='label label-sm label-success'>Active</span>";
				} else {
					$status = "<span class='label label-sm label-warning'>Inactive</span>";
				}
				
				$date = date('Y-m-d');
				$notification="";
				if($date>=$row->startDate && $date<=$row->expireDate)
				{
					$notification='<a class="dropdown-item" target="_blank" href="'.base_url().'index.php/offer/offernotification/'.$row->code.'/'.$row->startDate.'/'.$row->expireDate.'/'.$row->cityCode.'"><i class="ti-bell"></i> Send Notification</a>';
				}
				$actionHtml='<div class="btn-group">
					<button type="button" class="btn btn-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<i class="ti-settings"></i>
					</button>
					<div class="dropdown-menu animated slideInUp" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 35px, 0px);">
						<a class="dropdown-item  blue" data-toggle="modal" data-target="#responsive-modal" data-seq="'.$row->code.'" href><i class="ti-eye"></i> Open</a>
						<a class="dropdown-item" href="'.base_url().'index.php/offer/edit/'.$row->code.'"><i class="ti-pencil-alt"></i> Edit</a>
						'.$notification.'
						<a class="dropdown-item  mywarning" data-seq="'.$row->code.'" id="'.$row->code.'"><i class="ti-trash" href></i> Delete</a>
					</div>
				</div>';
				
				
				if($row->image!=null){
					$path= base_url().'uploads/offer/'.$row->image;
						$offer_image='<div class="m-r-10"><img src="'.$path.'?'.time().'" alt="user" class="circle" width="45"></div>
							<div class="">';
				}
				else
				{
					$offer_image='NO Image';
				}
				
				$startDate = date('d-m-Y',strtotime($row->startDate));
			
				$endDate =  date('d-m-Y',strtotime($row->expireDate));
			 
					$data[] = array(
							$srno,
							$row->code,
							$row->cityName,
							$row->offerTitle,
							$offer_image,
							$row->description,
							$startDate,
							$endDate,
							$status,
							$actionHtml
						);
					$srno++;
			}
			$dataCount=sizeof($this->GlobalModel->selectQuery($orderColumns,$tableName,$condition,$orderBy,$join,$joinType,array(),'','','',$extraCondition)->result());
				$output = array(
								"draw"			  =>     intval($_GET["draw"]),
								"recordsTotal"    =>      $dataCount,
								"recordsFiltered" =>     $dataCount,
								"data"            =>     $data
							);
				echo json_encode($output);
		} else{
			$dataCount=0;
			$data =array();
			$output = array(
							"draw"			  =>     intval($_GET["draw"]),
							"recordsTotal"    =>     $dataCount,
							"recordsFiltered" =>     $dataCount,
							"data"            =>     $data
						);
			echo json_encode($output);
		}
	}
	
	public function save() {
		$title=trim($this->input->post('offerTitle'));
		$addID = $this->session->userdata['logged_in'.$this->session_key]['code'];
		$userRole = $this->session->userdata['logged_in'.$this->session_key]['role'];
		$userName = $this->session->userdata['logged_in'.$this->session_key]['username']; 
		$role = "";
		$ip=$_SERVER['REMOTE_ADDR'];
		$text = $role." ".$userName.' added new offer "'.$title.'" from '.$ip; 
		$log_text = array('code' => "demo",'addID'=>$addID,'logText' => $text);
		switch($userRole){
			case "ADM" : $role="Admin"; break;
			case "USR" : $role="User"; break; 
		}
		$this->form_validation->set_rules('offerTitle', 'Title is required', 'required');
		$this->form_validation->set_rules('start', 'Start Date is required', 'required');
		$this->form_validation->set_rules('end', 'End date is required', 'required');
		if ($this->form_validation->run()== FALSE) {	 	
			$data['error_message'] ='* Fields are Required!';
			$data['city'] = $this->GlobalModel->selectActiveData('citymaster');
			$this->load->view('dashboard/header');
			$this->load->view('dashboard/offer/add',$data);
			$this->load->view('dashboard/footer'); 
		} else {
			$start = $this->input->post("start");
			$end = $this->input->post("end");
			$startDate=date('Y-m-d',strtotime(str_replace('/','-',$start)));
			$expireDate=date('Y-m-d',strtotime(str_replace('/','-',$end)));
			$isActive = $this->input->post("isActive");
			$cityCode = trim($this->input->post('cityCode'));
			$data = array(
				'cityCode'=>trim($this->input->post('cityCode')),
				'offerTitle' =>trim($this->input->post("offerTitle")),
				'description' =>trim($this->input->post("offerDescription")),
				'startDate' =>$startDate,
				'expireDate' =>$expireDate,
				'termsCondition' =>trim($this->input->post("offetTerms_condi")),
				'addID' =>$addID,
				'addIP'=>$ip,
				'isActive' =>$this->input->post("isActive")
			);
				
			$code = $this->GlobalModel->addNew($data, 'offers', 'OFR');
			if($code != 'false')
			{
				if (!empty($_FILES['offerImage']['name'])) 
				{
					$uploadDir = 'uploads/offer/'; 
					$uploadPath = $uploadDir;
					
					$tmpFile = $_FILES['offerImage']['tmp_name'];
					$ext = pathinfo($_FILES['offerImage']['name'], PATHINFO_EXTENSION);
					$image_name = substr($code, 0, 3) . '_img_' . time() . '.' . $ext;
					$filename =  $uploadDir.$image_name;
					move_uploaded_file($tmpFile, $filename);
					
					$subData = array('image' => $image_name);
					$filedoc=$this->GlobalModel->doEdit($subData,'offers',$code);
				} 
				//$this->sendNotification($code,$startDate,$expireDate,$isActive,$cityCode);
				$response['status']=true;
				$response['message']="Offer Successfully Added.";
				$this->GlobalModel->activityAdd($log_text,'activitymaster','ACT');
			} else{
				$response['status']=false;
				$response['message']="Failed to add offer.";
			}
			$this->session->set_flashdata('response', json_encode($response));
			redirect(base_url().'index.php/Offer/listRecords');
		}
	}
	
	public function edit(){
		$code = $this->uri->segment('3');
		$data['offerdata'] = $this->GlobalModel->selectDataById($code,'offers');
		$data['city'] = $this->GlobalModel->selectActiveData('citymaster');
		$this->load->view('dashboard/header');
        $this->load->view('dashboard/offer/edit',$data);
		$this->load->view('dashboard/footer'); 
	}
	
	public function update(){
		$code = $this->input->post('code');
		$title=trim($this->input->post('offerTitle'));
		$addID = $this->session->userdata['logged_in'.$this->session_key]['code'];
		$userRole = $this->session->userdata['logged_in'.$this->session_key]['role'];
		$userName = $this->session->userdata['logged_in'.$this->session_key]['username']; 
		$role = "";
		$ip=$_SERVER['REMOTE_ADDR'];
		$text = $role." ".$userName.' added new offer "'.$title.'" from '.$ip; 
		$log_text = array('code' => "demo",'addID'=>$addID,'logText' => $text);
		switch($userRole){
			case "ADM" : $role="Admin"; break;
			case "USR" : $role="User"; break; 
		} 
		$this->form_validation->set_rules('offerTitle', 'Title is required', 'required|trim|min_length[5]');
		$this->form_validation->set_rules('start', 'Start Date is required', 'required');
		$this->form_validation->set_rules('end', 'End date is required', 'required');
		if ($this->form_validation->run()== FALSE) {	 	
			$data['error_message'] ='* Fields are Required!';
			$data['offerdata'] = $this->GlobalModel->selectDataById($code,'offers');
			$this->load->view('dashboard/header');
			$this->load->view('dashboard/offer/edit',$data);
			$this->load->view('dashboard/footer'); 
		} else {
		    $cityCode = trim($this->input->post('cityCode'));
			$start = $this->input->post("start");
			$end = $this->input->post("end");
			$startDate=date('Y-m-d',strtotime(str_replace('/','-',$start)));
			$expireDate=date('Y-m-d',strtotime(str_replace('/','-',$end)));
			$isActive = $this->input->post("isActive");
			$data = array(
				'cityCode'=>trim($this->input->post('cityCode')),
				'offerTitle' =>trim($this->input->post("offerTitle")),
				'description' =>trim($this->input->post("offerDescription")),
				'startDate' =>$startDate,
				'expireDate' =>$expireDate,
				'termsCondition' =>trim($this->input->post("offetTerms_condi")),
				'editID' =>$addID,
				'editIP'=>$ip,
				'isActive' =>$this->input->post("isActive")
			);
			$result = $this->GlobalModel->doEdit($data, 'offers', $code);
			if($result != 'false') {
				if (!empty($_FILES['offerImage']['name'])) 
				{
					$uploadDir = 'uploads/offer/'; 
					$uploadPath = $uploadDir;
					$tmpFile = $_FILES['offerImage']['tmp_name'];
					$ext = pathinfo($_FILES['offerImage']['name'], PATHINFO_EXTENSION);
					$image_name = substr($code, 0, 3) . '_img_' . time() . '.' . $ext;
					$filename =  $uploadDir.$image_name;
					move_uploaded_file($tmpFile, $filename);
					$subData = array('image' => $image_name);
					$filedoc=$this->GlobalModel->doEdit($subData,'offers',$code);
				} 
				$response['status']=true;
				$response['message']="Offer Successfully Updated.";
				$this->GlobalModel->activityAdd($log_text,'activitymaster','ACT');
			} else{
				$response['status']=false;
				$response['message']="No change in offer.";
			}
			//$this->sendNotification($code,$startDate,$expireDate,$isActive,$cityCode);
			$this->session->set_flashdata('response', json_encode($response));
			redirect(base_url().'index.php/Offer/listRecords');
		}
	}
	
	public function offernotification()
	{
		$code = $this->uri->segment('3');
		$datestart = $this->uri->segment('4');
		$dateEnd = $this->uri->segment('5');
		$cityCode = $this->uri->segment('6');
		
		$data['offerCode']=$code;
		$data['datestart']=$datestart;
		$data['dateEnd']=$dateEnd;
		$data['cityCode']=$cityCode;
		
		$data['listLength']=0;
		 $date = date('Y-m-d');
		
            if($date>=$datestart && $date<=$dateEnd)
            {
				if($cityCode=="")
				{
					$result = $this->db->query("Select firebaseId from clientmaster where isActive=1");
				} 
				
				else 
				{
					$result = $this->db->query("Select firebaseId from clientmaster where isActive=1 and cityCode='".$cityCode."'");

				}
				
				if($result->num_rows()>0)
				{
					$data['listLength']= sizeof($result->result_array());
					//print_r($data);
					//exit;
				}
			}
			else
			{
				echo "offer expired";
			}
		$this->load->view('dashboard/offer/notification',$data);
	}
	
	
	public function sendOffersNotify()
	{
		$offerCode= $this->input->post('offerCode');
		$cityCode= $this->input->post('cityCode');
		$start= $this->input->post('startVal');
		$end= $this->input->post('endVal');
		
		//get notification data
		$offerDataResult = $this->GlobalModel->selectActiveDataByField('code',$offerCode,'offers');
		if($offerDataResult->num_rows()>0)
		{
			//print_r($offerDataResult->result_array());
			$row = $offerDataResult->result_array()[0];
			$notify['code'] =  $row['code'];
			$notify['offerTitle'] = $row['offerTitle'];
			$notify['description'] = $row['description'];
			$notify['termsCondition'] = trim($row['termsCondition']);
			$image = $row['image'];
			if($image=="")
			{
				$notify['image'] = "";
			} 
			else
			{
				$notify['image'] = base_url().'uploads/offer/'.$image;
			}
			$notify['type'] = 'Offer';
			$query="Select code,firebaseId from clientmaster where isActive=1 and cityCode='".$cityCode."' limit ".$start.", ".$end;
			$result = $this->db->query($query);
			//print_r($result->result_array());
			if($result)
			{
				$DeviceIdsArr =array();
				foreach($result->result() as $rowData)
				{
					if($rowData->firebaseId!=""){
						$DeviceIdsArr[] = $rowData->firebaseId;
					}
				}
				
				$random=rand(1,999);
				$dataArr = array();
				$dataArr['device_id'] = $DeviceIdsArr;
				$dataArr['message'] = $notify['description'];//Message which you want to send
				$dataArr['title'] = $notify['offerTitle'];
				$dataArr['image'] = $notify['image'];
				$dataArr['type'] = $notify['type'];
				$dataArr['product_id'] =  $notify['code'];
				$dataArr['random_id']= $random;

				$notification['device_id'] = $DeviceIdsArr;
				$notification['message'] = $notify['description'];//Message which you want to send
				$notification['title'] = $notify['offerTitle'];
				$notification['image'] = $notify['image'];
				$notification['product_id'] = $notify['code'] ;
				$dataArr['type'] = $notify['type'];
				$notification['random_id']= $random;
				
				$notify = $this->notificationlibv_3->sendNotification($dataArr,$notification);
				//print_r($notify);
				//exit;
				if($notify)
				{
					print_r($notify);
				    return 'Notification Send Successfully';
				}
				else 
				{
				    return 'Notification Failed';
				}
			}
		}
		//end notification data
	}
	
	
	
	
	
	
	public function sendNotification($code,$datestart,$dateEnd,$isActive,$cityCode)
	{
        if($isActive==1)
        {
            $date = date('Y-m-d');
            if($date>=$datestart && $date<=$dateEnd)
            {
                $data = $this->GlobalModel->selectActiveDataByField('code',$code,'offers');
                if($data->num_rows()>0)
                {
                    $row = $data->result_array()[0];
                    $notify['code'] =  $row['code'];
                    $notify['offerTitle'] = $row['offerTitle'];
                    $notify['description'] = $row['description'];
                    $notify['termsCondition'] = trim($row['termsCondition']);
                    $image = $row['image'];
                    if($image=="")
                    {
                        $notify['image'] = "";
                    } 
                    else 
                    {
                        $notify['image'] = base_url().'uploads/offer/'.$image;
                    }
                    $notify['type'] = 'Offer';
                    if($cityCode=="")
                    {
                        $result = $this->db->query("Select firebaseId from clientmaster where isActive=1");
                    } 
                    else 
                    {
                        $result = $this->db->query("Select firebaseId from clientmaster where isActive=1 and cityCode='".$cityCode."'");
                    }
                    if($result->num_rows()>0)
                    {
                        $listLength = sizeof($result->result_array());
                        $listLength.'<br>';
                        $modVal = $listLength / 100;
                        if($modVal>0 && $modVal<100)
                        {
                            $offset = $modVal*100;
                            if($cityCode=="")
                            {
                                $result = $this->db->query("SELECT firebaseId FROM clientmaster where isActive=1");
                            } 
                            else 
                            {
                                $result = $this->db->query("SELECT firebaseId FROM clientmaster where isActive=1 and cityCode ='".$cityCode."'");
                            }
                            if($result)
                            {
                                $DeviceIdsArr =array();
                                foreach($result->result() as $rowData)
                                {
                                    $DeviceIdsArr[] = $rowData->firebaseId;
                                }
								
                                $random=rand(1,999);
                                $dataArr = array();
                                $dataArr['device_id'] = $DeviceIdsArr;
                                $dataArr['message'] = $notify['description'];//Message which you want to send
                                $dataArr['title'] = $notify['offerTitle'];
                                $dataArr['image'] = $notify['image'];
                                $dataArr['type'] = $notify['type'];
                                $dataArr['product_id'] =  $notify['code'];
                                $dataArr['random_id']= $random;
                                
                                $notification['device_id'] = $DeviceIdsArr;
                                $notification['message'] = $notify['description'];//Message which you want to send
                                $notification['title'] = $notify['offerTitle'];
                                $notification['image'] = $notify['image'];
                                $notification['product_id'] = $notify['code'];
                                $dataArr['type'] = $notify['type'];
                                $notification['random_id']= $random;
                            
                                $notify = $this->notificationlibv_3->sendNotification($dataArr,$notification);
                                if($notify)
                                {
                                    return 'Notification Send Successfully';
                                } 
                                else 
                                {
                                    return 'Notification Failed';
                                }
                            }
                        } 
                        else 
                        {
                            for($i=1;$i<=$modVal;$i++)
                            {
                                $offset = $i*100;
                                if($cityCode=="")
                                {
                                    $result = $this->db->query("SELECT firebaseId FROM clientmaster where isActive=1 limit ".$offset.",100");
                                } 
                                else 
                                {
                                 $result = $this->db->query("SELECT firebaseId FROM clientmaster where isActive=1 and cityCode ='".$cityCode."' limit ".$offset.",100");
                                }
                                if($result)
                                {
                                    $DeviceIdsArr =array();
                                    foreach($result->result() as $rowData)
                                    {
                                        $DeviceIdsArr[] = $rowData->firebaseId;
                                    }
                                    $random=rand(1,999);
                                    $dataArr = array();
                                    $dataArr['device_id'] = $DeviceIdsArr;
                                    $dataArr['message'] = $notify['description'];//Message which you want to send
                                    $dataArr['title'] = $notify['offerTitle'];
                                    $dataArr['image'] = $notify['image'];
                                    $dataArr['type'] = $notify['type'];
                                    $dataArr['product_id'] =  $notify['code'];
                                    $dataArr['random_id']= $random;
                                    $notification['device_id'] = $DeviceIdsArr;
                                    $notification['message'] = $notify['description'];//Message which you want to send
                                    $notification['title'] = $notify['offerTitle'];
                                    $notification['image'] = $notify['image'];
                                    $notification['product_id'] = $notify['code'] ;
                                    $dataArr['type'] = $notify['type'];
                                    $notification['random_id']= $random;
                                    
                                    $notify = $this->notificationlibv_3->sendNotification($dataArr,$notification);
                                    if($notify)
                                    {
                                        return 'Notification Send Successfully';
                                    } 
                                    else 
                                    {
                                        return 'Notification Failed';
                                    }
                                }
                            }
                        }
                    } 
                    else 
                    {
                        return 'Notification Failed';
                    }
                }
            }
        }
	}  
	
	public function delete(){
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
		$text = $role." ".$userName.' deleted offer of code '.$code.'  from '.$ip; 
		
		$log_text = array(
							'code' => "demo",
							'addID'=>$addID,
							'logText' => $text
						);
		$this->GlobalModel->activityAdd($log_text,'activitymaster','ACT'); 
		 
		$data=array(
					'isActive' => 0,
					'isDelete' => 1,
					'deleteID' => $addID,
					'deleteIP' => $ip
				 );
	  
		$resultData=$this->GlobalModel->doEdit($data,'offers',$code);
		
		 
		//Activity Track Ends
		
		echo $this->GlobalModel->delete($code,'offers');
		
	}
	
	public function view(){
		$code = $this->input->GET	('code');
		$data['offerdata'] = $this->GlobalModel->selectDataById($code,'offers');
		$cityCode = $data['offerdata']->result()[0]->cityCode;
		$data['city'] = "";
		if($cityCode!=""){
			$citydata = $this->GlobalModel->selectDataById($cityCode, 'citymaster');
			if($citydata){
				$data['city'] = $citydata->result()[0]->cityName;
			} 
		}
        $this->load->view('dashboard/offer/view',$data);
	}
}
?>