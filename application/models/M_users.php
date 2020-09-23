<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_users extends CI_Model {

  private $_tblname = 'users';

  public function getUser($email) {
    $this->db->select('users.password, user_type.type');
    $this->db->from($this->_tblname);
    $this->db->join('user_type', 'users.id_type = user_type.id');
    $this->db->where(['email' => $email]);
    return $this->db->get()->row_array();
  }

}
?>
