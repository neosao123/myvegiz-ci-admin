<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Query extends CI_Controller
{  
	 public function __construct()
    {
        parent::__construct();
    }
	
	public function run()
	{
	$this->db->query("UPDATE `ordermaster` SET `orderStatus` = 'PND', `editDate` = NULL WHERE `ordermaster`.`code` = 'ORDER72699_2';");
	
	}
	
	  public function test()
	{
		$res = $this->db->query("select * from locationmaster");
		if($res->num_rows()>0)
		{
			echo "<pre>";
			print_r($res->result());
			echo "</pre>";
		}	
	}
}