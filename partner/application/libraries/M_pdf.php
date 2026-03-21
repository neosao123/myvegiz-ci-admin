<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class M_pdf {
    
    public function __construct()
	{
		$CI = & get_instance();
        log_message('Debug', 'mPDF class is loaded.');
	}
  
    function load($param=NULL)
    {
        include_once APPPATH.'/third_party/MPDF57/mpdf.php';
        if ($params == NULL)
        {
            $param = '"en-GB-x","A4","","",10,10,10,10,6,3';          		
        }
		
		$mpdfObj=new mPDF();
		$mpdfObj->mPDFF($param);
		
        return $mpdfObj;
    }
}
?>