<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_courses extends CI_Model {

  public function getCourses() {
    return $this->db->get('courses')->result_array();
  }

}
?>
