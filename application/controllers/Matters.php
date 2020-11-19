<?php
require APPPATH . 'controllers/api/Token.php';

defined('BASEPATH') OR exit('No direct script access allowed');

class Matters extends Token {
  public function __construct() {
    parent::__construct();
    $this->load->library('form_validation');
    $this->load->model('m_matters');
  }

  public function index_get($id = NULL) {
    $data = $this->m_matters->getMatters($id);
    
    if(!$data) {
      $this->response([
        'status' => FALSE,
        'message' => 'Cannot get matters'
      ], 400);
    }

    $this->response([
      'status' => TRUE,
      'message' => 'Succesfully get matters',
      'data' => $data
    ], 200);
  }
}

?>
