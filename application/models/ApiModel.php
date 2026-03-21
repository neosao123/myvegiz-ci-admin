<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class ApiModel extends CI_Model{

 	function _construct() {
        parent::_construct();
		date_default_timezone_set('Asia/Kolkata');
    }
	
	function selectData($tblname,$limit='',$offset='',$condData=array()) {
      //  $query = $this->db->query("SELECT * FROM `".$tblname."` WHERE `isDelete`='0'" );
	  $condition=array('isDelete' => 0);
	  $condition=array_merge($condition,$condData);
	  $query = $this->db->order_by('id', 'ASC')->get_where($tblname,$condition, $limit, $offset);
        return $query;
    }
	
	// Read data using username and password
    public function login($condition) 
    {
        $this->db->select('*');
        $this->db->from('clientmaster');
        $this->db->where($condition);
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() == 1) 
        {
            return TRUE;
        }
        else
        {
			return FALSE;
        }
    }

	public function generateOTPMaster($contactNumber)
	{
		//generate random OTP
		//$otp = $this->randomOTP(6);
		$otp='123456';
		if($contactNumber=="8482940592")
		{
		   $otp='123123'; 
		}
		$result = $this->db->query("select * from registerOTP where otp='".$otp."' AND contactNumber='".$contactNumber."'");
		if($result){
			if($result->num_rows() > 0){
			//$otp = $this->randomOTP(6);
			$this->db->query("insert into `registerOTP`(`otp`,`contactNumber`) values('".$otp."','".$contactNumber."')");
			return $otp;
			} else {
				$this->db->query("insert into `registerOTP`(`otp`,`contactNumber`) values('".$otp."','".$contactNumber."')");
				return $otp;
			}
		}//if result
				
	}  //generateOTPMaster
	
	//generate random otp
	public function randomOTP($n) 
	{ 
		$characters = '0123456789'; 
		$randomString = ''; 
	  
		for ($i = 0; $i < $n; $i++) { 
			$index = rand(0, strlen($characters) - 1); 
			$randomString .= $characters[$index]; 
		} 
	  
		return $randomString; 
	}
    //check otp exists
	public function checkRegisterOTP($otp,$contactNumber)
	{
		$result = $this->db->query("select * from registerOTP where otp='".$otp."' AND contactNumber='".$contactNumber."'")->num_rows();
		if($result>0){			 
			$this->db->query("Delete from registerOTP where contactNumber='".$contactNumber."'");
			return true;
		} else {
			return false;
		}	
	}
	// My Read data from database to show data in admin page
	public function read_user_information($condition) 
    {  
        $this->db->select('`clientmaster.code`, `clientmaster.name`, `clientmaster.emailId`,`clientmaster.cityCode` ,`clientmaster.mobile`,`clientmaster.comCode`, `clientmaster.status`, `clientmaster.forgot`, `clientmaster.cartCode`, `clientmaster.isActive`, `citymaster.cityName` , `clientmaster.isCodEnabled`');
        $this->db->from('clientmaster');
		$this->db->join('citymaster','clientmaster.cityCode=citymaster.code');
        $this->db->where($condition);
        $this->db->limit(1);
        $query = $this->db->get();
		//echo $this->db->last_query();
        if ($query->num_rows()>0) 
        {
            return $query->result();
        } 
        else 
        {
            return false;
        }
    }// End My Read data from database to show data in admin page
	
	
	//login data for delivery boy
	public function login_delivery($condition) 
    {
        $this->db->select('*');
        $this->db->from('usermaster');
        $this->db->where($condition);
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() == 1) 
        {
			
            return TRUE;
        }
        else
        {
			return FALSE;
        }
    }
	
	
	// My Read data from database to show data in admin page
	public function read_Delivery_information($condition) 
    {  
        $this->db->select('`code`, `cityCode`,`empCode`,`name`, `username`, `role`,`userEmail`, `profilePhoto`,`mobile`, `isActive`,`deliveryType`,`latitude`,`longitude`');
        $this->db->from('usermaster');
        $this->db->where($condition);
        $this->db->limit(1);
        $query = $this->db->get();

        if ($query->num_rows() == 1) 
        {
            return $query->result();
        } 
        else 
        {
            return false;
        }
    }// End My Read data from database to show data in admin page
	
	
	public function getFirebaseIdsByAddress($arrdessCode)
	{
		$query = $this->db->query("SELECT usermaster.code,usermaster.firebase_id FROM usermaster left join `useraddresslineentries` ON usermaster.code=useraddresslineentries.userCode where useraddresslineentries.addressCode='".$arrdessCode."'" );
        return $query;
	}
	 
}
?>