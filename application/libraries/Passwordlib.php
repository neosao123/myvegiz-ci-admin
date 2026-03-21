<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    
class Passwordlib {
    var $CI=null;
    public function __construct()
    {
        $this->CI = & get_instance();
    }


    public function base64url_encode($data) { 
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '='); 
    } 
    public function base64url_decode($data) { 
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT)); 
    }
    
}
?>