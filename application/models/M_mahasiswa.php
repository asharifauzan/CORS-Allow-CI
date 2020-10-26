<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_mahasiswa extends CI_Model {

  public function getMahasiswa($id) {
    if(!$id) {
      return $this->db->select('users.*')
                      ->from('users')
                      ->join('user_type', 'user_type.id = users.id_type')
                      ->where('user_type.type', 'mahasiswa')
                      ->get()->result_array();
    }

    return $this->db->select('users.*')
                    ->from('users')
                    ->join('user_type', 'user_type.id = users.id_type')
                    ->where('user_type.type', 'mahasiswa')
                    ->where('users.id', $id)
                    ->get()->result_array();
  }

  public function getIdType() {
    return $this->db->select('id')
                    ->get_where('user_type', ['type' => 'mahasiswa'])->row_array()['id'];
  }

  public function addMahasiswa($data) {
    return $this->db->insert('users', $data);
  }

  public function deleteMahasiswa($id) {
    return $this->db->delete('users', ['id' => $id]);
  }

}
?>
