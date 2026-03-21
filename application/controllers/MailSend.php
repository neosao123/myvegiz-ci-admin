<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class MailSend extends CI_Controller {
	public function __construct()
    {
        parent::__construct();
        $this->load->model('GlobalModel');
		$this->load->model('GlobalModel1'); 
		$this->load->model('Testing'); 
		$this->load->helper('file'); 
		$this->load->library('pagination');
	
    }
   
	public function p()
	{
		print_r($this->GlobalModel->selectData('sentmails')->result());
		exit();
	}
	
	public function sentboxMail()
	{
		//$data['sentmails'] = $this->GlobalModel->selectData('sentmails');
		/* print_r($data['sentmails']->result());
		exit(); */
		$this->load->view('dashboard/header');
		$this->load->view('dashboard/inboxMail');
		$this->load->view('dashboard/footer');
	}
	
	public function getMailList()
	{
		$totalRows=0;
		
		$mailCategory=$this->input->post('mailCategory');
		$limitNo=$this->input->post('limitNo');
		$offset=$this->input->post('offset');
		
		$totalRows=$this->Testing->totalMail($mailCategory);
		
		if($mailCategory!="ALL")
		{
			$Records= $this->Testing->getSentMailByField($offset,$limitNo,$mailCategory);
		}
		else
		{
			$Records= $this->Testing->getSentMail($offset,$limitNo);
		} 
		
		$tableHtml='';
		
		foreach($Records->result() as $row)
		{
			 $tableHtml.='<tr class="unread">
										
				<td class="chb">
					<div>
						
					</div>
				</td>
				
				<td class="user-name">
					<h6 class="m-b-0">'.$row->mailTo.'</h6>
				</td>
				
				<td class="max-texts"> 
					<a class="link" data-seq='.$row->code.' href="javascript: void(0)"> 
						<span class="blue-grey-text text-darken-4">'.$row->mailSubject.'</span>
					</a>
				</td>';
				
				
				if($row->mailAttachments!=null || $row->mailAttachments!='')
				{
				 $tableHtml.='<td class="clip"><i class="fa fa-paperclip"></i></td>';
				}
				else
				{
				 $tableHtml.='<td></td>';
				}
			
				 $tableHtml.='<td class="time">'.date('Y-m-d', strtotime($row->editDate)).' </td>
			</tr>';
			
		}  
		
		$data=[
			'tableHtml'=>$tableHtml,
			'totalRows'=>$totalRows
		];
		print_r(json_encode($data));
	}
	
	public function getMsg()
	{
		$code=$this->input->post('code');
		
		//Activity Track Starts
							
		$addID = $this->session->userdata['logged_in']['code'];
		$userRole = $this->session->userdata['logged_in']['role'];
		$userName = $this->session->userdata['logged_in']['username']; 
		$role = "";
		
		switch($userRole){
				case "ADM" : $role="Admin"; break;
				case "USR" : $role="User"; break;
			}
		
		$ip=$_SERVER['REMOTE_ADDR']; 
		
		//Activity Track Ends
		
		$Records= $this->GlobalModel->selectDataByField('code',$code,'sentmails');
		
		foreach($Records->result() as $row)
		{
			echo '<div class="card-body border-bottom">
				<h4 class="m-b-0">'.$row->mailSubject.'</h4>
				</div>
				<div class="card-body border-bottom">
					<div class="d-flex no-block align-items-center m-b-20">
						<div class="">
							<span>from : '.$row->mailFrom.'</span>
						</div>
					</div>
					<div class="d-flex no-block align-items-center m-b-30">
						<div class="">
							<span>to : '.$row->mailTo.'</span>
						</div>
					</div>
					<div class="d-flex no-block align-items-center m-b-30">
						<div class="">
							<span>cc : '.$row->mailCc.'</span>
						</div>
					</div>
					<p>'.$row->mailText .'</p>
					
				</div>';
				
				if($row->mailAttachments!=null || $row->mailAttachments!='')
				{
					$splittedstring=explode(",",$row->mailAttachments);
					$n=sizeof($splittedstring);
					echo '<div class="card-body">
						<h4><i class="fa fa-paperclip m-r-10 m-b-10"></i> Attachments <span>('.$n.')</span></h4>';
					
					for($i=0;$i<$n;$i++)
					{
						echo '<div class="row">
							<div class="col-md-2">
								<a href="'.$splittedstring[$i].'">Attachment Link</a>
							</div>
						</div>';
					}
					
					echo '</div>';
				}
				
				//Activity Track start	
				
				$text = $role." ".$userName.' viewed mail of "'.$row->mailSubject.'" from '.$ip;
				
				$log_text = array(
								'code' => "demo",
								'addID'=>$addID,
								'logText' => $text
							);
				
				$this->GlobalModel->activityAdd($log_text,'activitymaster','ACT');
				
				//Activity Track Ends	
			
		}
	}
	
	
	// Send Email
	
	public function sendEmail()
	{	
		
		$mailCategory=$this->input->post('category_type');
		
		$data=[
			'mailCategory'=>$mailCategory,
			'mailFrom'=>$this->input->post('example_from'),
			'mailTo'=>$this->input->post('example_to'),
			'mailCc'=>$this->input->post('example_cc'),
			'mailSubject'=>$this->input->post('example_subject'),
			'mailText'=>trim($this->input->post('summernote')),
			'mailAttachments'=>rtrim($this->input->post('upload_path'),','),
			'addID'=>$addID,
			'addIP'=>$ip
		];
	
		$code=$this->GlobalModel->addNew($data,'sentmails','Mail');
		
		 $config = array (
		  'protocol' =>'mail',
		  'mailtype' => 'text',
		  'charset'  => 'utf-8',
		  'priority' => '1',
		  'smtp_crypto'=>'tls'
			
		 );
		
		$this->load->library('email',$config);
		$this->email->set_newline("\r\n");
		$this->email->from($this->input->post('example_from'));
		$this->email->to($this->input->post('example_to'));
		$this->email->cc($this->input->post('example_cc'));
		$this->email->subject($this->input->post('example_subject'));
		$this->email->message(strip_tags($this->input->post('summernote')));
		
		$splittedstring=explode(",",$this->input->post('upload_path'));
		$n=sizeof($splittedstring);
		for($i=0;$i<$n;$i++)
		{
			$this->email->attach($splittedstring[$i]);
		}
		
		if($this->email->send())
		{
			if($mailCategory=='PAYSLIP')
			{
				for($i=0;$i<$n;$i++)
				{
					unlink($splittedstring[$i]);
				} 
			}
			
			$data=[
				'mailStatus'=>'1'
			];
			
			$this->GlobalModel->doEdit($data,'sentmails',$code);
			
			echo "<script>window.close();</script>";
		}
		else
		{
			$data=[
					'mailStatus'=>'0'
			];
			
			$this->GlobalModel->doEdit($data,'sentmails',$code);
			$this->session->set_flashdata('msg','<h4 id="msg" style="color:red" >Mail Not Send Successfully...!</h4>');
			echo "<script>window.history.back();location.reload(); </script>";
			//print_r( $this->email->print_debugger()); 
		} 
	}
	
	// End Send Email
	
	// Upload File 
	
	public function upload_file()
	{
		
		$mailCategory=$this->uri->segment(3);
		
		$uploadPath='';
		
		switch($mailCategory)
		{
			case 'RFQ': 
				
				if(! file_exists(FCPATH.'uploads/attachments/RFQ'))
				{
					mkdir(FCPATH.'uploads/attachments/RFQ');
				}
				
				$uploadPath='./uploads/attachments/RFQ/';
				
			break;
			
			case 'PO': 
				
				if(! file_exists(FCPATH.'uploads/attachments/PO'))
				{
					mkdir(FCPATH.'uploads/attachments/PO');
				}
				
				$uploadPath='./uploads/attachments/PO/';
				
			break;
			
			case 'SO': 
				
				if(! file_exists(FCPATH.'uploads/attachments/SO'))
				{
					mkdir(FCPATH.'uploads/attachments/SO');
				}
				
				$uploadPath='./uploads/attachments/SO/';
				
			break;
			
			case 'QFC': 
				
				if(! file_exists(FCPATH.'uploads/attachments/QFC'))
				{
					mkdir(FCPATH.'uploads/attachments/QFC');
				}
				
				$uploadPath='./uploads/attachments/QFC/';
				
			break;
			
			case 'PAYSLIP': 
				
				if(! file_exists(FCPATH.'uploads/attachments/PAYSLIP'))
				{
					mkdir(FCPATH.'uploads/attachments/PAYSLIP');
				}
				
				$uploadPath='./uploads/attachments/PAYSLIP/';
				
			break;
		}
		
		$config['upload_path']=$uploadPath;
		$config['allowed_types']='jpg|pdf';
		$config['overwrite'] = TRUE;
		$this->load->library('upload',$config);
		if($this->upload->do_upload('file'))
		{
			$file_name=$_FILES['file']['name'];
			$uploadFilePath=$uploadPath.str_replace(' ','_',$file_name);
			echo $uploadFilePath;
			$this->upload->data();
		}
		else
		{         
			echo json_encode('');
			return $this->upload->display_errors();
		}
	}
	
	// End Upload
}

?>