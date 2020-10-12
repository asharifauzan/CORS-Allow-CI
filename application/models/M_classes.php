<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class M_classes extends CI_Model {

  private $_tblname = 'class';

  public function getClasses() {
    $this->db->select(['class.id', 'class.className as kelas',
                       'GROUP_CONCAT(users.id) as students_id',
                       'GROUP_CONCAT(users.name) as students_name',
                       'schedules.day as hari',
                       'schedules.id_lecturer as dosen', 'courses.name as mata_kuliah'])
             ->from($this->_tblname)
             ->join('students', 'students.id_class = class.id')
             ->join('users', 'students.id_user = users.id')
             ->join('schedules', 'class.id_schedule = schedules.id')
             ->join('courses', 'schedules.id_courses = courses.code')
             ->group_by('class.id');
    return $this->db->get()->result_array();
  }
}

?>
