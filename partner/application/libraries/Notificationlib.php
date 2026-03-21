<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	
class Notificationlib {
	var $CI=null;
	// private static $API_ACCESS_KEY = 'AIzaSyCfGyCTfYKVkPY9Md9KIPkDszKWzFY08eg';
    public function __construct()
	{
		$this->CI = & get_instance();
	}
	
	public function sendNotification($data,$notification) {
		
		$API_ACCESS_KEY = 'AAAA1vIBpSk:APA91bGOMnnKJ2DlF1UraRY_nQZ09DljykMk72f0_NTurbZPP4yyBoDh3_rSR0qWatdHTxtAFCtASWLGQy7tBQ_gn5zE4Ufg_5jtZh6V4g8mbbcFk1MnL_4rHhkiNGkO28vBofj2bM1o';
	       $url = 'https://fcm.googleapis.com/fcm/send';
	        $message = array(
	            'title' => $data['title'],
	            'message' => $data['message'],
				"image"=>$data['image'],
	            'productCode' => $data['product_id'],
				'random_id' => $data['random_id'],
				'priority' => 1,
				'vibrate' => 1,
				'sound' => 1,
				
	        );
			
			$notifyData = array(
	            'title' => $notification['title'],
	            'body' => $notification['message'],
				"image"=>$notification['image'],
	            'productCode' => $notification['product_id'],
				'random_id' => $notification['random_id'],
				'priority' => 1,
				'vibrate' => 1,
				'sound' => 1,
				
	        );
	        
	       $headers = array(
					'Authorization:key='.$API_ACCESS_KEY,'Content-Type:application/json'
				);
	
	        $fields = array(
	            'registration_ids' => $data['device_id'],
	            'data' => $message,
				'notification' => $notifyData 
	        );
	
	
	
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
	            	curl_setopt($ch,CURLOPT_POSTFIELDS, json_encode($fields));
	                // curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
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
	
	
	public function pushNotification($data,$notification) {
		
		$API_ACCESS_KEY = 'AAAA1vIBpSk:APA91bGOMnnKJ2DlF1UraRY_nQZ09DljykMk72f0_NTurbZPP4yyBoDh3_rSR0qWatdHTxtAFCtASWLGQy7tBQ_gn5zE4Ufg_5jtZh6V4g8mbbcFk1MnL_4rHhkiNGkO28vBofj2bM1o';
	       $url = 'https://fcm.googleapis.com/fcm/send';
	        $message = array(
	            'title' => $data['title'],
	            'message' => $data['message'],
	            'orderCode' => $data['order_id'],
				'random_id' => $data['random_id']
	        );
			
			$notifyData = array(
	            'title' => $notification['title'],
	            'body' => $notification['message'],
	            'orderCode' => $notification['order_id'],
				'random_id' => $notification['random_id']
	        );
	        
	       $headers = array(
					'Authorization:key='.$API_ACCESS_KEY,'Content-Type:application/json'
				);
	
	        $fields = array(
	            'registration_ids' => $data['device_id'],
	            'data' => $message,
				'notification' => $notifyData 
	        );
	
	
	
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
	            	curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode($fields));
	                // curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
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