<?php
require APPPATH . 'controllers/api/Token.php';

defined('BASEPATH') OR exit('No direct script access allowed');

class Courses extends Token {

  public function __construct() {
    parent::__construct();
    parent::authToken($admin_access = true);
    $this->load->model('m_courses');
    $this->load->library('form_validation');
  }

  public function index_get($id = NULL) {
    $courses = $this->m_courses->getCourses($id);

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

  public function index_delete($id) {
    // jika gagal delete course
    if ( !$this->m_courses->deleteCourse($id) ) {
      $this->response([
        'status' => FALSE,
        'message' => 'Failed to delete course'
      ], 400);
    }

    // berhasil delete courses
    $this->response([
      'status' => TRUE,
      'message' => 'Succesfully delete course'
    ], 200);
  }

  public function index_put($id) {
    // data dari method PUT yg ingin divalidasi
    $set_data['name'] = $this->put('name');
    $this->form_validation->set_data($set_data);

    $this->form_validation->set_rules('name', 'Name', 'required');

    if ( !$this->form_validation->run() ) {
      $form_error = $this->form_validation->error_array();
      $this->response([
        'status' => FALSE,
        'message' => 'form tidak lengkap',
        'error' => ['form_error' => $form_error]
      ], 400);
    }

    // menangkap form update
    $data = [
      'name' => $this->put('name')
    ];

    // jika gagal update course
    if (!$this->m_courses->updateCourse($id, $data)) {
      $this->response([
        'status' => FALSE,
        'message' => 'Failed to update course'
      ], 400);
    }

    // berhasil update course
    $this->response([
      'status' => TRUE,
      'message' => 'Succesfully update course'
    ], 200);
  }

}
?>
