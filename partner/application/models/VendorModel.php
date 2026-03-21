<?php
defined('BASEPATH') or exit('No direct script access allowed');
date_default_timezone_set('Asia/Kolkata');
class VendorModel extends CI_Model
{

    public function _construct()
    {
        parent::_construct();
        date_default_timezone_set('Asia/Kolkata');
    }
    // Read vendor indormation
    public function read_user_information($username)
    {
        $condition = "ownerContact =" . "'" . $username . "'";
        $this->db->select('*');
        $this->db->from('vendor');
        $this->db->where($condition);
        $this->db->limit(1);
        $query = $this->db->get();

        if ($query->num_rows() == 1) {
            return $query->result();
        } else {
            return false;
        }
    }
	
    //FOR USER AUTHENTICATION 
    public function login($data)
    {
        $condition = "`ownerContact` ='" . $data['username'] . "' AND `password` = '" . $data['password'] . "' AND `isActive` = '1'";
        $this->db->select('*');
        $this->db->from('vendor');
        $this->db->where($condition);
        $this->db->limit(1);
        $query = $this->db->get();
		
        if ($query->num_rows() == 1) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
}