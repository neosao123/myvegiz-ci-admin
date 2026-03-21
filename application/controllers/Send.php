<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Send extends CI_Controller {
    var $session_key;
	public function __construct()
	{
		parent::__construct();
		$this->load->helper('form', 'url', 'html');
		$this->load->model('GlobalModel');
		$this->load->library('sendemail');
	}
	public function connectServer(){
		//echo 1;
		$result = exec("/opt/cpanel/ea-nodejs16/bin/node server/server.js", $node_output,$rcode);
		if($rcode==1){
			log_message('error','Server Started');
		}else{
			log_message('error','Failed to start server');
		}
	}
	
    public function index(){
		/*$url = base_url().'Send/connectServer';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		$result = curl_exec($ch);
		curl_close($ch);
		print_r($result);
		exit();*/
        $data = array();
        $allmsgs = $this->db->select('*')->from('tbl_msg')->get()->result_array();
        $data['allMsgs'] = $allmsgs;
         $this->load->view('dashboard/header');
        $this->load->view('dashboard/message',$data);
        $this->load->view('dashboard/footer');
    }
    public function send(){
        $dbCode = $this->input->post('deliveryBoyCode');
        $orderCode = $this->input->post('orderCode');
        $arr['latitude'] = $this->input->post('latitude');
        $arr['longitude'] = $this->input->post('longitude');
        $arr['addDate'] = date('Y-m-d H:i:s');
		$filename = 'assets/order_tracking/'. $orderCode.'.json';
		if (file_exists($filename)){
			$jsonString = file_get_contents($filename);
			$data = json_decode($jsonString, true);
			array_unshift($data,$arr);
			$newJsonString = json_encode($data);
			file_put_contents($filename, $newJsonString);
		}else{
			$content = json_encode(array($arr));
			file_put_contents($filename, $content, FILE_APPEND | LOCK_EX);
		}
		$response['latitude'] = $this->input->post('latitude');
		$response['longitude'] = $this->input->post('longitude');
		$response['addDate'] = date('d/m/Y h:i A');
		$response['success'] = true;
        echo json_encode($response);
    }
	
	public function sendEmail(){
		$orderCode = $this->input->post('orderCode');
		$clientEmail = 'shraddharhatole14997@gmail.com';
		$deliveryBoyEmail = 'shraddharhatole14997@gmail.com';
		$checkActiveConnectedPort = $this->GlobalModel->selectQuery('activeports.port,activeports.id','activeports',array('activeports.status'=>1,"activeports.isConnected"=>0),array('activeports.id'=>'ASC'),array(),array(),array(),'1');
		if($checkActiveConnectedPort){
			if($checkActiveConnectedPort->num_rows()>0){
				$port=$checkActiveConnectedPort->result_array()[0]['port'];
				$id=$checkActiveConnectedPort->result_array()[0]['id'];
				$updateOrder['trackingPort']=$port;
				$update = $this->GlobalModel->doEdit($updateOrder,'vendorordermaster',$orderCode);
				$updatePort['isConnected']=1;
				$this->db->query("update activeports set isConnected=1 where id='".$id."'");
				echo 'connection successful';
				/*if($update){
					$url1 = base_url().'Send/trackOrder/1/'.$orderCode;
					$clsubject = 'Order Tracking Details: '.$orderCode;
					$clmessage = 'Track your order by visiting following: <br>'.$url1; 
					$clResult = $this->sendemail->sendMailOnly($clientEmail,$clsubject,$clmessage);
					if($clResult['success']=='true'){
						echo 'Mail send to client\n';
					}
					$url2 = base_url().'Send/trackOrder/2/'.$orderCode;
					$dbsubject = 'Update Order Tracking: '.$orderCode;
					$dbmessage = 'Update your current locations by visiting following: <br>'.$url2; 
					$dbResult = $this->sendemail->sendMailOnly($deliveryBoyEmail,$dbsubject,$dbmessage);
					if($dbResult['success']=='true'){
						echo 'Mail send to delivery boy';
					}
				}*/
			}
		}
	}
	
	public function trackOrder(){
		$type = $this->uri->segment(3);
		$orderCode = $this->uri->segment(4);
		$orderDetails = $this->GlobalModel->selectQuery('vendorordermaster.trackingPort,vendorordermaster.deliveryBoyCode','vendorordermaster',array('vendorordermaster.code'=>$orderCode,"vendorordermaster.isActive"=>1));
		if($orderDetails){
			if($orderDetails->num_rows()>0){
				$result= $orderDetails->result_array()[0];
				$data['port'] = $result['trackingPort'];
				$data['deliveryBoyCode'] = $result['deliveryBoyCode'];
				$data['orderCode'] = $orderCode;
				$data['type']=$type;
				$data['trackingDetails']=false;
				if (file_exists('assets/order_tracking/'. $orderCode.'.json')){
					$jsonString = file_get_contents('assets/order_tracking/'. $orderCode.'.json');
					$fileData = json_decode($jsonString, true);
					if(!empty($fileData[0])){
						$data['trackingDetails']=$fileData;
					}
				}
				$this->load->view('dashboard/header');
				$this->load->view('dashboard/order/realtime_messages',$data);
				$this->load->view('dashboard/footer');
			}
		}
	}
}