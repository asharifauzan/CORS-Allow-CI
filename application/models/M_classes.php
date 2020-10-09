<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class M_classes extends CI_Model {

  private $_tblname = 'class';

  public function getClasses() {
    // mengambil jadwal kelas beserta dosen dan metkulnya
    $this->db->select(['schedules.id', 'id_user as mahasiswa',
                      'className as kelas', 'active',
                      'day', 'courses.name as mata_kuliah',
                      'id_lecturer as dosen'])
    ->from('schedules')
    ->join($this->_tblname, 'schedules.id_class = class.id')
    ->join('courses', 'courses.code=schedules.id_courses')
    ->order_by('schedules.id', 'asc');
    return $this->db->get()->result_array();
  }

}

?>
