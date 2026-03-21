<?php  
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once FCPATH . 'vendor/autoload.php';
use Google\Auth\Credentials\ServiceAccountCredentials;
	
class Notificationlibv_3 {
	// private static $API_ACCESS_KEY = 'AIzaSyCfGyCTfYKVkPY9Md9KIPkDszKWzFY08eg';
	public function __construct()
	{
		$this->CI = & get_instance();
	}
	
	public function sendNotification($data, $notification,$ringType="")
	{
		$image = isset($data['image']) && !empty($data['image']) 
			? $data['image']. '?v=' . time()  
			: base_url('uploads/logo.png');

		$url = 'https://fcm.googleapis.com/v1/projects/myvegiz-a3615/messages:send';
		$serviceAccountPath = FCPATH . 'myvegiz-a3615-firebase-adminsdk-k1wrf-c87a10b550.json';

		if (!file_exists($serviceAccountPath)) {
			throw new Exception('Service account key file not found: ' . $serviceAccountPath);
		}

		$credentials = new ServiceAccountCredentials(
			'https://www.googleapis.com/auth/firebase.messaging',
			$serviceAccountPath
		);
		$accessToken = $credentials->fetchAuthToken()['access_token'];

		$responses = [];

		foreach ($notification['device_id'] as $token) {
			$body = [
				'message' => [
					"data" => [
						"title" => $data['title'],
						"body" => $data['message'],						
						"type" => "ringing",
					    'sound' => 'ringing.mp3',
						'channel_id' =>"1",
					],
					"notification" => [
						"title" => $notification['title'],
						"body" => $notification['message'],						
						"image" => $image
					],
					"android" => [
						"priority" => "high",
						"ttl" => "1000s",
						"notification" => [
							'sound' => 'ringing.mp3',
							'image' => $image,
							'channel_id' => "1",
							'vibrate_timings' => ["0.5s", "1s", "0.5s"],
						],
					],
					"token" => $token
				]
			];

			// Use cURL instead of Laravel's HTTP client
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
			curl_setopt($ch, CURLOPT_HTTPHEADER, [
				'Authorization: Bearer ' . $accessToken,
				'Content-Type: application/json'
			]);

			$result = curl_exec($ch);
			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

			if (curl_errno($ch)) {
				$responses[$token] = 'Curl Error: ' . curl_error($ch);
			} elseif ($httpCode >= 200 && $httpCode < 300) {
				$responses[$token] = json_decode($result, true);
			} else {
				$responses[$token] = 'Error: ' . $result;
			}

			curl_close($ch);
		}

		return $responses;
		
		//print_r($responses);
	}
	
	public function sendDeliveryNotification($data, $notification,$ringType="normal")
	{
		$image = isset($data['image']) && !empty($data['image']) 
			? $data['image'] 
			: base_url('notify.png');

		$url = 'https://fcm.googleapis.com/v1/projects/myvegiz-a3615/messages:send';
		$serviceAccountPath = FCPATH . 'myvegiz-a3615-firebase-adminsdk-k1wrf-c87a10b550.json';

		if (!file_exists($serviceAccountPath)) {
			throw new Exception('Service account key file not found: ' . $serviceAccountPath);
		}

		$credentials = new ServiceAccountCredentials(
			'https://www.googleapis.com/auth/firebase.messaging',
			$serviceAccountPath
		);
		$accessToken = $credentials->fetchAuthToken()['access_token'];

		$responses = [];

		foreach ($data['device_id'] as $token) {
			$body = [
				'message' => [
					'token' => $token,
					'data' => [
						'title' => (string) $data['title'],
						'message' => (string) $data['message'],
						'type' => 'ringing',
						'sound' => 'ringing.mp3'
					],
					'notification' => [
						'title' => $notification['title'],
						'body' => $notification['message'],
						'image' => $image,
					],
					'android' => [
						'priority' => 'high',
						'ttl' => '1000s',
						'notification' => [
							'sound' => 'ringing.mp3',
							'image' => $image,
							'vibrate_timings' => ["0.5s", "1s", "0.5s"],
						],
					]
				]
			];

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
			curl_setopt($ch, CURLOPT_HTTPHEADER, [
				'Authorization: Bearer ' . $accessToken,
				'Content-Type: application/json'
			]);

			$result = curl_exec($ch);
			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

			if (curl_errno($ch)) {
				$responses[$token] = 'Curl Error: ' . curl_error($ch);
			} elseif ($httpCode >= 200 && $httpCode < 300) {
				$responses[$token] = json_decode($result, true);
			} else {
				$responses[$token] = 'Error: ' . $result;
			}

			curl_close($ch);
		}

		return $responses;
	}
	
	public function pushNotification($data, $notification)
	{
		$image = isset($data['image']) && !empty($data['image']) 
				? $data['image'] 
				: base_url('uploads/logo.png');
		$url = 'https://fcm.googleapis.com/v1/projects/myvegiz-a3615/messages:send';
		$serviceAccountPath = FCPATH . 'myvegiz-a3615-firebase-adminsdk-k1wrf-c87a10b550.json';

			if (!file_exists($serviceAccountPath)) {
				throw new Exception('Service account key file not found: ' . $serviceAccountPath);
			}

			// Get access token from service account
			$credentials = new \Google\Auth\Credentials\ServiceAccountCredentials(
				'https://www.googleapis.com/auth/firebase.messaging',
				$serviceAccountPath
			);
			$accessToken = $credentials->fetchAuthToken()['access_token'];

			$responses = [];

			foreach ($data['device_id'] as $token) {
				$body = [
					'message' => [
						'token' => $token,
						'data' => [
							'title' => (string)$data['title'],
							'type' => (string)$data['type'],
							'message' => (string)$data['message']
						],
						'notification' => [
							'title' => (string)$notification['title'],
							'body' => (string)$notification['message'],
							'image' => $image
						],
						'android' => [
							'priority' => 'high',
							'notification' => [
								'image' => $image,
								'sound' => 'default'
							]
						]
					]
				];

				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
				curl_setopt($ch, CURLOPT_HTTPHEADER, [
					'Authorization: Bearer ' . $accessToken,
					'Content-Type: application/json'
				]);

				$result = curl_exec($ch);
				$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

				if (curl_errno($ch)) {
					$responses[$token] = 'Curl Error: ' . curl_error($ch);
				} elseif ($httpCode >= 200 && $httpCode < 300) {
					$responses[$token] = json_decode($result, true);
				} else {
					$responses[$token] = 'Error: ' . $result;
				}

				curl_close($ch);
			}

			return $responses;
	}
	
}