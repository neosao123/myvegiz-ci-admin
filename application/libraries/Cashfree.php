<?php
class Cashfree
{
	public function create_order(string $order_id, int $amount, string $currency, string $cust_id, string $email, string $phone, string $name){
	    try {
			$post_array = [
				"order_id" => $order_id,
				"order_amount" => $amount,
				"order_currency" => $currency,
				"customer_details" => [
					"customer_id" => $cust_id,
					"customer_email" => $email,
					"customer_phone" => $phone,
					"customer_name" => $name
				],
				"order_meta" => [
					"return_url" => base_url() . "Apiv1_8/Payment/returnurl?order_id={$order_id}",
					"notify_url" => base_url() . "Apiv1_8/Payment/process",
					"payment_methods" => "cc,dc,upi"
				]
			];

			$post_obj = json_encode($post_array);

			$url = (CASHFREE_MODE == 'TEST') ? CASHFREE_TEST_URL : CASHFREE_LIVE_URL;
			$clientID = (CASHFREE_MODE == 'TEST') ? CASHFREE_TEST_CLIENT_ID : CASHFREE_LIVE_CLIENT_ID;
			$clientSecret = (CASHFREE_MODE == 'TEST') ? CASHFREE_TEST_CLIENT_SECRET : CASHFREE_LIVE_CLIENT_SECRET;

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, [
				'Content-Type: application/json',
				'x-api-version: 2025-01-01',
				'x-client-id: ' . $clientID,
				'x-client-secret: ' . $clientSecret
			]);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_obj);

			$response = curl_exec($ch);
			$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);

			log_message("error", "Cashfree response: " . $response);

			return json_decode($response, true);
		} catch (\Exception $e) {
			return ["error" => $e->getMessage()];
		}	
	}
	
	public function getOrderStatus(string $order_id)
	{
		try {
			$curl = curl_init();

			$url = (CASHFREE_MODE == 'TEST' ? CASHFREE_TEST_URL : CASHFREE_LIVE_URL);
			$url .= "/pg/orders/" . $order_id; 

			$clientID = CASHFREE_MODE == 'TEST' ? CASHFREE_TEST_CLIENT_ID : CASHFREE_LIVE_CLIENT_ID;
			$clientSecret = CASHFREE_MODE == 'TEST' ? CASHFREE_TEST_CLIENT_SECRET : CASHFREE_LIVE_CLIENT_SECRET;

			curl_setopt_array($curl, array(
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'GET',
				CURLOPT_HTTPHEADER => array(
					'x-api-version: 2025-01-01',
					'x-client-id: ' . $clientID,
					'x-client-secret: ' . $clientSecret,
					'Content-Type: application/json'
				),
			));

			$response = curl_exec($curl);
			curl_close($curl);

			return json_decode($response, true);
		} catch (\Exception $ex) {
			return ["exception" => $ex->getMessage()];
		}
	}

}