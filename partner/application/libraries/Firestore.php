<?php

class Firestore
{
    public function __construct()
    {
        //return "construct function was initialized.";
    }

    /**
     * @param $restaurant = string code of restaurant
     * @param $method = string value 'PATCH'
     * @return returns array with status (integer) and msg (string)
     */
    public function update_order_status(string $orderCode, string $status, string $orderType)
    {
        $project_id = FIRESTORE_PROJECT_ID;
        $firestore_key = FIREBASE_KEY;
        $firestore_data  = [
            'orderStatus' => ["stringValue" => $status],
            'orderType' => ["stringValue" => $orderType],
            'timeStamp'    => ['integerValue' => time()]
        ];
        $fireData = ["fields" => (object)$firestore_data];
        $json = json_encode($fireData);
        $url = "https://firestore.googleapis.com/v1beta1/projects/$project_id/databases/(default)/documents/Orders/" . $orderCode;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($json),
                'X-HTTP-Method-Override: PATCH'
            ),
            CURLOPT_URL => $url . '?key=' . $firestore_key,
            CURLOPT_USERAGENT => 'cURL',
            CURLOPT_POSTFIELDS => $json
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $response_array = json_decode($response, TRUE);
        return $response_array;
    }
}