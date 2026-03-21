<?php  
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	
	class Sendsms {
		var $CI=null;
		// private static $API_ACCESS_KEY = 'AIzaSyCfGyCTfYKVkPY9Md9KIPkDszKWzFY08eg';
		public function __construct()
		{
			$this->CI = & get_instance();
		}
		
		public function sendTextMessage($phone)
		{
			$ch = curl_init();
			$AUTH_KEY = SMSKEY;
			$senderID = 'OSAENV';
			$templateID = 'OSASTR';
			
			$url = "http://2factor.in/API/V1/";
			$url .= $AUTH_KEY."/ADDON_SERVICES/SEND/TSMS";
			
			$post = [
				'From' => $senderID,
				'To' => $phone,
				'TemplateName'   => $templateID,
				'VAR1'   => AppLink
			];
			
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
			// grab URL and pass it to the browser
			$res = curl_exec($ch);
			$err = curl_error($ch);
			
			curl_close($ch);
			$result = json_decode($res);			 
					
			if($result->Status=='Success'){
				$resp['status'] =true;
			} else {
				$resp['status'] =false;
			}
			return $resp;
		}
		
		public function sendOtpMessage($otp,$contactNumber)
		{
			$ch = curl_init();
			$senderID = SENDERID;
			$AUTH_KEY= SMSKEY;
			$message= "Your OTP For Forgot password is ".$otp.". Do not share with anyone. Thank You";
			$message = urlencode($message);
		    $smsContentType = "English";
			$url = "http://msg.icloudsms.com/rest/services/sendSMS/sendGroupSms?";
			$url .= "AUTH_KEY=".$AUTH_KEY."&message=".$message."&senderId=".$senderID."&routeId=1&mobileNos=".$contactNumber."&smsContentType=".$smsContentType;
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			// grab URL and pass it to the browser
			$output = curl_exec($ch);	
			if (curl_errno($ch)) {
				$res['status'] = "falied";
				$res['msg']  = 'error:' . curl_error($ch); 
				log_message('error', 'error:' . curl_error($ch));
				return $res;
			}
			curl_close($ch);
			$json = json_decode($output);
			if($json->responseCode=="3001") {
				$res['status'] = true;
				$res['msg'] = "SMS was successfully sent to ".$contactNumber. " & content => " .$message;
				log_message('error',$res['msg']);
			} else {
				$res['status'] = false;
				$res['msg'] = "Error Code ".$json->responseCode.", Failed to send message to ".$contactNumber. " & content => " .$message;
			}
			return $res;
		}
	}

?>