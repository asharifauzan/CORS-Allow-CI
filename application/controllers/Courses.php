<?php
require APPPATH . 'controllers/api/Token.php';

defined('BASEPATH') OR exit('No direct script access allowed');

class Courses extends Token {

  public function __construct() {
    parent::__construct();
    parent::authToken();
    $this->load->model('m_courses');
    $this->load->library('form_validation');
  }

  public function index_get() {
    $courses = $this->m_courses->getCourses();

    // jika course tidak ada
    if (!$courses) {
      $this->response([
        'status' => FALSE,
        'message'=> 'Cannot get course'
      ], 404);
    }

    $this->response([
      'status' => TRUE,
      'message' => 'Succesfully get course',
      'data' => $courses
    ], 200);
  }

}
?>
