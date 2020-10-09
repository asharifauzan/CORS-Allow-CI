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
    // memberikan response sukses dan mengirim daftar kelas
    $this->response([
      'status' => TRUE,
      'message' => 'success',
      'data' => $class
    ], 400);
  }
  
}
?>
