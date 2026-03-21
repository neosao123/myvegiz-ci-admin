<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	
class Sendmail {
	var $CI=null;
    public function __construct()
	{
		$this->CI = & get_instance();
	}
	
	public function sendMail($to,$attachment='',$subject,$message)
	{
		$mailConfig['mailpath'] = "/usr/sbin/sendmail";
		$mailConfig['protocol'] = "sendmail";
		$mailConfig['smtp_host'] = "relay-hosting.secureserver.net";
		$mailConfig['smtp_user'] = HR_EMAIL;
		$mailConfig['smtp_pass'] = HR_EMAIL_PASSWORD;
		$mailConfig['smtp_port'] = "25";
		$mailConfig['mailtype'] = "html";

		$this->CI->load->library('email', $mailConfig);
		$this->CI->email->from(HR_EMAIL, 'ROCKTECH ENGINEERS');
		$this->CI->email->to($to);
		$this->CI->email->subject($subject);
		$this->CI->email->message($message);
		$this->CI->email->attach($attachment);
		if($this->CI->email->send())
		{
			return true;
		}
		else
		{
			return false; 
		}
	}
	
	public function sendFreeMail($to,$attachment='',$subject,$message)
	{
		$config = array (
		  'protocol' =>'mail',
		  'mailtype' => 'text',
		  'charset'  => 'utf-8',
		  'priority' => '1',
		  'smtp_crypto'=>'tls'
			
		 );
		 
		$this->CI->load->library('email',$config);
		$this->CI->email->set_newline("\r\n");
		$this->CI->email->from(HR_EMAIL,'ROCKTECH ENGINEERS');
		$this->CI->email->to($to);
		$this->CI->email->subject($subject);
		$this->CI->email->message($message);
		$this->CI->email->attach($attachment);
		if($this->CI->email->send())
		{
			return true;
		}
		else
		{
			return false; 
		}
	}
}
?>