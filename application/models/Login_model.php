<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login_model extends CI_Model{

  public function __construct()
  {
    parent::__construct();
    $this->load->database();

  }

  public function is_valid($email){
    $this->db->select('*');
    $this->db->from('users');
    $this->db->where('email',$email);
    $query = $this->db->get();
    return $query->row();
  }

  public function is_valid_num($email){
    $this->db->select('*');
    $this->db->from('users');
    $this->db->where('email',$email);
    $query = $this->db->get();
    return $query->num_rows();
  }

}

?>
