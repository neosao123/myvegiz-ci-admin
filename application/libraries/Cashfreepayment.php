<?php
class Cashfreepayment
{

	public function payment(string $order_id, int $amount, string $currency, string $cust_id, string $email, string $phone, string $name)
	{
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
				    "return_url" => base_url() . "Apiv1_8/Payment/returnurl?order_id={order_id}",
				    "notify_url" => base_url() . "Apiv1_8/Payment/process",
					"payment_methods" => "cc,dc,upi"
				]
			];

			$post_obj = json_encode($post_array);

			$url  = CASHFREE_MODE == 'TEST' ? CASHFREE_TEST_URL : CASHFREE_LIVE_URL;
			$clientID = CASHFREE_MODE == 'TEST' ? CASHFREE_TEST_CLIENT_ID : CASHFREE_LIVE_CLIENT_ID;
			$clientSecret = CASHFREE_MODE == 'TEST' ? CASHFREE_TEST_CLIENT_SECRET : CASHFREE_LIVE_CLIENT_SECRET;

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/json',
				'Accept: application/json',
				'x-client-id: ' . $clientID,
				'x-client-secret: ' . $clientSecret,
				'x-api-version:2025-01-01'
			));
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_ENCODING, "");
			curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
			curl_setopt($ch, CURLOPT_TIMEOUT, 30);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_obj);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			//execute post
			$apiResult = curl_exec($ch);
			curl_close($ch);
			log_message("error", "cashfree result=>".json_encode($apiResult));
			return json_decode($apiResult, true);
		} catch (\Exception $ex) {
			return ["execption" => $ex->getMessage()];
		}
	}

	public function getOrderStatus(string $order_id)
	{
		try {
			$url = CASHFREE_MODE == 'TEST' ? CASHFREE_TEST_URL : CASHFREE_LIVE_URL;
			$url .= "/" . $order_id . "/payments";

			$clientID = CASHFREE_MODE == 'TEST' ? CASHFREE_TEST_CLIENT_ID : CASHFREE_LIVE_CLIENT_ID;
			$clientSecret = CASHFREE_MODE == 'TEST' ? CASHFREE_TEST_CLIENT_SECRET : CASHFREE_LIVE_CLIENT_SECRET;

			$ch = curl_init();
			curl_setopt(
				$ch,
				CURLOPT_HTTPHEADER,
				array(
					'Content-Type: application/json',
					'Accept: application/json',
					'x-client-id: ' . $clientID,
					'x-client-secret: ' . $clientSecret,
					'x-api-version:2025-01-01'
				)
			);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			//execute post
			$apiResult = curl_exec($ch);
			curl_close($ch);
			$s = json_decode($apiResult, true);
			return ["payments" => $s];
		} catch (\Exception $ex) {
			return ["exception" => $ex->getMessage()];
		}
	}

	public function getPaymentStatus(string $order_id, string $cf_payment_id)
	{
		try {
			$curl = curl_init();
			$url = CASHFREE_MODE == 'TEST' ? CASHFREE_TEST_URL : CASHFREE_LIVE_URL;
			$url .= "/$order_id/payments/$cf_payment_id";

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
			return ["execption" => $ex->getMessage()];
		}
	}


	function initiateRefund(string $order_id, int $amount)
	{
		$refund_id = 'REFUND' . rand(9, 9999) . date('dm');
		$post_array = array('refund_id' => "$refund_id", 'refund_note' => 'refund', 'refund_amount' => $amount);
		$post_obj = json_encode($post_array);
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://sandbox.cashfree.com/pg/orders/' . $order_id . '/refunds',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => $post_obj,
			CURLOPT_HTTPHEADER => array(
				'x-api-version: 2025-01-01',
				'x-client-id: ' . CASHFREE_CLIENT_ID,
				'x-client-secret: ' . CASHFREE_CLIENT_SECRET,
				'Content-Type: application/json'
			),
		));

		$response = curl_exec($curl);
		curl_close($curl);
		return json_decode($response, true);
	}

	function getRefund(string $order_id, string $refund_id)
	{
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://sandbox.cashfree.com/pg/orders/' . $order_id . '/refunds/' . $refund_id,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_HTTPHEADER => array(
				'x-client-id: ' . CASHFREE_CLIENT_ID,
				'x-client-secret: ' . CASHFREE_CLIENT_SECRET,
				'x-api-version: 2025-01-01'
			),
		));

		$response = curl_exec($curl);
		curl_close($curl);
		return json_decode($response, true);
	}
}
