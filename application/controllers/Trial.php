<?php

class Trial extends CI_Controller
{
    var $session_key;
    public function __construct()
    {
        parent::__construct();
        $this->load->library('notificationlibv_3');
    }

    public function index()
    {
        $orderCode = "ORD1223";
        $title = "New Order Assigned";
        $message = "You have been assigned an order $orderCode by the administrator. Continue to deliver this assigned order.";
        $random = rand(0, 999);
        $DeviceIdsArr[] = "cXp0Rtl8Q0mNFNb6aXm8zo:APA91bGdjEFozGqKGD-oXv6DZC-rlZLLycCpb52m3sF8FLfKgX6r8xdJbIX69_nEciU22Cm8BxA-fdJ2ZkOM49pWewBu1pGK5wJlwLl4LmOvc1lxCoxYFfvQVJGmVIWfpVTJHaQsXV1Y";
        $dataArr = array();
        $dataArr['device_id'] = $DeviceIdsArr;
        $dataArr['message'] = $message; //Message which you want to send
        $dataArr['title'] = $title;
        $dataArr['order_id'] = $orderCode;
        $dataArr['random_id'] = $random;
        $dataArr['type'] = 'order';
        $notification['device_id'] = $DeviceIdsArr;
        $notification['message'] = $message; //Message which you want to send
        $notification['title'] = $title;
        $notification['order_id'] = $orderCode;
        $notification['random_id'] = $random;
        $notification['type'] = 'order';
        $notify = $this->notificationlibv_3->sendDeliveryNotification($dataArr, $notification, "ringing");
        print_r($notify);
    }
}
