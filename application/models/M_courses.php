<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_courses extends CI_Model {

  public function getCourses($id) {
    if ($id) {
      return $this->db->get_where('courses', ['code' => $id])->result_array();
    }
    return $this->db->get('courses')->result_array();
  }

  public function addCourse($data) {
    $this->db->insert('courses', $data);
    return $this->db->affected_rows();
  }

  public function deleteCourse($id) {
    $this->db->delete('courses', ['code' => $id]);
    return $this->db->affected_rows();
  }

  public function updateCourse($id, $data) {
    $this->db->update('courses', $data, ['code' => $id]);
    return $this->db->affected_rows();
  }

}
?>
