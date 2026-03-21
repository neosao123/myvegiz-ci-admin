<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	
class Notificationlib {
	var $CI=null;
	private static $API_ACCESS_KEY = 'AIzaSyCfGyCTfYKVkPY9Md9KIPkDszKWzFY08eg';
    public function __construct()
	{
		$this->CI = & get_instance();
	}
	
	public function sendNotification($data) {
	        $url = 'https://android.googleapis.com/gcm/send';
	        $message = array(
	            'title' => $data['title'],
	            'message' => $data['message'],
	            'product_Code' => $data['product_id'],
	            'tickerText' => '',
	            'msgcnt' => 1,
	            'vibrate' => 1
	        );
	        
	        $headers = array(
	        	'Authorization: key=' .self::$API_ACCESS_KEY,
	        	'Content-Type: application/json'
	        );
	
	        $fields = array(
	            'registration_ids' => array($data['device_id']),
	            'data' => $message,
	        );
	
	    	 return $this->CI->useCurl($url, $headers, json_encode($fields));

	    	//return self::useCurl($url, $headers, json_encode($fields));
    }
	
	
	// Curl 
	private function useCurl($url, $headers, $fields = null) {
	        // Open connection
	        $ch = curl_init();
	        if ($url) {
	            // Set the url, number of POST vars, POST data
	            curl_setopt($ch, CURLOPT_URL, $url);
	            curl_setopt($ch, CURLOPT_POST, true);
	            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	     
	            // Disabling SSL Certificate support temporarly
	            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	            if ($fields) {
	            	//curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
	                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
	            }
	     
	            // Execute post
	            $result = curl_exec($ch);
	            if ($result === FALSE) {
	                die('Curl failed: ' . curl_error($ch));
	            }
	     
	            // Close connection
	            curl_close($ch);
	
	            return $result;
        }
    }
		
		
}