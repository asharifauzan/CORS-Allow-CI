<?php
require APPPATH . 'controllers/api/Token.php';

defined('BASEPATH') OR exit('No direct script access allowed');

class Classes extends Token {

  public function __construct() {
    parent::__construct();
    parent::authToken($admin_access = true);
    $this->load->model('m_classes');
    $this->load->library('form_validation');
  }

  public function index_get() {
    // mengambil seluruh kelas pada databse
    $class = $this->m_classes->getClasses();

    for ($i=0; $i < count($class); $i++) {
      $students_id   = explode(',', $class[$i]['students_id']);
      $students_name = explode(',', $class[$i]['students_name']);
      $all_students = [];

      for ($j=0; $j < count($students_id); $j++) {
        $all_students[$j] = ['id' => $students_id[$j], 'name' => $students_name[$j]];
      }

      $class[$i]['students'] = $all_students;
      unset($class[$i]['students_id']);
      unset($class[$i]['students_name']);
    }

    // memberikan response sukses dan mengirim daftar kelas
    $this->response([
      'status' => TRUE,
      'message' => 'success',
      'data' => ($class)
    ], 400);
  }
  
}
?>
