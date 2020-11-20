<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_matters extends CI_Model {

  public function getMatters($id) {
    if($id) {
      return $this->db->get_where('matters', ['id' => $id])->result_array();
    }
    return $this->db->get('matters')->result_array();
  }

  public function addMatter($data) {
    $this->db->insert('matters', $data);
    return $this->db->affected_rows();
  }

  public function deleteMatter($id) {
    $this->db->delete('matters', ['id' => $id]);
    return $this->db->affected_rows();
  }

  public function updateMatter($data, $id) {
    $this->db->update('matters', $data, ['id' => $id]);
    return $this->db->affected_rows();
  }
}

?>
