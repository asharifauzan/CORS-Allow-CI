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

  public function index_post() {
    $this->form_validation->set_rules('name', 'Name', 'required');

    // jika form validation tidak jalan
    if ( !$this->form_validation->run() ) {
      $form_error = $this->form_validation->error_array();
      $this->response([
        'status' => FALSE,
        'message' => 'form tidak lengkap',
        'error' => ['form_error' => $form_error]
      ], 400);
    }

    // menangkap form add
    $data = [
      'code' => $this->post('code'),
      'name' => $this->post('name')
    ];

    // jika gagal add course
    if (!$this->m_courses->addCourse($data)) {
      $this->response([
        'status' => FALSE,
        'message' => 'Failed to add course'
      ], 400);
    }

    // berhasil add course
    $this->response([
      'status' => TRUE,
      'message' => 'Succesfully add new course'
    ], 200);
  }

}
?>
