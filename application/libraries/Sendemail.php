<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	
class Sendemail {
	private $CI;
    public function __construct()
	{
		$this->CI = &get_instance();
		$this->CI->load->library('phpmailer_lib');
	}
	
	public function sendMailOnly($to,$subject,$message)
	{
		// PHPMailer object
        $mail = $this->CI->phpmailer_lib->load(); 
        // SMTP configuration
		$mail->isSMTP();
		$mail->Host = 'localhost';
		// $mail->Host = 'smtp.example.com';
		$mail->SMTPAuth = false;
		$mail->SMTPAutoTLS = false; 
		$mail->Port = 25; 
        $mail->SMTPAuth = false;
		$mail->Username = mailId;
		$mail->Password = mailpassword;
		//send mail form
        $mail->setFrom(mailId, AppName);
        //$mail->addReplyTo(mailId, 'Click2Courier');
        // Add a recipient
        $mail->addAddress($to);  
        // Email subject
        $mail->Subject = $subject;
        //$mail->addAttachment($uploadfile, 'My uploaded file');
		// Set email format to HTML
        $mail->isHTML(true);        
        // Email body content
		$mail->CharSet = "UTF-8";
        $mailContent = $message;
        $mail->Body = $mailContent;
        // Send email
		// return $a = $mail->send();
        if(!$mail->send()){
            $response['success'] = 'false';
			$response['message'] = 'Mailer Error: ' . $mail->ErrorInfo;            
        }else{
            $response['success'] = 'true';
			$response['message'] = 'Mail send successfully';
        } 
		return $response;
	}
	
	//automated mail sent to sales@getgologistics.com from info@ambhconcepts.in
	public function sendMailInfoToSales($subject,$message)
	{
		// PHPMailer object
        $mail = $this->CI->phpmailer_lib->load(); 
        // SMTP configuration
		$mail->isSMTP();
		$mail->Host = 'localhost';
		$mail->SMTPAuth = false;
		$mail->SMTPAutoTLS = false; 
		$mail->Port = 25; 
        $mail->SMTPAuth = false;
		$mail->Username = fromMailToGetGo;
		$mail->CharSet = "UTF-8";
		$mail->Password = fromMailToGetGoPassword;
		//send mail form
        $mail->setFrom(mailId, 'Click2Courier');
        //$mail->addReplyTo(mailId, 'Click2Courier');
        // Add a recipient
        $mail->addAddress(getGoSalesMail);
        // Email subject
        $mail->Subject = $subject;
        //$mail->addAttachment($uploadfile, 'My uploaded file');
		// Set email format to HTML
        $mail->isHTML(true);        
        // Email body content
        $mailContent = $message;
        $mail->Body = $mailContent;
        // Send email
        if(!$mail->send()){
            $response['success'] = 'false';
			$response['message'] = 'Mailer Error: ' . $mail->ErrorInfo;            
        }else{
            $response['success'] = 'true';
			$response['message'] = 'Mail send successfully';
        } 
		return $response;
	}
	
	function sendMailWithAttachment($to,$subject,$message,$attachments=array()){
		
		// PHPMailer object
        $mail = $this->CI->phpmailer_lib->load(); 
        // SMTP configuration
		$mail->isSMTP();
		$mail->Host = 'localhost';
		$mail->SMTPAuth = false;
		$mail->SMTPAutoTLS = false; 
		$mail->Port = 25; 
        $mail->SMTPAuth = false;
		$mail->Username = mailId;
		$mail->Password = mailpassword;
		//send mail form
        $mail->setFrom(mailId, 'Click2Courier');
        //$mail->addReplyTo(mailId, 'Click2Courier');
        // Add a recipient
        $mail->addAddress($to);  
        // Email subject
        $mail->Subject = $subject;
		if(sizeof($attachments)>0)
		for($ct=0;$ct<count($attachments);$ct++){
			$mail->AddAttachment($attachments[$ct]);
		}
		// Set email format to HTML
        $mail->isHTML(true);        
        // Email body content
        $mailContent = $message;
        $mail->Body = $mailContent;
        // Send email
        if(!$mail->send()){
            $response['success'] = 'false';
			$response['message'] = 'Mailer Error: ' . $mail->ErrorInfo;            
        }else{
            $response['success'] = 'true';
			$response['message'] = 'Mail send successfully';
        } 
		return $response;
	}
	
	public function sendGetGoMessage($to,$subject,$message,$attachments=array()){
		// PHPMailer object
        $mail = $this->CI->phpmailer_lib->load(); 
        // SMTP configuration
		$mail->isSMTP();
		$mail->Host = 'localhost';
		$mail->SMTPAuth = false;
		$mail->SMTPAutoTLS = false; 
		$mail->Port = 25; 
        $mail->SMTPAuth = false;
		$mail->Username = mailId;
		$mail->Password = mailpassword;
		//send mail form
        $mail->setFrom(mailId, 'Click2Courier');
        //$mail->addReplyTo(mailId, 'Click2Courier');
        // Add a recipient
        $mail->addAddress($to);  
        // Email subject
        $mail->Subject = $subject;
		if(sizeof($attachments)>0)
		for($ct=0;$ct<count($attachments);$ct++){
			$mail->AddAttachment($attachments[$ct]);
		}
		// Set email format to HTML
        $mail->isHTML(true);        
        // Email body content
        $mailContent = $message;
        $mail->Body = $mailContent;
        // Send email
        if(!$mail->send()){
            $response['success'] = 'false';
			$response['message'] = 'Mailer Error: ' . $mail->ErrorInfo;            
        }else{
            $response['success'] = 'true';
			$response['message'] = 'Mail send successfully';
        } 
		return $response;
	}

}
?>