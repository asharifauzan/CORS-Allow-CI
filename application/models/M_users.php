<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_users extends CI_Model {

  private $_tblname = 'users';

  public function getUser($email) {
    $this->db->select('users.id, users.name, users.email, users.password, user_type.type');
    $this->db->from($this->_tblname);
    $this->db->join('user_type', 'users.id_type = user_type.id');
    $this->db->where(['email' => $email]);
    return $this->db->get()->row_array();
  }

  public function getUserByRole($role, $id) {
    $this->db->select('users.*')
    ->from('users')
    ->join('user_type', 'user_type.id = users.id_type')
    ->where('user_type.type', $role);

    // get all user by role
    if(!$id) {
      return $this->db->get()->result_array();
    }

    // get by id user role
    return $this->db->where('users.id', $id)
                    ->get()->result_array();
  }

  public function getIdType($role) {
    return $this->db->select('id')
                    ->get_where('user_type', ['type' => $role])->row_array()['id'];
  }

  public function addUser($data) {
    return $this->db->insert('users', $data);
  }

  public function deleteUser($id) {
    return $this->db->delete('users', ['id' => $id]);
  }

}
?>
